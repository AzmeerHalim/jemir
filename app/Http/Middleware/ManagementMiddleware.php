<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagementMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403);
        }

        $allowedRoles = [
            UserRole::Coordinator,
            UserRole::Committee,
            UserRole::StudentAffairs,
        ];

        if (! in_array(auth()->user()->role, $allowedRoles, true)) {
            abort(403, 'Management access required.');
        }

        return $next($request);
    }
}
