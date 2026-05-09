@extends('layouts.app')
@section('title', 'Bulk Import Assets')

@section('header-actions')
<a href="{{ route('assets.index') }}" class="btn-secondary btn-sm">← Back to Assets</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    @if(session('success'))
    <div class="flash-success"><span>&#x2705;</span> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash-error"><span>&#x274C;</span> {{ session('error') }}</div>
    @endif

    {{-- Instructions --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4C2; Bulk Import Assets via Excel</h3>
        </div>
        <div class="ui-card-body space-y-3 text-sm text-gray-600">
            <p>Upload an <strong>.xlsx</strong>, <strong>.xls</strong>, or <strong>.csv</strong> file. The first row must be the header row with these column names:</p>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 rounded">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b">Column</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b">Required</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 border-b">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="px-3 py-1.5 font-mono">name</td><td class="px-3 py-1.5 text-red-500">Yes</td><td class="px-3 py-1.5">Asset name</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">description</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Short description</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">category</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">e.g. IT Equipment, Furniture</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">brand</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Manufacturer / brand</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">model</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Model name or number</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">sku</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Internal SKU code</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">barcode</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Barcode / serial</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">unit_price</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Numeric, e.g. 1200.00</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">currency</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Default: USD</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">stock_quantity</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Integer, default 0</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">min_stock_level</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Low-stock threshold</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">status</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Default: active</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">is_requestable</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">1 = yes, 0 = no</td></tr>
                        <tr class="bg-gray-50"><td class="px-3 py-1.5 font-mono">requires_approval</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">1 = yes, 0 = no</td></tr>
                        <tr><td class="px-3 py-1.5 font-mono">notes</td><td class="px-3 py-1.5 text-gray-400">No</td><td class="px-3 py-1.5">Any extra notes</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="pt-1">
                <a href="{{ route('assets.import-template') }}" class="btn-secondary btn-sm">&#x2B07;&#xFE0F; Download Template (.xlsx)</a>
            </div>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="ui-card">
        <div class="ui-card-header">
            <h3 class="text-sm font-semibold text-gray-800 m-0">&#x1F4E4; Upload File</h3>
        </div>
        <div class="ui-card-body">
            <form method="POST" action="{{ route('assets.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="ui-label">File <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#1a3a5c] file:text-white hover:file:bg-[#15304d] cursor-pointer border border-gray-200 rounded-lg p-1">
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Accepted: .xlsx, .xls, .csv &mdash; max 5 MB</p>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="btn-primary">&#x1F4E5; Import Assets</button>
                    <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
