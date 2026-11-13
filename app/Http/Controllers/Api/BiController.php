<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTerminal;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * A read-only feed for a reporting tool such as Power BI.
 *
 * Why this exists rather than a database login: the database holds merchant
 * contacts, phone numbers and the movements of named technicians, and it has
 * to stay reachable only from the server itself. This hands out flat tables
 * over the same HTTPS the portal already uses - so no database port is opened,
 * the token can be withdrawn the moment it is no longer wanted, and the
 * columns that leave are chosen here rather than being "whatever is in the
 * table".
 *
 * Everything is one row per record and already joined, because a reporting
 * tool should not have to know how this system stores things.
 */
class BiController extends Controller
{
    /** Never more than this many rows in one answer, whatever is asked for. */
    private const MAX_PER_PAGE = 1000;

    private const DEFAULT_PER_PAGE = 500;

    /**
     * A dataset as CSV, streamed straight from the database.
     *
     * Holding the whole thing in memory first is what makes an export fall
     * over on the day the data finally gets big, so it is never assembled.
     */
    private function csv($query, string $orderBy): StreamedResponse
    {
        $name = str_replace('.', '_', explode('.', $orderBy)[0]);
        $file = 'miav_'.$name.'_'.now()->format('Y-m-d').'.csv';

        return response()->stream(function () use ($query, $orderBy) {
            $out = fopen('php://output', 'w');
            fwrite($out, "﻿");   // so Excel reads accents properly

            $wroteHeader = false;
            foreach ($query->orderBy($orderBy)->cursor() as $row) {
                $row = (array) $row;
                if (! $wroteHeader) {
                    fputcsv($out, array_keys($row));
                    $wroteHeader = true;
                }
                fputcsv($out, array_values($row));
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$file}\"",
        ]);
    }

    /**
     * The client this token is allowed to see, or null for all of them.
     *
     * A token minted for an outside analyst carries `bi:client:7` alongside
     * `bi:read`, and then every dataset below is narrowed to that client.
     * Without it the feed returns the whole system, which is right for our
     * own reporting and wrong for anyone else's.
     */
    private function clientScope(Request $request): ?int
    {
        $token = $request->user()?->currentAccessToken();
        foreach ((array) ($token->abilities ?? []) as $ability) {
            if (str_starts_with((string) $ability, 'bi:client:')) {
                return (int) substr($ability, strlen('bi:client:'));
            }
        }

        return null;
    }

    /** What is on offer, so the feed can be explored without documentation. */
    public function index(Request $request): JsonResponse
    {
        $base = rtrim(url('/api/bi'), '/');

        $client = $this->clientScope($request);
        $clientName = $client
            ? DB::table('clients')->where('id', $client)->value('company_name')
            : null;

        return response()->json([
            'name'     => 'MIAV reporting feed',
            'scope'    => $client
                ? "Limited to {$clientName}"
                : 'All clients',
            'datasets' => [
                ['name' => 'terminals', 'url' => "{$base}/terminals", 'description' => 'Every POS terminal and its current state'],
                ['name' => 'visits',    'url' => "{$base}/visits",    'description' => 'One row per visit, with the technician and client'],
                ['name' => 'findings',  'url' => "{$base}/findings",  'description' => 'One row per device examined on a visit'],
                ['name' => 'tickets',   'url' => "{$base}/tickets",   'description' => 'Support tickets'],
                ['name' => 'clients',   'url' => "{$base}/clients",   'description' => 'Clients'],
                ['name' => 'technicians', 'url' => "{$base}/technicians", 'description' => 'Who the field staff are'],
            ],
            'paging' => [
                'parameters'  => ['page', 'per_page (max '.self::MAX_PER_PAGE.')', 'since (ISO date, rows changed on or after)'],
                'how_to_read' => 'Follow "next_page" until it is null.',
            ],
            'taking_a_copy' => [
                'how'  => 'Add ?format=csv to any dataset for the whole thing in one file, unpaged.',
                'note' => 'Combine with ?since=YYYY-MM-DD to copy only what has changed.',
            ],
        ]);
    }

