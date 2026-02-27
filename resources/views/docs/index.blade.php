@extends('docs.layout')
@section('content')

{{-- HERO --}}
<div style="text-align:center; padding: 40px 0 48px;">
    <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies"
         style="height:80px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.15); margin-bottom:20px;">
    <h1 style="font-size:32px; font-weight:800; color:#0d1117; margin-bottom:8px;">
        Revival Technologies
    </h1>
    <p style="font-size:17px; color:#57606a; font-weight:400;">
        MIAV Dashboard &mdash; Documentation &amp; User Manuals
    </p>
    <p style="font-size:13px; color:#8c959f; margin-top:6px;">
        Version 1.0 &nbsp;&middot;&nbsp; February 2026 &nbsp;&middot;&nbsp; For Users, Managers &amp; Admins
    </p>
</div>

{{-- USER MANUALS --}}
<h2 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.4px;color:#57606a;
           margin-bottom:16px;border:none;padding:0;">
    USER MANUALS &mdash; SELECT YOUR GUIDE
</h2>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:18px; margin-bottom:48px;">

    <a href="{{ url('/docs/system') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #0969da;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(9,105,218,.12)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#9881;&#65039;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#0969da;margin-bottom:4px;">
                ALL STAFF &mdash; FULL SYSTEM
            </div>
            <div style="font-size:16px;font-weight:700;color:#0d1117;margin-bottom:8px;">System Manual</div>
            <p style="font-size:13px;color:#57606a;line-height:1.5;margin:0;">
                Complete guide covering dashboard, employees, assets, projects, clients,
                ticketing &amp; staged resolution, and admin settings.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#0969da;">
                Open manual &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/mobile') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #1a7f37;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(26,127,55,.12)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#128241;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#1a7f37;margin-bottom:4px;">
                TECHNICIANS &amp; FIELD STAFF
            </div>
            <div style="font-size:16px;font-weight:700;color:#0d1117;margin-bottom:8px;">Mobile App Guide</div>
            <p style="font-size:13px;color:#57606a;line-height:1.5;margin:0;">
                How to use the MIAV mobile app &mdash; receiving jobs, updating ticket statuses,
                recording visits, and completing staged resolutions in the field.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#1a7f37;">
                Open guide &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/reports') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #9a3412;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(154,52,18,.12)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#128202;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9a3412;margin-bottom:4px;">
                MANAGERS &amp; ADMINS
            </div>
            <div style="font-size:16px;font-weight:700;color:#0d1117;margin-bottom:8px;">Reports Manual</div>
            <p style="font-size:13px;color:#57606a;line-height:1.5;margin:0;">
                Step-by-step instructions for generating all reports &mdash; employee performance,
                asset summaries, project status, ticket analytics, and exports.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#9a3412;">
                Open guide &rarr;
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/projects') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #d0d7de;border-radius:10px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #7c3aed;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(124,58,237,.12)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px;margin-bottom:10px;">&#128203;</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#7c3aed;margin-bottom:4px;">
                PROJECT MANAGERS
            </div>
            <div style="font-size:16px;font-weight:700;color:#0d1117;margin-bottom:8px;">Project Flow Guide</div>
            <p style="font-size:13px;color:#57606a;line-height:1.5;margin:0;">
                End-to-end project lifecycle &mdash; creation, team assignment, milestones,
                field operations linking, closure reports, and client communication.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#7c3aed;">
                Open guide &rarr;
            </div>
        </div>
    </a>

</div>

{{-- REFERENCE SECTION --}}
<h2 style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.4px;color:#57606a;
           margin-bottom:16px;border:none;padding:0;">
    REFERENCE DOCUMENTS
</h2>

<div style="background:#fff;border:1px solid #d0d7de;border-radius:10px;overflow:hidden;margin-bottom:48px;">

    <a href="{{ url('/docs/srs') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #f0f4f8;
                    transition:background .15s;"
             onmouseover="this.style.background='#f6f8fa'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#128196;</div>
            <div>
                <div style="font-weight:600;color:#0d1117;font-size:14px;">Software Requirements Specification (SRS)</div>
                <div style="font-size:13px;color:#57606a;margin-top:2px;">
                    Functional &amp; non-functional requirements, user stories, system constraints, and data models
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#0969da;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

    <a href="{{ url('/docs/overview') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;
                    transition:background .15s;"
             onmouseover="this.style.background='#f6f8fa'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:22px;flex-shrink:0;">&#127962;</div>
            <div>
                <div style="font-weight:600;color:#0d1117;font-size:14px;">Business Overview</div>
                <div style="font-size:13px;color:#57606a;margin-top:2px;">
                    System summary, module features, and high-level workflows
                </div>
            </div>
            <div style="margin-left:auto;font-size:12px;font-weight:600;color:#0969da;flex-shrink:0;">View &rarr;</div>
        </div>
    </a>

</div>

{{-- VERSION BANNER --}}
<div style="background:#f0f4f8;border:1px solid #d0d7de;border-radius:10px;padding:20px 28px;
            display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-weight:700;font-size:14px;color:#0d1117;margin-bottom:4px;">MIAV Dashboard &mdash; Current Version</div>
        <div style="font-size:12.5px;color:#57606a;">
            Laravel 11 &nbsp;&middot;&nbsp; PHP 8.2 &nbsp;&middot;&nbsp; MySQL &nbsp;&middot;&nbsp; Spatie Permissions &nbsp;&middot;&nbsp; AdminLTE
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <span style="background:#fff;border:1px solid #d0d7de;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;color:#57606a;">
            &#127760; 51.21.252.67
        </span>
        <a href="{{ url('/login') }}"
           style="background:#1a3a5c;padding:7px 18px;border-radius:6px;font-size:12px;font-weight:600;
                  color:#fff;text-decoration:none;">
            Open App &rarr;
        </a>
    </div>
</div>

@endsection