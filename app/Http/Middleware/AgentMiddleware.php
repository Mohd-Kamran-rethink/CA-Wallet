<?php

namespace App\Http\Middleware;

use Closure;

class AgentMiddleware
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
        if (session()->has('user')&& session('user')->role=="agent") {
            return $next($request);
        }
        else
        {
            return redirect('/');
        }
    }
}
