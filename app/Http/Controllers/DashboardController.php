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
        $clients = Client::with('depositHistories')->whereDoesntHave('depositHistories', function ($query) use ($startDate) {
            $query->whereIn('deposit_histories.id', function ($subquery) {
                $subquery->select(DB::raw('MAX(deposit_histories.id)'))
                    ->from('deposit_histories')
                    ->join('deposits', 'deposits.id', '=', 'deposit_histories.deposit_id')
                    ->whereColumn('deposits.client_id', 'clients.id')
                    ->groupBy('deposits.client_id');
            })->where('deposit_histories.created_at', '>=', $startDate);
        })
        ->when($agent, function ($query, $agent) {
            $query->where(function ($query) use ($agent) {
                $query->where('agent_id', '=', $agent->id);
            });
        })
        ->where('isDeleted','=','No')
        ->select('clients.*', DB::raw('DATEDIFF(NOW(), (SELECT MAX(created_at) FROM deposit_histories WHERE deposit_histories.deposit_id IN (SELECT id FROM deposits WHERE deposits.client_id = clients.id))) AS days_since_last_deposit'))
        ->where(DB::raw('DATEDIFF(NOW(), (SELECT MAX(created_at) FROM deposit_histories WHERE deposit_histories.deposit_id IN (SELECT id FROM deposits WHERE deposits.client_id = clients.id)))'), '>', 0)
        ->orderBy('days_since_last_deposit', 'DESC')
        ->paginate(40);



        
        $lastEntry = MasterAttendance::where('user_id', session('user')->id)->whereDate('created_at', now()->format('Y-m-d'))->first();
        $role = session("user")->role;
        $id = session("user")->id;
        $agents = User::where("role", '=', 'agent')->orderBy('id', "desc")->get();
        $managers = User::where("role", '=', 'manager')->orderBy('id', "desc")->get();
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
        return view('Admin.Dashboard.index', compact("agents", 'managers', 'leads', 'lastEntry', 'clients'));
    }
}
