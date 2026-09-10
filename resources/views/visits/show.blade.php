{{-- resources/views/visits/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Visit Details')

@push('styles')
<style>
.vs-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.vs-head-top{padding:14px 18px;border-bottom:1px solid var(--mv-line)}
.vs-head-top h2{margin:0;font-size:15px;font-weight:600;color:var(--mv-ink)}
.vs-dl{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px 24px;margin:0;padding:14px 18px}
.vs-dl dt,.vs-list dt{margin:0 0 3px;font-size:12px;font-weight:500;color:var(--mv-muted)}
.vs-dl dd,.vs-list dd{margin:0;font-size:13.5px;color:var(--mv-ink);word-break:break-word}
.vs-sub{display:block;margin-top:3px;font-size:12px;color:var(--mv-muted)}
.vs-layout{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:16px;align-items:start;margin-top:16px}
@media (max-width:1100px){.vs-layout{grid-template-columns:minmax(0,1fr)}}
.vs-col{display:flex;flex-direction:column;gap:16px;min-width:0}
.vs-list{display:flex;flex-direction:column;gap:14px;margin:0;padding:16px 18px}
.vs-list dd{color:var(--mv-ink-2);line-height:1.55}
.vs-body{padding:16px 18px}
.vs-none{font-size:13px;color:var(--mv-muted)}
.mv-page .badge{display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.vs-count{font-size:12px;color:var(--mv-muted);font-variant-numeric:tabular-nums}
.vs-tablewrap{overflow-x:auto;border:1px solid var(--mv-line);border-radius:8px}
.vs-tablewrap .ui-table{width:100%}
.vs-crit{color:var(--mv-crit) !important}
.vs-meta{display:flex;flex-wrap:wrap;gap:4px 20px;margin-top:10px;font-size:12px;color:var(--mv-muted)}
.vs-disclose{margin-top:12px}
.vs-disclose summary{list-style:none;display:inline-flex;align-items:center;gap:4px;cursor:pointer;font-size:12.5px;font-weight:500;color:var(--mv-accent-ink);user-select:none}
.vs-disclose summary::-webkit-details-marker{display:none}
.vs-disclose summary:hover{text-decoration:underline}
.vs-disclose summary .mv-i{width:14px;height:14px;transition:transform .15s ease}
.vs-disclose[open] summary .mv-i{transform:rotate(90deg)}
.vs-code{margin-top:8px;padding:10px 12px;max-height:220px;overflow:auto;white-space:pre-wrap;word-break:break-all;font-family:var(--mv-mono);font-size:12px;color:var(--mv-ink-2);background:var(--mv-surface-2);border:1px solid var(--mv-line);border-radius:8px}
.vs-code.is-flush{margin-top:0}
.vs-code.is-short{max-height:96px}
.vs-files{display:flex;flex-direction:column;gap:8px;margin:0;padding:0;list-style:none}
.vs-files li{display:flex;align-items:center;flex-wrap:wrap;gap:6px;font-size:13px;color:var(--mv-ink-2)}
.vs-files .mv-i{width:15px;height:15px;color:var(--mv-muted)}
.vs-files strong{font-weight:500;color:var(--mv-ink)}
.vs-files a{font-weight:500;color:var(--mv-accent-ink)}
.vs-files .vs-raw{word-break:break-all}
@media (prefers-reduced-motion: reduce){.vs-disclose summary .mv-i{transition:none}}
</style>
@endpush

@section('content')
{{-- Toolbar --}}
<div class="vs-bar">
    <a href="{{ route('visits.index') }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>All Visits</a>
    <a href="{{ route('visits.edit', $visit) }}" class="btn-secondary btn-sm"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg>Edit Visit</a>
</div>

{{-- Header --}}
<section class="ui-card">
    <div class="vs-head-top">
        <h2>Visit <span class="mv-mono">#{{ $visit->id }}</span> Details</h2>
    </div>
    <dl class="vs-dl">
        <div>
            <dt>Status</dt>
            <dd>
                @if($visit->completed_at)
                    <span class="badge badge-green">Completed</span>
                    <span class="vs-sub">{{ $visit->completed_at->format('F j, Y \a\t g:i A') }}</span>
                @else
                    <span class="badge badge-yellow">In Progress</span>
                @endif
            </dd>
        </div>
        <div>
            <dt>Merchant</dt>
            <dd>
                {{ $visit->merchant_name ?? 'Unknown Merchant' }}
                <span class="vs-sub">ID: <span class="mv-mono">{{ $visit->merchant_id }}</span></span>
            </dd>
        </div>
        <div>
            <dt>Employee</dt>
            <dd>
                {{ optional($visit->employee)->full_name ?? 'Unknown Employee' }}
                <span class="vs-sub">ID: <span class="mv-mono">{{ $visit->employee_id }}</span></span>
            </dd>
        </div>
        <div>
            <dt>Assignment</dt>
            <dd>
                @if($visit->assignment_id)
                    <span class="mv-mono">{{ $visit->assignment_id }}</span>
                @else
                    <span class="vs-none">No Assignment</span>
                @endif
            </dd>
        </div>
        <div>
            <dt>Created</dt>
            <dd>{{ $visit->created_at ? $visit->created_at->format('F j, Y \a\t g:i A') : '—' }}</dd>
        </div>
        <div>
            <dt>Updated</dt>
            <dd>{{ $visit->updated_at ? $visit->updated_at->format('F j, Y \a\t g:i A') : '—' }}</dd>
        </div>
    </dl>
</section>

<div class="vs-layout">

    {{-- ====== MAIN ====== --}}
    <div class="vs-col">

        {{-- Summary, action points, notes --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Visit Notes</h3>
            </div>
            <dl class="vs-list">
                <div>
                    <dt>Visit Summary</dt>
                    <dd>{{ $visit->visit_summary ?: 'No summary provided.' }}</dd>
                </div>
                @if(!empty($visit->action_points))
                <div>
                    <dt>Action Points</dt>
                    <dd>{{ $visit->action_points }}</dd>
                </div>
                @endif
                @if(!empty($visit->terminal_comments))
                {{-- Corrective Action (the tablet sends it as terminal_comments) --}}
                <div>
                    <dt>Corrective Action</dt>
                    <dd>{{ $visit->terminal_comments }}</dd>
                </div>
                @endif
                @if(!empty($visit->condition_notes))
                <div>
                    <dt>Condition Notes</dt>
                    <dd>{{ $visit->condition_notes }}</dd>
                </div>
                @endif
            </dl>
        </section>

        {{-- Primary Terminal --}}
        @php $terminal = $visit->getCompleteTerminalInfo(); @endphp
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Primary Terminal</h3>
                @if(!empty($terminal) && isset($terminal['found_in_pos_terminals']))
                    @if($terminal['found_in_pos_terminals'])
                        <span class="badge badge-green"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-check-circle"/></svg>Terminal data</span>
                    @else
                        <span class="badge badge-yellow"><svg class="mv-i mv-ei" aria-hidden="true"><use href="#i-alert-triangle"/></svg>Terminal not found (showing basic data only)</span>
                    @endif
                @endif
            </div>
            <div class="vs-body">
                @if(!empty($terminal))
                    <div class="vs-tablewrap">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>Terminal ID</th>
                                    <th>Status</th>
                                    <th>Condition</th>
                                    <th>Model</th>
                                    <th>Serial No.</th>
                                    @if(!empty($terminal['issues']))
                                    <th>Issues</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="mv-mono">{{ $terminal['terminal_id'] ?? '—' }}</span></td>
                                    <td>{{ $terminal['status'] ?? ($terminal['current_status'] ?? '—') }}</td>
                                    <td>{{ $terminal['condition_status'] ?? ($terminal['condition'] ?? '—') }}</td>
                                    <td>{{ $terminal['terminal_model'] ?? '—' }}</td>
                                    <td><span class="mv-mono">{{ $terminal['serial_number'] ?? '—' }}</span></td>
                                    @if(!empty($terminal['issues']))
                                    <td class="vs-crit">{{ $terminal['issues'] }}</td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(!empty($terminal['last_service_date']) || !empty($terminal['next_service_due']))
                    <div class="vs-meta">
                        @if(!empty($terminal['last_service_date']))
                        <span>Last Service: {{ $terminal['last_service_date'] }}</span>
                        @endif
                        @if(!empty($terminal['next_service_due']))
                        <span>Next Service Due: {{ $terminal['next_service_due'] }}</span>
                        @endif
                    </div>
                    @endif
                    <details class="vs-disclose">
                        <summary><svg class="mv-i" aria-hidden="true"><use href="#i-chevron-right"/></svg>View extra terminal details</summary>
                        <div class="vs-code">{{ json_encode($terminal, JSON_PRETTY_PRINT) }}</div>
                    </details>
                @else
                    <span class="vs-none">No primary terminal data.</span>
                @endif
            </div>
        </section>

        {{-- Other Terminals --}}
        @php $otherTerminals = is_array($visit->other_terminals_found) ? $visit->other_terminals_found : []; @endphp
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Other Terminals</h3>
                @if(count($otherTerminals))
                    <span class="vs-count">Found {{ count($otherTerminals) }} additional terminal(s)</span>
                @endif
            </div>
            <div class="vs-body">
                @if(count($otherTerminals))
                    <div class="vs-code is-flush">{{ json_encode($otherTerminals, JSON_PRETTY_PRINT) }}</div>
                @else
                    <span class="vs-none">None found.</span>
                @endif
            </div>
        </section>
    </div>

    {{-- ====== SIDE ====== --}}
    <div class="vs-col">

        @if(!empty($visit->contact_person) || !empty($visit->new_contact_person) || !empty($visit->new_phone_number) || !empty($visit->new_physical_address))
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Contact</h3>
            </div>
            <dl class="vs-list">
                @if(!empty($visit->contact_person))
                <div>
                    <dt>Contact Person</dt>
                    <dd>
                        {{ $visit->contact_person }}
                        @if(!empty($visit->phone_number))
                        <span class="vs-sub">{{ $visit->phone_number }}</span>
                        @endif
                    </dd>
                </div>
                @endif
                @if(!empty($visit->new_contact_person) || !empty($visit->new_phone_number) || !empty($visit->new_physical_address))
                {{-- New / updated contact details captured on the tablet --}}
                <div>
                    <dt>New / Updated Contact</dt>
                    <dd>
                        @if(!empty($visit->new_contact_person))
                        {{ $visit->new_contact_person }}
                        @endif
                        @if(!empty($visit->new_phone_number))
                        <span class="vs-sub">{{ $visit->new_phone_number }}</span>
                        @endif
                        @if(!empty($visit->new_physical_address))
                        <span class="vs-sub">{{ $visit->new_physical_address }}</span>
                        @endif
                    </dd>
                </div>
                @endif
            </dl>
        </section>
        @endif

        {{-- Evidence --}}
        @php $evidence = is_array($visit->evidence) ? $visit->evidence : []; @endphp
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Evidence</h3>
                @if(count($evidence))
                    <span class="vs-count">{{ count($evidence) }}</span>
                @endif
            </div>
            <div class="vs-body">
                @if(count($evidence))
                    <ul class="vs-files">
                        @foreach($evidence as $idx => $e)
                        <li>
                            <svg class="mv-i" aria-hidden="true"><use href="#i-paperclip"/></svg>
                            <strong>Evidence {{ $idx + 1 }}:</strong>
                            @if(\Illuminate\Support\Str::startsWith($e, ['http://','https://','/storage/']))
                                <a href="{{ $e }}" target="_blank" rel="noopener">View Evidence</a>
                            @else
                                <span class="vs-raw">{{ $e }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                @else
                    <span class="vs-none">No evidence.</span>
                @endif
            </div>
        </section>

        @if(!empty($visit->signature))
        {{-- Signature --}}
        <section class="ui-card">
            <div class="ui-card-header">
                <h3>Signature</h3>
            </div>
            <div class="vs-body">
                @if(\Illuminate\Support\Str::startsWith($visit->signature, ['data:image/', 'data:application/']))
                    <span class="vs-none">Digital signature captured</span>
                    <div class="vs-code is-short">{{ \Illuminate\Support\Str::limit($visit->signature, 200) }}...</div>
                @else
                    {{ $visit->signature }}
                @endif
            </div>
        </section>
        @endif
    </div>
</div>
@endsection
