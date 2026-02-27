@extends('docs.layout')
@section('content')

{{-- HERO --}}
<div style="text-align:center; padding: 40px 0 48px;">
    <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies"
         style="height:80px; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.5); margin-bottom:20px;">
    <h1 style="font-size:32px; font-weight:800; color:#f0f6fc; margin-bottom:8px;">
        Revival Technologies
    </h1>
    <p style="font-size:17px; color:#8b949e; font-weight:400;">
        MIAV Dashboard &mdash; User Manuals &amp; Docs
    </p>
    <p style="font-size:13px; color:#6e7681; margin-top:6px;">
        Version 1.0 &nbsp;&middot;&nbsp; February 2026 &nbsp;&middot;&nbsp; All Roles
    </p>
</div>

{{-- USER MANUALS SECTION --}}
<h2 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.4px;color:#6e7681;
           margin-bottom:16px;border:none;padding:0;">
    USER MANUALS &mdash; SELECT YOUR ROLE
</h2>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:18px; margin-bottom:48px;">

    <a href="{{ url('/docs/system') }}" style="text-decoration:none;">
        <div style="background:#161b22;border:1px solid #30363d;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #388bfd;"
             onmouseover="this.style.background='#1c2128';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.background='#161b22';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#9881;&#65039;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#388bfd;margin-bottom:4px;">
                ADMIN / MANAGER
            </div>
            <div style="font-size:16px;font-weight:700;color:#f0f6fc;margin-bottom:8px;">System Manual</div>
            <p style="font-size:13px;color:#8b949e;line-height:1.5;margin:0;">
                Dashboard overview, employee management, assets, field operations, projects,
                clients, reports, and admin settings.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#388bfd;">
                Open manual &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/testing') }}" style="text-decoration:none;">
        <div style="background:#161b22;border:1px solid #30363d;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #3fb950;"
             onmouseover="this.style.background='#1c2128';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.background='#161b22';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#129514;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#3fb950;margin-bottom:4px;">
                QA / TESTING
            </div>
            <div style="font-size:16px;font-weight:700;color:#f0f6fc;margin-bottom:8px;">Testing Guide</div>
            <p style="font-size:13px;color:#8b949e;line-height:1.5;margin:0;">
                Full QA checklist &mdash; authentication, employee management, assets, tickets,
                deployments, reports, and role-based access.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#3fb950;">
                Open guide &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/tickets') }}" style="text-decoration:none;">
        <div style="background:#161b22;border:1px solid #30363d;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #bc8cff;"
             onmouseover="this.style.background='#1c2128';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.background='#161b22';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#127915;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#bc8cff;margin-bottom:4px;">
                SUPPORT TEAM
            </div>
            <div style="font-size:16px;font-weight:700;color:#f0f6fc;margin-bottom:8px;">Ticket System Guide</div>
            <p style="font-size:13px;color:#8b949e;line-height:1.5;margin:0;">
                Ticket types, assignment types (public vs direct), status filtering,
                and POS terminal vs internal ticket handling.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#bc8cff;">
                Open guide &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/staged-resolution') }}" style="text-decoration:none;">
        <div style="background:#161b22;border:1px solid #30363d;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #f0883e;"
             onmouseover="this.style.background='#1c2128';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.background='#161b22';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#128295;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#f0883e;margin-bottom:4px;">
                TECHNICIANS
            </div>
            <div style="font-size:16px;font-weight:700;color:#f0f6fc;margin-bottom:8px;">Staged Resolution</div>
            <p style="font-size:13px;color:#8b949e;line-height:1.5;margin:0;">
                Multi-stage ticket resolution, step creation, transfers between employees,
                audit trail tracking, and SLA management.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#f0883e;">
                Open guide &rarr;
            </div>
        </div>
    </a>

</div>

{{-- TECHNICAL SECTION --}}
<h2 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.4px;color:#6e7681;
           margin-bottom:16px;border:none;padding:0;">
    TECHNICAL &amp; REFERENCE DOCUMENTS
</h2>

<div style="background:#161b22;border:1px solid #30363d;border-radius:12px;overflow:hidden;margin-bottom:48px;">

    <a href="{{ url('/docs/api') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #21262d;
                    transition:background .15s;"
             onmouseover="this.style.background='#1c2128'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#128450;&#65039;</div>
            <div>
                <div style="font-weight:600;color:#f0f6fc;font-size:14px;">API Documentation (Swagger)</div>
                <div style="font-size:13px;color:#8b949e;margin-top:2px;">
                    Full REST API reference for all web and future mobile integrations
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#388bfd;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

    <a href="{{ url('/docs/system') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #21262d;
                    transition:background .15s;"
             onmouseover="this.style.background='#1c2128'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#128196;</div>
            <div>
                <div style="font-weight:600;color:#f0f6fc;font-size:14px;">System Documentation</div>
                <div style="font-size:13px;color:#8b949e;margin-top:2px;">
                    Full technical reference &mdash; data models, workflows, business rules, user roles
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#388bfd;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

    <a href="{{ url('/docs/overview') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #21262d;
                    transition:background .15s;"
             onmouseover="this.style.background='#1c2128'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#127962;</div>
            <div>
                <div style="font-weight:600;color:#f0f6fc;font-size:14px;">Business Overview</div>
                <div style="font-size:13px;color:#8b949e;margin-top:2px;">
                    System summary, module features, and high-level workflows
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#388bfd;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

    <a href="{{ url('/docs/deployment') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;
                    transition:background .15s;"
             onmouseover="this.style.background='#1c2128'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#128640;</div>
            <div>
                <div style="font-weight:600;color:#f0f6fc;font-size:14px;">Deployment Guide</div>
                <div style="font-size:13px;color:#8b949e;margin-top:2px;">
                    Dev &amp; production server setup, SSH access, git workflow, and artisan commands
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#388bfd;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

</div>

{{-- VERSION BANNER --}}
<div style="background:#161b22;border:1px solid #30363d;border-radius:12px;padding:22px 28px;
            display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-weight:700;font-size:14px;color:#f0f6fc;margin-bottom:4px;">MIAV Dashboard &mdash; Current Version</div>
        <div style="font-size:12.5px;color:#8b949e;">
            Laravel 11 &nbsp;&middot;&nbsp; PHP 8.2 &nbsp;&middot;&nbsp; MySQL &nbsp;&middot;&nbsp; Spatie Permissions &nbsp;&middot;&nbsp; AdminLTE
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <span style="background:#1c2128;border:1px solid #30363d;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;color:#8b949e;">
            &#127760; 51.21.252.67
        </span>
        <a href="{{ url('/login') }}"
           style="background:#1f6feb;padding:6px 16px;border-radius:6px;font-size:12px;font-weight:600;
                  color:#fff;text-decoration:none;">
            Open App &rarr;
        </a>
    </div>
</div>

@endsection