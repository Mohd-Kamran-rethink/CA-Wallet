<?php

namespace App\Http\Controllers;

use App\Manager;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginView()
    {
        if(session()->has('user'))
        {
            return redirect('/dashboard');
        }
        else
        {
            $settings=Setting::first();
            return view('Admin.Auth.Login',compact('settings'));
        }
    }

    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', '=', $req->email)->first();
        if ($user) {
            if (Hash::check($req->password, $user->password)) {
                session()->put('user', $user);
                return redirect('/dashboard');
            } else {
                return redirect('/')->with(['msg-error-password' => 'Invalid password']);
            }
        }
        else
        {
            return redirect('/')->with(['msg-error-username' => "Email is not registered with us"]);
        }
    }
    public function logout()
    {
        if(session()->has('user'))
        {
            session()->remove('user');
        }
        return redirect('/');
    }
}
