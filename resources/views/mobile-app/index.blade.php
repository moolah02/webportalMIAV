@extends('layouts.app')

@section('title', 'Mobile App')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div>
        <p class="text-sm text-gray-500">Android app for field technicians. Download the latest version to install or update it on the tablets.</p>
    </div>

    @if($release)
    <div class="ui-card">
        <div class="p-5 flex flex-wrap items-center gap-4">
            <div class="flex-1" style="min-width:220px">
                <div class="text-xs text-gray-400 uppercase tracking-wide">Latest version</div>
                <div class="text-2xl font-bold text-gray-800">
                    v{{ $release['version'] }}
                    <span class="text-sm font-medium text-gray-400">(build {{ $release['build'] }})</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Released {{ \Carbon\Carbon::parse($release['released_at'])->format('d M Y') }}
                    &middot; {{ number_format($release['size'] / 1048576, 1) }} MB
                </div>
            </div>
            <a href="{{ route('mobile-app.download') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-semibold text-white"
               style="background:#1a3a5c">
                ⬇ Download APK
            </a>
        </div>

        @if(!empty($release['notes']))
        <div class="border-t border-gray-100 p-5">
            <div class="text-xs text-gray-400 uppercase tracking-wide mb-2">What's new</div>
            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                @foreach($release['notes'] as $note)
                <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="ui-card">
        <div class="p-5 text-sm text-gray-600 space-y-2">
            <div class="font-semibold text-gray-800">How to install on a tablet</div>
            <ol class="list-decimal pl-5 space-y-1">
                <li>Open this page on the tablet (log in as usual) and tap <strong>Download APK</strong>, or copy the file to the tablet.</li>
                <li>Open the downloaded file. If Android asks, allow installing apps from this source.</li>
                <li>Tap <strong>Update</strong> / <strong>Install</strong>. The technician's data and login are kept.</li>
                <li>Check the version shown on the app's login screen or Profile page reads
                    <strong>{{ $release['version'] }} ({{ $release['build'] }})</strong>.</li>
            </ol>
            <div class="text-xs text-gray-400 pt-1">SHA-256: <span class="font-mono" style="word-break:break-all">{{ $release['sha256'] }}</span></div>
        </div>
    </div>
    @else
    <div class="ui-card">
        <div class="p-5 text-sm text-gray-500 italic">No app version has been published yet.</div>
    </div>
    @endif

</div>
@endsection
