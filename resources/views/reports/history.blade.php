{{-- resources/views/reports/history.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 m-0">Report Audit Trail</h1>
            <p class="text-gray-500 text-sm mt-1 mb-0">Full history of every report run and export, who triggered it, and from where.</p>
        </div>
        <a href="{{ route('reports.builder') }}"
           class="inline-flex items-center gap-2 bg-[#1a3a5c] text-white px-5 py-2.5 rounded-lg no-underline font-medium text-sm hover:bg-[#152e4a] transition-colors">
            ← Report Builder
        </a>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Runs</div>
            <div class="text-2xl font-bold text-gray-900">{{ $runs->total() }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Previews</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $runs->getCollection()->where('action', 'preview')->count() }}</div>
            <div class="text-xs text-gray-400">this page</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Exports</div>
            <div class="text-2xl font-bold text-emerald-600">{{ $runs->getCollection()->where('action', 'export')->count() }}</div>
            <div class="text-xs text-gray-400">this page</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Unique Users</div>
            <div class="text-2xl font-bold text-gray-900">{{ $runs->getCollection()->pluck('user_id')->unique()->count() }}</div>
            <div class="text-xs text-gray-400">this page</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">#</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">User</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Action</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Data Source</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Columns</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Rows</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">IP Address</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">When</th>
                </tr>
            </thead>
            <tbody>
                @forelse($runs as $run)
                @php
                    $payload  = is_array($run->payload) ? $run->payload : [];
                    $table    = $payload['base']['table'] ?? '—';
                    $colCount = isset($payload['select']) ? count($payload['select']) : '—';
                    $isExport = $run->action === 'export';
                @endphp
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    {{-- ID --}}
                    <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $run->id }}</td>

                    {{-- User --}}
                    <td class="px-5 py-3.5">
                        @if($run->user)
                            <div class="font-medium text-gray-900">{{ $run->user->first_name }} {{ $run->user->last_name }}</div>
                            <div class="text-xs text-gray-400">{{ $run->user->email ?? '' }}</div>
                        @else
                            <span class="text-gray-400 italic">Unknown</span>
                        @endif
                    </td>

                    {{-- Action badge --}}
                    <td class="px-5 py-3.5">
                        @if($isExport)
                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                ↓ Export
                                @if($run->format)
                                    <span class="uppercase">· {{ $run->format }}</span>
                                @endif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                ▶ Preview
                            </span>
                        @endif
                    </td>

                    {{-- Data source --}}
                    <td class="px-5 py-3.5">
                        <code class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ ucwords(str_replace('_', ' ', $table)) }}</code>
                    </td>

                    {{-- Columns --}}
                    <td class="px-5 py-3.5 text-gray-600">{{ $colCount }}</td>

                    {{-- Rows --}}
                    <td class="px-5 py-3.5">
                        <span class="font-medium text-gray-900">{{ number_format($run->result_count) }}</span>
                    </td>

                    {{-- IP --}}
                    <td class="px-5 py-3.5">
                        <span class="text-gray-500 text-xs font-mono">{{ $run->ip_address ?? '—' }}</span>
                    </td>

                    {{-- When --}}
                    <td class="px-5 py-3.5">
                        @if($run->executed_at)
                            <div class="text-gray-700">{{ $run->executed_at->format('d M Y, H:i') }}</div>
                            <div class="text-xs text-gray-400">{{ $run->executed_at->diffForHumans() }}</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">📋</div>
                        <div class="font-medium">No report runs recorded yet.</div>
                        <div class="text-xs mt-1">Every time someone runs or exports a report it will appear here.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($runs->hasPages())
        <div class="mt-4">
            {{ $runs->links() }}
        </div>
    @endif
</div>
@endsection
