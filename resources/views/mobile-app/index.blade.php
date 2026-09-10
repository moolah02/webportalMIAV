@extends('layouts.app')

@section('title', 'Mobile App')

@push('styles')
<style>
    .ma { display: grid; gap: 16px; max-width: 880px; }
    .ma .mv-i { width: 16px; height: 16px; }
    .ma-intro { margin: 0; font-size: 13.5px; color: var(--mv-muted); }
    .ma-note { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; font-size: 13.5px; line-height: 1.5; border: 1px solid; }
    .ma-note .mv-i { margin-top: 2px; flex-shrink: 0; }
    .ma-note strong { font-weight: 600; }

    .ma-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .ma-release { display: flex; align-items: center; gap: 16px; padding: 18px 20px; flex-wrap: wrap; }
    .ma-release-main { flex: 1; min-width: 240px; }
    .ma-label { display: inline-flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 500; color: var(--mv-muted); }
    .ma-version { margin-top: 4px; display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; }
    .ma-version strong { font-family: var(--mv-mono); font-size: 22px; font-weight: 500; letter-spacing: -.01em; color: var(--mv-ink); }
    .ma-version span { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-muted); }
    .ma-facts { margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px 14px; font-size: 12.5px; color: var(--mv-muted); }
    .ma-facts b { font-weight: 500; color: var(--mv-ink-2); }
    .ma-download { display: inline-flex; align-items: center; gap: 8px; }

    .ma-section { border-top: 1px solid var(--mv-line); padding: 14px 20px; }
    .ma-section-title { font-size: 12.5px; font-weight: 600; color: var(--mv-ink); margin-bottom: 6px; }
    .ma-section ul, .ma-section ol { margin: 0; padding-left: 18px; display: grid; gap: 4px; font-size: 13.5px; color: var(--mv-ink-2); line-height: 1.5; }
    .ma-hash { border-top: 1px solid var(--mv-line); padding: 10px 20px; font-size: 12px; color: var(--mv-muted); }
    .ma-hash span { font-family: var(--mv-mono); color: var(--mv-ink-2); word-break: break-all; }

    .ma-steps { padding: 16px 20px; }
    .ma-steps h3 { margin: 0 0 8px; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .ma-steps ol { margin: 0; padding-left: 18px; display: grid; gap: 6px; font-size: 13.5px; color: var(--mv-ink-2); line-height: 1.5; }
    .ma-empty { padding: 36px 20px; text-align: center; font-size: 13.5px; color: var(--mv-muted); }
    .ma-empty .mv-i { width: 28px; height: 28px; display: block; margin: 0 auto 8px; color: var(--mv-line-strong); }
</style>
@endpush

@section('content')
<div class="ma">

    <p class="ma-intro">Android app for field technicians. Download a version below to install or update it on the tablets.</p>

    @if($newDesign)
    <div class="alert alert-info ma-note">
        <svg class="mv-i" aria-hidden="true"><use href="#i-info"/></svg>
        <div>
            <strong>Two versions are available.</strong>
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
        <div class="ma-card">
            <div class="ma-release">
                <div class="ma-release-main">
                    <div class="ma-label"><svg class="mv-i" aria-hidden="true"><use href="#i-phone"/></svg>{{ $v['label'] }}</div>
                    <div class="ma-version">
                        <strong>v{{ $r['version'] }}</strong>
                        <span>build {{ $r['build'] }}</span>
                    </div>
                    <div class="ma-facts">
                        <span>Shows on the tablet as <b>“{{ $v['app'] }}”</b></span>
                        <span>Released <b>{{ \Carbon\Carbon::parse($r['released_at'])->format('d M Y') }}</b></span>
                        <span><b>{{ number_format($r['size'] / 1048576, 1) }} MB</b></span>
                    </div>
                </div>
                <a href="{{ route('mobile-app.download', ['channel' => $v['channel']]) }}" class="btn-primary ma-download">
                    <svg class="mv-i" aria-hidden="true"><use href="#i-download"/></svg> Download APK
                </a>
            </div>

            @if(!empty($r['notes']))
            <div class="ma-section">
                <div class="ma-section-title">What’s new</div>
                <ul>
                    @foreach($r['notes'] as $note)
                    <li>{{ $note }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="ma-hash">SHA-256: <span>{{ $r['sha256'] }}</span></div>
        </div>
    @endforeach

    @if($release || $newDesign)
    <div class="ma-card ma-steps">
        <h3>How to install on a tablet</h3>
        <ol>
            <li>Open this page on the tablet (log in as usual) and tap <strong>Download APK</strong> for the version you want, or copy the file to the tablet.</li>
            <li>Open the downloaded file. If Android asks, allow installing apps from this source.</li>
            <li>Tap <strong>Update</strong> / <strong>Install</strong>. The current app keeps its data and login when updated.</li>
            <li>Check the version on the app’s login screen or Profile page matches the one shown here.</li>
        </ol>
    </div>
    @else
    <div class="ma-card ma-empty">
        <svg class="mv-i" aria-hidden="true"><use href="#i-phone"/></svg>
        No app version has been published yet.
    </div>
    @endif

</div>
@endsection
