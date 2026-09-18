<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ctrl+K search across the portal: terminals, visits, job assignments, tickets,
 * clients, projects and employees, matched by ID, code or name. A group is only
 * searched when the user may open that kind of page (same permissions as its routes).
 */
class SearchController extends Controller
{
    private const PER_GROUP = 5;

    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2 || mb_strlen($q) > 100) {
            return response()->json(['results' => []]);
        }

        $user = $request->user();
        $can  = fn (array $perms) => collect($perms)->contains(fn ($p) => $user->hasPermission($p));
        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
        $num  = ctype_digit(ltrim($q, '#')) ? (int) ltrim($q, '#') : null;
        $out  = [];

        if ($can(['view_terminals', 'manage_terminals', 'all'])) {
            $rows = DB::table('pos_terminals')
                ->where(fn ($w) => $w->where('terminal_id', 'like', $like)
                    ->orWhere('merchant_name', 'like', $like)
                    ->orWhere('serial_number', 'like', $like))
                ->orderByRaw('terminal_id = ? desc', [$q])->orderBy('terminal_id')
                ->limit(self::PER_GROUP)
                ->get(['id', 'terminal_id', 'merchant_name', 'city', 'source']);
            foreach ($rows as $r) {
                $out[] = $this->item('Terminals', 'card', $r->terminal_id,
                    [$r->merchant_name, $r->city, $r->source === 'field_discovery' ? 'Discovered on site' : null],
                    route('pos-terminals.show', $r->id));
            }
        }

        if ($can(['view_visits', 'all'])) {
            $rows = DB::table('visits as v')
                ->leftJoin('employees as e', 'e.id', '=', 'v.employee_id')
                ->leftJoin('visit_terminals as vt', 'vt.visit_id', '=', 'v.id')
                ->leftJoin('pos_terminals as pt', 'pt.id', '=', 'vt.terminal_id')
                ->where(function ($w) use ($like, $num) {
                    $w->where('v.merchant_name', 'like', $like)->orWhere('pt.terminal_id', 'like', $like);
                    if ($num !== null) {
                        $w->orWhere('v.id', $num);
                    }
                })
                ->orderByDesc('v.completed_at')->orderByDesc('v.id')
                ->limit(self::PER_GROUP * 2)
                ->get(['v.id', 'v.merchant_name', 'v.completed_at', 'e.first_name', 'e.last_name', 'pt.terminal_id'])
                ->unique('id')->take(self::PER_GROUP);
            foreach ($rows as $r) {
                $out[] = $this->item('Visits', 'pin', 'Visit #' . $r->id . ($r->merchant_name ? ' · ' . trim($r->merchant_name) : ''),
                    [$r->completed_at ? Carbon::parse($r->completed_at)->format('j M Y') : null, trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')), $r->terminal_id],
                    route('visits.show', $r->id));
            }
        }

        if ($can(['view_jobs', 'manage_jobs', 'assign_jobs', 'all'])) {
            $rows = DB::table('job_assignments as j')
                ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
                ->where('j.assignment_id', 'like', $like)
                ->orderByDesc('j.id')->limit(self::PER_GROUP)
                ->get(['j.id', 'j.assignment_id', 'j.status', 'j.scheduled_date', 'c.company_name']);
            foreach ($rows as $r) {
                $out[] = $this->item('Job Assignments', 'clipboard', $r->assignment_id,
                    [$r->company_name, $r->status ? ucwords(str_replace('_', ' ', $r->status)) : null, $r->scheduled_date ? Carbon::parse($r->scheduled_date)->format('j M Y') : null],
                    route('jobs.show', $r->id));
            }
        }

        if ($can(['view_tickets', 'manage_tickets', 'all'])) {
            $rows = DB::table('tickets')
                ->where(function ($w) use ($like, $num) {
                    $w->where('ticket_id', 'like', $like)->orWhere('title', 'like', $like);
                    if ($num !== null) {
                        $w->orWhere('id', $num);
                    }
                })
                ->orderByDesc('id')->limit(self::PER_GROUP)
                ->get(['id', 'ticket_id', 'title', 'status', 'priority']);
            foreach ($rows as $r) {
                $out[] = $this->item('Tickets', 'ticket', $r->title ?: ($r->ticket_id ?: 'Ticket #' . $r->id),
                    [$r->ticket_id, $r->status ? ucwords(str_replace('_', ' ', $r->status)) : null, $r->priority ? ucfirst($r->priority) . ' priority' : null],
                    route('tickets.show', $r->id));
            }
        }

        if ($can(['view_clients', 'manage_clients', 'all'])) {
            $rows = DB::table('clients')
                ->where(fn ($w) => $w->where('company_name', 'like', $like)->orWhere('client_code', 'like', $like))
                ->orderBy('company_name')->limit(self::PER_GROUP)
                ->get(['id', 'company_name', 'client_code', 'city']);
            foreach ($rows as $r) {
                $out[] = $this->item('Clients', 'building', $r->company_name, [$r->client_code, $r->city], route('clients.show', $r->id));
            }
        }

        if ($can(['view_projects', 'manage_projects', 'all'])) {
            $rows = DB::table('projects')
                ->where(fn ($w) => $w->where('project_name', 'like', $like)->orWhere('project_code', 'like', $like))
                ->orderByDesc('id')->limit(self::PER_GROUP)
                ->get(['id', 'project_name', 'project_code', 'status']);
            foreach ($rows as $r) {
                $out[] = $this->item('Projects', 'folder', $r->project_name ?: $r->project_code,
                    [$r->project_code, $r->status ? ucfirst($r->status) : null], route('projects.show', $r->id));
            }
        }

        if ($can(['view_employees', 'manage_employees', 'all'])) {
            $rows = DB::table('employees')
                ->where(fn ($w) => $w->whereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) LIKE ?", [$like])
                    ->orWhere('email', 'like', $like)
                    ->orWhere('employee_number', 'like', $like))
                ->orderBy('first_name')->limit(self::PER_GROUP)
                ->get(['id', 'first_name', 'last_name', 'email', 'employee_number']);
            foreach ($rows as $r) {
                $out[] = $this->item('Employees', 'user', trim($r->first_name . ' ' . $r->last_name) ?: $r->email,
                    [$r->email, $r->employee_number], route('employees.show', $r->id));
            }
        }

        return response()->json(['results' => $out]);
    }

    private function item(string $group, string $icon, ?string $title, array $sub, string $url): array
    {
        return [
            'group' => $group,
            'icon'  => $icon,
            'title' => (string) $title,
            'sub'   => implode(' · ', array_filter(array_map(fn ($p) => is_string($p) ? trim($p) : $p, $sub), fn ($p) => $p !== null && $p !== '')),
            'url'   => $url,
        ];
    }
}
