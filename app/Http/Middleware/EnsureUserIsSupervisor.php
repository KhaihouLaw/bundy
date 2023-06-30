<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class EnsureUserIsSupervisor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $is_supervisor = Auth::user()->isSupervisor();
        $is_approver = Auth::user()->isApprover();
        if ($is_supervisor || $is_approver) {
            return $next($request);
        }
        return redirect('dashboard');
    }
}
