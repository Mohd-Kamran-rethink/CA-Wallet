<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use App\DepositHistory;
use App\DuplicateLead;
use App\Language;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\PhoneAgent as AppPhoneAgent;
use App\PhoneNumber;
use App\Source;
use App\State;
use App\User;
use App\Zone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhoneAgent;

class LeadsController extends Controller
{

    public function list(Request $req)
    {
        $languages = Language::get();
        $sources = Source::get();
        $phoneNumber = AppPhoneAgent::leftJoin('phone_numbers', 'phone_agents.number_id', 'phone_numbers.id')
            ->where('agent_id', '=', session('user')->id)
            ->where('phone_agents.status', '=', 'active')
            ->where('phone_numbers.status', '=', 'active')
            ->select('phone_numbers.*', 'phone_agents.platform as platformNew')
            ->get();
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->leftJoin('users', 'lead_statuses.agent_id', '=', 'users.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name', 'users.name as agentName')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // ignore ids of   Deposited,Not Interested,Demo id,Id created,Call back
        $ignoredSourceIds = [];
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
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            // if session has agesnt show all his assigned leads
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
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
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

            ->when($agent, function ($query) use ($ignoredSourceIds) {
                $query->where(function ($query) use ($ignoredSourceIds) {
                    $query->whereNotIn('leads.status_id', $ignoredSourceIds);
                    $query->whereNotNull('leads.status_id');
                });
            })
            ->where('is_approved', '=', 'Yes')
            
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->paginate(45);

        return view('Admin.Leads.list', compact('phoneNumber', 'sources', 'languages', 'leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }
    public function duplicateLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // ignore ids of   Deposited,Not Interested,Demo id,Id created,Call back
        $ignoredSourceIds = [1, 12, 6, 7, 8];
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
        $leads = DB::table('duplicate_leads')
            ->join('sources', 'duplicate_leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'duplicate_leads.agent_id', '=', 'users.id')

            // filter by agent
            ->when($FilterAgent, function ($query, $FilterAgent) {
                $query->where(function ($query) use ($FilterAgent) {
                    $query->where('duplicate_leads.agent_id', '=', $FilterAgent);
                });
            })

            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('duplicate_leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->where('is_approved', '=', 'Yes')
            ->select('duplicate_leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('duplicate_leads.date')

            ->paginate(45);
        return view('Admin.Leads.duplicateLeads', compact('leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }
    // pending leads
    public function pendingLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // ignore ids of   Deposited,Not Interested,Demo id,Id created,Call back
        $ignoredSourceIds = [1, 12, 6, 7, 8];
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
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            // filter by general terms
            ->where('leads.agent_id', '=', 'null')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })

            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')

            ->paginate(45);
        return view('Admin.Leads.PendingLeads', compact('leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }
    //leads for approval only show to default manager
    public function nonApprovedLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();

        // statuses and agents list to filter out data
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();


        $manager = null;


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
            // ->when($manager, function ($query, $manager) {
            //     $query->where(function ($query) use ($manager) {
            //         $query->where('leads.manager_id', '=', $manager->id);
            //     });
            // })

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
            ->where('is_approved', '=', 'No')
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->paginate(45);
        return view('Admin.Leads.leadsForApproval', compact('leads', 'searchTerm', 'Filterstatus', 'FilterAgent', 'statuses', 'leads_status_history', 'agents'));
    }
    public function approveLead(Request $req)
    {
        $leadIds = explode(',', $req->leadIds);
        foreach ($leadIds as $key => $value) {
            $lead = Lead::find($value);
            $lead->is_approved = "Yes";
            $lead->update();
        }
        return redirect()->back()->with(['msg-success' => "Leads approved successfully"]);
    }
    public function deleteLeads(Request $req)
    {
        $leadIds = explode(',', $req->leadIds);
        foreach ($leadIds as $key => $value) {
            $lead = Lead::find($value);
            $lead->delete();
        }
        return redirect()->back()->with(['msg-success' => "Leads rejected successfully"]);
    }
    public function importView()
    {
        return view('Admin.Leads.import');
    }
    //leads import by agent
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
        $manager = '';
        $sessionUser = session('user');


        // Define validation rules for each column
        $validationRules = [
            'Sources' => ['required'],
            'Date' => ['required'],
            'Name' => ['required'],
            'Number' => ['required', 'numeric'],
            'State' => ['required'],
            'Zone' => ['required'],
        ];

        $columnHeaders = array_shift($rows);
        $sources = Source::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        //conditons if user is agent or manager accordingly
        if ($sessionUser->role == 'agent') {
            $agents = User::where('role', '=', 'agent')->where('email', '=', session('user')->email)->pluck('name', 'id')->map(function ($name) {
                return trim($name);
            })->toArray();
        } else if ($sessionUser->role == 'manager') {
            $agents = User::where('role', '=', 'agent')->pluck('name', 'id')->map(function ($name) {
                return trim($name);
            })->toArray();
            $manager = User::where('role', '=', 'manager')->where('email', '=', session('user')->email)->first();
        }




        foreach ($rows as $row) {

            $data = array_combine($columnHeaders, $row);
            $validator = Validator::make($data, $validationRules);

            // date manipulation

            $DateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
            $unixTimestamp = ($DateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
            $formattedDate = $date->format('Y-m-d'); // format the date in the desired format

            //for leads_date
            $leads_dateDateserialNumber = $data['Leads Date']; // This is the serial number for the date "01/01/2021"
            $leads_dateunixTimestamp = ($leads_dateDateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $leads_date = \Carbon\Carbon::createFromTimestamp($leads_dateunixTimestamp);
            $leads_dateformattedDate = $leads_date->format('Y-m-d');

            // If validation fails, add entry to errors array
            if ($validator->fails()) {
                $errors[] = $data;
                $errorCount++;
                continue;
            }


            $entryKey = $data['Date'] . $data['Name'] . $data['Number'];

            // If entry already exists, skip it
            if (isset($existingEntries[$entryKey])) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }

            // Search for the agent name in the $agents array and sources
            // $agentId = array_search(trim($data['Agent']), $agents);
            // $sourceId = array_search(trim($data['Sources']), $sources);
            $agentId = array_search(strtolower(trim(session('user')->name)), array_map('strtolower', $agents));
            $sourceId = array_search(strtolower(trim($data['Sources'])), array_map('strtolower', $sources));

            // If agent or source id is not found skip the entry
            if (!$agentId || !$sourceId) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }
            $existingLead = Lead::where('agent_id', $agentId)
                ->where('source_id', $sourceId)
                ->where('date', $formattedDate)
                ->where('created_at', '>=', Carbon::now()->subDays(15))
                ->first();
            // Add entry to results and if agent is importing than manger_id will be blank

            $entry = [
                'source_id' => $sourceId,
                'name' => $data['Name'],
                'date' => $formattedDate,
                'number' => $data['Number'],
                'language' => $data['Language'],
                'state' => $data['State'],
                'zone' => $data['Zone'],
                'idName' => $data['ID NAME'] ?? '',
                'agent_id' => $agentId,
                'manager_id' => $sessionUser->role == 'agent' ? 1 : $manager->id,
                'is_approved' => $sessionUser->role == 'agent' ? 'No' : 'Yes',
                'leads_date' => $data['Leads Date'] ? $leads_dateformattedDate : '',
            ];
            if ($existingLead) {
                DuplicateLead::create($entry);
                $skipped[] = $data;
                $skippedCount++;
                continue;
            } else {
                Lead::create($entry);
            }

            $entries[] = $entry;
            $existingEntries[$entryKey] = true;
            $addedCount++;
        }
        $sessionMsg = $sessionUser->role == 'agent' ? "We have sent your leads to manager for approval" : 'Imported Successfully';
        return  redirect('/leads')->with([
            'msg-success' => $sessionMsg,
            'added' => $addedCount,
            'skippedCount' => $skippedCount,
            'skipped' => $skipped,
            'errors' => $errors,
            'error_count' => $errorCount,
        ]);
    }
    // leads import by manager
    public function leadsImportByManager(Request $req)
    {
        // validation excel rule
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
        $manager = '';
        $sessionUser = session('user');

        // Define validation rules for each column
        $validationRules = [
            'Sources' => ['required'],
            'Name' => ['required'],
            'Date' => ['required'],
            'Number' => ['required', 'numeric'],
            'State' => ['required'],
            'Zone' => ['required'],
        ];

        $columnHeaders = array_shift($rows);
        $sources = Source::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        $groups = [];
        $agents = User::where('role', '=', 'agent')->where('agent_type', '=', 'Normal')->get();
        $keyValueAgents = [];
        $assignedLeads = []; // Array to store assigned leads
        foreach ($rows as $row) {

            $data = array_combine($columnHeaders, $row);
            $validator = Validator::make($data, $validationRules);

            // date manipulation
            $DateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
            $unixTimestamp = ($DateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
            $formattedDate = $date->format('Y-m-d'); // format the date in the desired format

            //for leads_date
            $leads_dateDateserialNumber = $data['Leads Date']; // This is the serial number for the date "01/01/2021"
            $leads_dateunixTimestamp = ($leads_dateDateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $leads_date = \Carbon\Carbon::createFromTimestamp($leads_dateunixTimestamp);
            $leads_dateformattedDate = $leads_date->format('Y-m-d');

            // If validation fails, add entry to errors array
            if ($validator->fails()) {
                $errors[] = $data;
                $errorCount++;
                continue;
            }
            $entryKey = $data['Date'] . $data['Name'] . $data['Number'] . $data['State'] . $data['Language'];
            // If entry already exists, skip it
            if (isset($existingEntries[$entryKey])) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }
            $sourceId = array_search(strtolower(trim($data['Sources'])), array_map('strtolower', $sources));
            // If source id is not found skip the entry
            if (!$sourceId) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }
            $existingLead = Lead::
                // where('agent_id', $agentId)
                where('source_id', $sourceId)
                ->where('date', $formattedDate)
                ->where('language', '=', $data['Language'])
                ->where('state', '=', $data['State'])
                ->where('created_at', '>=', Carbon::now()->subDays(15))
                ->where('number', '=', $data['Number'])
                ->first();



            $entry = [
                'source_id' => $sourceId,
                'name' => $data['Name'] ?? '',
                'date' => $formattedDate,
                'number' => $data['Number'],
                'language' => $data['Language'],
                'idName' => $data['ID NAME'] ?? '',
                'zone' => $data['Zone'],
                'state' => $data['State'],
                'manager_id' => $sessionUser->role,
                'agent_id' => 'null',
                'is_approved' => 'Yes',
                'leads_date' => $data['Leads Date'] ? $leads_dateformattedDate : '',
            ];
            if ($existingLead) {
                DuplicateLead::create($entry);
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }


            if (!isset($groups[trim(strtolower($data['Zone'])) . '-' . trim(strtolower($data['State']))])) {
                $groups[trim(strtolower($data['Zone'])) . '-' . trim(strtolower($data['State']))] = [];
            }

            $groups[trim(strtolower($data['Zone'])) . '-' . trim(strtolower($data['State']))][] = $entry;


            $entries[] = $entry;
            $existingEntries[$entryKey] = true;
            $addedCount++;
        }
        foreach ($agents as $key => $value) {
            if (!isset($keyValueAgents[trim(strtolower($value->zone)) . '-' . trim(strtolower($value->state))])) {
                $keyValueAgents[trim(strtolower($value->zone)) . '-' . trim(strtolower($value->state))] = [];
            }
            $keyValueAgents[trim(strtolower($value->zone)) . '-' . trim(strtolower($value->state))][] = $value;
        }
        foreach ($groups as $key => $value) {
            $leadsWithThisGroup = $value;

            if (isset($keyValueAgents[$key])) {
                $agentsWithThisgroup = $keyValueAgents[$key];
            }
            if (isset($agentsWithThisgroup)) {
                $totalLeads = count($leadsWithThisGroup);
                $totalAgents = count($agentsWithThisgroup);
                $leadsPerUser = intval($totalLeads / $totalAgents);
                $remainingLeads = $totalLeads % $totalAgents;
                $leadIndex = 0;

                foreach ($agentsWithThisgroup as $agent) {
                    $leadsAssigned = 0;
                    $assignedLeadsCount = $leadsPerUser;

                    if ($remainingLeads > 0) {
                        $assignedLeadsCount++;
                        $remainingLeads--;
                    }

                    for ($i = 0; $i < $assignedLeadsCount; $i++) {
                        if ($leadIndex < $totalLeads) {
                            $lead = $leadsWithThisGroup[$leadIndex];
                            $lead["agent_id"] = $lead["state"] == $agent->state && $lead["zone"] == $agent->zone ? $agent->id : 'null';
                            $assignedLeads[] = $lead;
                            $leadsAssigned++;
                            $leadIndex++;
                        }
                    }
                }
            } else {
                foreach ($leadsWithThisGroup as $key => $value) {
                    Lead::create($value);
                }
            }
        }
        foreach ($assignedLeads as $key => $value) {
            Lead::create($value);
        }
        $sessionMsg = $sessionUser->role == 'agent' ? "We have sent your leads to manager for approval" : 'Imported Successfully';
        return  redirect('/leads')->with([
            'msg-success' => $sessionMsg,
            'added' => $addedCount,
            'skippedCount' => $skippedCount,
            'skipped' => $skipped,
            'errors' => $errors,
            'error_count' => $errorCount,
        ]);
    }
    // single status change
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
        if ($statusValue->id == 5 && $req->transfered_language) {
            $languageAgent = User::where('language', '=', $req->transfered_language)->inRandomOrder()->first();
            $lead->agent_id = $languageAgent->id ?? 'null';
        }
        // firstly update lead table
        $lead->remark = $remark;
        $lead->current_status = $statusValue->name;
        $lead->status_id = $statusId;
        $lead->followup_date = $date ?? null;
        $lead->updated_at = $date ?? null;
        $lead->amount = $amount ?? null;
        $lead->idName = $req->IdName ?? null;
        $lead->transfered_language = $req->transfered_language ?? null;
        $lead->update();

        // create lead status entery 
        $lead_status = new  LeadStatus();
        $lead_status->lead_id = $lead_id;
        $lead_status->status_id = $statusId;
        $lead_status->remark = $remark;
        $lead_status->followup_date = $date ?? null;
        $lead_status->agent_id = session('user')->role ? session('user')->id : '';
        $lead_status->transfered_language = $req->transfered_language ?? null;

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
            $lead->status_id = $statusId;
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
            $lead_status->agent_id = session('user')->role ? session('user')->id : '';
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
    //folllow up for demoid status leads module
    public function demoIdLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // we are using statsus id 6 becuase this is will not delte only change this id is for status demoid
        $status_id = 18;
        $status = LeadStatusOption::find($status_id);
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }

        if (session('user')->role == "manager") {
            $manager = User::find(session('user')->id);
        }
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $FilterAgent = $req->query('agent_id');

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
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
            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
           // ->whereDate('leads.followup_date', now()->toDateString())
            ->where('leads.status_id', '=', $status_id)
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->paginate(45);
        return view('Admin.Leads.followLeads', compact('status', 'statuses', 'leads', 'searchTerm', 'FilterAgent', 'leads_status_history', 'agents'));
    }
    //folllow up for createdid status leads module
    public function createdIdLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // we are using statsus id 6 becuase this is will not delte only change this id is for status demoid
        $status_id = 7;
        $status = LeadStatusOption::find($status_id);
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }

        if (session('user')->role == "manager") {
            $manager = User::find(session('user')->id);
        }
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $FilterAgent = $req->query('agent_id');

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
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
            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
           // ->whereDate('leads.followup_date', now()->toDateString())
            ->where('leads.status_id', '=', $status_id)
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->paginate(45);
        return view('Admin.Leads.followLeads', compact('status', 'statuses', 'leads', 'searchTerm', 'FilterAgent', 'leads_status_history', 'agents'));
    }
    //folllow up for callback status leads module
    public function callbackLeads(Request $req)
    {
        // seperately send lead status history and will render this in modal using jquerry
        $leads_status_history = DB::table('lead_statuses')
            ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
            ->select('lead_statuses.*', 'lead_status_options.name as status_name')
            ->orderBy('lead_statuses.id', 'desc')
            ->get();
        $statuses = LeadStatusOption::where('isDeleted', '=', 'No')->get();
        $agents = User::where('role', '=', 'agent')->get();
        $agent = null;
        $manager = null;
        // we are using statsus id 6 becuase this is will not delte only change this id is for status demoid
        $status_id = 8;
        $status = LeadStatusOption::find($status_id);
        if (session('user')->role == "agent") {
            $agent = User::find(session('user')->id);
        }

        if (session('user')->role == "manager") {
            $manager = User::find(session('user')->id);
        }
        // querry paramaters
        $searchTerm = $req->query('table_search');
        $FilterAgent = $req->query('agent_id');

        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->join('users', 'leads.agent_id', '=', 'users.id')
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
            // filter by general terms
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
           // ->whereDate('leads.followup_date', now()->toDateString())
            ->where('leads.status_id', '=', $status_id)
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->orderByDesc('leads.date')
            ->paginate(45);

        return view('Admin.Leads.followLeads', compact('status', 'statuses', 'leads', 'searchTerm', 'FilterAgent', 'leads_status_history', 'agents'));
    }
    // mannual add
    public function mannualAdd(Request $req)
    {
        $agentId = session('user')->id;
        $date = Carbon::now()->format('Y-m-d');
        $source=Source::find($req->Mansource_id);
       
        $rules = [
            'lead_number' => 'required',
            'Mansource_id' => 'required|not_in:0'
        ];
        if($source->agentPhone)
        {
            $rules['AgentPhone'] = 'required|not_in:0';
        }
        if($source->statusID)
        {
            $rules['man_status'] = 'required|not_in:0';
        }
        $req->validate($rules);
        $phoneNumber=null;
        $status=null;
        $statusID='';
        $phoneNumberID=null;
        $statusname=null;
        
        if ($req->ajax()) {
            $source = Source::find($req->Mansource_id);
            $existingLead = Lead::where('agent_id', $agentId)
                ->where('number', '=', $req->lead_number)
                ->where('created_at', '>=', Carbon::now()->subDays(15))
                ->first();
                if($req->man_status)
                {
                    $status = LeadStatusOption::find($req->man_status);
                    $statusID=$status->id;
                    $statusname=$status->name;
                }
                if($req->AgentPhone)
                {
                    // $PhoneAgentHistory=AppPhoneAgent::find($req->AgentPhone);
                    $phoneNumber = PhoneNumber::find($req->AgentPhone);
                    $phoneNumberID=$phoneNumber->id;
                }


            if (!$existingLead) {
                $lead = new Lead();
                $lead->source_id = $source->id??'';
                $lead->number = str_replace('+91', '', $req->lead_number);
                $lead->agent_id = $agentId??'';
                $lead->source_number = $phoneNumberID;
                $lead->date = $date;
                $lead->status_id = $statusID;
                $lead->current_status = $statusname;
                $lead->name = $req->client_name ?? '';
                $lead->is_approved = 'Yes';
                $result = $lead->save();
                if ($result && $statusID!=null) {
                    $leadHistory = new LeadStatus();
                    $leadHistory->status_id = $statusID;
                    $leadHistory->agent_id = session('user')->id;
                    $leadHistory->lead_id = $lead->id??'';
                    $leadHistory->save();
                }
                return ['msg-success' => 'Lead added successfully '];
            } else {
                // $duplicateLead=new DuplicateLead();
                // $duplicateLead->source_id = $source->id;
                // $duplicateLead->number = $req->lead_number;
                // $duplicateLead->agent_id = $agentId;
                // $duplicateLead->source_number = $phoneNumber->id;
                // $duplicateLead->date = $date;
                // $duplicateLead->status_id = $status->id;
                // $duplicateLead->current_status = $status->name;
                // $duplicateLead->name = $req->client_name;
                // $duplicateLead->is_approved = 'Yes';
                // $result = $duplicateLead->save();


                return ['msg-error' => 'The lead with the number ' . $req->lead_number . ' is already present in the database within the last 15 days.'];
            }
        }
    }
}
