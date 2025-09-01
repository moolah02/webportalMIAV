{{-- File: resources/views/asset-approvals/show.blade.php --}}
@extends('layouts.app')

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
      <h2 class="title">Review Request {{ $assetRequest->request_number }}</h2>
      <p class="subtitle">Submitted by {{ $assetRequest->employee->full_name }}</p>
    </div>
    <a href="{{ route('asset-approvals.index') }}" class="btn">← Back to Approvals</a>
  </div>

  <div class="grid">
    <!-- Main -->
    <div class="main">
      <!-- Overview -->
      <div class="card">
        <div class="row between center mb-16">
          <h4 class="h4">📊 Request Overview</h4>
          <div class="row gap-8">
            <span class="status-badge status-{{ $assetRequest->status }}">{{ ucfirst($assetRequest->status) }}</span>
            <span class="priority-badge priority-{{ $assetRequest->priority }}">{{ ucfirst($assetRequest->priority) }} Priority</span>
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
      <div class="card">
        <div class="row between center mb-12">
          <h4 class="h4">📦 Request Items</h4>

          @if($assetRequest->status === 'pending')
          <div class="row gap-8">
            <button class="btn btn-primary" onclick="openApprove()">✅ Approve</button>
            <button class="btn btn-danger" onclick="openReject()">❌ Reject</button>
          </div>
          @endif
        </div>

        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Asset</th>
                <th class="t-center">Requested</th>
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
                <td class="t-right">${{ number_format($item->unit_price_at_request, 2) }}</td>
                <td class="t-right">${{ number_format($item->total_price, 2) }}</td>
                <td class="t-center">
                  <span class="status-badge status-{{ $item->item_status }}">{{ ucfirst(str_replace('_',' ',$item->item_status)) }}</span>
                </td>
              </tr>
              @endforeach
              <tr class="tfoot">
                <td colspan="3" class="t-right strong">Grand Total:</td>
                <td class="t-right strong primary">${{ number_format($assetRequest->total_estimated_cost, 2) }}</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      @if($assetRequest->status !== 'pending')
      <!-- Decision summary -->
      <div class="card">
        <h4 class="h4 mb-12">{{ in_array($assetRequest->status,['approved','fulfilled']) ? '✅ Approval Details' : '❌ Rejection Details' }}</h4>
        <div class="mb-8 strong">{{ $assetRequest->approver->full_name ?? 'System' }}</div>
        <div class="small muted">{{ $assetRequest->approved_at ? $assetRequest->approved_at->format('M d, Y \a\t g:i A') : 'Unknown' }}</div>
        @if($assetRequest->approval_notes || $assetRequest->rejection_reason)
          <div class="pill mt-12 {{ $assetRequest->status === 'rejected' ? 'pill-danger' : 'pill-success' }}">
            {{ $assetRequest->approval_notes ?? $assetRequest->rejection_reason }}
          </div>
        @endif
      </div>
      @endif
    </div>

    <!-- Sidebar -->
    <aside class="side">
      <div class="card">
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
        <button type="button" class="btn" onclick="closeModals()">Cancel</button>
        <button type="submit" class="btn btn-primary">Approve</button>
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
        <button type="button" class="btn" onclick="closeModals()">Cancel</button>
        <button type="submit" class="btn btn-danger">Reject</button>
      </div>
    </form>
  </div>
</div>

<style>
/* (unchanged styles trimmed to save space) */
/* layout */
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.title{margin:0;color:#333;font-size:28px;font-weight:700}
.subtitle{color:#666;margin-top:6px}
.grid{display:grid;grid-template-columns:2fr 1fr;gap:24px}
.main,.side{min-width:0}
.card{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.06);padding:20px}
.row{display:flex;align-items:center}
.between{justify-content:space-between}.center{align-items:center}.end{justify-content:flex-end}
.gap-8{gap:8px}.mb-12{margin-bottom:12px}.mb-16{margin-bottom:16px}.mt-12{margin-top:12px}.mt-16{margin-top:16px}.mt-6{margin-top:6px}.ml-8{margin-left:8px}
.h4{margin:0;color:#333}
.grid4{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px}
.muted{font-size:12px;color:#666;text-transform:uppercase;letter-spacing:.02em}
.small{font-size:13px;color:#666}
.strong{font-weight:600}.primary{color:#2196f3}.danger{color:#d3aaa7}
.note{background:#f8f9fa;padding:12px;border-radius:6px;border-left:4px solid #a3c5e1}
.pill{padding:10px;border-radius:8px}.pill-success{background:#e8f5e8;color:#a3d9a6}.pill-danger{background:#ffebee;color:#d32f2f}

/* table */
.table-wrap{overflow-x:auto}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:12px;border:1px solid #e6e6e6;text-align:left}
.table th{background:#f8f9fa}
.t-center{text-align:center}.t-right{text-align:right}
.tfoot td{background:#f8f9fa}

/* badges & buttons */
.status-badge{padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600}
.status-pending{background:#fff3e0;color:#f57c00}
.status-approved{background:#e8f5e8;color:#2e7d32}
.status-rejected{background:#ffebee;color:#d32f2f}
.status-fulfilled{background:#e3f2fd;color:#1976d2}
.priority-badge{padding:4px 8px;border-radius:8px;font-size:11px;font-weight:600}
.priority-low{background:#e8f5e8;color:#2e7d32}
.priority-normal{background:#e3f2fd;color:#1976d2}
.priority-high{background:#fff3e0;color:#f57c00}
.priority-urgent{background:#ffebee;color:#d32f2f}

.btn{padding:8px 14px;border:2px solid #ddd;border-radius:6px;background:#fff;color:#333;cursor:pointer;font-weight:600;transition:.2s}
.btn:hover{border-color:#2196f3;color:#2196f3}
.btn-primary{background:#2196f3;border-color:#2196f3;color:#fff}
.btn-primary:hover{background:#1976d2;border-color:#1976d2;color:#fff}
.btn-danger{background:#f44336;border-color:#f44336;color:#fff}

/* avatar */
.avatar{width:64px;height:64px;margin:0 auto 8px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-size:26px}

/* modals */
.modal{display:none;position:fixed;z-index:1000;inset:0;background:rgba(0,0,0,.5)}
.modal-content{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.25);padding:20px;width:90%;max-width:520px}
.textarea{width:100%;padding:10px;border:2px solid #ddd;border-radius:6px}
.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:12px}

@media (max-width: 860px){
  .grid{grid-template-columns:1fr}
}
</style>

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
