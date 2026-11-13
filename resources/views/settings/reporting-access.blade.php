@extends('layouts.app')
@section('title', 'Reporting Access')

@section('header-actions')
<a href="{{ route('settings.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Back to Settings</a>
@endsection

@push('styles')
<style>
    .ra { display: grid; gap: 24px; max-width: 1080px; }
    .ra-intro { margin: 0; font-size: 13px; color: var(--mv-muted); line-height: 1.6; }
    .ra-section { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 28px; padding-top: 24px; border-top: 1px solid var(--mv-line); }
    .ra-section.is-first { border-top: 0; padding-top: 0; }
    .ra-section h2 { margin: 0; font-size: 14.5px; font-weight: 600; color: var(--mv-ink); }
    .ra-section p.ra-desc { margin: 4px 0 0; font-size: 13px; line-height: 1.5; color: var(--mv-muted); }
    .ra-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; }
    .ra-card-body { padding: 16px 20px; display: grid; gap: 14px; }
    .ra .form-group { margin: 0; }
    .ra .form-label { display: block; margin-bottom: 5px; }
    .ra .form-input { width: 100%; height: 36px; padding: 0 11px; font-size: 13.5px; }
    .ra .form-hint { margin-top: 4px; font-size: 12px; color: var(--mv-muted); }
    .ra-foot { display: flex; justify-content: flex-end; gap: 10px; padding-top: 14px; border-top: 1px solid var(--mv-line); }
    .ra-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ra-table th { text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: var(--mv-muted); padding: 10px 14px; border-bottom: 1px solid var(--mv-line); }
    .ra-table td { padding: 12px 14px; border-bottom: 1px solid var(--mv-line); vertical-align: middle; }
    .ra-table tr:last-child td { border-bottom: 0; }
    .ra-scope { display: inline-block; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 500; }
    .ra-scope-one { background: color-mix(in srgb, var(--mv-good) 14%, transparent); color: var(--mv-good); }
    .ra-scope-all { background: color-mix(in srgb, var(--mv-warn) 16%, transparent); color: var(--mv-warn); }
    .ra-scope-full { background: color-mix(in srgb, var(--mv-crit) 14%, transparent); color: var(--mv-crit); }
    .ra-dim { color: var(--mv-muted); font-size: 12.5px; }
    .ra-token { margin: 0; padding: 14px 16px; background: var(--mv-surface-2, #0f172a0a); border: 1px solid var(--mv-line); border-radius: 8px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; word-break: break-all; }
    .ra-urls { margin: 0; padding: 0; list-style: none; display: grid; gap: 4px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12.5px; color: var(--mv-muted); }
    .ra-empty { padding: 28px 20px; text-align: center; color: var(--mv-muted); font-size: 13px; }
</style>
@endpush

@section('content')
<div class="ra">

    @if (session('issued_token'))
        <div class="alert alert-success" role="alert" style="display:grid;gap:10px;">
            <strong>Token for “{{ session('issued_name') }}”</strong>
            <p class="ra-token">{{ session('issued_token') }}</p>
            <span class="ra-dim">Copy it now — it is shown once and cannot be read back. Send it through something private, not WhatsApp.</span>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="ra-section is-first">
        <div>
            <h2>What this is</h2>
            <p class="ra-desc">Read-only access to MIAV data for a reporting tool such as Power BI.</p>
        </div>
        <div class="ra-card">
            <div class="ra-card-body">
                <p class="ra-intro">
                    A token lets someone <strong>read</strong> the feed below and nothing else — it cannot
                    record a visit, change a terminal or sign in to this portal. Tie it to a client and the
                    holder sees only that client’s terminals, visits, findings and tickets.
                </p>
                <ul class="ra-urls">
                    <li>{{ $feed_url }}</li>
                    <li>{{ $feed_url }}/terminals</li>
                    <li>{{ $feed_url }}/visits</li>
                    <li>{{ $feed_url }}/findings</li>
                    <li>{{ $feed_url }}/tickets</li>
                    <li>{{ $feed_url }}/clients</li>
                    <li>{{ $feed_url }}/technicians</li>
                </ul>
                <p class="ra-intro">
                    Sent as a header: <code>Authorization: Bearer &lt;token&gt;</code>
                </p>
            </div>
        </div>
    </div>

    <div class="ra-section">
        <div>
            <h2>Issue a token</h2>
            <p class="ra-desc">Name it after the person or tool it is for, so the list below means something later.</p>
        </div>
        <div class="ra-card">
            <form method="POST" action="{{ route('settings.reporting-access.store') }}">
                @csrf
                <div class="ra-card-body">
                    <div class="form-group">
                        <label class="form-label" for="ra-name">Who it is for</label>
                        <input class="form-input" id="ra-name" name="name" required maxlength="120"
                               placeholder="e.g. Mbonisi Mangena – Power BI" value="{{ old('name') }}">
                        @error('name')<div class="form-hint" style="color:var(--mv-crit)">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="ra-client">Limit to one client</label>
                        <select class="form-input" id="ra-client" name="client_id">
                            <option value="">Every client — our own reporting only</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->company_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-hint">
                            @if ($clients->isEmpty())
                                There are no clients yet, so a token issued now would see everything once data is imported.
                            @else
                                Anyone outside the company should be limited to the client they work for.
                            @endif
                        </div>
                    </div>

                    <div class="ra-foot">
                        <button type="submit" class="btn-primary btn-sm">Issue token</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ra-section">
        <div>
            <h2>Issued tokens</h2>
            <p class="ra-desc">Withdrawing one takes effect immediately.</p>
        </div>
        <div class="ra-card">
            @if ($tokens->isEmpty())
                <p class="ra-empty">No tokens have been issued.</p>
            @else
                <table class="ra-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Can see</th>
                            <th>Last used</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tokens as $token)
                            <tr>
                                <td>
                                    {{ $token->name }}
                                    <div class="ra-dim">issued {{ $token->created_at?->toFormattedDateString() ?? '—' }}</div>
                                </td>
                                <td>
                                    @if ($token->is_full)
                                        <span class="ra-scope ra-scope-full">Everything (a tablet token)</span>
                                    @elseif ($token->client)
                                        <span class="ra-scope ra-scope-one">{{ $token->client }} only</span>
                                    @elseif ($token->is_feed)
                                        <span class="ra-scope ra-scope-all">Every client</span>
                                    @else
                                        <span class="ra-dim">—</span>
                                    @endif
                                </td>
                                <td class="ra-dim">{{ $token->last_used ? $token->last_used->diffForHumans() : 'never used' }}</td>
                                <td style="text-align:right">
                                    <form method="POST" action="{{ route('settings.reporting-access.destroy', $token->id) }}"
                                          onsubmit="return confirm('Withdraw “{{ $token->name }}”? It stops working immediately.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary btn-sm">Withdraw</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
