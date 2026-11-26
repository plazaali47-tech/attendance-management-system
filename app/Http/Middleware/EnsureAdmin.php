<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Only admin accounts may open the page (used on Manage Users).
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user() && $request->user()->is_admin, 403, 'Only an admin can manage user accounts.');

        return $next($request);
    }
}
