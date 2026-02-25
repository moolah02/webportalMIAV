@extends('docs.layout')
@section('content')

{{-- HERO --}}
<div style="text-align:center; padding: 40px 0 48px;">
    <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies"
         style="height:80px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.12); margin-bottom:20px;">
    <h1 style="font-size:32px; font-weight:800; color:#1a3a5c; margin-bottom:8px;">
        Revival Technologies
    </h1>
    <p style="font-size:17px; color:#64748b; font-weight:400;">
        MIAV Dashboard — User Manuals &amp; Technical Documentation
    </p>
    <p style="font-size:13px; color:#94a3b8; margin-top:6px;">
        Version 1.0 &nbsp;·&nbsp; February 2026 &nbsp;·&nbsp; All Roles
    </p>
</div>

{{-- USER MANUALS SECTION --}}
<h2 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#64748b;
           margin-bottom:16px;border:none;padding:0;">
    📘 USER MANUALS — SELECT YOUR ROLE
</h2>

<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:18px; margin-bottom:48px;">

    <a href="{{ url('/docs/system') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #dde3ec;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #2563a8;"
             onmouseover="this.style.boxShadow='0 6px 24px rgba(37,99,168,.15)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px; margin-bottom:10px;">⚙️</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#2563a8;margin-bottom:4px;">
                ADMIN / MANAGER
            </div>
            <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:8px;">System Manual</div>
            <p style="font-size:13px;color:#64748b;line-height:1.5;margin:0;">
                Dashboard overview, employee management, assets, field operations, projects,
                clients, reports, and admin settings.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#2563a8;">
                Open manual →
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/testing') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #dde3ec;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #059669;"
             onmouseover="this.style.boxShadow='0 6px 24px rgba(5,150,105,.15)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px; margin-bottom:10px;">🧪</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#059669;margin-bottom:4px;">
                QA / TESTING
            </div>
            <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:8px;">Testing Guide</div>
            <p style="font-size:13px;color:#64748b;line-height:1.5;margin:0;">
                Full QA checklist — authentication, employee management, assets, tickets,
                deployments, reports, and role-based access.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#059669;">
                Open guide →
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/tickets') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #dde3ec;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #7c3aed;"
             onmouseover="this.style.boxShadow='0 6px 24px rgba(124,58,237,.15)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px; margin-bottom:10px;">🎫</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#7c3aed;margin-bottom:4px;">
                SUPPORT TEAM
            </div>
            <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:8px;">Ticket System Guide</div>
            <p style="font-size:13px;color:#64748b;line-height:1.5;margin:0;">
                Ticket types, assignment types (public vs direct), status filtering,
                and POS terminal vs internal ticket handling.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#7c3aed;">
                Open guide →
            </div>
        </div>
    </a>

    <a href="{{ url('/docs/staged-resolution') }}" style="text-decoration:none;">
        <div style="background:#fff;border:1px solid #dde3ec;border-radius:12px;padding:24px;
                    transition:all .2s;cursor:pointer;border-top:4px solid #ea580c;"
             onmouseover="this.style.boxShadow='0 6px 24px rgba(234,88,12,.15)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='none'">
            <div style="font-size:28px; margin-bottom:10px;">🔄</div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#ea580c;margin-bottom:4px;">
                TECHNICIANS
            </div>
            <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:8px;">Staged Resolution</div>
            <p style="font-size:13px;color:#64748b;line-height:1.5;margin:0;">
                Multi-stage ticket resolution, step creation, transfers between employees,
                audit trail tracking, and SLA management.
            </p>
            <div style="margin-top:14px;font-size:13px;font-weight:600;color:#ea580c;">
                Open guide →
            </div>
        </div>
    </a>

</div>

{{-- TECHNICAL SECTION --}}
<h2 style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#64748b;
           margin-bottom:16px;border:none;padding:0;">
    🔧 TECHNICAL &amp; REFERENCE DOCUMENTS
