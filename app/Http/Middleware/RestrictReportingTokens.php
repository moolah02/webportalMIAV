<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a reporting token inside the reporting feed.
 *
 * Sanctum's `abilities` middleware only constrains the routes that ask for
 * it, and the tablet API asks only for `auth:sanctum` - so a token minted
 * with `bi:read` authenticated perfectly well against every other endpoint
 * and could create a terminal. Handing that to an outside analyst while
 * calling it read-only would have been untrue.
 *
 * This runs across the whole API: a token that carries `bi:read` and not `*`
 * may reach /api/bi and nothing else. Tablet tokens hold `*` and are
 * unaffected, as are session-authenticated portal users.
 */
class RestrictReportingTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        // A session on the portal has a stand-in token with no abilities.
        if (! $token || $token instanceof TransientToken) {
            return $next($request);
        }

        $abilities = (array) ($token->abilities ?? []);

        $reportingOnly = in_array('bi:read', $abilities, true)
            && ! in_array('*', $abilities, true);

        if ($reportingOnly && ! $request->is('api/bi', 'api/bi/*')) {
            return response()->json([
                'success' => false,
                'message' => 'This token may only read the reporting feed at /api/bi.',
            ], 403);
        }

        return $next($request);
    }
}
