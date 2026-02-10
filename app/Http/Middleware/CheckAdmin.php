<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Redirect with notification
            return redirect('/')->with('error', 'Unauthorized access!');
        }

        return $next($request);
    }
}
