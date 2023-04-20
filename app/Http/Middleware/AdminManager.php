<?php

namespace App\Http\Middleware;

use Closure;

class AdminManager
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
        if (session()->has('user') && session('user')->role=="manager" && session('user')->is_admin=="Yes") {
        
            return $next($request);
        }
        else
        {
            return redirect()->back();
        }
    }
}
