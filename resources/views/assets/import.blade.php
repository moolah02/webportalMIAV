@extends('layouts.app')
@section('title', 'Bulk Import Assets')

@section('header-actions')
<a href="{{ route('assets.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Assets</a>
@endsection

@push('styles')
<style>
.mv-header-actions a { text-decoration: none !important; }
.as-import a[class*="btn-"] { text-decoration: none !important; }
.as-import { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 16px; align-items: start; max-width: 1200px; }
@media (max-width: 1100px) { .as-import { grid-template-columns: minmax(0, 1fr); } }
.as-import .ui-card-body { padding: 16px 18px 18px; }
.as-import .as-lead { margin: 0 0 12px; font-size: 13.5px; line-height: 1.55; color: var(--mv-ink-2); }
.as-import .as-lead strong { font-weight: 600; color: var(--mv-ink); }
.as-import .as-cols { border: 1px solid var(--mv-line); border-radius: 8px; overflow-x: auto; }
.as-import .as-cols .ui-table thead th { padding: 8px 12px; }
.as-import .as-cols .ui-table tbody td { padding: 7px 12px; font-size: 13px; }
.as-import .as-cols .ui-table tbody tr:first-child td { border-top: 0; }
.as-import .as-cols .mv-mono { font-size: 12.5px; color: var(--mv-ink); }
.as-import .as-no { color: var(--mv-muted); }
.as-import .as-tpl { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-top: 14px; font-size: 12.5px; color: var(--mv-muted); }
.as-import .ui-label { margin-bottom: 5px; }
.as-import .as-req { color: var(--mv-crit); }
.as-import .as-file {
  display: block; width: 100%; padding: 10px; font-size: 13px; color: var(--mv-ink-2); cursor: pointer;
  background: var(--mv-surface-2); border: 1px dashed var(--mv-line-strong); border-radius: 8px;
}
.as-import .as-file:hover { border-color: var(--mv-accent); }
.as-import .as-file::file-selector-button {
  margin-right: 12px; padding: 6px 12px; cursor: pointer; font: inherit; font-weight: 500;
  color: var(--mv-ink); background: var(--mv-surface); border: 1px solid var(--mv-line-strong); border-radius: 7px;
}
.as-import .as-hint { margin: 6px 0 0; font-size: 12px; color: var(--mv-muted); }
.as-import .as-err { margin: 4px 0 0; font-size: 12px; color: var(--mv-crit); }
.as-import .as-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--mv-line); }
</style>
@endpush

@section('content')
<div class="as-import">

    {{-- Instructions --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3>Bulk Import Assets via Excel</h3>
        </div>
        <div class="ui-card-body">
            <p class="as-lead">Upload an <strong>.xlsx</strong>, <strong>.xls</strong>, or <strong>.csv</strong> file. The first row must be the header row with these column names:</p>
            <div class="as-cols">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Required</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="mv-mono">name</td><td><span class="badge badge-blue">Yes</span></td><td>Asset name</td></tr>
                        <tr><td class="mv-mono">description</td><td class="as-no">No</td><td>Short description</td></tr>
                        <tr><td class="mv-mono">category</td><td class="as-no">No</td><td>e.g. IT Equipment, Furniture</td></tr>
                        <tr><td class="mv-mono">brand</td><td class="as-no">No</td><td>Manufacturer / brand</td></tr>
                        <tr><td class="mv-mono">model</td><td class="as-no">No</td><td>Model name or number</td></tr>
                        <tr><td class="mv-mono">sku</td><td class="as-no">No</td><td>Internal SKU code</td></tr>
                        <tr><td class="mv-mono">barcode</td><td class="as-no">No</td><td>Barcode / serial</td></tr>
                        <tr><td class="mv-mono">unit_price</td><td class="as-no">No</td><td>Numeric, e.g. 1200.00</td></tr>
                        <tr><td class="mv-mono">currency</td><td class="as-no">No</td><td>Default: USD</td></tr>
                        <tr><td class="mv-mono">stock_quantity</td><td><span class="badge badge-blue">Yes</span></td><td>Integer, e.g. 10</td></tr>
                        <tr><td class="mv-mono">min_stock_level</td><td><span class="badge badge-blue">Yes</span></td><td>Low-stock alert threshold</td></tr>
                        <tr><td class="mv-mono">status</td><td class="as-no">No</td><td>Default: active</td></tr>
                        <tr><td class="mv-mono">is_requestable</td><td class="as-no">No</td><td>1 = yes, 0 = no</td></tr>
                        <tr><td class="mv-mono">requires_approval</td><td class="as-no">No</td><td>1 = yes, 0 = no</td></tr>
                        <tr><td class="mv-mono">notes</td><td class="as-no">No</td><td>Any extra notes</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="as-tpl">
                <span>Start from the template to get the headers right.</span>
                <a href="{{ route('assets.import-template') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-download"/></svg> Download Template (.xlsx)</a>
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3>Upload File</h3>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('assets.import') }}" enctype="multipart/form-data">
                @csrf
                <div>
                    <label class="ui-label" for="import_file">File <span class="as-req">*</span></label>
                    <input type="file" name="file" id="import_file" accept=".xlsx,.xls,.csv" required class="as-file">
                    @error('file')<p class="as-err">{{ $message }}</p>@enderror
                    <p class="as-hint">Accepted: .xlsx, .xls, .csv &mdash; max 5 MB</p>
                </div>
                <div class="as-actions">
                    <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-upload"/></svg> Import Assets</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
