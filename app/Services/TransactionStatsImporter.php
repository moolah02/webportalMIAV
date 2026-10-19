<?php

namespace App\Services;

use App\Models\PosTerminal;
use App\Models\TerminalTransactionStat;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Loads the transaction file the client/processor supplies (CSV or Excel) into
 * terminal_transaction_stats. Rows are matched to devices on the terminal code.
 *
 * Nothing is guessed: a row whose device is unknown, or whose figures are not
 * numbers, is skipped and reported back with the reason and line number.
 */
class TransactionStatsImporter
{
    /** Accepted spellings for each column, lower-cased, non-letters stripped. */
    private const COLUMNS = [
        'terminal' => ['terminalid', 'terminalcode', 'terminal', 'tid', 'deviceid', 'device', 'posterminalid'],
        'merchant' => ['merchantname', 'merchant', 'outlet', 'merchantid'],
        'start' => ['periodstart', 'startdate', 'start', 'from', 'fromdate', 'datefrom'],
        'end' => ['periodend', 'enddate', 'end', 'to', 'todate', 'dateto'],
        'count' => ['transactionscount', 'transactions', 'transactioncount', 'txncount', 'txns', 'count', 'volume', 'transactionvolume'],
        'value' => ['transactionsvalue', 'transactionvalue', 'value', 'amount', 'totalvalue', 'turnover'],
        'target' => ['targetcount', 'target', 'targettransactions', 'monthlytarget'],
    ];

    /**
     * @return array{imported:int, updated:int, skipped:array<int,array{line:int,reason:string,value:string}>, rows:int, period:array{start:string,end:string}|null}
     */
    public function import(UploadedFile $file, ?string $defaultStart, ?string $defaultEnd, ?int $employeeId): array
    {
        $rows = $this->readRows($file);
        $result = ['imported' => 0, 'updated' => 0, 'skipped' => [], 'rows' => 0, 'period' => null];

        if (empty($rows)) {
            $result['skipped'][] = ['line' => 0, 'reason' => 'The file has no rows', 'value' => $file->getClientOriginalName()];

            return $result;
        }

        $map = $this->mapHeader(array_shift($rows));
        if (! isset($map['terminal'])) {
            $result['skipped'][] = ['line' => 1, 'reason' => 'No terminal code column found in the file header', 'value' => ''];

            return $result;
        }
        if (! isset($map['count'])) {
            $result['skipped'][] = ['line' => 1, 'reason' => 'No transactions column found in the file header', 'value' => ''];

            return $result;
        }

        $terminals = PosTerminal::query()->select(['id', 'terminal_id'])->get()
            ->keyBy(fn ($t) => $this->normaliseCode($t->terminal_id));

        $periodMin = null;
        $periodMax = null;

        foreach ($rows as $index => $row) {
            $line = $index + 2;  // header is line 1
            $result['rows']++;

            $code = $this->cell($row, $map['terminal'] ?? null);
            if ($code === '') {
                $result['skipped'][] = ['line' => $line, 'reason' => 'No terminal code on this row', 'value' => ''];

                continue;
            }

            $terminal = $terminals[$this->normaliseCode($code)] ?? null;
            if (! $terminal) {
                $result['skipped'][] = ['line' => $line, 'reason' => 'Terminal code is not in the system', 'value' => $code];

                continue;
            }

            $countRaw = $this->cell($row, $map['count'] ?? null);
            $count = $this->toNumber($countRaw);
            if ($count === null) {
                $result['skipped'][] = ['line' => $line, 'reason' => 'Transactions value is not a number', 'value' => $countRaw];

                continue;
            }

            $start = $this->toDate($this->cell($row, $map['start'] ?? null)) ?: $defaultStart;
            $end = $this->toDate($this->cell($row, $map['end'] ?? null)) ?: $defaultEnd;
            if (! $start || ! $end) {
                $result['skipped'][] = ['line' => $line, 'reason' => 'No period on the row and none chosen for the upload', 'value' => $code];

                continue;
            }
            if ($start > $end) {
                $result['skipped'][] = ['line' => $line, 'reason' => 'Period start is after period end', 'value' => $start.' – '.$end];

                continue;
            }

            $periodMin = $periodMin === null || $start < $periodMin ? $start : $periodMin;
            $periodMax = $periodMax === null || $end > $periodMax ? $end : $periodMax;

            $existing = TerminalTransactionStat::where('pos_terminal_id', $terminal->id)
                ->whereDate('period_start', $start)->whereDate('period_end', $end)->first();

            $values = [
                'terminal_code' => $code,
                'transactions_count' => (int) $count,
                'transactions_value' => $this->toNumber($this->cell($row, $map['value'] ?? null)),
                'target_count' => ($t = $this->toNumber($this->cell($row, $map['target'] ?? null))) !== null ? (int) $t : null,
                'source_file' => $file->getClientOriginalName(),
                'imported_by' => $employeeId,
            ];

            if ($existing) {
                $existing->fill($values)->save();
                $result['updated']++;
            } else {
                TerminalTransactionStat::create($values + [
                    'pos_terminal_id' => $terminal->id,
                    'period_start' => $start,
                    'period_end' => $end,
                ]);
                $result['imported']++;
            }
        }

        if ($periodMin && $periodMax) {
            $result['period'] = ['start' => $periodMin, 'end' => $periodMax];
        }

        return $result;
    }

