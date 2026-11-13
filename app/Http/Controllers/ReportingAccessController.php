<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Issuing and withdrawing the tokens that read the reporting feed.
 *
 * The same thing `php artisan miav:bi-token` does, on a page, so handing an
 * analyst access does not need anyone on the server. Tying a token to a
 * client is the part that matters: without it the holder sees every client
 * MIAV serves, which is almost never what is meant.
 */
class ReportingAccessController extends Controller
{
    public function index()
    {
        $tokens = PersonalAccessToken::query()
            ->orderByDesc('id')
            ->get()
            ->map(function ($token) {
                $abilities = (array) $token->abilities;
                $clientId = null;
                foreach ($abilities as $ability) {
                    if (str_starts_with((string) $ability, 'bi:client:')) {
                        $clientId = (int) substr($ability, strlen('bi:client:'));
                    }
                }

                return (object) [
                    'id'          => $token->id,
                    'name'        => $token->name,
                    'is_feed'     => in_array('bi:read', $abilities, true),
                    'is_full'     => in_array('*', $abilities, true),
                    'client'      => $clientId ? Client::find($clientId)?->company_name : null,
                    'last_used'   => $token->last_used_at,
                    'created_at'  => $token->created_at,
                ];
            });

        return view('settings.reporting-access', [
            'tokens'   => $tokens,
            'clients'  => Client::orderBy('company_name')->get(),
            'feed_url' => url('/api/bi'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'client_id' => 'nullable|integer|exists:clients,id',
        ]);

        $owner = Employee::orderBy('id')->first();
        if (! $owner) {
            return back()->with('error', 'There are no employees, so there is nobody to issue a token against.');
        }

        $abilities = ['bi:read'];
        if (! empty($data['client_id'])) {
            // Narrows every dataset to this client - see BiController::clientScope.
            $abilities[] = 'bi:client:'.$data['client_id'];
        }

        $plain = $owner->createToken($data['name'], $abilities)->plainTextToken;

        // Shown once. It is flashed rather than stored because a token that can
        // be read back off a page later is a token that leaks later.
        return back()->with('issued_token', $plain)
                     ->with('issued_name', $data['name']);
    }

    public function destroy(int $id)
    {
        $token = PersonalAccessToken::find($id);
        if (! $token) {
            return back()->with('error', 'That token no longer exists.');
        }

        $name = $token->name;
        $token->delete();

        return back()->with('status', "«{$name}» was withdrawn and stops working immediately.");
    }
}
