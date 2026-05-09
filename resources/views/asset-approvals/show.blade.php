@extends('layouts.app')
@section('title', 'Approval Details')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="flash-success mb-5">&#x2705; {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error mb-5">&#x274C; {{ session('error') }}</div>
@endif
@if($errors->any())
<div class="flash-error mb-5">&#x274C; {{ $errors->first() }}</div>
@endif

{{-- Page Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-sm text-gray-500 mt-1">Submitted by <span class="font-semibold text-gray-700">{{ $assetRequest->employee->full_name }}</span></p>
    </div>
    <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">&#x2190; Back to Approvals</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6">

    {{-- Main Column --}}
    <div class="space-y-5">

        {{-- Overview Card --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4CA; Request Overview</h3>
                <div class="flex gap-2">
                    @php
                        $statusClass = match($assetRequest->status) {
                            'approved'  => 'badge-green',
                            'fulfilled' => 'badge-blue',
                            'rejected'  => 'badge-red',
                            'pending'   => 'badge-yellow',
                            default     => 'badge-gray',
                        };
                        $priorityClass = match($assetRequest->priority) {
                            'urgent' => 'badge-red',
                            'high'   => 'badge-orange',
                            'normal' => 'badge-blue',
                            'low'    => 'badge-gray',
                            default  => 'badge-gray',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($assetRequest->status) }}</span>
                    <span class="badge {{ $priorityClass }}">{{ ucfirst($assetRequest->priority) }} Priority</span>
                </div>
            </div>
            <div class="ui-card-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Submitted</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $assetRequest->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $assetRequest->created_at->format('g:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Needed By</div>
                        <div class="text-sm font-semibold {{ $assetRequest->needed_by_date?->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'ASAP' }}
                        </div>
                        @if($assetRequest->needed_by_date?->isPast())
                        <div class="text-xs text-red-500">Overdue</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Items</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $assetRequest->items->sum('quantity_requested') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Estimated Cost</div>
                        <div class="text-sm font-semibold text-[#1a3a5c]">${{ number_format($assetRequest->total_estimated_cost, 2) }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Business Justification</div>
                    <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 border border-gray-100">{{ $assetRequest->business_justification }}</div>
                </div>

                @if($assetRequest->delivery_instructions)
                <div class="mt-3">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Delivery Instructions</div>
                    <div class="text-sm text-gray-600">{{ $assetRequest->delivery_instructions }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Request Items Card --}}
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4E6; Request Items</h3>
                @if($assetRequest->status === 'pending')
                <div class="flex gap-2">
                    <button class="btn-primary btn-sm" onclick="openApprove()">&#x2705; Approve</button>
                    <button class="btn-danger btn-sm" onclick="openReject()">&#x274C; Reject</button>
                </div>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="shared-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th class="text-center">Requested</th>
                            <th class="text-center">In Stock</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-center">Item Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assetRequest->items as $item)
                        @php $stock = $item->asset->stock_quantity ?? 0; @endphp
                        <tr>
                            <td>
                                <div class="font-semibold text-sm text-gray-800">{{ $item->asset->name }}</div>
                                @if($item->asset->brand || $item->asset->model)
                                <div class="text-xs text-gray-400">{{ trim($item->asset->brand . ' ' . $item->asset->model) }}</div>
                                @endif
                            </td>
                            <td class="text-center text-sm font-semibold text-gray-800">{{ $item->quantity_requested }}</td>
                            <td class="text-center">
                                <span class="font-semibold text-sm {{ $stock >= $item->quantity_requested ? 'text-green-700' : ($stock > 0 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $stock }}
                                </span>
                                @if($stock < $item->quantity_requested)
                                <div class="text-xs text-amber-600">&#x26A0; Low stock</div>
                                @endif
                            </td>
                            <td class="text-right text-sm text-gray-700">${{ number_format($item->unit_price_at_request, 2) }}</td>
                            <td class="text-right text-sm font-semibold text-gray-800">${{ number_format($item->total_price, 2) }}</td>
                            <td class="text-center">
                                <span class="badge badge-gray">{{ ucfirst(str_replace('_', ' ', $item->item_status)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 border-t-2 border-gray-200">
                            <td colspan="4" class="text-right text-sm font-semibold text-gray-700 py-3">Grand Total:</td>
                            <td class="text-right text-sm font-bold text-[#1a3a5c] py-3">${{ number_format($assetRequest->total_estimated_cost, 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Decision Summary (non-pending) --}}
        @if($assetRequest->status !== 'pending')
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">
                    {{ in_array($assetRequest->status, ['approved','fulfilled']) ? '&#x2705; Approval Details' : '&#x274C; Rejection Details' }}
                </h3>
            </div>
            <div class="ui-card-body">
                <div class="text-sm font-semibold text-gray-800">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>

                @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
                <div class="mt-3 p-3 rounded-lg text-sm {{ $assetRequest->status === 'rejected' ? 'bg-red-50 border border-red-100 text-red-700' : 'bg-green-50 border border-green-100 text-green-700' }}">
                    {{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}
                </div>
                @endif

                @if($assetRequest->status === 'approved')
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-3">Ready to assign the approved assets to the requester?</p>
                    <a href="{{ route('assets.index', ['tab' => 'assign', 'employee_id' => $assetRequest->employee_id, 'from_request' => $assetRequest->id]) }}"
                       class="btn-primary">
                        &#x1F4E6; Assign Assets Now
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <aside class="space-y-5">
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F464; Requester</h3>
            </div>
            <div class="ui-card-body text-center">
                <div class="w-14 h-14 rounded-full bg-[#1a3a5c] text-white flex items-center justify-center text-xl font-bold mx-auto mb-3">
                    {{ strtoupper(substr($assetRequest->employee->full_name, 0, 1)) }}
                </div>
                <div class="font-semibold text-gray-800 text-sm">{{ $assetRequest->employee->full_name }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
                <div class="text-xs text-gray-400">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
                @if($assetRequest->employee->email)
                <div class="mt-3 text-xs">
                    <a href="mailto:{{ $assetRequest->employee->email }}" class="text-[#1a3a5c] underline">{{ $assetRequest->employee->email }}</a>
                </div>
                @endif
            </div>
        </div>

        @if($assetRequest->status === 'pending')
        <div class="ui-card">
            <div class="ui-card-header">
                <h3 class="text-sm font-semibold text-gray-800 m-0">&#x26A1; Quick Actions</h3>
            </div>
            <div class="ui-card-body space-y-2">
                <button onclick="openApprove()" class="btn-primary w-full justify-center">&#x2705; Approve Request</button>
                <button onclick="openReject()" class="btn-danger w-full justify-center">&#x274C; Reject Request</button>
            </div>
        </div>
        @endif
    </aside>

</div>

{{-- Approve Modal --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-md">
        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x2705; Approve Request</h3>
            <button onclick="closeModals()" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('asset-approvals.approve', $assetRequest) }}">
                @csrf
                <div class="mb-4">
                    <label class="ui-label">Approval Notes <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                    <textarea name="approval_notes" rows="3" class="ui-textarea" placeholder="Any notes for the requester..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary flex-1">Approve</button>
                    <button type="button" onclick="closeModals()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="ui-card w-full max-w-md">
        <div class="ui-card-header" style="background:#1a3a5c;">
            <h3 class="text-sm font-semibold text-white m-0">&#x274C; Reject Request</h3>
            <button onclick="closeModals()" class="text-white/70 hover:text-white text-xl leading-none border-0 bg-transparent cursor-pointer">&times;</button>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('asset-approvals.reject', $assetRequest) }}">
                @csrf
                <div class="mb-4">
                    <label class="ui-label">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="4" class="ui-textarea" required placeholder="Explain why this request is being rejected..."></textarea>
                    <p class="text-xs text-gray-400 mt-1">This reason will be visible to the employee.</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-danger flex-1">Reject</button>
                    <button type="button" onclick="closeModals()" class="btn-secondary">Cancel</button>
                </div>
            </form>
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
