<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ContractReport;
use App\Models\Employee;
use App\Models\PosTerminal;
use App\Models\ReportException;
use App\Models\ReportRun;
use App\Models\TerminalTransactionStat;
use App\Services\ContractReportService;
use App\Services\TransactionStatsImporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The contractual service reports: weekly operational and monthly executive
 * (supply agreement clause 10). One screen per type, the same data engine,
 * exports to PDF / CSV / Excel, plus the two places the office supplies what
 * the field cannot: transaction figures and reasons for non-visit.
 */
class ContractReportController extends Controller
{
    public function __construct(private readonly ContractReportService $service)
    {
    }

    public function operational(Request $request)
    {
        return $this->showReport($request, ContractReport::TYPE_WEEKLY);
    }

    public function executive(Request $request)
    {
        return $this->showReport($request, ContractReport::TYPE_MONTHLY);
    }

    private function showReport(Request $request, string $type)
    {
        [$start, $end, $clientId, $region] = $this->resolveScope($request, $type);

        $report = $this->service->build($type, $start, $end, $clientId, $region);
        $comparison = $type === ContractReport::TYPE_MONTHLY
            ? $this->service->comparison($type, $start, $end, $clientId, $region)
            : null;

        return view('reports.contract.show', [
            'type' => $type,
            'report' => $report,
            'comparison' => $comparison,
            'record' => $this->findOrNewRecord($type, $start, $end, $clientId),
            'clients' => Client::orderBy('company_name')->get(['id', 'company_name', 'name']),
            'regions' => $this->regionOptions(),
            'owners' => Employee::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'reasonCodes' => ReportException::REASON_CODES,
            'scope' => compact('start', 'end', 'clientId', 'region'),
            'periodOptions' => $this->periodOptions($type),
        ]);
    }

    /** Period + filters from the request, falling back to the last complete period. */
    private function resolveScope(Request $request, string $type): array
    {
        $validated = $request->validate([
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'region' => ['nullable', 'string', 'max:100'],
        ]);

        [$defaultStart, $defaultEnd] = $this->service->defaultPeriod($type);

        $start = $validated['period_start'] ?? $defaultStart;
        $end = $validated['period_end'] ?? $defaultEnd;

        // A month report always covers whole months; a week always Mon-Sun.
        if ($type === ContractReport::TYPE_MONTHLY) {
            $s = CarbonImmutable::parse($start, ContractReportService::TZ);
            $start = $s->startOfMonth()->toDateString();
            $end = $s->endOfMonth()->toDateString();
        }

        return [
            CarbonImmutable::parse($start)->toDateString(),
            CarbonImmutable::parse($end)->toDateString(),
            $validated['client_id'] ?? null,
            $validated['region'] ?? null,
        ];
    }

    /** The last 12 periods of this type, for the period picker. */
    private function periodOptions(string $type): array
    {
        $options = [];
        $cursor = CarbonImmutable::now(ContractReportService::TZ);

        for ($i = 0; $i < 12; $i++) {
            if ($type === ContractReport::TYPE_MONTHLY) {
                $month = $cursor->subMonthsNoOverflow($i + 1);
                $options[] = [
                    'start' => $month->startOfMonth()->toDateString(),
                    'end' => $month->endOfMonth()->toDateString(),
                    'label' => $month->format('F Y'),
                ];
            } else {
                $week = $cursor->subWeeks($i + 1);
                $from = $week->startOfWeek(CarbonImmutable::MONDAY);
                $to = $week->endOfWeek(CarbonImmutable::SUNDAY);
                $options[] = [
                    'start' => $from->toDateString(),
                    'end' => $to->toDateString(),
                    'label' => $from->format('j M').' – '.$to->format('j M Y'),
                ];
            }
        }

        return $options;
    }

    private function regionOptions(): array
    {
        return PosTerminal::query()->whereNotNull('region')->where('region', '!=', '')
            ->distinct()->orderBy('region')->pluck('region')->all();
    }

