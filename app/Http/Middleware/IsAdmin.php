<?php

namespace App\Http\Middleware;
use Closure;
use Auth;
use App\Models\Role;
use App\Models\User;
use DB;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::user()->hasRole('Admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}