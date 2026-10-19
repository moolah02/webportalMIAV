<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ContractReport;
use App\Models\PosTerminal;
use App\Models\ReportException;
use App\Models\SystemSetting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Builds the contractual service reports: weekly operational and monthly
 * executive. One pass over a period produces every item the contract asks for
 * (clause 10): device-by-device status, merchant visited or not, reason for
 * non-visit / non-activation, faults, corrective action, open issues,
 * escalation owner, ageing, transaction activity against target, and the
 * recommendations the client is asked to act on.
 *
 * Read-only: nothing here writes. The controller owns persistence.
 *
 * TIMEZONES — the two clocks in this database:
 *   visits.completed_at and technician_visits.visit_date/started_at are stored
 *   as LOCAL Harare time (the tablet sends local time), while created_at /
 *   updated_at / resolved_at are UTC. Period bounds are therefore prepared
 *   twice, and each query uses the pair matching the column it filters on.
 */
class ContractReportService
{
    public const TZ = 'Africa/Harare';

    /** Ticket states that mean the issue is no longer open. */
    private const CLOSED_TICKET_STATES = ['resolved', 'closed', 'cancelled', 'completed'];

    /** Device status recorded on a visit that means the device was working. */
    private const STATUS_WORKING = 'working';

    /** Average days per month, used to pro-rata a monthly target over a week. */
    private const DAYS_PER_MONTH = 30.44;

    /**
     * @param  string  $type     weekly|monthly
     * @param  string  $start    Y-m-d (local)
     * @param  string  $end      Y-m-d (local, inclusive)
     * @param  int|null  $clientId  null = all clients
     * @param  string|null  $region  pos_terminals.region value, null = all regions
     */
    public function build(string $type, string $start, string $end, ?int $clientId = null, ?string $region = null): array
    {
        $periodStart = CarbonImmutable::parse($start, self::TZ)->startOfDay();
        $periodEnd = CarbonImmutable::parse($end, self::TZ)->endOfDay();
        $days = $this->daysInPeriod($periodStart, $periodEnd);

        // Local-clock bounds (visits), and the same instants in UTC (created_at etc).
        $localFrom = $periodStart->format('Y-m-d H:i:s');
        $localTo = $periodEnd->format('Y-m-d H:i:s');
        $utcFrom = $periodStart->setTimezone('UTC')->format('Y-m-d H:i:s');
        $utcTo = $periodEnd->setTimezone('UTC')->format('Y-m-d H:i:s');

        $client = $clientId ? Client::find($clientId) : null;

        $terminals = $this->terminalsInScope($clientId, $region, $utcTo);
        $terminalIds = $terminals->pluck('id')->all();

        $visitsByTerminal = $this->visitsByTerminal($terminalIds, $localFrom, $localTo, $utcFrom, $utcTo);
        $lastVisitBefore = $this->lastVisitBeforePeriod($terminalIds, $localFrom, $utcFrom);
        $assignmentsByTerminal = $this->assignmentsByTerminal($terminalIds, $periodStart, $periodEnd);
        $exceptions = $this->exceptions($terminalIds, $periodStart, $periodEnd);
        $openIssues = $this->openIssues($terminalIds, $periodEnd);
        $ticketFlow = $this->ticketFlow($terminalIds, $utcFrom, $utcTo);
        $transactions = $this->transactions($terminals, $periodStart, $periodEnd, $days);

        $devices = [];
        foreach ($terminals as $terminal) {
            $devices[] = $this->buildDeviceRow(
                $terminal,
                $visitsByTerminal[$terminal->id] ?? [],
                $lastVisitBefore[$terminal->id] ?? null,
                $assignmentsByTerminal[$terminal->id] ?? null,
                $exceptions,
                $openIssues['by_terminal'][$terminal->id] ?? [],
                $transactions['by_terminal'][$terminal->id] ?? null
            );
        }

        return [
            'meta' => [
                'type' => $type,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'period_label' => $this->periodLabel($type, $periodStart, $periodEnd),
                'days' => $days,
                'client_id' => $clientId,
                'client_name' => $client?->company_name ?? $client?->name ?? 'All clients',
                'region' => $region ?: 'All regions',
                'generated_at' => Carbon::now(self::TZ)->format('Y-m-d H:i'),
            ],
            'summary' => $this->summarise($devices, $openIssues, $ticketFlow, $transactions),
            'devices' => $devices,
            'merchants' => $this->merchantCoverage($devices),
            'faults' => $this->faults($devices),
            'corrective' => $this->correctiveActions($devices),
            'open_issues' => $openIssues,
            'tickets' => $ticketFlow,
            'transactions' => $transactions,
            'technicians' => $this->technicianActivity($visitsByTerminal),
            'new_devices' => $this->newDevices($terminals, $utcFrom, $utcTo),
        ];
    }