    public function terminals(Request $request): Response
    {
        // Not aliased: EFFECTIVE_STATUS_SQL names pos_terminals outright, so
        // an alias would leave it pointing at a table that is not in the query.
        $query = DB::table('pos_terminals')
            ->leftJoin('clients as c', 'c.id', '=', 'pos_terminals.client_id')
            ->select([
                'pos_terminals.id',
                'pos_terminals.terminal_id as terminal_code',
                'pos_terminals.merchant_name',
                'pos_terminals.region',
                'pos_terminals.city',
                'pos_terminals.province',
                'pos_terminals.area',
                'pos_terminals.business_type',
                'pos_terminals.terminal_model',
                'pos_terminals.serial_number',
                'pos_terminals.source as how_it_was_added',
                'pos_terminals.installation_date',
                'pos_terminals.last_service_date',
                'pos_terminals.created_at',
                'pos_terminals.updated_at',
                'c.company_name as client',
            ])
            ->selectRaw(PosTerminal::EFFECTIVE_STATUS_SQL.' as status')
            // Whether anyone has ever been to it - the question the office
            // asks most and the one a plain column cannot answer.
            ->selectRaw('EXISTS (SELECT 1 FROM visit_terminals vt WHERE vt.terminal_id = pos_terminals.id) as has_been_visited');

        if ($client = $this->clientScope($request)) {
            $query->where('pos_terminals.client_id', $client);
        }

        return $this->page($request, $query, 'pos_terminals.id');
    }

    public function visits(Request $request): Response
    {
        $query = DB::table('visits as v')
            ->leftJoin('employees as e', 'e.id', '=', 'v.employee_id')
            ->select([
                'v.id',
                'v.completed_at',
                'v.merchant_name',
                'v.site_visited',
                'v.non_visit_reason as reason_not_visited',
                'v.non_activation_reason as reason_device_not_active',
                'v.escalation_owner_name as escalation_owner',
                'v.visit_summary',
                'v.action_points as issues_found',
                'v.terminal_comments as corrective_action',
                'v.created_at',
                'v.updated_at',
            ])
            ->selectRaw("TRIM(CONCAT(COALESCE(e.first_name, ''), ' ', COALESCE(e.last_name, ''))) as technician")
            ->selectRaw('e.employee_number as technician_number');

        // A visit carries no client of its own - it belongs to one through the
        // terminals it was about, so the scope is applied there.
        if ($client = $this->clientScope($request)) {
            $query->whereExists(function ($q) use ($client) {
                $q->selectRaw('1')
                  ->from('visit_terminals as svt')
                  ->join('pos_terminals as sp', 'sp.id', '=', 'svt.terminal_id')
                  ->whereColumn('svt.visit_id', 'v.id')
                  ->where('sp.client_id', $client);
            });
        }

        return $this->page($request, $query, 'v.id');
    }

    public function findings(Request $request): Response
    {
        $query = DB::table('visit_terminals as vt')
            ->leftJoin('visits as v', 'v.id', '=', 'vt.visit_id')
            ->leftJoin('pos_terminals as t', 't.id', '=', 'vt.terminal_id')
            ->leftJoin('clients as c', 'c.id', '=', 't.client_id')
            ->select([
                'vt.id',
                'vt.visit_id',
                'vt.terminal_id',
                't.terminal_id as terminal_code',
                't.merchant_name',
                't.region',
                'c.company_name as client',
                'vt.status as status_on_visit',
                'vt.state as device_state',
                'vt.condition',
                'vt.serial_number',
                'vt.terminal_model',
                'vt.comments',
                'v.completed_at as visit_completed_at',
                'vt.created_at',
                'vt.updated_at',
            ]);

        if ($client = $this->clientScope($request)) {
            $query->where('t.client_id', $client);
        }

        return $this->page($request, $query, 'vt.id');
    }

