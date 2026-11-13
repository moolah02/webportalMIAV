<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTerminal;
use Illuminate\Http\JsonResponse;
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

    /** What is on offer, so the feed can be explored without documentation. */
    public function index(Request $request): JsonResponse
    {
        $base = rtrim(url('/api/bi'), '/');

        return response()->json([
            'name'     => 'MIAV reporting feed',
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
        ]);
    }

    public function terminals(Request $request): JsonResponse
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

        return $this->page($request, $query, 'pos_terminals.id');
    }

    public function visits(Request $request): JsonResponse
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

        return $this->page($request, $query, 'v.id');
    }

    public function findings(Request $request): JsonResponse
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

        return $this->page($request, $query, 'vt.id');
    }

    public function tickets(Request $request): JsonResponse
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

        return $this->page($request, $query, 'k.id');
    }

    public function clients(Request $request): JsonResponse
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

        return $this->page($request, $query, 'c.id');
    }

    /**
     * Field staff, by name and number only.
     *
     * No address, no personal telephone number, no email: a reporting tool
     * needs to say who did the work, not how to contact them at home.
     */
    public function technicians(Request $request): JsonResponse
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

        return $this->page($request, $query, 'e.id');
    }

    /**
     * One page of a dataset, in the shape Power BI can loop over.
     *
     * `since` lets a refresh ask only for what has changed, so a daily pull
     * does not drag the whole history across every morning.
     */
    private function page(Request $request, $query, string $orderBy): JsonResponse
    {
        $validated = $request->validate([
            'page'     => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:'.self::MAX_PER_PAGE,
            'since'    => 'sometimes|date',
        ]);

        $table = explode(' as ', $orderBy)[0];
        $alias = str_contains($orderBy, '.') ? explode('.', $orderBy)[0] : $table;

        if (!empty($validated['since'])) {
            $query->where($alias.'.updated_at', '>=', $validated['since']);
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
