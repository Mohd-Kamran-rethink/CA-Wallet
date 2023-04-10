<?php

namespace App\Http\Controllers;

use App\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginView()
    {
        if(session()->has('Manager'))
        {
            return redirect('/dashboard');
        }
        return view('Admin.Auth.Login');
    }
    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $manager = Manager::where('email', '=', $req->email)
            ->first();
        if ($manager) {
            if (Hash::check($req->password, $manager->password)) {
                session()->put('Manager', $manager);
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
        if(session()->has('Manager'))
        {
            session()->remove('Manager');
        }
        return redirect('/');
    }
}
