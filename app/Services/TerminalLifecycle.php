<?php

namespace App\Services;

use App\Models\PosTerminal;
use App\Models\TerminalSiteAssignment;
use Illuminate\Support\Facades\DB;

/**
 * Moving a device through its working life: out to a site, back into stock,
 * and out again - without ever making a second record for the same device.
 *
 * Every transition goes through here so the device row and its site history
 * can never disagree. Each one is a single transaction: a half-finished move
 * would leave a device both at a site and in stock.
 *
 * The condition columns (`status`, `current_status`) are never written here.
 * Whether a device works is what technicians record on visits; where it is, is
 * the office's to say.
 */
class TerminalLifecycle
{
    public const DEPLOYED = 'deployed';
    public const COLLECTED = 'collected';
    public const RETIRED = 'retired';

    /** The site fields a spell at a site snapshots, in both tables. */
    private const SITE_FIELDS = [
        'client_id',
        'merchant_id',
        'merchant_name',
        'physical_address',
        'city',
        'region',
        'province',
        'area',
    ];

    /**
     * Take a device off its site and into stock, ready to go out again.
     *
     * The site details stay on the device row on purpose: every screen that
     * shows a device reads them, and a collected device reading "previously at
     * Foo" is more use to whoever is holding it than a blank. What marks it as
     * not-at-a-site is the lifecycle status.
     */
    public function collect(PosTerminal $terminal, ?string $notes = null, ?int $by = null): TerminalSiteAssignment
    {
        return DB::transaction(function () use ($terminal, $notes, $by) {
            $closed = $this->closeOpenAssignment(
                $terminal,
                TerminalSiteAssignment::ENDED_COLLECTED,
                $notes,
                $by,
            );

            $terminal->forceFill(['lifecycle_status' => self::COLLECTED])->save();

            return $closed;
        });
    }

    /**
     * Send a device out to a site.
     *
     * [$replacing] is the device coming off that site, if this is a swap: it
     * goes into stock, its spell is closed as replaced, and the two rows point
     * at each other so the trail from the old device to the new one is
     * readable from either end.
     *
     * @param  array<string, mixed>  $site  The site fields; anything missing is
     *                                      taken from [$replacing] when given.
     */
    public function deploy(
        PosTerminal $terminal,
        array $site = [],
        ?PosTerminal $replacing = null,
        ?string $notes = null,
        ?int $by = null,
    ): TerminalSiteAssignment {
        return DB::transaction(function () use ($terminal, $site, $replacing, $notes, $by) {
            $resolved = $this->resolveSite($site, $replacing);

            if ($replacing !== null && $replacing->is($terminal)) {
                // Replacing a device with itself would close the spell it is
                // about to start. Treat it as a plain redeployment.
                $replacing = null;
            }

            if ($replacing !== null) {
                $this->closeOpenAssignment(
                    $replacing,
                    TerminalSiteAssignment::ENDED_MIGRATED,
                    $notes,
                    $by,
                    $terminal->id,
                );

                $replacing->forceFill(['lifecycle_status' => self::COLLECTED])->save();
            }

            // A device already standing somewhere else is leaving that site.
            $this->closeOpenAssignment(
                $terminal,
                TerminalSiteAssignment::ENDED_MIGRATED,
                'Moved to another site.',
                $by,
            );

            $terminal->forceFill($resolved + ['lifecycle_status' => self::DEPLOYED])->save();

            return TerminalSiteAssignment::create($resolved + [
                'pos_terminal_id' => $terminal->id,
                'started_at' => now(),
                'recorded_by' => $by,
                'notes' => $notes,
            ]);
        });
    }

    /** Take a device out of service for good. It keeps its history. */
    public function retire(PosTerminal $terminal, ?string $notes = null, ?int $by = null): void
    {
        DB::transaction(function () use ($terminal, $notes, $by) {
            $this->closeOpenAssignment(
                $terminal,
                TerminalSiteAssignment::ENDED_RETIRED,
                $notes,
                $by,
            );

            $terminal->forceFill(['lifecycle_status' => self::RETIRED])->save();
        });
    }

    /**
     * Devices in stock, ready to be sent out.
     *
     * Ordered so the ones that have been waiting longest go first, which is
     * usually what you want off a shelf.
     */
    public function availableStock(): \Illuminate\Database\Eloquent\Builder
    {
        return PosTerminal::query()
            ->where('lifecycle_status', self::COLLECTED)
            ->orderBy('updated_at');
    }

    /**
     * Has this device ever stood at a site?
     *
     * This is the "new device or previously collected one" the field team asked
     * to be able to tell apart, and it is derived rather than stored, so it
     * cannot drift out of step with the history it describes.
     */
    public function hasBeenDeployed(PosTerminal $terminal): bool
    {
        return TerminalSiteAssignment::where('pos_terminal_id', $terminal->id)->exists();
    }

    /**
     * Close the device's current spell at a site, if it has one.
     *
     * Returns the row it closed, or a fresh closed row when the device had no
     * open spell - a device imported before this existed, or one collected
     * twice - so the history still shows the move rather than nothing at all.
     */
    private function closeOpenAssignment(
        PosTerminal $terminal,
        string $reason,
        ?string $notes,
        ?int $by,
        ?int $replacedBy = null,
    ): TerminalSiteAssignment {
        $open = TerminalSiteAssignment::where('pos_terminal_id', $terminal->id)
            ->whereNull('ended_at')
            ->orderByDesc('started_at')
            ->first();

        if ($open === null) {
            return TerminalSiteAssignment::create(
                $this->siteFrom($terminal) + [
                    'pos_terminal_id' => $terminal->id,
                    'started_at' => $terminal->installation_date ?: $terminal->created_at ?: now(),
                    'ended_at' => now(),
                    'ended_reason' => $reason,
                    'replaced_by_terminal_id' => $replacedBy,
                    'recorded_by' => $by,
                    'notes' => $notes,
                ],
            );
        }

        $open->forceFill([
            'ended_at' => now(),
            'ended_reason' => $reason,
            'replaced_by_terminal_id' => $replacedBy,
            'recorded_by' => $by ?? $open->recorded_by,
            'notes' => trim((string) ($open->notes ? $open->notes."\n" : '').(string) $notes) ?: null,
        ])->save();

        return $open;
    }

    /**
     * The site to record, preferring what was passed and falling back to the
     * device being replaced - a swap keeps the site, that is the point of it.
     *
     * @param  array<string, mixed>  $site
     * @return array<string, mixed>
     */
    private function resolveSite(array $site, ?PosTerminal $replacing): array
    {
        $fallback = $replacing !== null ? $this->siteFrom($replacing) : [];
        $resolved = [];

        foreach (self::SITE_FIELDS as $field) {
            $given = $site[$field] ?? null;
            $resolved[$field] = (is_string($given) ? trim($given) : $given) !== null
                && (! is_string($given) || trim($given) !== '')
                    ? (is_string($given) ? trim($given) : $given)
                    : ($fallback[$field] ?? null);
        }

        return $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    private function siteFrom(PosTerminal $terminal): array
    {
        $site = [];
        foreach (self::SITE_FIELDS as $field) {
            $site[$field] = $terminal->{$field};
        }

        return $site;
    }
}
