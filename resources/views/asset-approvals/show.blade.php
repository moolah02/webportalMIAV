@extends('layouts.app')
@section('title', 'Approval Details')

@push('styles')
<style>
.aa-show .rq-flash { margin-bottom: 16px; }
.aa-show .rq-head { display: flex; align-items: center; justify-content: space-between; gap: 12px 16px; flex-wrap: wrap; margin-bottom: 16px; }
.aa-show .rq-head-main { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; min-width: 0; }
.aa-show .rq-head-main .code-chip { font-size: 13px; padding: 3px 8px; }
.aa-show .rq-head-by { margin-left: 6px; font-size: 13px; color: var(--mv-muted); }
.aa-show .rq-head-by strong { font-weight: 500; color: var(--mv-ink-2); }
.aa-show .rq-head-actions { display: flex; align-items: center; gap: 8px; }
.aa-show .rq-head-actions .btn-secondary, .aa-show .rq-head-actions .btn-danger, .aa-show .rq-head-actions .btn-success { height: 36px; padding-top: 0; padding-bottom: 0; }

.aa-show .rq-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 20px; align-items: start; }
.aa-show .rq-col { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

.aa-show .rq-kv { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 0; border-bottom: 1px solid var(--mv-line); }
.aa-show .rq-kv > div { padding: 14px 18px; border-left: 1px solid var(--mv-line); min-width: 0; }
.aa-show .rq-kv > div:first-child { border-left: 0; }
.aa-show .rq-kv dt { margin: 0 0 3px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
.aa-show .rq-kv dd { margin: 0; font-size: 13.5px; font-weight: 500; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.aa-show .rq-kv dd small { display: block; font-size: 12px; font-weight: 400; color: var(--mv-muted); }
.aa-show .rq-kv dd.is-crit { color: var(--mv-crit); }
.aa-show .rq-kv .badge { margin-top: 4px; }

.aa-show .rq-label { margin: 0 0 3px; font-size: 12px; font-weight: 500; color: var(--mv-muted); }
.aa-show .rq-text { margin: 0; font-size: 13.5px; line-height: 1.6; color: var(--mv-ink-2); white-space: pre-line; }
.aa-show .rq-block + .rq-block { margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--mv-line); }

.aa-show .ui-table th.num, .aa-show .ui-table td.num { text-align: right; }
.aa-show .rq-stock { font-weight: 600; font-variant-numeric: tabular-nums; }
.aa-show .rq-stock.is-good { color: var(--mv-good); }
.aa-show .rq-stock.is-warn { color: var(--mv-warn); }
.aa-show .rq-stock.is-crit { color: var(--mv-crit); }
.aa-show .rq-stock + .badge { margin-left: 6px; }
.aa-show .ui-table tfoot td { padding: 12px 14px; border-top: 1px solid var(--mv-line); background: var(--mv-surface-2); font-size: 13.5px; color: var(--mv-ink); font-variant-numeric: tabular-nums; }
.aa-show .ui-table tfoot .rq-total { font-size: 15px; font-weight: 600; }

.aa-show .ui-card-header h2 .mv-i { width: 16px; height: 16px; margin-right: 6px; vertical-align: -3px; }
.aa-show .rq-tone-good { color: var(--mv-good); }
.aa-show .rq-tone-crit { color: var(--mv-crit); }
.aa-show .rq-note { margin-top: 10px; padding: 10px 12px; border-radius: 8px; background: var(--mv-surface-2); border: 1px solid var(--mv-line); font-size: 13px; line-height: 1.5; color: var(--mv-ink-2); white-space: pre-line; }
.aa-show .rq-note.is-crit { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.aa-show .rq-assign { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--mv-line); }
.aa-show .rq-assign p { margin: 0; font-size: 13px; color: var(--mv-ink-2); }

.aa-show .rq-person { display: flex; align-items: center; gap: 10px; }
.aa-show .rq-avatar { width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; display: grid; place-items: center; background: var(--mv-accent-soft); color: var(--mv-accent-ink); font-size: 13px; font-weight: 600; }
.aa-show .rq-person-name { font-size: 13.5px; font-weight: 500; color: var(--mv-ink); }
.aa-show .rq-person-sub { font-size: 12.5px; color: var(--mv-muted); }
.aa-show .rq-contact { display: flex; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--mv-line); font-size: 13px; word-break: break-all; }
.aa-show .rq-contact .mv-i { color: var(--mv-muted); }
.aa-show .rq-contact a { color: var(--mv-accent-ink); text-decoration: none; }
.aa-show .rq-contact a:hover { text-decoration: underline; }

.aa-show .rq-timeline { list-style: none; margin: 0; padding: 0; }
.aa-show .rq-timeline li { position: relative; display: flex; gap: 12px; padding-bottom: 16px; }
.aa-show .rq-timeline li:last-child { padding-bottom: 0; }
.aa-show .rq-timeline li:not(:last-child)::before { content: ""; position: absolute; left: 10px; top: 25px; bottom: 3px; width: 1px; background: var(--mv-line); }
.aa-show .rq-tl-dot { width: 21px; height: 21px; flex-shrink: 0; border-radius: 50%; display: grid; place-items: center; background: var(--mv-surface); border: 1px solid var(--mv-line-strong); color: var(--mv-muted); }
.aa-show .rq-tl-dot .mv-i { width: 12px; height: 12px; stroke-width: 2.25; }
.aa-show .rq-timeline .is-done .rq-tl-dot { background: var(--mv-good-soft); border-color: #C6E6D2; color: var(--mv-good); }
.aa-show .rq-timeline .is-crit .rq-tl-dot { background: var(--mv-crit-soft); border-color: #F2CACA; color: var(--mv-crit); }
.aa-show .rq-timeline .is-muted .rq-tl-dot { background: var(--mv-surface-2); color: var(--mv-ink-2); }
.aa-show .rq-tl-title { font-size: 13.5px; font-weight: 500; line-height: 21px; color: var(--mv-ink); }
.aa-show .rq-timeline .is-todo .rq-tl-title { font-weight: 400; color: var(--mv-muted); }
.aa-show .rq-tl-meta { font-size: 12px; color: var(--mv-muted); }

/* Modals (JS toggles .hidden on the overlay) */
.aa-show .rq-modal { position: fixed; inset: 0; z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(22, 32, 44, .45); }
.aa-show .rq-modal-card { width: 100%; max-width: 440px; background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; box-shadow: 0 16px 40px rgba(22, 32, 44, .18); }
.aa-show .rq-modal-head { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--mv-line); }
.aa-show .rq-modal-head h3 { flex: 1; margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
.aa-show .rq-modal-ic { width: 28px; height: 28px; flex-shrink: 0; border-radius: 8px; display: grid; place-items: center; }
.aa-show .rq-modal-ic.is-good { background: var(--mv-good-soft); color: var(--mv-good); }
.aa-show .rq-modal-ic.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); }
.aa-show .rq-modal-close { width: 30px; height: 30px; border: 0; border-radius: 7px; background: transparent; color: var(--mv-muted); display: grid; place-items: center; cursor: pointer; }
.aa-show .rq-modal-close:hover { background: var(--mv-surface-2); color: var(--mv-ink); }
.aa-show .rq-modal-body { padding: 16px 18px 18px; }
.aa-show .rq-modal-body form { margin: 0; }
.aa-show .rq-modal-body .ui-label { margin-bottom: 6px; }
.aa-show .rq-modal-body .ui-textarea { font-size: 13.5px; }
.aa-show .rq-opt { font-weight: 400; color: var(--mv-muted); }
.aa-show .rq-req { color: var(--mv-crit); }
.aa-show .rq-hint { margin: 5px 0 0; font-size: 12px; color: var(--mv-muted); }
.aa-show .rq-modal-foot { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }

@media (max-width: 1100px) { .aa-show .rq-layout { grid-template-columns: minmax(0, 1fr); } }
@media (max-width: 700px) {
    .aa-show .rq-kv { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .aa-show .rq-kv > div:nth-child(3) { border-left: 0; }
    .aa-show .rq-kv > div:nth-child(n+3) { border-top: 1px solid var(--mv-line); }
}
</style>
@endpush

@section('content')
@php
    $statusClass = match($assetRequest->status) {
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
    $status = $assetRequest->status;
    $isApproved = in_array($status, ['approved', 'fulfilled']);
    $decisionState = $isApproved ? 'done' : ($status === 'rejected' ? 'crit' : ($status === 'cancelled' ? 'muted' : 'todo'));
    $decisionLabel = $status === 'rejected' ? 'Rejected' : ($status === 'cancelled' ? 'Cancelled' : 'Approved');
@endphp
<div class="aa-show">

    {{-- Validation errors (session flashes are shown by the layout) --}}
    @if($errors->any())
    <div class="flash-error rq-flash">
        <svg class="mv-i" aria-hidden="true"><use href="#i-alert-circle"/></svg>
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Header row --}}
    <div class="rq-head">
        <div class="rq-head-main">
            <span class="code-chip">{{ $assetRequest->request_number }}</span>
            <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $assetRequest->status)) }}</span>
            <span class="badge {{ $priorityClass }}">{{ ucfirst($assetRequest->priority) }} Priority</span>
            <span class="rq-head-by">Submitted by <strong>{{ $assetRequest->employee->full_name }}</strong></span>
        </div>
        <div class="rq-head-actions">
            <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
                Back to Approvals
            </a>
            @if($assetRequest->status === 'pending')
            <button type="button" class="btn-danger" onclick="openReject()">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg>
                Reject Request
            </button>
            <button type="button" class="btn-success" onclick="openApprove()">
                <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check"/></svg>
                Approve Request
            </button>
            @endif
        </div>
    </div>

    <div class="rq-layout">

        {{-- Main Column --}}
        <div class="rq-col">

            {{-- Overview Card --}}
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Request Overview</h2>
                </div>
                <dl class="rq-kv">
                    <div>
                        <dt>Submitted</dt>
                        <dd>{{ $assetRequest->created_at->format('M d, Y') }}<small>{{ $assetRequest->created_at->format('g:i A') }}</small></dd>
                    </div>
                    <div>
                        <dt>Needed By</dt>
                        <dd class="{{ $assetRequest->needed_by_date?->isPast() ? 'is-crit' : '' }}">
                            {{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'ASAP' }}
                        </dd>
                        @if($assetRequest->needed_by_date?->isPast())
                        <span class="badge badge-red">Overdue</span>
                        @endif
                    </div>
                    <div>
                        <dt>Total Items</dt>
                        <dd>{{ $assetRequest->items->sum('quantity_requested') }}</dd>
                    </div>
                    <div>
                        <dt>Estimated Cost</dt>
                        <dd>${{ number_format($assetRequest->total_estimated_cost, 2) }}</dd>
                    </div>
                </dl>
                <div class="ui-card-body">
                    <div class="rq-block">
                        <div class="rq-label">Business Justification</div>
                        <p class="rq-text">{{ $assetRequest->business_justification }}</p>
                    </div>
                    @if($assetRequest->delivery_instructions)
                    <div class="rq-block">
                        <div class="rq-label">Delivery Instructions</div>
                        <p class="rq-text">{{ $assetRequest->delivery_instructions }}</p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Request Items Card --}}
            <section class="ui-card overflow-hidden">
                <div class="ui-card-header">
                    <h2>Request Items</h2>
                    <span class="badge badge-gray">{{ $assetRequest->items->count() }} lines</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th class="num">Requested</th>
                                <th>In Stock</th>
                                <th class="num">Unit Price</th>
                                <th class="num">Subtotal</th>
                                <th>Item Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetRequest->items as $item)
                            @php $stock = $item->asset->stock_quantity ?? 0; @endphp
                            <tr>
                                <td>
                                    <div class="cell-primary">{{ $item->asset->name }}</div>
                                    @if($item->asset->brand || $item->asset->model)
                                    <div class="cell-sub">{{ trim($item->asset->brand . ' ' . $item->asset->model) }}</div>
                                    @endif
                                </td>
                                <td class="num"><span class="cell-primary">{{ $item->quantity_requested }}</span></td>
                                <td>
                                    <span class="rq-stock {{ $stock >= $item->quantity_requested ? 'is-good' : ($stock > 0 ? 'is-warn' : 'is-crit') }}">{{ $stock }}</span>
                                    @if($stock < $item->quantity_requested)
                                    <span class="badge badge-yellow">Low stock</span>
                                    @endif
                                </td>
                                <td class="num">${{ number_format($item->unit_price_at_request, 2) }}</td>
                                <td class="num"><span class="cell-primary">${{ number_format($item->total_price, 2) }}</span></td>
                                <td>
                                    <span class="badge {{ $itemStatusClass($item->item_status) }}">{{ ucfirst(str_replace('_', ' ', $item->item_status)) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="num">Grand Total</td>
                                <td class="num rq-total">${{ number_format($assetRequest->total_estimated_cost, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

            {{-- Decision Summary (non-pending) --}}
            @if($assetRequest->status !== 'pending')
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>
                        @if($isApproved)
                            <svg class="mv-i rq-tone-good" aria-hidden="true"><use href="#i-check-circle"/></svg>Approval Details
                        @else
                            <svg class="mv-i rq-tone-crit" aria-hidden="true"><use href="#i-x-circle"/></svg>Rejection Details
                        @endif
                    </h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person-name">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                    <div class="rq-person-sub">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>

                    @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
                    <div class="rq-note {{ $assetRequest->status === 'rejected' ? 'is-crit' : '' }}">{{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}</div>
                    @endif

                    @if($assetRequest->status === 'approved')
                    <div class="rq-assign">
                        <p>Ready to assign the approved assets to the requester?</p>
                        <a href="{{ route('assets.index', ['tab' => 'assign', 'employee_id' => $assetRequest->employee_id, 'from_request' => $assetRequest->id]) }}"
                           class="btn-primary btn-sm">
                            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-box"/></svg>
                            Assign Assets Now
                        </a>
                    </div>
                    @endif
                </div>
            </section>
            @endif

        </div>

        {{-- Sidebar --}}
        <aside class="rq-col">
            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Requester</h2>
                </div>
                <div class="ui-card-body">
                    <div class="rq-person">
                        <span class="rq-avatar" aria-hidden="true">{{ strtoupper(substr($assetRequest->employee->full_name, 0, 1)) }}</span>
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

            <section class="ui-card">
                <div class="ui-card-header">
                    <h2>Request Status</h2>
                </div>
                <div class="ui-card-body">
                    <ol class="rq-timeline">
                        <li class="is-done">
                            <span class="rq-tl-dot"><svg class="mv-i" aria-hidden="true"><use href="#i-check"/></svg></span>
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
                                <div class="rq-tl-meta">{{ $assetRequest->approved_at->format('M d, Y \a\t g:i A') }}@if($assetRequest->approver) &middot; {{ $assetRequest->approver->full_name }}@endif</div>
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
                                <div class="rq-tl-meta">{{ $assetRequest->fulfilled_at->format('M d, Y \a\t g:i A') }}@if($assetRequest->fulfiller) &middot; {{ $assetRequest->fulfiller->full_name }}@endif</div>
                                @endif
                            </div>
                        </li>
                    </ol>
                </div>
            </section>
        </aside>

    </div>

    {{-- Approve Modal --}}
    <div id="approveModal" class="hidden rq-modal" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
        <div class="rq-modal-card">
            <div class="rq-modal-head">
                <span class="rq-modal-ic is-good"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-check-circle"/></svg></span>
                <h3 id="approveModalTitle">Approve Request</h3>
                <button type="button" onclick="closeModals()" class="rq-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="rq-modal-body">
                <form method="POST" action="{{ route('asset-approvals.approve', $assetRequest) }}">
                    @csrf
                    <label class="ui-label" for="approveNotes">Approval Notes <span class="rq-opt">(optional)</span></label>
                    <textarea id="approveNotes" name="approval_notes" rows="3" class="ui-textarea" placeholder="Any notes for the requester..."></textarea>
                    <div class="rq-modal-foot">
                        <button type="button" onclick="closeModals()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden rq-modal" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
        <div class="rq-modal-card">
            <div class="rq-modal-head">
                <span class="rq-modal-ic is-crit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x-circle"/></svg></span>
                <h3 id="rejectModalTitle">Reject Request</h3>
                <button type="button" onclick="closeModals()" class="rq-modal-close" aria-label="Close"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg></button>
            </div>
            <div class="rq-modal-body">
                <form method="POST" action="{{ route('asset-approvals.reject', $assetRequest) }}">
                    @csrf
                    <label class="ui-label" for="rejectReason">Rejection Reason <span class="rq-req">*</span></label>
                    <textarea id="rejectReason" name="rejection_reason" rows="4" class="ui-textarea" required placeholder="Explain why this request is being rejected..."></textarea>
                    <p class="rq-hint">This reason will be visible to the employee.</p>
                    <div class="rq-modal-foot">
                        <button type="button" onclick="closeModals()" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function openApprove() { document.getElementById('approveModal').classList.remove('hidden'); }
function openReject()   { document.getElementById('rejectModal').classList.remove('hidden'); }
function closeModals()  {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.add('hidden');
}
document.addEventListener('click', e => { if (e.target.id === 'approveModal' || e.target.id === 'rejectModal') closeModals(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });
</script>

@endsection
