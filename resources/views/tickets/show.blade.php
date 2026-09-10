{{-- resources/views/tickets/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Ticket ' . $ticket->ticket_id)

@section('header-actions')
    <a href="{{ route('tickets.edit', $ticket) }}" class="btn-primary btn-sm">
        <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg> Edit Ticket
    </a>
@endsection

@push('styles')
<style>
    .tk-crumb { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--mv-muted); text-decoration: none; margin-bottom: 14px; }
    .tk-crumb:hover { color: var(--mv-ink); }
    .tk-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 16px; align-items: start; }
    .tk-card { background: var(--mv-surface); border: 1px solid var(--mv-line); border-radius: 10px; margin-bottom: 16px; }
    .tk-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 13px 18px; border-bottom: 1px solid var(--mv-line); }
    .tk-card-head h2 { margin: 0; font-size: 14px; font-weight: 600; color: var(--mv-ink); }
    .tk-card-body { padding: 18px; }
    .tk-id { font-family: var(--mv-mono); font-size: 12.5px; color: var(--mv-muted); }
    .tk-title { margin: 4px 0 10px; font-size: 17px; font-weight: 600; color: var(--mv-ink); letter-spacing: -.01em; }
    .tk-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .tk-chip { display: inline-flex; align-items: center; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; line-height: 1.7; background: var(--mv-surface-2); color: var(--mv-ink-2); border: 1px solid var(--mv-line); }
    .tk-chip.is-accent { background: var(--mv-accent-soft); color: var(--mv-accent-ink); border-color: transparent; }
    .tk-chip.is-good { background: var(--mv-good-soft); color: var(--mv-good); border-color: transparent; }
    .tk-chip.is-warn { background: var(--mv-warn-soft); color: var(--mv-warn); border-color: transparent; }
    .tk-chip.is-crit { background: var(--mv-crit-soft); color: var(--mv-crit); border-color: transparent; }
    .tk-text { font-size: 13.5px; line-height: 1.6; color: var(--mv-ink-2); white-space: pre-wrap; margin: 0; }
    .tk-dl { margin: 0; display: grid; gap: 0; }
    .tk-dl > div { display: flex; justify-content: space-between; gap: 12px; padding: 9px 0; border-bottom: 1px solid var(--mv-line); font-size: 13px; }
    .tk-dl > div:last-child { border-bottom: 0; }
    .tk-dl dt { color: var(--mv-muted); font-weight: 400; }
    .tk-dl dd { margin: 0; color: var(--mv-ink); text-align: right; font-variant-numeric: tabular-nums; }
    .tk-steps { list-style: none; margin: 0; padding: 0; }
    .tk-step { display: grid; grid-template-columns: 28px minmax(0, 1fr); gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--mv-line); }
    .tk-step:first-child { padding-top: 0; }
    .tk-step:last-child { border-bottom: 0; padding-bottom: 0; }
    .tk-step-no { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--mv-line-strong); display: grid; place-items: center; font-size: 12px; font-weight: 600; color: var(--mv-ink-2); background: var(--mv-surface); }
    .tk-step-head { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 4px; }
    .tk-step-who { font-size: 13px; font-weight: 500; color: var(--mv-ink); }
    .tk-step-when { font-size: 12px; color: var(--mv-muted); }
    .tk-step-desc { font-size: 13px; color: var(--mv-ink-2); margin: 0; }
    .tk-step-note { font-size: 12.5px; color: var(--mv-muted); margin: 4px 0 0; }
    .tk-empty { font-size: 13px; color: var(--mv-muted); margin: 0; }
    @media (max-width: 1000px) { .tk-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $statusTone = ['open' => 'is-accent', 'in_progress' => 'is-warn', 'pending' => 'is-warn', 'on_hold' => '', 'resolved' => 'is-good', 'closed' => 'is-good', 'cancelled' => 'is-crit'];
    $priorityTone = ['critical' => 'is-crit', 'high' => 'is-warn', 'medium' => 'is-accent', 'low' => ''];
    $stepTone = ['in_progress' => 'is-warn', 'completed' => 'is-good', 'resolved' => 'is-good', 'transferred' => 'is-accent'];
    $headline = fn ($v) => $v ? \Illuminate\Support\Str::headline($v) : '—';
    $steps = $ticket->steps()->with(['employee', 'transferredToEmployee'])->orderBy('step_number')->get();
@endphp

<a href="{{ route('tickets.index') }}" class="tk-crumb">
    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg> Support Tickets
</a>

<div class="tk-layout">
    <div>
        <div class="tk-card">
            <div class="tk-card-body">
                <div class="tk-id">{{ $ticket->ticket_id }}</div>
                <h2 class="tk-title">{{ $ticket->title }}</h2>
                <div class="tk-chips">
                    <span class="tk-chip {{ $statusTone[$ticket->status] ?? '' }}">{{ $headline($ticket->status) }}</span>
                    <span class="tk-chip {{ $priorityTone[$ticket->priority] ?? '' }}">{{ $headline($ticket->priority) }} priority</span>
                    <span class="tk-chip">{{ $headline($ticket->issue_type) }}</span>
                    @if($ticket->ticket_type)
                        <span class="tk-chip">{{ $ticket->ticket_type === 'pos_terminal' ? 'POS Terminal' : 'Internal' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="tk-card">
            <div class="tk-card-head"><h2>Description</h2></div>
            <div class="tk-card-body"><p class="tk-text">{{ $ticket->description }}</p></div>
        </div>

        @if($ticket->resolution)
        <div class="tk-card">
            <div class="tk-card-head">
                <h2>Resolution</h2>
                @if($ticket->resolved_at)<span class="tk-step-when">{{ $ticket->resolved_at->format('M j, Y g:i A') }}</span>@endif
            </div>
            <div class="tk-card-body"><p class="tk-text">{{ $ticket->resolution }}</p></div>
        </div>
        @endif

        <div class="tk-card">
            <div class="tk-card-head">
                <h2>Ticket Steps &amp; Audit Trail</h2>
                <span class="tk-chip">{{ $steps->count() }}</span>
            </div>
            <div class="tk-card-body">
                @if($steps->isEmpty())
                    <p class="tk-empty">No steps recorded yet.</p>
                @else
                <ol class="tk-steps">
                    @foreach($steps as $step)
                    <li class="tk-step">
                        <div class="tk-step-no">{{ $step->step_number }}</div>
                        <div>
                            <div class="tk-step-head">
                                <span class="tk-step-who">{{ $step->employee->name ?? 'Unknown' }}</span>
                                <span class="tk-chip {{ $stepTone[$step->status] ?? '' }}">{{ $headline($step->status) }}</span>
                                <span class="tk-step-when">{{ $step->created_at?->format('M j, Y g:i A') }}</span>
                            </div>
                            <p class="tk-step-desc">{{ $step->description }}</p>
                            @if($step->notes)<p class="tk-step-note">Notes: {{ $step->notes }}</p>@endif
                            @if($step->resolution_notes)<p class="tk-step-note">Resolution: {{ $step->resolution_notes }}</p>@endif
                            @if($step->transferred_reason)
                                <p class="tk-step-note">Transferred to {{ $step->transferredToEmployee->name ?? 'another employee' }}: {{ $step->transferred_reason }}</p>
                            @endif
                            @if($step->completed_at)<p class="tk-step-note">Completed {{ $step->completed_at->format('M j, Y g:i A') }}</p>@endif
                        </div>
                    </li>
                    @endforeach
                </ol>
                @endif
            </div>
        </div>
    </div>

    <aside>
        <div class="tk-card">
            <div class="tk-card-head"><h2>Details</h2></div>
            <div class="tk-card-body" style="padding-top:6px; padding-bottom:6px;">
                <dl class="tk-dl">
                    <div><dt>Assigned To</dt><dd>{{ $ticket->assignedTo ? $ticket->assignedTo->first_name . ' ' . $ticket->assignedTo->last_name : 'Unassigned' }}</dd></div>
                    <div><dt>Assignment</dt><dd>{{ $ticket->assignment_type === 'direct' ? 'Direct' : 'Public' }}</dd></div>
                    <div><dt>Created By</dt><dd>{{ $ticket->technician ? $ticket->technician->first_name . ' ' . $ticket->technician->last_name : '—' }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $ticket->created_at?->format('M j, Y g:i A') }}</dd></div>
                    <div><dt>Updated</dt><dd>{{ $ticket->updated_at?->format('M j, Y g:i A') }}</dd></div>
                    @if($ticket->estimated_resolution_time)
                        <div><dt>Est. Resolution</dt><dd>{{ $ticket->estimated_resolution_time }} {{ \Illuminate\Support\Str::plural('day', (int) $ticket->estimated_resolution_time) }}</dd></div>
                    @endif
                    @if($ticket->client)
                        <div><dt>Client</dt><dd>{{ $ticket->client->company_name }}</dd></div>
                    @endif
                    @if($ticket->mobile_created)
                        <div><dt>Source</dt><dd>Mobile app</dd></div>
                    @endif
                </dl>
            </div>
        </div>

        @if($ticket->posTerminal)
        <div class="tk-card">
            <div class="tk-card-head"><h2>POS Terminal</h2></div>
            <div class="tk-card-body" style="padding-top:6px; padding-bottom:6px;">
                <dl class="tk-dl">
                    <div><dt>Terminal ID</dt><dd class="mv-mono">{{ $ticket->posTerminal->terminal_id }}</dd></div>
                    <div><dt>Merchant</dt><dd>{{ $ticket->posTerminal->merchant_name ?? '—' }}</dd></div>
                    @if($ticket->posTerminal->physical_address)
                        <div><dt>Address</dt><dd>{{ $ticket->posTerminal->physical_address }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
        @endif

        @if($ticket->visit)
        <div class="tk-card">
            <div class="tk-card-head"><h2>Site Visit</h2></div>
            <div class="tk-card-body">
                <a href="{{ route('site_visits.show', $ticket->visit) }}" class="btn-secondary btn-sm">
                    <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-external"/></svg> Open visit #{{ $ticket->visit->id }}
                </a>
            </div>
        </div>
        @endif
    </aside>
</div>
@endsection
