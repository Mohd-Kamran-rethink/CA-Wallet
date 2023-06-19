<?php

namespace App\Http\Controllers;

use App\Client;
use App\Lead;
use App\MasterAttendance;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leads;

class DashboardController extends Controller
{
    public function view()
    {
        // incosistent clients
        $agent = null;
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }
        $manager = null;
        // ignore ids of   Deposited,Not Interested,Demo id,Id created,Call back
        $ignoredSourceIds = [1, 12, 6, 7, 8];
        $startDate = now()->subDays(4);
        $lastEntry = MasterAttendance::where('user_id', session('user')->id)->whereDate('created_at', now()->format('Y-m-d'))->first();
        $role = session("user")->role;
        $id = session("user")->id;
        $agents = User::where("role", '=', 'agent')->orderBy('id', "desc")->get();
        $managers = User::where("role", '=', 'manager')->orderBy('id', "desc")->get();
        // total leads
        if (session('user')->agent_type != 'Retention') {

            $leads = Lead::when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('agent_id', '=', $agent->id);
                });
            })
                ->when($agent, function ($query) use ($ignoredSourceIds) {
                    $query->where(function ($query) use ($ignoredSourceIds) {
                        $query->whereNotIn('leads.status_id', $ignoredSourceIds);
                        $query->whereNotNull('leads.status_id');
                    });
                })
                ->where('is_approved', '=', 'Yes')->get()->count();
        } else if (session('user')->agent_type == 'Retention') {
            $leads = Lead::where('agent_id', '=', session('user')->id)->get()->count();
        }
        $DuplicateleadsCounts = DB::table('duplicate_leads')
            ->join('sources', 'duplicate_leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'duplicate_leads.agent_id', '=', 'users.id')
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('duplicate_leads.agent_id', '=', $agent->id);
                });
            })
            ->select('duplicate_leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('duplicate_leads.date')
            ->get()->count();
        $Pendingleads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            ->where('leads.agent_id', '=', 'null')
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->get()->count();
        $clientsCount = Client::where('agent_id', '=', session('user')->id)->get()->count();

        return view('Admin.Dashboard.index', compact('clientsCount', 'Pendingleads', 'DuplicateleadsCounts', "agents", 'managers', 'leads', 'lastEntry'));
    }
}
