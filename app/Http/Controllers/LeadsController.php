<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\Source;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LeadsController extends Controller
{

    public function list(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();

        $agent = null;
        $manager = null;
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }

        if (session('user')->role == "manager") {
            $manager = User::find(session('user')->id);
        }
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $Filterstatus = $req->query('status');
        $FilterAgent = $req->query('agent_id');

        // get details of the status from status id 
        $currentStatus = LeadStatusOption::find($Filterstatus);

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
            // if session has manager show all the leads added by thi smanager
            ->when($manager, function ($query, $manager) {
                $query->where(function ($query) use ($manager) {
                    $query->where('leads.manager_id', '=', $manager->id);
                });
            })
            // if session has agesnt show all his assigned leads
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
                });
            })
            // filter by agent
            ->when($FilterAgent, function ($query, $FilterAgent) {
                $query->where(function ($query) use ($FilterAgent) {
                    $query->where('leads.agent_id', '=', $FilterAgent);
                });
            })
            // filter by status
            ->when($currentStatus, function ($query, $currentStatus) {
                $query->where(function ($query) use ($currentStatus) {
                    $query->Where('leads.current_status', '=', $currentStatus->name);
                });
            })
            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->paginate(45);


        return view('Admin.Leads.list', compact('leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }

    public function importView()
    {
        return view('Admin.Leads.import');
    }
    public function import(Request $req)
    {
        $validatedData = $req->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $req->file('excel_file');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->path());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->path());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $entries = [];
        $existingEntries = [];
        $errors = [];
        $skipped = [];
        $addedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;


        // Define validation rules for each column
        $validationRules = [
            'Sources' => ['required'],
            'Date' => ['required'],
            'Name' => ['required'],
            'Number' => ['required', 'numeric'],
            'Language' => [],
            'ID NAME' => [],
            'Agent' => ['required'],
        ];

        $columnHeaders = array_shift($rows);
        $sources = Source::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();

        $agents = User::where('role', '=', 'agent')->pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        $manager = User::where('role', '=', 'manager')->where('email', '=', session('user')->email)->first();



        foreach ($rows as $row) {

            $data = array_combine($columnHeaders, $row);
            $validator = Validator::make($data, $validationRules);

            // date manipulation

            $DateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
            $unixTimestamp = ($DateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
            $formattedDate = $date->format('d-m-Y'); // format the date in the desired format

            // If validation fails, add entry to errors array
            if ($validator->fails()) {
                $errors[] = $data;
                $errorCount++;
                continue;
            }


            $entryKey = $data['Date'] . $data['Name'] . $data['Number'] . $data['Agent'];

            // If entry already exists, skip it
            if (isset($existingEntries[$entryKey])) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }

            // Search for the agent name in the $agents array and sources
            // $agentId = array_search(trim($data['Agent']), $agents);
            // $sourceId = array_search(trim($data['Sources']), $sources);
            $agentId = array_search(strtolower(trim($data['Agent'])), array_map('strtolower', $agents));
            $sourceId = array_search(strtolower(trim($data['Sources'])), array_map('strtolower', $sources));

            // If agent or source id is not found skip the entry
            if (!$agentId || !$sourceId) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }

            // Add entry to results
            $entry = [
                'source_id' => $sourceId,
                'name' => $data['Name'],
                'date' => $formattedDate,
                'number' => $data['Number'],
                'language' => $data['Language'],
                'idName' => $data['ID NAME'],
                'agent_id' => $agentId,
                'manager_id' => $manager->id,
            ];
            Lead::create($entry);

            $entries[] = $entry;
            $existingEntries[$entryKey] = true;
            $addedCount++;
        }

        return  redirect('/leads')->with([
            'msg-success' => "Imported Successfully",
            'added' => $addedCount,
            'skippedCount' => $skippedCount,
            'skipped' => $skipped,
            'errors' => $errors,
            'error_count' => $errorCount,
        ]);
    }
    public function submitStatus(Request $req)
    {
        // request data
        $client = null;
        $lead_id = $req->leadId;
        $statusId = $req->status;
        $date = $req->date;
        $amount = $req->amount;
        $remark = $req->remark;
        $clientIDName = $req->IdName;

        // find leads
        $lead = Lead::find($lead_id);
        $agent = User::where('role', '=', 'agent')->find($lead->agent_id);


        // get status full details from status id
        $statusValue = LeadStatusOption::find($statusId);


        // check if status is deposted and  client exists if not exists than create new
        if ($statusValue->name == "Deposited") {
            $client = Client::where('ca_id', '=', trim($clientIDName))->where('number', '=', $lead->number)->first();

            if (!$client) {
                $client = new Client();
                $client->name = '';
                $client->number = $lead->number;
                $client->ca_id = $req->IdName;
            }
            $client->agent_id = $agent->id;
            $client->deposit_amount = $amount;
            $client->save();
            $deposit = new Deposit();
            $deposit->agent_id = $agent->id;
            $deposit->client_id = $client->id;
            $deposit->deposit_amount = $amount;
            $deposit->type = 'deposit';
            $deposit->save();
        }
        // firstly update lead table
        $lead->remark = $remark;
        $lead->current_status = $statusValue->name;
        $lead->followup_date = $date ?? null;
        $lead->updated_at = $date ?? null;
        $lead->amount = $amount ?? null;
        $lead->idName = $req->IdName ?? null;
        $lead->update();
        // create lead status entery 
        $lead_status = new  LeadStatus();
        $lead_status->lead_id = $lead_id;
        $lead_status->status_id = $statusId;
        $lead_status->remark = $remark;
        $lead_status->followup_date = $date ?? null;
        $lead_status->amount = $amount ?? null;
        $result = $lead_status->save();
        if ($result) {
            return redirect()->back()->with(['msg-success' => "Status has been changed"]);
        } else {
            return redirect()->back()->with(['msg-error' => "Somthing went wrong"]);
        }
    }
    // mass status change
    public function massStatusChange(Request $req)
    {

        $leadIds = explode(',', $req->leadIds);
        $statusId = $req->status;
        $date = $req->date;
        $remark = $req->remark;
        $statusValue = LeadStatusOption::find($statusId);
        foreach ($leadIds as $key => $lead_id) {
            // firstly update lead table
            $lead = Lead::find($lead_id);
            $lead->remark = $remark;
            $lead->current_status = $statusValue->name;
            $lead->followup_date = $date ?? null;
            $lead->updated_at = $date ?? null;
            $lead->amount = $amount ?? null;
            $lead->idName = $req->IdName ?? null;
            $lead->update();
            // create lead status entery 
            $lead_status = new  LeadStatus();
            $lead_status->lead_id = $lead_id;
            $lead_status->status_id = $statusId;
            $lead_status->remark = $remark;
            $lead_status->followup_date = $date ?? null;
            $lead_status->amount = $amount ?? null;
            $lead_status->save();
        }
        return redirect()->back()->with(['msg-success' => "Status has been changed"]);
    }
    // mass agent change
    public function massAgentChange(Request $req)
    {
        $leadIds = explode(',', $req->leadIds);
        $agent_id = $req->agent_id;
        foreach ($leadIds as $key => $lead_id) {
            // firstly update lead table
            $lead = Lead::find($lead_id);
            $lead->agent_id = $agent_id;
            $lead->update();
        }
        return redirect()->back()->with(['msg-success' => "Agent has been reassigned"]);
    }
    // to download sample file
    public function downloadfile()
    {
        $file = public_path('assets/Sample.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        return response()->download($file, 'Sample.xlsx', $headers);
    }
    // folllow up leads module
    public function followUp(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();

        $agent = null;
        $manager = null;
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }

        if (session('user')->role == "manager") {
            $manager = User::find(session('user')->id);
        }
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $Filterstatus = $req->query('status');
        $FilterAgent = $req->query('agent_id');

        // get details of the status from status id 
        $currentStatus = LeadStatusOption::find($Filterstatus);

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
            // if session has manager show all the leads added by thi smanager
            ->when($manager, function ($query, $manager) {
                $query->where(function ($query) use ($manager) {
                    $query->where('leads.manager_id', '=', $manager->id);
                });
            })
            // if session has agesnt show all his assigned leads
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
                });
            })
            // filter by agent
            ->when($FilterAgent, function ($query, $FilterAgent) {
                $query->where(function ($query) use ($FilterAgent) {
                    $query->where('leads.agent_id', '=', $FilterAgent);
                });
            })
            // filter by status
            ->when($currentStatus, function ($query, $currentStatus) {
                $query->where(function ($query) use ($currentStatus) {
                    $query->Where('leads.current_status', '=', $currentStatus->name);
                });
            })
            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->whereDate('leads.followup_date', now()->toDateString())
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->paginate(45);


        return view('Admin.Leads.followupList', compact('leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }
}
