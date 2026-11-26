<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Support\TerminalLocation;
use App\Services\TerminalLifecycle;
use Illuminate\Http\Request;

/**
 * Taking a device off a site, sending it out again, and swapping one device
 * for another - the three things the field team needed that the old
 * one-row-per-device model could not express.
 *
 * All the moving is done by App\Services\TerminalLifecycle; this validates
 * what was asked for and says what happened.
 */
class TerminalLifecycleController extends Controller
{
    public function __construct(private readonly TerminalLifecycle $lifecycle)
    {
    }

    /** Devices in stock, waiting to go out. */
    public function stock(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $terminals = $this->lifecycle->availableStock()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    foreach (['terminal_id', 'serial_number', 'terminal_model', 'merchant_name'] as $column) {
                        $q->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            })
            ->with('siteAssignments')
            ->paginate(25)
            ->withQueryString();

        return view('pos-terminals.stock', compact('terminals', 'search'));
    }

    /** Take this device off its site and into stock. */
    public function collect(Request $request, PosTerminal $posTerminal)
    {
        if ($posTerminal->isInStock()) {
            return back()->with('error', $posTerminal->terminal_id.' is already in stock.');
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->lifecycle->collect(
            $posTerminal,
            $data['notes'] ?? null,
            $request->user()?->id,
        );

        return redirect()
            ->route('pos-terminals.show', $posTerminal)
            ->with('success', $posTerminal->terminal_id.' is now in stock. Its visit history and the site it came off are kept.');
    }

    /**
     * Choose what replaces this device at its site.
     *
     * Offers the devices already in stock, and the option of a brand-new one -
     * which is the distinction the field team asked to be able to make.
     */
    public function showMigrate(PosTerminal $posTerminal)
    {
        if ($posTerminal->isInStock()) {
            return redirect()
                ->route('pos-terminals.show', $posTerminal)
                ->with('error', 'This device is in stock, so it is not at a site to be replaced. Deploy it instead.');
        }

        $stock = $this->lifecycle->availableStock()->get();

        return view('pos-terminals.migrate', [
            'posTerminal' => $posTerminal,
            'stock' => $stock,
        ]);
    }

    /** Put a replacement on this device's site and send this one to stock. */
    public function migrate(Request $request, PosTerminal $posTerminal)
    {
        $data = $request->validate([
            'replacement' => ['required', 'in:stock,new'],
            'replacement_terminal_id' => ['nullable', 'required_if:replacement,stock', 'exists:pos_terminals,id'],
            'new_terminal_id' => ['nullable', 'required_if:replacement,new', 'string', 'max:255'],
            'new_serial_number' => ['nullable', 'string', 'max:255'],
            'new_terminal_model' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['replacement'] === 'stock') {
            $replacement = PosTerminal::findOrFail($data['replacement_terminal_id']);

            if ($replacement->is($posTerminal)) {
                return back()->withInput()->with('error', 'A device cannot replace itself.');
            }
        } else {
            $code = trim($data['new_terminal_id']);

            // A terminal ID is how the field team names a device; two devices
            // sharing one would make every visit ambiguous.
            $clash = PosTerminal::where('terminal_id', $code)->first();
            if ($clash) {
                return back()->withInput()->with(
                    'error',
                    'Terminal '.$code.' already exists'
                        .($clash->isInStock() ? ' and is in stock — choose it from the list instead.' : '.'),
                );
            }

            // Created with no site: deploy() puts it on the site being
            // replaced, in the same transaction. Condition columns are left at
            // their defaults - a device nobody has visited has no reading.
            $replacement = PosTerminal::create([
                'terminal_id' => $code,
                'merchant_name' => $posTerminal->merchant_name,
                'terminal_model' => $data['new_terminal_model'] ?: null,
                'serial_number' => $data['new_serial_number'] ?: null,
                'source' => 'office',
            ]);
        }

        $this->lifecycle->deploy(
            $replacement,
            site: [],
            replacing: $posTerminal,
            notes: $data['notes'] ?? null,
            by: $request->user()?->id,
        );

        return redirect()
            ->route('pos-terminals.show', $replacement)
            ->with('success', $replacement->terminal_id.' has taken over from '.$posTerminal->terminal_id
                .', which is now in stock. Both keep their own visit history.');
    }

    /** Choose where a device in stock should go. */
    public function showDeploy(PosTerminal $posTerminal)
    {
        // company_name is the one people recognise, but it is nullable.
        $clients = Client::orderByRaw('COALESCE(NULLIF(company_name, ""), name)')->get();

        return view('pos-terminals.deploy', [
            'posTerminal' => $posTerminal,
            'clients' => $clients,
            'previous' => $posTerminal->siteAssignments()->first(),
        ]);
    }

    /** Send a device out to a site. */
    public function deploy(Request $request, PosTerminal $posTerminal)
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'merchant_name' => ['required', 'string', 'max:255'],
            'merchant_id' => ['nullable', 'string', 'max:255'],
            'physical_address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $notes = $data['notes'] ?? null;
        unset($data['notes']);

        // The form asks for the location once. Province is the same thing
        // under a second column name, kept in step here.
        TerminalLocation::pair($data);

        $this->lifecycle->deploy(
            $posTerminal,
            site: $data,
            replacing: null,
            notes: $notes,
            by: $request->user()?->id,
        );

        return redirect()
            ->route('pos-terminals.show', $posTerminal)
            ->with('success', $posTerminal->terminal_id.' is now at '.$data['merchant_name']
                .'. Everywhere it has been before is still on its record.');
    }

    /** Take a device out of service, keeping its history. */
    public function retire(Request $request, PosTerminal $posTerminal)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->lifecycle->retire($posTerminal, $data['notes'] ?? null, $request->user()?->id);

        return redirect()
            ->route('pos-terminals.show', $posTerminal)
            ->with('success', $posTerminal->terminal_id.' is out of service. Its record and visit history are kept.');
    }
}
