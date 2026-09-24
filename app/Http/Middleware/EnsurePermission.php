<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * permission:<key>[,<key>...] lets the request through when the account has
 * any of the listed permissions.
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! $request->user() || ! $request->user()->canAccess($permissions)) {
            abort(403);
        }

        return $next($request);
    }
}
