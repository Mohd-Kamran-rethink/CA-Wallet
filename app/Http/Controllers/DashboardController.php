<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Leads;

class DashboardController extends Controller
{
    public function view()
    {
        $agents = User::where("role", '=', 'agent')->orderBy('id', "desc")->get();
        $managers = User::where("role", '=', 'manager')->orderBy('id', "desc")->get();
        return view('Admin.Dashboard.index',compact("agents",'managers'));
    }
}