    /** @return array<int, array<int, string>> */
    private function readRows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['xlsx', 'xls', 'ods'], true)) {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($file->getRealPath())->getActiveSheet();

            $rows = [];
            foreach ($sheet->toArray(null, true, false, false) as $row) {
                $rows[] = array_map(fn ($cell) => (string) ($cell ?? ''), $row);
            }

            return $this->dropEmptyRows($rows);
        }

        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return [];
        }
        $first = true;
        while (($row = fgetcsv($handle, 0, $this->delimiter($file))) !== false) {
            if ($first) {
                // Strip a UTF-8 BOM written by Excel.
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($row[0] ?? ''));
                $first = false;
            }
            $rows[] = array_map(fn ($cell) => (string) ($cell ?? ''), $row);
        }
        fclose($handle);

        return $this->dropEmptyRows($rows);
    }

    private function dropEmptyRows(array $rows): array
    {
        return array_values(array_filter($rows, fn ($row) => trim(implode('', $row)) !== ''));
    }

    /** Excel in some locales writes semicolon-separated CSV. */
    private function delimiter(UploadedFile $file): string
    {
        $line = (string) fgets(fopen($file->getRealPath(), 'r'));

        return substr_count($line, ';') > substr_count($line, ',') ? ';' : ',';
    }

    /** @return array<string,int> column key => index in the row */
    private function mapHeader(array $header): array
    {
        $map = [];
        foreach ($header as $index => $label) {
            $key = preg_replace('/[^a-z]/', '', strtolower((string) $label));
            if ($key === '') {
                continue;
            }
            foreach (self::COLUMNS as $name => $spellings) {
                if (! isset($map[$name]) && in_array($key, $spellings, true)) {
                    $map[$name] = $index;
                }
            }
        }

        return $map;
    }

    private function cell(array $row, ?int $index): string
    {
        return $index === null ? '' : trim((string) ($row[$index] ?? ''));
    }

    private function normaliseCode(?string $code): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $code));
    }

    /** Accepts "1 234", "1,234", "1234.00"; returns null when not a number. */
    private function toNumber(string $value): float|int|null
    {
        if ($value === '') {
            return null;
        }
        $clean = str_replace([' ', ',', "\u{00A0}"], ['', '', ''], $value);

        return is_numeric($clean) ? $clean + 0 : null;
    }

    /** Accepts Y-m-d, d/m/Y, d-m-Y and Excel serial dates; returns Y-m-d. */
    private function toDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) {
            // Excel serial day number (days since 1899-12-30).
            return CarbonImmutable::create(1899, 12, 30)->addDays((int) (float) $value)->toDateString();
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd M Y', 'j M Y', 'Y-m-d H:i:s', 'm/d/Y'] as $format) {
            $date = CarbonImmutable::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->toDateString();
            }
        }

        try {
            return CarbonImmutable::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
