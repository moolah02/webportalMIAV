@extends('layouts.app')
@section('title', 'Request Details')

@push('styles')
<style>
.ar-show .rq-head { display: flex; align-items: center; justify-content: space-between; gap: 12px 16px; flex-wrap: wrap; margin-bottom: 16px; }
.ar-show .rq-head-main { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; min-width: 0; }
.ar-show .rq-head-main .code-chip { font-size: 13px; padding: 3px 8px; }
.ar-show .rq-head-by { margin-left: 6px; font-size: 13px; color: var(--mv-muted); }
.ar-show .rq-head-by strong { font-weight: 500; color: var(--mv-ink-2); }
.ar-show .rq-head-actions { display: flex; align-items: center; gap: 8px; }
.ar-show .rq-head-actions form { margin: 0; }
.ar-show .rq-head-actions .btn-secondary, .ar-show .rq-head-actions .btn-danger { height: 36px; padding-top: 0; padding-bottom: 0; }

.ar-show .rq-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 20px; align-items: start; }
.ar-show .rq-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

.ar-show .rq-kv { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 0; }
.ar-show .rq-kv > div { padding: 14px 18px; border-left: 1px solid var(--mv-line); min-width: 0; }
.ar-show .rq-kv > div:first-child { border-left: 0; }
.ar-show .rq-kv dt { margin: 0 0 3px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
.ar-show .rq-kv dd { margin: 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.ar-show .rq-kv dd small { display: block; font-size: 12px; font-weight: 400; color: var(--mv-muted); }

.ar-show .rq-text { margin: 0; font-size: 13.5px; line-height: 1.6; color: var(--mv-ink-2); white-space: pre-line; }
.ar-show .rq-sub-label { margin: 16px 0 3px; padding-top: 14px; border-top: 1px solid var(--mv-line); font-size: 12px; font-weight: 500; color: var(--mv-muted); }

.ar-show .ui-table th.num, .ar-show .ui-table td.num { text-align: right; }
.ar-show .ui-table th.mid, .ar-show .ui-table td.mid { text-align: center; }
.ar-show .rq-dash { color: var(--mv-muted); }

.ar-show .rq-timeline { list-style: none; margin: 0; padding: 0; }
.ar-show .rq-timeline li { position: relative; display: flex; gap: 12px; padding-bottom: 16px; }
.ar-show .rq-timeline li:last-child { padding-bottom: 0; }
.ar-show .rq-timeline li:not(:last-child)::before { content: ""; position: absolute; left: 10px; top: 25px; bottom: 3px; width: 1px; background: var(--mv-line); }
.ar-show .rq-tl-dot { width: 21px; height: 21px; flex-shrink: 0; border-radius: 50%; display: grid; place-items: center; background: var(--mv-surface); border: 1px solid var(--mv-line-strong); color: var(--mv-muted); }
.ar-show .rq-tl-dot .mv-i { width: 12px; height: 12px; stroke-width: 2.25; }
.ar-show .rq-timeline .is-done .rq-tl-dot { background: var(--mv-good-soft); border-color: #C6E6D2; color: var(--mv-good); }
.ar-show .rq-timeline .is-crit .rq-tl-dot { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.ar-show .rq-timeline .is-muted .rq-tl-dot { background: var(--mv-surface-2); color: var(--mv-ink-2); }
.ar-show .rq-tl-title { font-size: 13.5px; font-weight: 500; line-height: 21px; color: var(--mv-ink); }
.ar-show .rq-timeline .is-todo .rq-tl-title { font-weight: 400; color: var(--mv-muted); }
.ar-show .rq-tl-meta { font-size: 12px; color: var(--mv-muted); }

.ar-show .rq-person { display: flex; align-items: center; gap: 10px; }
.ar-show .rq-avatar { width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; display: grid; place-items: center; background: var(--mv-accent-soft); color: var(--mv-accent-ink); font-size: 13px; font-weight: 600; }
.ar-show .rq-person-name { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.ar-show .rq-person-sub { font-size: 12.5px; color: var(--mv-muted); }
.ar-show .rq-contact { display: flex; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--mv-line); font-size: 13px; word-break: break-all; }
.ar-show .rq-contact .mv-i { color: var(--mv-muted); }
.ar-show .rq-contact a { color: var(--mv-accent-ink); text-decoration: none; }
.ar-show .rq-contact a:hover { text-decoration: underline; }

.ar-show .ui-card-header h2 .mv-i { width: 16px; height: 16px; margin-right: 6px; vertical-align: -3px; }
.ar-show .rq-tone-good { color: var(--mv-good); }
.ar-show .rq-tone-crit { color: var(--mv-crit); }
.ar-show .rq-note { margin-top: 10px; padding: 10px 12px; border-radius: 8px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); font-size: 13px; line-height: 1.5; color: var(--mv-ink-2); white-space: pre-line; }
.ar-show .rq-note.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }

@media (max-width: 1100px) { .ar-show .rq-layout { grid-template-columns: minmax(0, 1fr); } }
@media (max-width: 700px) {
    .ar-show .rq-kv { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .ar-show .rq-kv > div:nth-child(3) { border-left: 0; }
    .ar-show .rq-kv > div:nth-child(n+3) { border-top: 1px solid var(--mv-line); }
}
</style>
@endpush

@section('content')
@php
    $status = $assetRequest->status;
    $statusClass = match($status) {
        'approved'           => 'badge-green',
        'fulfilled'          => 'badge-blue',
        'partially_approved' => 'badge-blue',
        'rejected'           => 'badge-red',
        'pending'            => 'badge-yellow',
        default              => 'badge-gray',
    };
    $priorityClass = match($assetRequest->priority) {
        'urgent' => 'badge-red',
        'high'   => 'badge-yellow',
        default  => 'badge-gray',
    };
    $itemStatusClass = fn ($s) => match($s) {
        'approved'           => 'badge-green',
        'partially_approved' => 'badge-blue',
        'fulfilled'          => 'badge-blue',
        'rejected'           => 'badge-red',
        'pending'            => 'badge-yellow',
        default              => 'badge-gray',
    };
    $submittedDone = in_array($status, ['pending', 'approved', 'fulfilled', 'rejected', 'cancelled']);
    $decisionState = in_array($status, ['approved', 'fulfilled']) ? 'done' : ($status === 'rejected' ? 'crit' : ($status === 'cancelled' ? 'muted' : 'todo'));
    $decisionLabel = $status === 'rejected' ? 'Rejected' : ($status === 'cancelled' ? 'Cancelled' : 'Approved');
@endphp
<div class="ar-show">

    {{-- Header row --}}
    <div class="rq-head">
        <div class="rq-head-main">
            <span class="code-chip">{{ $assetRequest->request_number }}</span>
            <span class="status-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
            <span class="status-badge {{ $priorityClass }}">{{ ucfirst($assetRequest->priority) }} Priority</span>
            <span class="rq-head-by">Submitted by <strong>{{ $assetRequest->employee->full_name }}</strong></span>
        </div>
        <div class="rq-head-actions">
            @if($assetRequest->employee_id === auth()->id())
                @if(in_array($assetRequest->status, ['pending', 'draft']))
                <form action="{{ route('asset-requests.cancel', $assetRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-danger">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x-circle"/></svg>
                        Cancel Request
                    </button>
                </form>
                @endif
                <a href="{{ route('asset-requests.index') }}" class="btn-secondary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
                    My Requests
                </a>
            @else
                <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
                    Back to Approvals
                </a>
            @endif
        </div>
    </div>

    <div class="rq-layout">
        {{-- Main Content --}}
        <div class="rq-col">

            {{-- Request Details --}}
            <section class="ui-card">
                <dl class="rq-kv">
                    <div>
                        <dt>Submitted</dt>
                        <dd>{{ $assetRequest->created_at->format('M d, Y') }}<small>{{ $assetRequest->created_at->format('g:i A') }}</small></dd>
                    </div>
                    <div>
                        <dt>Needed By</dt>
                        <dd>{{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt>Total Cost</dt>
                        <dd>${{ number_format($assetRequest->total_estimated_cost, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Items</dt>
                        <dd>{{ $assetRequest->total_items }} items</dd>
                    </div>
                </dl>
            </section>

            {{-- Business Justification --}}
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Business Justification</h2>
                </div>
                <div class="ui-card-body">
                    <p class="rq-text">{{ $assetRequest->business_justification }}</p>
                    @if($assetRequest->delivery_instructions)
                    <div class="rq-sub-label">Delivery Instructions</div>
                    <p class="rq-text">{{ $assetRequest->delivery_instructions }}</p>
                    @endif
                </div>
            </section>

            {{-- Requested Items --}}
            <section class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h2>Requested Items</h2>
                    <span class="badge badge-gray">{{ $assetRequest->items->count() }} lines</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th class="num">Requested</th>
                                <th class="num">Approved</th>
                                <th class="num">Fulfilled</th>
                                <th class="num">Unit Price</th>
                                <th class="num">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetRequest->items as $item)
                            <tr>
                                <td>
                                    <div class="cell-primary">{{ $item->asset->name }}</div>
                                    <div class="cell-sub">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                                </td>
                                <td class="num">{{ $item->quantity_requested }}</td>
                                <td class="num">{!! $item->quantity_approved ? e($item->quantity_approved) : '<span class="rq-dash">-</span>' !!}</td>
                                <td class="num">{!! $item->quantity_fulfilled ? e($item->quantity_fulfilled) : '<span class="rq-dash">-</span>' !!}</td>
                                <td class="num">${{ number_format($item->unit_price_at_request, 2) }}</td>
                                <td class="num"><span class="cell-primary">${{ number_format($item->total_price, 2) }}</span></td>
                                <td>
                                    <span class="status-badge {{ $itemStatusClass($item->item_status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->item_status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="rq-col">

            {{-- Request Status (timeline) --}}
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Request Status</h2>
                </div>
                <div class="ui-card-body">
                    <ol class="rq-timeline">
                        <li class="is-{{ $submittedDone ? 'done' : 'todo' }}">
                            <span class="rq-tl-dot">
                                @if($submittedDone)<svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg>@endif
                            </span>
                            <div>
                                <div class="rq-tl-title">Submitted</div>
                                <div class="rq-tl-meta">{{ $assetRequest->created_at->format('M d, Y \a\t g:i A') }}</div>
                            </div>
                        </li>
                        <li class="is-{{ $decisionState }}">
                            <span class="rq-tl-dot">
                                @if($decisionState === 'done')
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg>
                                @elseif($decisionState === 'crit' || $decisionState === 'muted')
                                    <svg class="mv-i" aria-hidden="true"><use href="#i-x"/></svg>
                                @endif
                            </span>
                            <div>
                                <div class="rq-tl-title">{{ $decisionLabel }}</div>
                                @if(in_array($decisionState, ['done', 'crit']) && $assetRequest->approved_at)
                                <div class="rq-tl-meta">{{ $assetRequest->approved_at->format('M d, Y \a\t g:i A') }}</div>
                                @endif
                            </div>
                        </li>
                        <li class="is-{{ $status === 'fulfilled' ? 'done' : 'todo' }}">
                            <span class="rq-tl-dot">
                                @if($status === 'fulfilled')<svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg>@endif
                            </span>
                            <div>
                                <div class="rq-tl-title">Fulfilled</div>
                                @if($status === 'fulfilled' && $assetRequest->fulfilled_at)
                                <div class="rq-tl-meta">{{ $assetRequest->fulfilled_at->format('M d, Y \a\t g:i A') }}</div>
                                @endif
                            </div>
                        </li>
                    </ol>
                </div>
            </section>

            {{-- Requester Info --}}
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Requester</h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person">
                        <span class="rq-avatar" aria-hidden="true">{{ strtoupper(mb_substr($assetRequest->employee->full_name ?? '', 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="rq-person-name">{{ $assetRequest->employee->full_name }}</div>
                            <div class="rq-person-sub">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
                            <div class="rq-person-sub">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
                        </div>
                    </div>
                    @if($assetRequest->employee->email)
                    <div class="rq-contact">
                        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-mail"/></svg>
                        <a href="mailto:{{ $assetRequest->employee->email }}">{{ $assetRequest->employee->email }}</a>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Approval Info --}}
            @if($assetRequest->status === 'approved' || $assetRequest->status === 'fulfilled')
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2><svg class="mv-i rq-tone-good" aria-hidden="true"><use href="#i-check-circle"/></svg>Approval Details</h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person-name">{{ $assetRequest->approver->full_name }}</div>
                    <div class="rq-person-sub">{{ $assetRequest->approved_at->format('M d, Y \a\t g:i A') }}</div>
                    @if($assetRequest->approval_notes)
                    <div class="rq-note">{{ $assetRequest->approval_notes }}</div>
                    @endif
                </div>
            </section>
            @elseif($assetRequest->status === 'rejected')
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2><svg class="mv-i rq-tone-crit" aria-hidden="true"><use href="#i-x-circle"/></svg>Rejection Details</h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person-name">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                    <div class="rq-person-sub">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
                    @if($assetRequest->rejection_reason)
                    <div class="rq-note is-crit">{{ $assetRequest->rejection_reason }}</div>
                    @endif
                </div>
            </section>
            @endif

            {{-- Fulfillment Info --}}
            @if($assetRequest->status === 'fulfilled' && $assetRequest->fulfiller)
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Fulfillment Details</h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person-name">{{ $assetRequest->fulfiller->full_name }}</div>
                    <div class="rq-person-sub">{{ $assetRequest->fulfilled_at->format('M d, Y \a\t g:i A') }}</div>
                    @if($assetRequest->fulfillment_notes)
                    <div class="rq-note">{{ $assetRequest->fulfillment_notes }}</div>
                    @endif
                </div>
            </section>
            @endif
        </aside>
    </div>

</div>
@endsection
