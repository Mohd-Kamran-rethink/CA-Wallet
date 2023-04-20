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
        if (session()->has('user')) {
            if((session('user')->role=='manager') && (session('user')->is_admin=='Yes'))
            {
                return $next($request);
            }
            else
            {
                session()->remove('user');
                return redirect('/')->with(['msg-error-username'=>'You dont have permission to login.']);
            }

        }
        else
        {
            return redirect('/');
        }
    }
}
