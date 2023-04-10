<?php

namespace App\Http\Middleware;

use Closure;

class ValidateUsers
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
        if (session()->has('Manager')) {
            return $next($request);
        }
        else
        {
            return redirect('/');
        }
    }
}
