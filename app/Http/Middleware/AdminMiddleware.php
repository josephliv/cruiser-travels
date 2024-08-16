<?php

namespace App\Http\Middleware;
use Closure;

use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (!Auth::user()->is_admin) {
        // Redirect non-admin users
        return redirect('dashboard')->with('error', 'you must be an admin user!');
    }
        return $next($request);
    }
}