    /**
     * The same figures for the period immediately before this one, for the
     * month-on-month movement in the executive report.
     */
    public function comparison(string $type, string $start, string $end, ?int $clientId = null, ?string $region = null): array
    {
        [$prevStart, $prevEnd] = $this->previousPeriod($type, $start, $end);
        $previous = $this->build($type, $prevStart, $prevEnd, $clientId, $region);

        return [
            'period_label' => $previous['meta']['period_label'],
            'summary' => $previous['summary'],
        ];
    }

    /** The period immediately before [$start, $end], same length. */
    public function previousPeriod(string $type, string $start, string $end): array
    {
        $s = CarbonImmutable::parse($start, self::TZ);
        $e = CarbonImmutable::parse($end, self::TZ);

        if ($type === ContractReport::TYPE_MONTHLY) {
            $prev = $s->subMonthNoOverflow();

            return [$prev->startOfMonth()->toDateString(), $prev->endOfMonth()->toDateString()];
        }

        $days = $this->daysInPeriod($s, $e);

        return [$s->subDays($days)->toDateString(), $s->subDay()->toDateString()];
    }

    /** Default period for a report type: the last COMPLETE week or month. */
    public function defaultPeriod(string $type, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now(self::TZ);

        if ($type === ContractReport::TYPE_MONTHLY) {
            $month = $now->subMonthNoOverflow();

            return [$month->startOfMonth()->toDateString(), $month->endOfMonth()->toDateString()];
        }

        $week = $now->subWeek();

        return [
            $week->startOfWeek(CarbonImmutable::MONDAY)->toDateString(),
            $week->endOfWeek(CarbonImmutable::SUNDAY)->toDateString(),
        ];
    }

    // ---------------------------------------------------------------- loading

    /** Devices that existed on or before the end of the period. */
    private function terminalsInScope(?int $clientId, ?string $region, string $utcTo)
    {
        return PosTerminal::query()
            ->with('client:id,company_name,name,monthly_transaction_target')
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->when($region, fn ($q) => $q->where('region', $region))
            ->where(function ($q) use ($utcTo) {
                $q->where('created_at', '<=', $utcTo)->orWhereNull('created_at');
            })
            ->orderBy('merchant_name')
            ->orderBy('terminal_id')
            ->get();
    }

    /** Visits inside the period, grouped by device, oldest first. */
    private function visitsByTerminal(array $terminalIds, string $localFrom, string $localTo, string $utcFrom, string $utcTo): array
    {
        if (empty($terminalIds)) {
            return [];
        }

        $rows = DB::table('visit_terminals as vt')
            ->join('visits as v', 'v.id', '=', 'vt.visit_id')
            ->leftJoin('employees as e', 'e.id', '=', 'v.employee_id')
            ->whereIn('vt.terminal_id', $terminalIds)
            ->where(function ($q) use ($localFrom, $localTo, $utcFrom, $utcTo) {
                // completed_at is local time; fall back to created_at (UTC) when a
                // visit was synced without a completion time.
                $q->whereBetween('v.completed_at', [$localFrom, $localTo])
                    ->orWhere(function ($q2) use ($utcFrom, $utcTo) {
                        $q2->whereNull('v.completed_at')->whereBetween('v.created_at', [$utcFrom, $utcTo]);
                    });
            })
            ->orderByRaw('COALESCE(v.completed_at, v.created_at) ASC')
            ->select([
                'vt.terminal_id', 'vt.status', 'vt.state', 'vt.condition', 'vt.comments as terminal_comments',
                'v.id as visit_id', 'v.completed_at', 'v.created_at', 'v.employee_id',
                'v.visit_summary', 'v.action_points', 'v.terminal_comments as corrective_action',
                'v.condition_notes', 'v.evidence',
                'e.first_name', 'e.last_name',
            ])
            ->get();

        $byTerminal = [];
        foreach ($rows as $row) {
            $byTerminal[$row->terminal_id][] = $row;
        }

        return $byTerminal;
    }

