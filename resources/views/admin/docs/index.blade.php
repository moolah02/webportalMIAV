{{-- resources/views/admin/docs/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;padding-bottom:16px;border-bottom:1px solid #e5e7eb;">
        <div>
            <h1 style="margin:0;color:#111827;font-size:28px;font-weight:600;letter-spacing:-0.025em;">Documentation Manager</h1>
            <p style="color:#6b7280;margin:4px 0 0 0;font-size:15px;">Edit the content of each public documentation page from here.</p>
        </div>
        <a href="{{ url('/docs') }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;background:#1a3a5c;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;font-size:14px;">
            🔗 View Live Docs
        </a>
    </div>

    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:14px 18px;border-radius:8px;margin-bottom:24px;font-size:14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Pages table --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:14px 20px;text-align:left;font-weight:600;color:#374151;">Page</th>
                    <th style="padding:14px 20px;text-align:left;font-weight:600;color:#374151;">Slug</th>
                    <th style="padding:14px 20px;text-align:left;font-weight:600;color:#374151;">Last Edited</th>
                    <th style="padding:14px 20px;text-align:left;font-weight:600;color:#374151;">Edited By</th>
                    <th style="padding:14px 20px;text-align:right;font-weight:600;color:#374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:16px 20px;">
                        <div style="font-weight:600;color:#111827;">{{ $page->title }}</div>
                        @if($page->subtitle)
                            <div style="color:#6b7280;font-size:12px;margin-top:2px;">{{ Str::limit($page->subtitle, 80) }}</div>
                        @endif
                    </td>
                    <td style="padding:16px 20px;">
                        <code style="background:#f3f4f6;padding:3px 8px;border-radius:4px;font-size:12px;color:#374151;">{{ $page->slug }}</code>
                    </td>
                    <td style="padding:16px 20px;color:#6b7280;">
                        {{ $page->updated_at ? $page->updated_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td style="padding:16px 20px;color:#6b7280;">
                        {{ $page->editor?->name ?? '—' }}
                    </td>
                    <td style="padding:16px 20px;text-align:right;">
                        <div style="display:inline-flex;gap:8px;">
                            <a href="{{ url('/docs/' . $page->slug) }}" target="_blank"
                               style="background:#f3f4f6;color:#374151;padding:7px 14px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:500;">
                                👁 View
                            </a>
                            <a href="{{ route('admin.docs.edit', $page->slug) }}"
                               style="background:#1a3a5c;color:#fff;padding:7px 14px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:500;">
                                ✏️ Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px;text-align:center;color:#9ca3af;">
                        No documentation pages found. Run <code>php artisan db:seed --class=DocPageSeeder</code> to populate them.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
