{{-- File: resources/views/asset-approvals/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Approval Details')

@section('content')
<div class="container">

  {{-- Flash messages (so you can see errors from reject, etc.) --}}
  @if(session('success'))
    <div class="pill pill-success" style="margin-bottom:12px">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="pill pill-danger" style="margin-bottom:12px">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="pill pill-danger" style="margin-bottom:12px">
      {{ $errors->first() }}
    </div>
  @endif

  <!-- Header -->
  <div class="header">
    <div>
      <p class="subtitle">Submitted by {{ $assetRequest->employee->full_name }}</p>
    </div>
    <a href="{{ route('asset-approvals.index') }}" class="btn-secondary">← Back to Approvals</a>
  </div>

  <div class="grid">
    <!-- Main -->
    <div class="main">
      <!-- Overview -->
      <div class="ui-card">
        <div class="row between center mb-16">
          <h4 class="h4">📊 Request Overview</h4>
          <div class="row gap-8">
            <span class="badge badge-gray">{{ ucfirst($assetRequest->status) }}</span>
            <span class="badge badge-gray">{{ ucfirst($assetRequest->priority) }} Priority</span>
          </div>
        </div>

        <div class="grid4 mb-16">
          <div>
            <label class="muted">Submitted</label>
            <div class="strong">{{ $assetRequest->created_at->format('M d, Y \a\t g:i A') }}</div>
          </div>
          <div>
            <label class="muted">Needed By</label>
            <div class="strong {{ $assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast() ? 'danger' : '' }}">
              {{ $assetRequest->needed_by_date ? $assetRequest->needed_by_date->format('M d, Y') : 'ASAP' }}
              @if($assetRequest->needed_by_date && $assetRequest->needed_by_date->isPast())
                <span class="muted">• Overdue</span>
              @endif
            </div>
          </div>
          <div>
            <label class="muted">Total Items</label>
            <div class="strong">{{ $assetRequest->items->sum('quantity_requested') }}</div>
          </div>
          <div>
            <label class="muted">Estimated Cost</label>
            <div class="strong primary">${{ number_format($assetRequest->total_estimated_cost, 2) }}</div>
          </div>
        </div>

        <div>
          <label class="muted">Business Justification</label>
          <div class="note">{{ $assetRequest->business_justification }}</div>
        </div>

        @if($assetRequest->delivery_instructions)
        <div class="mt-12">
          <label class="muted">Delivery Instructions</label>
          <div class="small">{{ $assetRequest->delivery_instructions }}</div>
        </div>
        @endif
      </div>

      <!-- Items -->
      <div class="ui-card">
        <div class="row between center mb-12">
          <h4 class="h4">📦 Request Items</h4>

          @if($assetRequest->status === 'pending')
          <div class="row gap-8">
            <button class="btn-primary" onclick="openApprove()">✅ Approve</button>
            <button class="btn-danger" onclick="openReject()">❌ Reject</button>
          </div>
          @endif
        </div>

        <div class="table-wrap">
          <table class="ui-table">
            <thead>
              <tr>
                <th>Asset</th>
                <th class="t-center">Requested</th>
                <th class="t-center">In Stock</th>
                <th class="t-right">Unit Price</th>
                <th class="t-right">Subtotal</th>
                <th class="t-center">Item Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assetRequest->items as $item)
              <tr>
                <td>
                  <div class="strong">{{ $item->asset->name }}</div>
                  <div class="small muted">{{ $item->asset->brand }} {{ $item->asset->model }}</div>
                </td>
                <td class="t-center">{{ $item->quantity_requested }}</td>
                <td class="t-center">
                  @php $stock = $item->asset->stock_quantity ?? 0; @endphp
                  <span style="font-weight:600; color:{{ $stock >= $item->quantity_requested ? '#2e7d32' : ($stock > 0 ? '#e65100' : '#c62828') }}">
                    {{ $stock }}
                    @if($stock < $item->quantity_requested)
                      <span style="font-size:11px; display:block; font-weight:400;">⚠️ Low stock</span>
                    @endif
                  </span>
                </td>
                <td class="t-right">${{ number_format($item->unit_price_at_request, 2) }}</td>
                <td class="t-right">${{ number_format($item->total_price, 2) }}</td>
                <td class="t-center">
                  <span class="badge badge-gray">{{ ucfirst(str_replace('_',' ',$item->item_status)) }}</span>
                </td>
              </tr>
              @endforeach
              <tr class="tfoot">
                <td colspan="4" class="t-right strong">Grand Total:</td>
                <td class="t-right strong primary">${{ number_format($assetRequest->total_estimated_cost, 2) }}</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      @if($assetRequest->status !== 'pending')
      <!-- Decision summary -->
      <div class="ui-card">
        <h4 class="h4 mb-12">{{ in_array($assetRequest->status,['approved','fulfilled']) ? '✅ Approval Details' : '❌ Rejection Details' }}</h4>
        <div class="mb-8 strong">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
        <div class="small muted">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
        @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
          <div class="pill mt-12 {{ $assetRequest->status === 'rejected' ? 'pill-danger' : 'pill-success' }}">
            {{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}
          </div>
        @endif

        @if($assetRequest->status === 'approved')
          <div style="margin-top:16px; padding-top:16px; border-top:1px solid #e5e7eb;">
            <p style="font-size:13px; color:#6b7280; margin-bottom:8px;">Ready to assign the approved assets to the requester?</p>
            <a href="{{ route('assets.index', ['tab' => 'assign', 'employee_id' => $assetRequest->employee_id, 'from_request' => $assetRequest->id]) }}"
               class="btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
              📦 Assign Assets Now
            </a>
          </div>
        @endif
      </div>
      @endif
    </div>

    <!-- Sidebar -->
    <aside class="side">
      <div class="ui-card">
        <h4 class="h4 mb-12">👤 Requester</h4>
        <div class="avatar">{{ substr($assetRequest->employee->full_name, 0, 1) }}</div>
        <div class="t-center strong">{{ $assetRequest->employee->full_name }}</div>
        <div class="t-center small muted">{{ $assetRequest->employee->role->name ?? 'Employee' }}</div>
        <div class="t-center small muted">{{ $assetRequest->employee->department->name ?? 'No Department' }}</div>
        @if($assetRequest->employee->email)
        <div class="t-center small mt-6">📧
          <a href="mailto:{{ $assetRequest->employee->email }}" class="link">{{ $assetRequest->employee->email }}</a>
        </div>
        @endif
      </div>
    </aside>
  </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" class="modal">
  <div class="modal-content">
    <h3>✅ Approve Request</h3>
    <form method="POST" action="{{ route('asset-approvals.approve', $assetRequest) }}">
      @csrf
      {{-- add @method('PATCH') too if your route uses PATCH --}}
      <label class="muted">Approval Notes (optional)</label>
      <textarea name="approval_notes" rows="3" class="textarea" placeholder="Any notes for the requester..."></textarea>
      <div class="modal-actions">
        <button type="button" class="btn-secondary" onclick="closeModals()">Cancel</button>
        <button type="submit" class="btn-primary">Approve</button>
      </div>
    </form>
  </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="modal">
  <div class="modal-content">
    <h3>❌ Reject Request</h3>
    <form method="POST" action="{{ route('asset-approvals.reject', $assetRequest) }}">
  @csrf
      <label class="muted">Rejection Reason *</label>
      <textarea name="rejection_reason" rows="4" class="textarea" required placeholder="Explain why this request is being rejected..."></textarea>
      <div class="modal-actions">
        <button type="button" class="btn-secondary" onclick="closeModals()">Cancel</button>
        <button type="submit" class="btn-danger">Reject</button>
      </div>
    </form>
  </div>
</div>


<script>
function openApprove(){ document.getElementById('approveModal').style.display='block'; }
function openReject(){ document.getElementById('rejectModal').style.display='block'; }
function closeModals(){
  document.getElementById('approveModal').style.display='none';
  document.getElementById('rejectModal').style.display='none';
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal')) closeModals();
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModals();
});
</script>
@endsection
