{{-- resources/views/admin/docs/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <div>
            <h1 class="m-0 text-gray-900 text-3xl font-semibold tracking-tight">Documentation Manager</h1>
            <p class="text-gray-500 mt-1 mb-0 text-[15px]">Edit the content of each public documentation page from here.</p>
        </div>
        <a href="{{ url('/docs') }}" target="_blank"
           class="inline-flex items-center gap-1.5 bg-[#1a3a5c] text-white px-5 py-2.5 rounded-lg no-underline font-medium text-sm hover:bg-[#152e4a] transition-colors">
            🔗 View Live Docs
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded-lg mb-6 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Pages table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Page</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Slug</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Last Edited</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-gray-700">Edited By</th>
                    <th class="px-5 py-3.5 text-right font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-900">{{ $page->title }}</div>
                        @if($page->subtitle)
                            <div class="text-gray-500 text-xs mt-0.5">{{ Str::limit($page->subtitle, 80) }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <code class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $page->slug }}</code>
                    </td>
                    <td class="px-5 py-4 text-gray-500">
                        {{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="px-5 py-4 text-gray-500">
                        {{ $page->editor?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex gap-2">
                            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank"
                               class="bg-gray-100 text-gray-700 px-3.5 py-1.5 rounded-md no-underline text-xs font-medium hover:bg-gray-200 transition-colors">
                                👁 View
                            </a>
                            <a href="{{ route('admin.docs.edit', $page->slug) }}"
                               class="bg-[#1a3a5c] text-white px-3.5 py-1.5 rounded-md no-underline text-xs font-medium hover:bg-[#152e4a] transition-colors">
                                ✏️ Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                        No documentation pages found. Run <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">php artisan db:seed --class=DocPageSeeder</code> to populate them.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
