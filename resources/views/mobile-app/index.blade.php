@extends('layouts.app')

@section('title', 'Mobile App')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div>
        <p class="text-sm text-gray-500">Android app for field technicians. Download a version below to install or update it on the tablets.</p>
    </div>

    @if($newDesign)
    <div class="ui-card" style="border-color:#C9D9EE">
        <div class="p-4 text-sm" style="background:#F4F8FD;color:#445162;border-radius:inherit">
            <strong style="color:#1F4F87">Two versions are available.</strong>
            The new design installs as a <strong>separate app</strong> called “MIAV New Design”, next to the current “MIAV” app,
            so a technician can try it without losing the current app or any visits still waiting to sync. Keep whichever you prefer.
        </div>
    </div>
    @endif

    @php
        $versions = [
            ['release' => $release,   'channel' => 'current',    'label' => 'Current design', 'app' => 'MIAV'],
            ['release' => $newDesign, 'channel' => 'new-design', 'label' => 'New design',     'app' => 'MIAV New Design'],
        ];
    @endphp

    @foreach($versions as $v)
        @continue(!$v['release'])
        @php $r = $v['release']; @endphp
        <div class="ui-card">
            <div class="p-5 flex flex-wrap items-center gap-4">
                <div class="flex-1" style="min-width:220px">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">{{ $v['label'] }}</div>
                    <div class="text-2xl font-bold text-gray-800">
                        v{{ $r['version'] }}
                        <span class="text-sm font-medium text-gray-400">(build {{ $r['build'] }})</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Shows on the tablet as “{{ $v['app'] }}”
                        &middot; released {{ \Carbon\Carbon::parse($r['released_at'])->format('d M Y') }}
                        &middot; {{ number_format($r['size'] / 1048576, 1) }} MB
                    </div>
                </div>
                <a href="{{ route('mobile-app.download', ['channel' => $v['channel']]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-semibold text-white"
                   style="background:#1a3a5c">
                    Download APK
                </a>
            </div>

            @if(!empty($r['notes']))
            <div class="border-t border-gray-100 p-5">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-2">What’s new</div>
                <ul class="list-disc pl-5 space-y-1 text-sm text-gray-700">
                    @foreach($r['notes'] as $note)
                    <li>{{ $note }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="border-t border-gray-100 px-5 py-3 text-xs text-gray-400">SHA-256: <span class="font-mono" style="word-break:break-all">{{ $r['sha256'] }}</span></div>
        </div>
    @endforeach

    @if($release || $newDesign)
    <div class="ui-card">
        <div class="p-5 text-sm text-gray-600 space-y-2">
            <div class="font-semibold text-gray-800">How to install on a tablet</div>
            <ol class="list-decimal pl-5 space-y-1">
                <li>Open this page on the tablet (log in as usual) and tap <strong>Download APK</strong> for the version you want, or copy the file to the tablet.</li>
                <li>Open the downloaded file. If Android asks, allow installing apps from this source.</li>
                <li>Tap <strong>Update</strong> / <strong>Install</strong>. The current app keeps its data and login when updated.</li>
                <li>Check the version on the app’s login screen or Profile page matches the one shown here.</li>
            </ol>
        </div>
    </div>
    @else
    <div class="ui-card">
        <div class="p-5 text-sm text-gray-500 italic">No app version has been published yet.</div>
    </div>
    @endif

</div>
@endsection
