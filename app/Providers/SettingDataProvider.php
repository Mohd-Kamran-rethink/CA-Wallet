<?php

namespace App\Providers;

use App\Client;
use App\Services\AttendanceService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Setting;
use App\User;
use Illuminate\Support\Facades\DB;

class SettingDataProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('Admin.index', function ($view) {
            // for break work 
            $userId = session('user')->id;
            $attendanceService = app(AttendanceService::class);
            $onBreak = $attendanceService->isOnBreak($userId);
            $settings = Setting::first();
            $user = session('user');
            $userData = null;
            if ($user) {
                $userData = User::find($user->id);
            }
            $ignoredSourceIds = [1, 12, 6, 7, 8];
            // total sidebar couts
            $agentsCount=User::where('role','=','agent')->get()->count();
            $managersCount=User::where('role','=','manager')->get()->count();
            // leads count
            $agent=null;
            if (session('user')->role == "agent") {
                $agent = User::find(session('user')->id);
            }
            $leadsCount= DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            // if session has agesnt show all his assigned leads
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
                });
            })
           ->when($agent, function ($query) use ($ignoredSourceIds) {
                $query->where(function ($query) use ($ignoredSourceIds) {
                    $query->whereNotIn('leads.status_id', $ignoredSourceIds);
                    $query->whereNotNull('leads.status_id');
                });
            })
            ->where('is_approved', '=', 'Yes')
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->get()->count();
            
            // duplicate leads
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
            // pending leads
            $Pendingleads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            // filter by general terms
            ->where('leads.agent_id','=','null')
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->get()->count();
            // demo id
            $Demostatus_id = 18;
            $idCreated_id = 7;
            $CalBack_id = 8;
            $DemoIdleads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->join('users', 'leads.agent_id', '=', 'users.id')
                // if session has agesnt show all his assigned leads
                ->when($agent, function ($query, $agent) {
                    $query->where(function ($query) use ($agent) {
                        $query->where('leads.agent_id', '=', $agent->id);
                    });
                })
                ->where('leads.status_id', '=', $Demostatus_id)
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->orderByDesc('leads.date')
                ->get()->count();

            $idCreatedleads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->join('users', 'leads.agent_id', '=', 'users.id')
                // if session has agesnt show all his assigned leads
                ->when($agent, function ($query, $agent) {
                    $query->where(function ($query) use ($agent) {
                        $query->where('leads.agent_id', '=', $agent->id);
                    });
                })
                
                ->where('leads.status_id', '=', $idCreated_id)
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->orderByDesc('leads.date')
                ->get()->count();
            $Callbackleads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->join('users', 'leads.agent_id', '=', 'users.id')
                // if session has agesnt show all his assigned leads
                ->when($agent, function ($query, $agent) {
                    $query->where(function ($query) use ($agent) {
                        $query->where('leads.agent_id', '=', $agent->id);
                    });
                })
               
                ->where('leads.status_id', '=', $CalBack_id)
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->orderByDesc('leads.date')
                ->get()->count();
            // leads fo approval
            $NonApproveleads = DB::table('leads')
                ->join('sources', 'leads.source_id', '=', 'sources.id')
                ->join('users', 'leads.agent_id', '=', 'users.id')
                ->where('is_approved', '=', 'No')
                ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
                ->orderByDesc('leads.date')
                ->get()->count();
            $clientsCount=Client::when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('agent_id', '=', $agent->id);
                });
            })
            ->get()->count();
            
            // couts end
            $view->with([
                'settings' => $settings,
                'userData' => $userData,
                'onBreak'=> $onBreak??0,
                'agentsCount'=>$agentsCount,
                'managersCount'=>$managersCount??0,
                'leadsCount'=>$leadsCount??0,
                'DuplicateleadsCounts'=>$DuplicateleadsCounts??0,
                'Pendingleads'=>$Pendingleads??0,
                'DemoIdleads'=>$DemoIdleads??0,
                'idCreatedleads'=>$idCreatedleads??0,
                'Callbackleads'=>$Callbackleads??0,
                'NonApproveleads'=>$NonApproveleads??0,
                'clientsCount'=>$clientsCount??0,
            ]);
        });
    }
}
