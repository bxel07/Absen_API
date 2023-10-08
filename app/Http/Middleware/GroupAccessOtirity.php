<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GroupAccessOtirity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles )
    {
        if (Auth::check()) {
            $userRole = Auth::user()->role;

            foreach ($roles as $allowedRole) {
                if (trim($allowedRole) === $userRole->name) {
                    return $next($request);
                }
            }
        }

        return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses halaman ini.'], 403);
    }
}
