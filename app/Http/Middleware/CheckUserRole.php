<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (Auth::check()) {
            $userRole = Auth::user()->role;

            if ($userRole->name === $role) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'], 403);
    }
}