    /** The most recent visit BEFORE the period, used to explain a non-visit. */
    private function lastVisitBeforePeriod(array $terminalIds, string $localFrom, string $utcFrom): array
    {
        if (empty($terminalIds)) {
            return [];
        }

        $rows = DB::table('visit_terminals as vt')
            ->join('visits as v', 'v.id', '=', 'vt.visit_id')
            ->whereIn('vt.terminal_id', $terminalIds)
            ->where(function ($q) use ($localFrom, $utcFrom) {
                $q->where('v.completed_at', '<', $localFrom)
                    ->orWhere(function ($q2) use ($utcFrom) {
                        $q2->whereNull('v.completed_at')->where('v.created_at', '<', $utcFrom);
                    });
            })
            ->orderByRaw('COALESCE(v.completed_at, v.created_at) DESC')
            ->select(['vt.terminal_id', 'vt.status', 'vt.state', 'v.completed_at', 'v.created_at'])
            ->get();

        $last = [];
        foreach ($rows as $row) {
            $last[$row->terminal_id] ??= $row;   // first row per device is the latest
        }

        return $last;
    }

    /** Jobs scheduled in the period, mapped to each device they cover. */
    private function assignmentsByTerminal(array $terminalIds, CarbonImmutable $periodStart, CarbonImmutable $periodEnd): array
    {
        if (empty($terminalIds)) {
            return [];
        }

        $assignments = DB::table('job_assignments')
            ->whereBetween('scheduled_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->leftJoin('employees as e', 'e.id', '=', 'job_assignments.technician_id')
            ->select([
                'job_assignments.id', 'job_assignments.assignment_id', 'job_assignments.status',
                'job_assignments.scheduled_date', 'job_assignments.pos_terminals',
                'e.first_name', 'e.last_name',
            ])
            ->get();

        $byTerminal = [];
        foreach ($assignments as $assignment) {
            foreach ($this->decodeTerminalList($assignment->pos_terminals) as $id) {
                if (in_array((int) $id, $terminalIds, true)) {
                    $byTerminal[(int) $id] ??= $assignment;
                }
            }
        }

        return $byTerminal;
    }

    /** job_assignments.pos_terminals is a JSON list of device ids. */
    private function decodeTerminalList(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (! is_string($value) || $value === '') {
            return [];
        }
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** Reasons recorded by the office for this period, keyed terminal|type. */
    private function exceptions(array $terminalIds, CarbonImmutable $periodStart, CarbonImmutable $periodEnd): array
    {
        if (empty($terminalIds)) {
            return [];
        }

        return ReportException::with('owner:id,first_name,last_name')
            ->whereIn('pos_terminal_id', $terminalIds)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->get()
            ->keyBy(fn ($e) => $e->pos_terminal_id.'|'.$e->type)
            ->all();
    }

    /**
     * Issues still open at the end of the period, with ageing and the owner
     * they sit with. Age is measured as at the period end (or now, if the
     * period has not finished yet) so a report does not keep ageing after it
     * was issued.
     */
    private function openIssues(array $terminalIds, CarbonImmutable $periodEnd): array
    {
        $asAt = $periodEnd->isFuture() ? CarbonImmutable::now(self::TZ) : $periodEnd;

        $items = [];
        $byTerminal = [];
        $buckets = ['0-7 days' => 0, '8-14 days' => 0, '15-30 days' => 0, 'Over 30 days' => 0];
        $byPriority = [];

        if (! empty($terminalIds)) {
            $rows = DB::table('tickets as t')
                ->leftJoin('pos_terminals as p', 'p.id', '=', 't.pos_terminal_id')
                ->leftJoin('employees as a', 'a.id', '=', 't.assigned_to')
                ->leftJoin('employees as tech', 'tech.id', '=', 't.technician_id')
                ->whereIn('t.pos_terminal_id', $terminalIds)
                ->whereNotIn(DB::raw('LOWER(t.status)'), self::CLOSED_TICKET_STATES)
                ->where('t.created_at', '<=', $asAt->setTimezone('UTC')->format('Y-m-d H:i:s'))
                ->select([
                    't.id', 't.ticket_id', 't.title', 't.issue_type', 't.priority', 't.status',
                    't.created_at', 't.pos_terminal_id',
                    'p.terminal_id as terminal_code', 'p.merchant_name',
                    'a.first_name as owner_first', 'a.last_name as owner_last',
                    'tech.first_name as tech_first', 'tech.last_name as tech_last',
                ])
                ->orderBy('t.created_at')
                ->get();

            foreach ($rows as $row) {
                $age = (int) CarbonImmutable::parse($row->created_at, 'UTC')->diffInDays($asAt);
                $bucket = match (true) {
                    $age <= 7 => '0-7 days',
                    $age <= 14 => '8-14 days',
                    $age <= 30 => '15-30 days',
                    default => 'Over 30 days',
                };
                $buckets[$bucket]++;

                $priority = ucfirst(strtolower((string) ($row->priority ?: 'unset')));
                $byPriority[$priority] = ($byPriority[$priority] ?? 0) + 1;

                $owner = trim(($row->owner_first ?? '').' '.($row->owner_last ?? ''))
                    ?: trim(($row->tech_first ?? '').' '.($row->tech_last ?? ''));

                $item = [
                    'ticket_id' => $row->ticket_id ?: '#'.$row->id,
                    'terminal_code' => $row->terminal_code,
                    'merchant_name' => $row->merchant_name,
                    'title' => $row->title,
                    'issue_type' => $row->issue_type,
                    'priority' => $priority,
                    'status' => $row->status,
                    'raised_on' => CarbonImmutable::parse($row->created_at, 'UTC')->setTimezone(self::TZ)->format('Y-m-d'),
                    'age_days' => $age,
                    'age_bucket' => $bucket,
                    'owner' => $owner ?: 'Unassigned',
                ];

                $items[] = $item;
                $byTerminal[$row->pos_terminal_id][] = $item;
            }
        }

        usort($items, fn ($a, $b) => $b['age_days'] <=> $a['age_days']);

        return [
            'total' => count($items),
            'items' => $items,
            'by_terminal' => $byTerminal,
            'by_age' => $buckets,
            'by_priority' => $byPriority,
            'oldest_days' => $items ? $items[0]['age_days'] : 0,
            'unassigned' => count(array_filter($items, fn ($i) => $i['owner'] === 'Unassigned')),
            'as_at' => $asAt->format('Y-m-d'),
        ];
    }

    /** Tickets raised and closed inside the period. */
    private function ticketFlow(array $terminalIds, string $utcFrom, string $utcTo): array
    {
        if (empty($terminalIds)) {
            return ['opened' => 0, 'resolved' => 0, 'avg_resolution_days' => null];
        }

        $opened = DB::table('tickets')->whereIn('pos_terminal_id', $terminalIds)
            ->whereBetween('created_at', [$utcFrom, $utcTo])->count();

        $resolvedRows = DB::table('tickets')->whereIn('pos_terminal_id', $terminalIds)
            ->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [$utcFrom, $utcTo])
            ->select(['created_at', 'resolved_at'])
            ->get();

        $days = [];
        foreach ($resolvedRows as $row) {
            if ($row->created_at && $row->resolved_at) {
                $days[] = CarbonImmutable::parse($row->created_at, 'UTC')
                    ->diffInHours(CarbonImmutable::parse($row->resolved_at, 'UTC')) / 24;
            }
        }

        return [
            'opened' => $opened,
            'resolved' => $resolvedRows->count(),
            'avg_resolution_days' => $days ? round(array_sum($days) / count($days), 1) : null,
        ];
    }

    /**
     * Transaction activity against target. The portal holds no transaction
     * feed, so figures come from the file uploaded for the period; a device
     * with no uploaded row is reported as "not supplied", never as zero.
     */
    private function transactions($terminals, CarbonImmutable $periodStart, CarbonImmutable $periodEnd, int $days): array
    {
        $terminalIds = $terminals->pluck('id')->all();
        $defaultTarget = (int) (SystemSetting::get('transaction_target_default', 0) ?: 0);
        $isMonth = $periodStart->isSameDay($periodStart->startOfMonth()) && $periodEnd->isSameDay($periodEnd->endOfMonth());

        $rows = empty($terminalIds) ? collect() : DB::table('terminal_transaction_stats')
            ->whereIn('pos_terminal_id', $terminalIds)
            ->where('period_start', '>=', $periodStart->toDateString())
            ->where('period_end', '<=', $periodEnd->toDateString())
            ->select([
                'pos_terminal_id',
                DB::raw('SUM(transactions_count) as transactions_count'),
                DB::raw('SUM(transactions_value) as transactions_value'),
                DB::raw('SUM(target_count) as target_count'),
            ])
            ->groupBy('pos_terminal_id')
            ->get()
            ->keyBy('pos_terminal_id');

        $byTerminal = [];
        $totalActual = 0;
        $totalTarget = 0;
        $withData = 0;
        $missing = [];

        foreach ($terminals as $terminal) {
            $row = $rows[$terminal->id] ?? null;

            // Monthly target from the device, else the client, else the system default.
            $monthly = $terminal->monthly_transaction_target
                ?: ($terminal->client->monthly_transaction_target ?? null)
                ?: $defaultTarget;
            $target = $row && $row->target_count
                ? (int) $row->target_count
                : ($monthly ? (int) ($isMonth ? $monthly : round($monthly * $days / self::DAYS_PER_MONTH)) : null);

            $actual = $row ? (int) $row->transactions_count : null;

            if ($actual === null) {
                $missing[] = $terminal->terminal_id;
            } else {
                $withData++;
                $totalActual += $actual;
            }
            if ($target !== null && $actual !== null) {
                $totalTarget += $target;
            }

            $byTerminal[$terminal->id] = [
                'actual' => $actual,
                'value' => $row && $row->transactions_value !== null ? (float) $row->transactions_value : null,
                'target' => $target,
                'achievement' => ($target && $actual !== null) ? round($actual / $target * 100) : null,
            ];
        }

        return [
            'supplied' => $withData > 0,
            'devices_total' => $terminals->count(),
            'devices_with_data' => $withData,
            'missing_devices' => $missing,
            'actual' => $totalActual,
            'target' => $totalTarget,
            'achievement' => $totalTarget > 0 ? round($totalActual / $totalTarget * 100) : null,
            'target_basis' => $isMonth ? 'monthly target' : 'monthly target pro-rata for '.$days.' days',
            'by_terminal' => $byTerminal,
        ];
    }

    // ------------------------------------------------------------ assembly

    private function buildDeviceRow(
        PosTerminal $terminal,
        array $visits,
        ?object $lastBefore,
        ?object $assignment,
        array $exceptions,
        array $openIssues,
        ?array $transactions
    ): array {
        $visited = count($visits) > 0;
        $last = $visited ? $visits[count($visits) - 1] : null;

        $status = $last?->status ?: null;
        $state = $last?->state ? strtoupper($last->state) : null;
        $working = $status !== null && str_contains(strtolower($status), self::STATUS_WORKING)
            && ! str_contains(strtolower($status), 'not');
        $active = $state === 'ACTIVE' || ($state === null && $working);

        $faults = [];
        $actions = [];
        $technicians = [];
        foreach ($visits as $visit) {
            foreach ($this->splitList($visit->action_points) as $fault) {
                $faults[] = $fault;
            }
            foreach ($this->splitList($visit->corrective_action) as $action) {
                $actions[] = $action;
            }
            $name = trim(($visit->first_name ?? '').' '.($visit->last_name ?? ''));
            if ($name !== '') {
                $technicians[$name] = true;
            }
        }

        $nonVisit = $visited ? null : $this->nonVisitReason($terminal, $lastBefore, $assignment, $exceptions);
        $nonActivation = ($visited && ! $active)
            ? $this->nonActivationReason($terminal, $last, $faults, $actions, $exceptions)
            : null;

        return [
            'terminal_id' => $terminal->id,
            'terminal_code' => $terminal->terminal_id,
            'merchant_name' => $terminal->merchant_name,
            'merchant_id' => $terminal->merchant_id,
            'client_name' => $terminal->client?->company_name ?? $terminal->client?->name,
            'region' => $terminal->region,
            'city' => $terminal->city,
            'address' => $terminal->physical_address,
            'model' => $terminal->terminal_model,
            'serial' => $terminal->serial_number,
            'source' => $terminal->source,
            'visited' => $visited,
            'visit_count' => count($visits),
            'last_visit_date' => $last ? $this->visitDate($last) : null,
            'technicians' => array_keys($technicians),
            'device_status' => $visited ? ($status ?: 'Not recorded') : $this->statusWithoutVisit($terminal),
            'device_state' => $state,
            'condition' => $last?->condition,
            'condition_notes' => $last?->condition_notes,
            'working' => $visited ? $working : null,
            'active' => $visited ? $active : null,
            'faults' => array_values(array_unique($faults)),
            'corrective_actions' => array_values(array_unique($actions)),
            'visit_summary' => $last?->visit_summary,
            'evidence_count' => $last ? count($this->decodeTerminalList($last->evidence)) : 0,
            'non_visit_reason' => $nonVisit,
            'non_activation_reason' => $nonActivation,
            'open_issues' => $openIssues,
            'open_issue_count' => count($openIssues),
            'oldest_issue_days' => $openIssues ? max(array_column($openIssues, 'age_days')) : 0,
            'escalation_owner' => $openIssues[0]['owner'] ?? ($nonVisit['owner'] ?? $nonActivation['owner'] ?? null),
            'transactions' => $transactions,
        ];
    }

    /** Status to show for a device with no visit in the period. */
    private function statusWithoutVisit(PosTerminal $terminal): string
    {
        $status = $terminal->current_status ?: $terminal->status;

        return $status ? ucfirst(str_replace('_', ' ', $status)).' (no visit in period)' : 'Not visited in period';
    }

    /**
     * Reason a device was not visited: what the office recorded, else what the
     * system can tell from the jobs and the previous visit.
     */
    private function nonVisitReason(PosTerminal $terminal, ?object $lastBefore, ?object $assignment, array $exceptions): array
    {
        $recorded = $exceptions[$terminal->id.'|'.ReportException::TYPE_NON_VISIT] ?? null;
        if ($recorded) {
            return [
                'source' => 'recorded',
                'code' => $recorded->reason_code,
                'reason' => ReportException::reasonLabel($recorded->reason_code),
                'note' => $recorded->reason_note,
                'owner' => $recorded->owner ? trim($recorded->owner->first_name.' '.$recorded->owner->last_name) : null,
                'status' => $recorded->status,
            ];
        }

        if ($assignment && strtolower((string) $assignment->status) !== 'completed') {
            $tech = trim(($assignment->first_name ?? '').' '.($assignment->last_name ?? ''));
            $scheduled = $assignment->scheduled_date ? CarbonImmutable::parse($assignment->scheduled_date)->format('d M') : null;

            return [
                'source' => 'derived',
                'code' => null,
                'reason' => 'Job '.($assignment->assignment_id ?: '#'.$assignment->id).' scheduled'
                    .($scheduled ? ' '.$scheduled : '').' — not completed',
                'note' => $tech !== '' ? 'Assigned to '.$tech : null,
                'owner' => $tech !== '' ? $tech : null,
                'status' => 'open',
            ];
        }

        if ($lastBefore && str_contains(strtolower((string) $lastBefore->status), 'not found')) {
            $when = $lastBefore->completed_at ?: $lastBefore->created_at;

            return [
                'source' => 'derived',
                'code' => null,
                'reason' => 'Device not found on site on the last visit'
                    .($when ? ' ('.CarbonImmutable::parse($when)->format('d M Y').')' : ''),
                'note' => null,
                'owner' => null,
                'status' => 'open',
            ];
        }

        return [
            'source' => 'derived',
            'code' => null,
            'reason' => 'No visit scheduled in period',
            'note' => null,
            'owner' => null,
            'status' => 'open',
        ];
    }

    /** Reason a visited device is not active/working. */
    private function nonActivationReason(PosTerminal $terminal, ?object $last, array $faults, array $actions, array $exceptions): array
    {
        $recorded = $exceptions[$terminal->id.'|'.ReportException::TYPE_NON_ACTIVATION] ?? null;
        if ($recorded) {
            return [
                'source' => 'recorded',
                'code' => $recorded->reason_code,
                'reason' => ReportException::reasonLabel($recorded->reason_code),
                'note' => $recorded->reason_note,
                'owner' => $recorded->owner ? trim($recorded->owner->first_name.' '.$recorded->owner->last_name) : null,
                'status' => $recorded->status,
            ];
        }

        $status = strtolower((string) ($last->status ?? ''));
        if (str_contains($status, 'not found')) {
            return [
                'source' => 'derived', 'code' => null,
                'reason' => 'Device not found on site during the visit',
                'note' => $faults ? implode('; ', $faults) : null,
                'owner' => null, 'status' => 'open',
            ];
        }

        return [
            'source' => 'derived',
            'code' => null,
            'reason' => $faults ? 'Faults recorded on visit: '.implode('; ', $faults) : 'Reason not recorded on the visit',
            'note' => $actions ? 'Action taken: '.implode('; ', $actions) : null,
            'owner' => null,
            'status' => 'open',
        ];
    }

    // ----------------------------------------------------------- aggregates

    private function summarise(array $devices, array $openIssues, array $ticketFlow, array $transactions): array
    {
        $total = count($devices);
        $visited = count(array_filter($devices, fn ($d) => $d['visited']));
        $active = count(array_filter($devices, fn ($d) => $d['active'] === true));
        $faulty = count(array_filter($devices, fn ($d) => $d['visited'] && $d['working'] === false));
        $withFaults = count(array_filter($devices, fn ($d) => ! empty($d['faults'])));
        $withActions = count(array_filter($devices, fn ($d) => ! empty($d['corrective_actions'])));

        return [
            'devices_total' => $total,
            'devices_visited' => $visited,
            'devices_not_visited' => $total - $visited,
            'coverage_pct' => $this->pct($visited, $total),
            'devices_active' => $active,
            'devices_inactive' => $visited - $active,
            'activation_pct' => $this->pct($active, $visited),
            'devices_faulty' => $faulty,
            'devices_with_faults' => $withFaults,
            'devices_with_corrective_action' => $withActions,
            'visits_total' => array_sum(array_column($devices, 'visit_count')),
            'open_issues' => $openIssues['total'],
            'open_issues_over_30' => $openIssues['by_age']['Over 30 days'] ?? 0,
            'tickets_opened' => $ticketFlow['opened'],
            'tickets_resolved' => $ticketFlow['resolved'],
            'avg_resolution_days' => $ticketFlow['avg_resolution_days'],
            'transactions_actual' => $transactions['actual'],
            'transactions_target' => $transactions['target'],
            'transactions_achievement' => $transactions['achievement'],
            'transactions_supplied' => $transactions['supplied'],
        ];
    }

    private function merchantCoverage(array $devices): array
    {
        $merchants = [];
        foreach ($devices as $device) {
            $key = $device['merchant_id'] ?: $device['merchant_name'] ?: 'unknown-'.$device['terminal_id'];
            $merchants[$key]['name'] ??= $device['merchant_name'];
            $merchants[$key]['devices'] = ($merchants[$key]['devices'] ?? 0) + 1;
            $merchants[$key]['visited'] = ($merchants[$key]['visited'] ?? false) || $device['visited'];
        }

        $visited = count(array_filter($merchants, fn ($m) => $m['visited']));

        return [
            'total' => count($merchants),
            'visited' => $visited,
            'not_visited' => count($merchants) - $visited,
            'coverage_pct' => $this->pct($visited, count($merchants)),
            'items' => array_values($merchants),
        ];
    }

    private function faults(array $devices): array
    {
        $byType = [];
        foreach ($devices as $device) {
            foreach ($device['faults'] as $fault) {
                $byType[$fault] = ($byType[$fault] ?? 0) + 1;
            }
        }
        arsort($byType);

        return [
            'total' => array_sum($byType),
            'distinct' => count($byType),
            'by_type' => $byType,
            'devices_affected' => count(array_filter($devices, fn ($d) => ! empty($d['faults']))),
        ];
    }

    private function correctiveActions(array $devices): array
    {
        $byType = [];
        $outstanding = [];
        foreach ($devices as $device) {
            foreach ($device['corrective_actions'] as $action) {
                $byType[$action] = ($byType[$action] ?? 0) + 1;
            }
            if (! empty($device['faults']) && empty($device['corrective_actions'])) {
                $outstanding[] = [
                    'terminal_code' => $device['terminal_code'],
                    'merchant_name' => $device['merchant_name'],
                    'faults' => $device['faults'],
                    'owner' => $device['escalation_owner'],
                ];
            }
        }
        arsort($byType);

        return [
            'total' => array_sum($byType),
            'by_type' => $byType,
            'outstanding' => $outstanding,
            'outstanding_count' => count($outstanding),
        ];
    }

    /**
     * Visits per technician, counted from the visit rows themselves so one
     * device seen by two technicians is not double-counted for either.
     *
     * @param  array<int, array<int, object>>  $visitsByTerminal
     */
    private function technicianActivity(array $visitsByTerminal): array
    {
        $byTech = [];
        foreach ($visitsByTerminal as $terminalId => $visits) {
            foreach ($visits as $visit) {
                $name = trim(($visit->first_name ?? '').' '.($visit->last_name ?? ''));
                if ($name === '') {
                    $name = 'Unknown technician';
                }
                $byTech[$name]['name'] ??= $name;
                $byTech[$name]['visits'] = ($byTech[$name]['visits'] ?? 0) + 1;
                $byTech[$name]['devices'][$terminalId] = true;
                $byTech[$name]['faults'] = ($byTech[$name]['faults'] ?? 0) + count($this->splitList($visit->action_points));
            }
        }

        $rows = [];
        foreach ($byTech as $tech) {
            $rows[] = [
                'name' => $tech['name'],
                'visits' => $tech['visits'],
                'devices' => count($tech['devices']),
                'faults' => $tech['faults'],
            ];
        }
        usort($rows, fn ($a, $b) => $b['visits'] <=> $a['visits']);

        return $rows;
    }

    private function newDevices($terminals, string $utcFrom, string $utcTo): array
    {
        $new = [];
        foreach ($terminals as $terminal) {
            if (! $terminal->created_at) {
                continue;
            }
            $created = $terminal->created_at->format('Y-m-d H:i:s');
            if ($created >= $utcFrom && $created <= $utcTo) {
                $new[] = [
                    'terminal_code' => $terminal->terminal_id,
                    'merchant_name' => $terminal->merchant_name,
                    'region' => $terminal->region,
                    'source' => $terminal->source === 'field_discovery' ? 'Found on site' : 'Added in office',
                    'added_on' => $terminal->created_at->setTimezone(self::TZ)->format('Y-m-d'),
                ];
            }
        }

        return $new;
    }

    // -------------------------------------------------------------- helpers

    /** "Issues Found" and "Corrective Action" are comma-joined picklists. */
    private function splitList(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== ''));
    }

    private function visitDate(object $visit): string
    {
        $when = $visit->completed_at ?: $visit->created_at;

        return $when ? CarbonImmutable::parse($when)->format('Y-m-d') : '';
    }

    /** Inclusive whole days in a period (Carbon returns a float). */
    private function daysInPeriod(CarbonImmutable $start, CarbonImmutable $end): int
    {
        return (int) round($start->startOfDay()->diffInDays($end->startOfDay())) + 1;
    }

    private function pct(int $part, int $whole): ?int
    {
        return $whole > 0 ? (int) round($part / $whole * 100) : null;
    }

    private function periodLabel(string $type, CarbonImmutable $start, CarbonImmutable $end): string
    {
        if ($type === ContractReport::TYPE_MONTHLY) {
            return $start->format('F Y');
        }

        return $start->format('j M').' – '.$end->format('j M Y');
    }
}
