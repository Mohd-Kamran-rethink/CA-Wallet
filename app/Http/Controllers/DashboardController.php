<?php

namespace App\Http\Controllers;

use App\Lead;
use App\User;
use Illuminate\Http\Request;
use Leads;

class DashboardController extends Controller
{
    public function view()
    {
        $role=session("user")->role;
        $id=session("user")->id;
        $agents = User::where("role", '=', 'agent')->orderBy('id', "desc")->get();
        $managers = User::where("role", '=', 'manager')->orderBy('id', "desc")->get();
        $leads=Lead::where($role=='manager'?"manager_id":"agent_id",'=',$id)->get()->count();
        return view('Admin.Dashboard.index',compact("agents",'managers','leads'));
    }
}