    public function tickets(Request $request): Response
    {
        $query = DB::table('tickets as k')
            ->leftJoin('pos_terminals as t', 't.id', '=', 'k.pos_terminal_id')
            ->leftJoin('clients as c', 'c.id', '=', 'k.client_id')
            ->select([
                'k.id',
                'k.ticket_id as ticket_code',
                'k.title',
                'k.issue_type',
                'k.priority',
                'k.status',
                't.terminal_id as terminal_code',
                't.merchant_name',
                'c.company_name as client',
                'k.created_at',
                'k.resolved_at',
                'k.updated_at',
            ]);

        if ($client = $this->clientScope($request)) {
            $query->where('k.client_id', $client);
        }

        return $this->page($request, $query, 'k.id');
    }

    public function clients(Request $request): Response
    {
        $query = DB::table('clients as c')->select([
            'c.id',
            'c.company_name',
            'c.status',
            'c.contract_start_date',
            'c.contract_end_date',
            'c.created_at',
            'c.updated_at',
        ]);

        if ($client = $this->clientScope($request)) {
            $query->where('c.id', $client);
        }

        return $this->page($request, $query, 'c.id');
    }

    /**
     * Field staff, by name and number only.
     *
     * No address, no personal telephone number, no email: a reporting tool
     * needs to say who did the work, not how to contact them at home.
     */
    public function technicians(Request $request): Response
    {
        $query = DB::table('employees as e')
            ->leftJoin('roles as r', 'r.id', '=', 'e.role_id')
            ->select([
                'e.id',
                'e.employee_number',
                'e.created_at',
                'e.updated_at',
                'r.display_name as role',
            ])
            ->selectRaw("TRIM(CONCAT(COALESCE(e.first_name, ''), ' ', COALESCE(e.last_name, ''))) as name");

        // Only the people who actually worked on this client's sites. The
        // full staff list is not a client's business.
        if ($client = $this->clientScope($request)) {
            $query->whereExists(function ($q) use ($client) {
                $q->selectRaw('1')
                  ->from('visits as sv')
                  ->join('visit_terminals as svt', 'svt.visit_id', '=', 'sv.id')
                  ->join('pos_terminals as sp', 'sp.id', '=', 'svt.terminal_id')
                  ->whereColumn('sv.employee_id', 'e.id')
                  ->where('sp.client_id', $client);
            });
        }

        return $this->page($request, $query, 'e.id');
    }

    /**
     * One page of a dataset, in the shape Power BI can loop over.
     *
     * `since` lets a refresh ask only for what has changed, so a daily pull
     * does not drag the whole history across every morning.
     */
    private function page(Request $request, $query, string $orderBy): Response
    {
        $validated = $request->validate([
            'page'     => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:'.self::MAX_PER_PAGE,
            'since'    => 'sometimes|date',
            'format'   => 'sometimes|in:json,csv',
        ]);

        $table = explode(' as ', $orderBy)[0];
        $alias = str_contains($orderBy, '.') ? explode('.', $orderBy)[0] : $table;

        if (!empty($validated['since'])) {
            $query->where($alias.'.updated_at', '>=', $validated['since']);
        }

        // A whole dataset as one CSV, for anyone keeping their own copy rather
        // than querying live. Streamed a row at a time and deliberately not
        // paged - a copy that stops at page one is not a copy.
        if (($validated['format'] ?? 'json') === 'csv') {
            return $this->csv($query, $orderBy);
        }

        $perPage = (int) ($validated['per_page'] ?? self::DEFAULT_PER_PAGE);
        $page    = (int) ($validated['page'] ?? 1);

        $total = (clone $query)->count();
        $rows  = $query->orderBy($orderBy)
                       ->forPage($page, $perPage)
                       ->get();

        $lastPage = max(1, (int) ceil($total / $perPage));
        $next = $page < $lastPage
            ? $request->fullUrlWithQuery(['page' => $page + 1, 'per_page' => $perPage])
            : null;

        return response()->json([
            'data'      => $rows,
            'page'      => $page,
            'per_page'  => $perPage,
            'total'     => $total,
            'last_page' => $lastPage,
            'next_page' => $next,
        ]);
    }
}
