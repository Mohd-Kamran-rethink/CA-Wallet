<?php

namespace App\Http\Controllers;

use App\Lead;
use App\MasterAttendance;
use App\User;
use Illuminate\Http\Request;
use Leads;

class DashboardController extends Controller
{
    public function view()
    {
        $lastEntry = MasterAttendance::where('user_id', session('user')->id)->whereDate('created_at', now()->format('Y-m-d') )->first();
        $role=session("user")->role;
        $id=session("user")->id;
        $agents = User::where("role", '=', 'agent')->orderBy('id', "desc")->get();
        $managers = User::where("role", '=', 'manager')->orderBy('id', "desc")->get();
        $leads=Lead::where($role=='manager'?"manager_id":"agent_id",'=',$id)->where('is_approved','=','Yes')->get()->count();
        return view('Admin.Dashboard.index',compact("agents",'managers','leads','lastEntry'));
    }
}
