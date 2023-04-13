<?php

namespace App\Http\Middleware;

use Closure;

class ValidateManager
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
        if (session()->has('user')&& session('user')->role=="manager") {
            return $next($request);
        }
        else
        {
            return redirect('/');
        }
    }
}
