<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\Source;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LeadsController extends Controller
{

    public function list(Request $req)
    {
        $statuses = LeadStatusOption::get();
        $agent=null;
        if (session('user')->role=="agent") {
            $agent = User::find(session('user')->id);
        }
            
        $searchTerm = $req->query('table_search');
        $Filterstatus = $req->query('status');

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
            ->leftjoin('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
                });
            })
            ->when($Filterstatus, function ($query, $Filterstatus) {
                $query->where(function ($query) use ($Filterstatus) {
                    $query->where('lead_statuses.name', '=', $Filterstatus);
                });
            })
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->select('leads.*','lead_statuses.name as status_name','sources.name as source_name', 'users.name as agent_name')
            ->paginate(10);
        return view('Admin.Leads.list', compact('leads', 'searchTerm', 'Filterstatus', 'statuses'));
    }

    public function importView()
    {
        return view('Admin.Leads.import');
    }
    public function import(Request $req)
    {
        $req->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $import = new LeadsImport;
        $data = Excel::toArray($import, $req->excel_file);

        $columns = array_map('strtolower', $data[0][0]);
        array_shift($data[0]);
        $sources = Source::pluck('id', 'name');
        $agents = User::where('role', '=', 'agent')->pluck('id', 'name');
        $manager = User::where('role', '=', 'manager')->where('email', '=', session('user')->email)->first();
        $insertData = [];
        foreach ($data[0] as $row) {
            $sourceId = $sources->get($row[array_search('sources', $columns)]);
            $agentId = $agents->get($row[array_search('agent', $columns)]);
            $insertData[] = [
                'name' => $row[array_search('name', $columns)],
                'number' => $row[array_search('number', $columns)],
                'language' => $row[array_search('language', $columns)],
                'date' => date('d-m-Y', strtotime($row[array_search('date', $columns)])),
                'idName' => $row[array_search('id name', $columns)],
                'source_id' => $sourceId,
                'agent_id' => $agentId,
                'manager_id' => $manager->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $result = DB::table('leads')->insert($insertData);
        if ($result) {
            return redirect('/leads')->with(['msg-success' => 'Excel file imported successfully.']);
        } else {
            return redirect('/leads')->with(['msg-error' => 'Something went wrong.']);
        }
    }
    public function submitStatus(Request $req)
    {
        if ($req->ajax()) {
            $lead_id = $req->leadId;
            $statusValue = $req->status;
            $date = $req->date;
            $remark = $req->remark;
            if (($statusValue == "Follow Up" || $statusValue == "Busy") && $date == '') {
                return ['dateError' => "Date is mandatory"];
            }
            else
            {
                $lead=Lead::find($lead_id);
                $lead_status =new  LeadStatus();
                $lead_status->lead_id=$lead_id; 
                $lead_status->name=$statusValue; 
                $lead_status->remark=$remark; 
                $lead_status->followup_date=$date??null;
                $lead_status->save();
                $lead->lead_status_id=$lead_status->id;
                $result =$lead->update();
                if($result)
                {
                    return ['msg-success' => "Status has been changed"];
                }
                else
                {
                    return ['msg-error' => "Somthing went wrong"];

                }

            }

        }
    }
}