</h2>

<div style="background:#fff;border:1px solid #dde3ec;border-radius:12px;overflow:hidden;margin-bottom:48px;">

    <a href="{{ url('/docs/api') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #f1f5f9;
                    transition:background .15s;"
             onmouseover="this.style.background='#f8fafc'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:24px;flex-shrink:0;">🗂️</div>
            <div>
                <div style="font-weight:600;color:#1e293b;font-size:14px;">API Documentation (Swagger)</div>
                <div style="font-size:13px;color:#64748b;margin-top:2px;">
                    Full REST API reference for all web and future mobile integrations ›
                </div>
            </div>
            <div style="margin-left:auto;font-size:13px;font-weight:600;color:#2563a8;flex-shrink:0;">View →</div>
        </div>
    </a>

    <a href="{{ url('/docs/system') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #f1f5f9;
                    transition:background .15s;"
             onmouseover="this.style.background='#f8fafc'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:24px;flex-shrink:0;">📄</div>
            <div>
                <div style="font-weight:600;color:#1e293b;font-size:14px;">System Documentation</div>
                <div style="font-size:13px;color:#64748b;margin-top:2px;">
                    Full technical reference — data models, workflows, business rules, user roles ›
                </div>
            </div>
            <div style="margin-left:auto;font-size:13px;font-weight:600;color:#2563a8;flex-shrink:0;">View →</div>
        </div>
    </a>

    <a href="{{ url('/docs/overview') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;border-bottom:1px solid #f1f5f9;
                    transition:background .15s;"
             onmouseover="this.style.background='#f8fafc'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:24px;flex-shrink:0;">🏢</div>
            <div>
                <div style="font-weight:600;color:#1e293b;font-size:14px;">Business Overview</div>
                <div style="font-size:13px;color:#64748b;margin-top:2px;">
                    System summary, module features, and high-level workflows ›
                </div>
            </div>
            <div style="margin-left:auto;font-size:13px;font-weight:600;color:#2563a8;flex-shrink:0;">View →</div>
        </div>
    </a>

    <a href="{{ url('/docs/deployment') }}" style="text-decoration:none;">
        <div style="display:flex;align-items:center;gap:16px;padding:18px 24px;
                    transition:background .15s;"
             onmouseover="this.style.background='#f8fafc'"
             onmouseout="this.style.background='transparent'">
            <div style="font-size:24px;flex-shrink:0;">🚀</div>
            <div>
                <div style="font-weight:600;color:#1e293b;font-size:14px;">Deployment Guide</div>
                <div style="font-size:13px;color:#64748b;margin-top:2px;">
                    Dev &amp; production server setup, SSH access, git workflow, and artisan commands ›
                </div>
            </div>
            <div style="margin-left:auto;font-size:13px;font-weight:600;color:#2563a8;flex-shrink:0;">View →</div>
        </div>
    </a>

</div>

{{-- VERSION BANNER --}}
<div style="background:linear-gradient(135deg,#1a3a5c,#2563a8);border-radius:12px;padding:24px 28px;color:#fff;
            display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-weight:700;font-size:15px;margin-bottom:4px;">MIAV Dashboard — Current Version</div>
        <div style="font-size:13px;opacity:.8;">
            Laravel 12 &nbsp;·&nbsp; PHP 8.2 &nbsp;·&nbsp; MySQL &nbsp;·&nbsp; Spatie Permissions &nbsp;·&nbsp; AdminLTE
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <span style="background:rgba(255,255,255,.15);padding:6px 14px;border-radius:99px;font-size:12px;font-weight:600;">
            Production: 51.21.252.67
        </span>
        <a href="{{ url('/login') }}"
           style="background:#e85d04;padding:6px 16px;border-radius:99px;font-size:12px;font-weight:600;
                  color:#fff;text-decoration:none;">
            Open App →
        </a>
    </div>
</div>

@endsection