    private function findOrNewRecord(string $type, string $start, string $end, ?int $clientId): ContractReport
    {
        return ContractReport::query()
            ->where('type', $type)
            ->whereDate('period_start', $start)
            ->whereDate('period_end', $end)
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->when(! $clientId, fn ($q) => $q->whereNull('client_id'))
            ->first()
            ?? new ContractReport([
                'type' => $type,
                'period_start' => $start,
                'period_end' => $end,
                'client_id' => $clientId,
                'status' => 'draft',
            ]);
    }

    // ------------------------------------------------------------- narrative

    /** Save the summary / recommendations / client actions for a period. */
    public function saveNotes(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'executive_summary' => ['nullable', 'string', 'max:5000'],
            'recommendations' => ['nullable', 'string', 'max:5000'],
            'client_actions' => ['nullable', 'string', 'max:5000'],
        ]);

        $record = $this->findOrNewRecord(
            $data['type'],
            CarbonImmutable::parse($data['period_start'])->toDateString(),
            CarbonImmutable::parse($data['period_end'])->toDateString(),
            $data['client_id'] ?? null
        );

        if ($record->isIssued()) {
            return back()->with('error', 'This report has been issued. Re-issue it to change the wording.');
        }

        $record->fill([
            'executive_summary' => $data['executive_summary'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'client_actions' => $data['client_actions'] ?? null,
            'prepared_by' => $record->prepared_by ?: Auth::id(),
        ])->save();

        return back()->with('success', 'Report notes saved.');
    }

    /** Freeze the figures as issued, so the sent report can be reproduced. */
    public function issue(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'region' => ['nullable', 'string', 'max:100'],
            'approved_by_name' => ['nullable', 'string', 'max:120'],
        ]);

        $start = CarbonImmutable::parse($data['period_start'])->toDateString();
        $end = CarbonImmutable::parse($data['period_end'])->toDateString();
        $clientId = $data['client_id'] ?? null;

        $report = $this->service->build($data['type'], $start, $end, $clientId, $data['region'] ?? null);
        $record = $this->findOrNewRecord($data['type'], $start, $end, $clientId);

        $record->fill([
            'status' => 'issued',
            'payload' => $report,
            'prepared_by' => $record->prepared_by ?: Auth::id(),
            'approved_by_name' => $data['approved_by_name'] ?? $record->approved_by_name,
            'approved_at' => $data['approved_by_name'] ? now() : $record->approved_at,
            'issued_at' => now(),
        ])->save();

        return back()->with('success', 'Report issued and the figures stored as sent.');
    }

    /** Record (or clear) why a device was not visited / not activated. */
    public function saveException(Request $request)
    {
        $data = $request->validate([
            'pos_terminal_id' => ['required', 'integer', 'exists:pos_terminals,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'type' => ['required', 'in:non_visit,non_activation'],
            'reason_code' => ['nullable', 'string', 'max:50'],
            'reason_note' => ['nullable', 'string', 'max:1000'],
            'escalation_owner_id' => ['nullable', 'integer', 'exists:employees,id'],
            'status' => ['nullable', 'in:open,resolved'],
        ]);

        $keys = [
            'pos_terminal_id' => $data['pos_terminal_id'],
            'period_start' => CarbonImmutable::parse($data['period_start'])->toDateString(),
            'period_end' => CarbonImmutable::parse($data['period_end'])->toDateString(),
            'type' => $data['type'],
        ];

        if (empty($data['reason_code'])) {
            ReportException::where($keys)->delete();

            return back()->with('success', 'Reason cleared — the system explanation applies again.');
        }

        if (! array_key_exists($data['reason_code'], ReportException::REASON_CODES)) {
            return back()->with('error', 'That reason is not on the list.');
        }

        ReportException::updateOrCreate($keys, [
            'reason_code' => $data['reason_code'],
            'reason_note' => $data['reason_note'] ?? null,
            'escalation_owner_id' => $data['escalation_owner_id'] ?? null,
            'status' => $data['status'] ?? 'open',
            'resolved_at' => ($data['status'] ?? 'open') === 'resolved' ? now() : null,
            'recorded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Reason recorded for this period.');
    }

    // --------------------------------------------------------------- exports

    public function export(Request $request)
    {
        $type = $request->query('type') === ContractReport::TYPE_MONTHLY
            ? ContractReport::TYPE_MONTHLY
            : ContractReport::TYPE_WEEKLY;

        [$start, $end, $clientId, $region] = $this->resolveScope($request, $type);
        $format = strtolower((string) $request->query('format', 'pdf'));

        if (! in_array($format, ['pdf', 'csv', 'xlsx'], true)) {
            abort(400, 'Unsupported export format: '.$format);
        }

        $report = $this->service->build($type, $start, $end, $clientId, $region);
        $comparison = $type === ContractReport::TYPE_MONTHLY
            ? $this->service->comparison($type, $start, $end, $clientId, $region)
            : null;
        $record = $this->findOrNewRecord($type, $start, $end, $clientId);

        $name = ($type === ContractReport::TYPE_MONTHLY ? 'monthly-executive-report_' : 'weekly-operational-report_')
            .$start.'_to_'.$end;

        $this->logRun($type, $start, $end, $clientId, $region, $format, count($report['devices']));

        return match ($format) {
            'csv' => $this->exportCsv($report, $name),
            'xlsx' => $this->exportXlsx($report, $record, $name),
            default => $this->exportPdf($report, $comparison, $record, $type, $name),
        };
    }

    private function exportPdf(array $report, ?array $comparison, ContractReport $record, string $type, string $name)
    {
        @ini_set('memory_limit', '512M');

        $pdf = Pdf::loadView('reports.contract.pdf', [
            'report' => $report,
            'comparison' => $comparison,
            'record' => $record,
            'type' => $type,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($name.'.pdf');
    }

    /** Device-by-device CSV: the granular evidence behind the report. */
    private function exportCsv(array $report, string $name): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$name.'.csv"',
        ];

        return response()->stream(function () use ($report) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");   // BOM so Excel reads accents correctly

            fputcsv($out, [
                'Terminal ID', 'Merchant', 'Client', 'Region', 'City', 'Visited', 'Visits in period',
                'Last visit', 'Technician(s)', 'Device status', 'State', 'Condition',
                'Reason not visited', 'Reason not active', 'Faults identified', 'Corrective action taken',
                'Open issues', 'Oldest open issue (days)', 'Escalation owner',
                'Transactions', 'Target', 'Achievement %', 'Photos',
            ]);

            foreach ($report['devices'] as $device) {
                fputcsv($out, [
                    $device['terminal_code'],
                    $device['merchant_name'],
                    $device['client_name'],
                    $device['region'],
                    $device['city'],
                    $device['visited'] ? 'Yes' : 'No',
                    $device['visit_count'],
                    $device['last_visit_date'],
                    implode('; ', $device['technicians']),
                    $device['device_status'],
                    $device['device_state'],
                    $device['condition'],
                    $device['non_visit_reason']['reason'] ?? '',
                    $device['non_activation_reason']['reason'] ?? '',
                    implode('; ', $device['faults']),
                    implode('; ', $device['corrective_actions']),
                    $device['open_issue_count'],
                    $device['oldest_issue_days'] ?: '',
                    $device['escalation_owner'],
                    $device['transactions']['actual'] ?? 'Not supplied',
                    $device['transactions']['target'] ?? '',
                    $device['transactions']['achievement'] ?? '',
                    $device['evidence_count'],
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }

    /** Multi-sheet Excel: summary, devices, open issues, faults, transactions. */
    private function exportXlsx(array $report, ContractReport $record, string $name): StreamedResponse
    {
        $book = new Spreadsheet;
        $book->getProperties()->setTitle($report['meta']['period_label'])->setCreator('Revival Technologies');

        $summary = $book->getActiveSheet();
        $summary->setTitle('Summary');
        $rows = [
            ['Report', $report['meta']['type'] === 'monthly' ? 'Monthly executive report' : 'Weekly operational report'],
            ['Period', $report['meta']['period_label']],
            ['Client', $report['meta']['client_name']],
            ['Region', $report['meta']['region']],
            ['Generated', $report['meta']['generated_at']],
            [],
            ['Devices in scope', $report['summary']['devices_total']],
            ['Merchants visited', $report['merchants']['visited'].' of '.$report['merchants']['total']],
            ['Devices visited', $report['summary']['devices_visited']],
            ['Devices not visited', $report['summary']['devices_not_visited']],
            ['Coverage %', $report['summary']['coverage_pct']],
            ['Devices active', $report['summary']['devices_active']],
            ['Activation % (of visited)', $report['summary']['activation_pct']],
            ['Faults identified', $report['faults']['total']],
            ['Corrective actions recorded', $report['corrective']['total']],
            ['Faults with no action recorded', $report['corrective']['outstanding_count']],
            ['Open issues', $report['open_issues']['total']],
            ['Open issues over 30 days', $report['summary']['open_issues_over_30']],
            ['Tickets opened', $report['tickets']['opened']],
            ['Tickets resolved', $report['tickets']['resolved']],
            ['Average resolution (days)', $report['tickets']['avg_resolution_days']],
            ['Transactions (actual)', $report['transactions']['supplied'] ? $report['transactions']['actual'] : 'Not supplied'],
            ['Transactions (target)', $report['transactions']['target'] ?: 'Not set'],
            ['Achievement %', $report['transactions']['achievement'] ?? ''],
            [],
            ['Executive summary', $record->executive_summary],
            ['Recommendations', $record->recommendations],
            ['Action required from the client', $record->client_actions],
        ];
        $summary->fromArray($rows, null, 'A1');
        $summary->getColumnDimension('A')->setWidth(34);
        $summary->getColumnDimension('B')->setWidth(60);

        $devices = $book->createSheet();
        $devices->setTitle('Devices');
        $deviceRows = [[
            'Terminal ID', 'Merchant', 'Client', 'Region', 'City', 'Visited', 'Visits', 'Last visit',
            'Technician(s)', 'Device status', 'State', 'Condition', 'Reason not visited', 'Reason not active',
            'Faults identified', 'Corrective action taken', 'Open issues', 'Oldest issue (days)',
            'Escalation owner', 'Transactions', 'Target', 'Achievement %', 'Photos',
        ]];
        foreach ($report['devices'] as $device) {
            $deviceRows[] = [
                $device['terminal_code'], $device['merchant_name'], $device['client_name'],
                $device['region'], $device['city'], $device['visited'] ? 'Yes' : 'No', $device['visit_count'],
                $device['last_visit_date'], implode('; ', $device['technicians']), $device['device_status'],
                $device['device_state'], $device['condition'],
                $device['non_visit_reason']['reason'] ?? '', $device['non_activation_reason']['reason'] ?? '',
                implode('; ', $device['faults']), implode('; ', $device['corrective_actions']),
                $device['open_issue_count'], $device['oldest_issue_days'] ?: '', $device['escalation_owner'],
                $device['transactions']['actual'] ?? 'Not supplied', $device['transactions']['target'] ?? '',
                $device['transactions']['achievement'] ?? '', $device['evidence_count'],
            ];
        }
        $devices->fromArray($deviceRows, null, 'A1');

        $issues = $book->createSheet();
        $issues->setTitle('Open issues');
        $issueRows = [['Ticket', 'Terminal ID', 'Merchant', 'Issue', 'Type', 'Priority', 'Status', 'Raised on', 'Age (days)', 'Ageing band', 'Escalation owner']];
        foreach ($report['open_issues']['items'] as $item) {
            $issueRows[] = [
                $item['ticket_id'], $item['terminal_code'], $item['merchant_name'], $item['title'],
                $item['issue_type'], $item['priority'], $item['status'], $item['raised_on'],
                $item['age_days'], $item['age_bucket'], $item['owner'],
            ];
        }
        $issues->fromArray($issueRows, null, 'A1');

        $faults = $book->createSheet();
        $faults->setTitle('Faults');
        $faultRows = [['Fault identified', 'Devices affected']];
        foreach ($report['faults']['by_type'] as $fault => $count) {
            $faultRows[] = [$fault, $count];
        }
        $faultRows[] = [];
        $faultRows[] = ['Corrective action taken', 'Devices'];
        foreach ($report['corrective']['by_type'] as $action => $count) {
            $faultRows[] = [$action, $count];
        }
        $faults->fromArray($faultRows, null, 'A1');

        $txn = $book->createSheet();
        $txn->setTitle('Transactions');
        $txnRows = [['Terminal ID', 'Merchant', 'Transactions', 'Value', 'Target', 'Achievement %']];
        foreach ($report['devices'] as $device) {
            $txnRows[] = [
                $device['terminal_code'], $device['merchant_name'],
                $device['transactions']['actual'] ?? 'Not supplied',
                $device['transactions']['value'] ?? '',
                $device['transactions']['target'] ?? '',
                $device['transactions']['achievement'] ?? '',
            ];
        }
        $txn->fromArray($txnRows, null, 'A1');

        foreach ([$devices, $issues, $faults, $txn] as $sheet) {
            foreach (range('A', 'W') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->freezePane('A2');
        }
        $book->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($book) {
            (new Xlsx($book))->save('php://output');
        }, $name.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function logRun(string $type, string $start, string $end, ?int $clientId, ?string $region, string $format, int $rows): void
    {
        ReportRun::create([
            'user_id' => Auth::id(),
            'payload' => [
                'base' => ['table' => $type === ContractReport::TYPE_MONTHLY ? 'Monthly executive report' : 'Weekly operational report'],
                'period' => [$start, $end],
                'client_id' => $clientId,
                'region' => $region,
            ],
            'result_count' => $rows,
            'executed_at' => now(),
            'action' => 'export',
            'format' => $format,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);
    }

    // ---------------------------------------------------- transaction figures

    /** Upload screen + what has been supplied so far. */
    public function transactions(Request $request)
    {
        $recent = TerminalTransactionStat::query()
            ->select([
                'period_start', 'period_end', 'source_file',
                DB::raw('COUNT(*) as devices'),
                DB::raw('SUM(transactions_count) as transactions'),
                DB::raw('MAX(created_at) as uploaded_at'),
            ])
            ->groupBy('period_start', 'period_end', 'source_file')
            ->orderByDesc('period_start')
            ->limit(25)
            ->get();

        [$start, $end] = $this->service->defaultPeriod(ContractReport::TYPE_MONTHLY);

        return view('reports.contract.transactions', [
            'recent' => $recent,
            'defaultStart' => $start,
            'defaultEnd' => $end,
            'terminalCount' => PosTerminal::count(),
            'defaultTarget' => \App\Models\SystemSetting::get('transaction_target_default'),
        ]);
    }

    public function importTransactions(Request $request, TransactionStatsImporter $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls,ods', 'max:10240'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
        ]);

        $result = $importer->import(
            $request->file('file'),
            $request->input('period_start') ? CarbonImmutable::parse($request->input('period_start'))->toDateString() : null,
            $request->input('period_end') ? CarbonImmutable::parse($request->input('period_end'))->toDateString() : null,
            Auth::id()
        );

        $message = $result['imported'].' added, '.$result['updated'].' updated';
        if (! empty($result['skipped'])) {
            $message .= ', '.count($result['skipped']).' skipped';
        }

        return back()
            ->with($result['imported'] + $result['updated'] > 0 ? 'success' : 'warning', 'Transaction file processed: '.$message.'.')
            ->with('import_result', $result);
    }

    /** A CSV the client can fill in, pre-filled with the devices on record. */
    public function transactionTemplate(Request $request): StreamedResponse
    {
        [$start, $end] = $this->service->defaultPeriod(ContractReport::TYPE_MONTHLY);
        $start = $request->query('period_start', $start);
        $end = $request->query('period_end', $end);

        $terminals = PosTerminal::query()->orderBy('merchant_name')->get(['terminal_id', 'merchant_name']);

        return response()->stream(function () use ($terminals, $start, $end) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['terminal_id', 'merchant_name', 'period_start', 'period_end', 'transactions_count', 'transactions_value', 'target_count']);
            foreach ($terminals as $terminal) {
                fputcsv($out, [$terminal->terminal_id, $terminal->merchant_name, $start, $end, '', '', '']);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transaction-activity-template_'.$start.'_to_'.$end.'.csv"',
        ]);
    }
}
