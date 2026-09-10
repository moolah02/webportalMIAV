<?php

namespace App\Services;

use App\Models\JobAssignment;
use App\Models\PosTerminal;
use App\Models\TechnicianVisit;
use App\Models\Visit;
use Illuminate\Support\Facades\Log;

/**
 * Keeps technician_visits (the report builder's "Technician Visits (Detail)"
 * table) in sync with visits recorded by the mobile app.
 *
 * The saved Visit is the source of truth; the mirror row is upserted by
 * visit_id, so running it again after an edit (or from the backfill command)
 * repairs rows that are missing or stale.
 *
 * Values are normalised to the column enums. The mobile app sends things like
 * state "ACTIVE" / "N/A", status "Not Found", condition "Bad" — an unmapped
 * value previously made MySQL reject the whole insert, so the visit silently
 * never reached the reports.
 */
class TechnicianVisitMirror
{
    private const STATUS_ENUM    = ['active', 'inactive', 'not_found', 'relocated', 'replaced'];
    private const CONDITION_ENUM = ['good', 'fair', 'poor', 'damaged'];

    private const STATUS_ALIASES = [
        'working'     => 'active',
        'not_working' => 'inactive',
        'missing'     => 'not_found',
        'notfound'    => 'not_found',
    ];

    private const CONDITION_ALIASES = [
        'bad' => 'poor',
    ];

    /**
     * Create or update the mirror row. Returns 'created', 'updated',
     * 'skipped' or 'failed'. Never throws — the visit itself is already saved.
     *
     * Only visits recorded through the mobile API (which always creates a
     * VisitTerminal row) are mirrored; web manual visits write their own
     * technician_visits row in SiteVisitController and are skipped.
     */
    public function sync(Visit $visit): string
    {
        try {
            $visit->loadMissing('visitTerminal');
            $vt = $visit->visitTerminal;
            if (!$vt) {
                return 'skipped';
            }

            $t = $visit->terminal ?? [];

            $posTerminal = $this->resolvePosTerminal($vt->terminal_id ?? ($t['terminal_id'] ?? null));

            $jobAssignmentId = null;
            if (!empty($visit->assignment_id)) {
                $ja = JobAssignment::find((int) $visit->assignment_id)
                    ?? JobAssignment::where('assignment_id', (string) $visit->assignment_id)->first();
                $jobAssignmentId = $ja?->id;
            }

            // Prefer the terminal state (ACTIVE/INACTIVE); fall back to the visit
            // status (Working / Not Working / Not Found) when state is N/A or empty.
            $state = $this->normalise($t['state'] ?? null, self::STATUS_ENUM, self::STATUS_ALIASES)
                ?? $this->normalise($vt->status ?? ($t['status'] ?? null), self::STATUS_ENUM, self::STATUS_ALIASES);

            $condition = $this->normalise($vt->condition ?? ($t['condition'] ?? null), self::CONDITION_ENUM, self::CONDITION_ALIASES);

            $model = $vt->terminal_model ?? $vt->device_type ?? ($t['device_type'] ?? ($t['terminal_model'] ?? null));

            $row = TechnicianVisit::updateOrCreate(
                ['visit_id' => (string) $visit->id],
                [
                    'technician_id'                => $visit->employee_id,
                    'pos_terminal_id'              => $posTerminal?->id,
                    'client_id'                    => $posTerminal?->client_id,
                    'job_assignment_id'            => $jobAssignmentId,
                    'merchant_id_snapshot'         => $visit->merchant_id,
                    'started_at'                   => $visit->completed_at,
                    'ended_at'                     => $visit->completed_at,
                    'status'                       => 'closed',
                    'outcome'                      => 'completed',
                    'terminal_status_during_visit' => $state,
                    'terminal_condition'           => $condition,
                    'issues_found'                 => $visit->action_points,
                    // Mobile sends the corrective action as terminal_comments.
                    'corrective_action'            => $visit->terminal_comments,
                    'visit_summary'                => $visit->visit_summary,
                    'other_terminals_found'        => $visit->other_terminals_found ?: null,
                    'serial_snapshot'              => $vt->serial_number ?? ($t['serial_number'] ?? null),
                    'device_type_snapshot'         => $model,
                ]
            );

            return $row->wasRecentlyCreated ? 'created' : 'updated';
        } catch (\Throwable $e) {
            Log::error('Failed to sync visit to technician_visits', [
                'visit_id' => $visit->id,
                'error'    => $e->getMessage(),
            ]);
            return 'failed';
        }
    }

    private function resolvePosTerminal($terminalId): ?PosTerminal
    {
        if ($terminalId === null || $terminalId === '') {
            return null;
        }
        // Mobile sends pos_terminals.id; older payloads may carry the terminal code.
        return PosTerminal::find((int) $terminalId)
            ?? PosTerminal::where('terminal_id', (string) $terminalId)->first();
    }

    private function normalise($value, array $allowed, array $aliases): ?string
    {
        if ($value === null) {
            return null;
        }
        $key = str_replace([' ', '-'], '_', strtolower(trim((string) $value)));
        $key = $aliases[$key] ?? $key;
        return in_array($key, $allowed, true) ? $key : null;
    }
}
