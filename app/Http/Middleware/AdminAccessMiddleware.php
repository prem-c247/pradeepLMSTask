<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role_id === User::ROLE_ADMIN) {
            return $next($request);
        }

        // Return a JSON response if the user is not an admin
        return response()->json(['status' => false, 'message' => 'Forbidden! You do not have right access to do this action'], 403);
    }
}
