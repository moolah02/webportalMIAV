@extends('layouts.app')

@section('title', 'Transaction Activity')

@section('header-actions')
    <a class="btn-secondary" href="{{ route('reports.transactions.template', ['period_start' => $defaultStart, 'period_end' => $defaultEnd]) }}">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download blank template
    </a>
@endsection

@push('styles')
<style>
.mv-page .tx-intro { color: var(--mv-muted); font-size: 13px; margin: -4px 0 18px; max-width: 760px; line-height: 1.6; }
.mv-page .tx-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 18px; }
.mv-page .tx-card-head { padding: 13px 16px; border-bottom: 1px solid var(--mv-line); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.mv-page .tx-card-head h2 { font-size: 15px; font-weight: 600; margin: 0; }
.mv-page .tx-card-head .tx-note { font-size: 12.5px; color: var(--mv-muted); }
.mv-page .tx-card-body { padding: 16px; }
.mv-page .tx-form { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; }
.mv-page .tx-field { display: flex; flex-direction: column; gap: 4px; }
.mv-page .tx-field label { font-size: 12px; color: var(--mv-muted); font-weight: 500; }
.mv-page .tx-field input { height: 36px; border: 1px solid var(--mv-line-strong); border-radius: 7px; padding: 0 10px;
    font: inherit; font-size: 13px; background: var(--mv-surface); color: var(--mv-ink); min-width: 170px; }
.mv-page .tx-field input[type=file] { padding: 6px 10px; height: auto; min-width: 280px; }
.mv-page table.tx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mv-page table.tx-table th { text-align: left; font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em;
    color: var(--mv-muted); font-weight: 600; padding: 9px 10px; border-bottom: 1px solid var(--mv-line); }
.mv-page table.tx-table td { padding: 9px 10px; border-bottom: 1px solid var(--mv-line); }
.mv-page table.tx-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
.mv-page .tx-empty { padding: 22px; text-align: center; color: var(--mv-muted); font-size: 13px; }
.mv-page .tx-cols { font-size: 13px; line-height: 1.8; }
.mv-page .tx-cols code { font-family: var(--mv-mono); font-size: 12px; background: var(--mv-surface-2);
    border: 1px solid var(--mv-line); border-radius: 4px; padding: 1px 5px; }
.mv-page .tx-skipped { margin-top: 12px; border: 1px solid var(--mv-line); border-radius: 8px; overflow: hidden; }
.mv-page .tx-skipped-head { background: var(--mv-warn-soft); color: #6b4708; font-size: 12.5px; font-weight: 600; padding: 8px 12px; }
</style>
@endpush

@section('content')
<p class="tx-intro">
    The portal records field work; it does not receive transactions. To report transaction activity against target,
    upload the file the client or the processor supplies for the period. Figures are matched to devices on the
    terminal code. Anything that cannot be matched is listed back to you, and never counted.
</p>

@php $result = session('import_result'); @endphp
@if($result)
    <div class="tx-card">
        <div class="tx-card-head">
            <h2>Last upload</h2>
            <span class="tx-note">{{ $result['rows'] }} row{{ $result['rows'] === 1 ? '' : 's' }} read</span>
        </div>
        <div class="tx-card-body">
            <div style="font-size:13.5px">
                {{ $result['imported'] }} added &middot; {{ $result['updated'] }} updated &middot;
                {{ count($result['skipped']) }} skipped
                @if($result['period'])
                    &middot; period {{ $result['period']['start'] }} to {{ $result['period']['end'] }}
                @endif
            </div>
            @if(! empty($result['skipped']))
                <div class="tx-skipped">
                    <div class="tx-skipped-head">Rows not loaded</div>
                    <table class="tx-table">
                        <thead><tr><th>Line</th><th>Value</th><th>Reason</th></tr></thead>
                        <tbody>
                        @foreach(array_slice($result['skipped'], 0, 50) as $skip)
                            <tr><td>{{ $skip['line'] }}</td><td>{{ $skip['value'] }}</td><td>{{ $skip['reason'] }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($result['skipped']) > 50)
                        <div class="tx-empty">and {{ count($result['skipped']) - 50 }} more.</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif

<div class="tx-card">
    <div class="tx-card-head">
        <h2>Upload transaction figures</h2>
        <span class="tx-note">CSV or Excel &middot; {{ $terminalCount }} devices on record</span>
    </div>
    <div class="tx-card-body">
        <form method="POST" action="{{ route('reports.transactions.import') }}" enctype="multipart/form-data" class="tx-form">
            @csrf
            <div class="tx-field">
                <label for="tx-file">File</label>
                <input type="file" id="tx-file" name="file" accept=".csv,.txt,.xlsx,.xls,.ods" required>
            </div>
            <div class="tx-field">
                <label for="tx-start">Period from</label>
                <input type="date" id="tx-start" name="period_start" value="{{ $defaultStart }}">
            </div>
            <div class="tx-field">
                <label for="tx-end">Period to</label>
                <input type="date" id="tx-end" name="period_end" value="{{ $defaultEnd }}">
            </div>
            <button type="submit" class="btn-primary">Upload</button>
        </form>
        <p class="tx-note" style="margin:12px 0 0">
            The dates above are used for any row that does not carry its own period.
        </p>
    </div>
</div>

<div class="tx-card">
    <div class="tx-card-head"><h2>What the file needs</h2></div>
    <div class="tx-card-body tx-cols">
        <div><code>terminal_id</code> — the terminal code as it appears on the device list (required)</div>
        <div><code>transactions_count</code> — number of transactions in the period (required)</div>
        <div><code>period_start</code>, <code>period_end</code> — optional; the dates above are used when absent</div>
        <div><code>transactions_value</code> — optional total value</div>
        <div><code>target_count</code> — optional target for that device and period</div>
        <p class="tx-note" style="margin:10px 0 0">
            Common spellings are accepted (for example <code>Terminal ID</code>, <code>TID</code>, <code>Txn Count</code>,
            <code>Volume</code>). Where no target is supplied, the target set on the device is used, then the client's
            default, then the system default@if($defaultTarget) (currently {{ $defaultTarget }} per device per month)@endif.
        </p>
    </div>
</div>

<div class="tx-card">
    <div class="tx-card-head">
        <h2>Figures supplied so far</h2>
        <span class="tx-note">Most recent 25 uploads</span>
    </div>
    @if($recent->isEmpty())
        <div class="tx-empty">Nothing uploaded yet. Reports will show transaction activity as "not supplied" until a file is loaded.</div>
    @else
        <table class="tx-table">
            <thead>
                <tr><th>Period</th><th>File</th><th class="num">Devices</th><th class="num">Transactions</th><th>Uploaded</th></tr>
            </thead>
            <tbody>
            @foreach($recent as $row)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($row->period_start)->format('d M Y') }}
                        &ndash; {{ \Illuminate\Support\Carbon::parse($row->period_end)->format('d M Y') }}</td>
                    <td>{{ $row->source_file ?: '—' }}</td>
                    <td class="num">{{ number_format($row->devices) }}</td>
                    <td class="num">{{ number_format($row->transactions) }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($row->uploaded_at)->timezone('Africa/Harare')->format('d M Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
