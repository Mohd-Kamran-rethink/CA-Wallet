<?php

namespace App\Http\Controllers;

use App\Lead;
use App\LeadStatusOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetentionController extends Controller
{
    public function list(Request $req)
    {
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->leftJoin('users', 'lead_statuses.agent_id', '=', 'users.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name', 'users.name as agentName')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();
        if (session('user')->role == 'agent' && session('user')->agent_type == 'Normal') {
            $threeDaysAgo = Carbon::now()->subDays(3)->toDateString();
            $sixDaysAgo = Carbon::now()->subDays(6)->toDateString();
            
            $leads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->leftJoin('users', 'leads.agent_id', '=', 'users.id')
                ->where('leads.current_status', '=', 'Deposited')
                ->where('leads.lead_type', '=', 'Retention')
                ->whereDate('leads.updated_at', '>=', $sixDaysAgo)
                ->whereDate('leads.updated_at', '<=', $threeDaysAgo)
                ->where('agent_id', '=', session('user')->id)
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->paginate(10);
        }
        else if(session('user')->role == 'agent' && session('user')->agent_type == 'Retention')
        {
            $leads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
                ->where('leads.current_status', '=', 'Deposited')
                ->where('agent_id', '=', session('user')->id)
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->paginate(10);
        }
        return view('Admin.Retention.retentionLeads', compact('statuses', 'leads', 'searchTerm', 'leads_status_history'));
    }
}
