<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\Source;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class LeadsController extends Controller
{

    public function list(Request $req)
    {
        $leads_status_history=DB::table('lead_statuses')
        ->join('lead_status_options', 'lead_statuses.status_id', '=', 'lead_status_options.id')
        ->select('lead_statuses.*','lead_status_options.name as status_name')
        ->get();
           
    
        $statuses = LeadStatusOption::where('isDeleted','=','No')->get();
        $agents = User::where('role','=','agent')->get();
        $agent=null;
        $manager=null;
        if (session('user')->role=="agent") {
            $agent = User::find(session('user')->id);
        }
         
        if (session('user')->role=="manager") {
            $manager = User::find(session('user')->id);
        }
        $searchTerm = $req->query('table_search');
        $Filterstatus = $req->query('status');
        $FilterAgent = $req->query('agent_id');

        $currentStatus=LeadStatusOption::find($Filterstatus);

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
            ->select('leads.*','sources.name as source_name', 'users.name as agent_name')
            ->paginate(10);
        return view('Admin.Leads.list', compact('leads', 'searchTerm', 'Filterstatus','FilterAgent', 'statuses','leads_status_history','agents'));
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
            'Number' => ['required','numeric'],
            'Language' => [],
            'ID NAME' => [],
            'Agent' => ['required'],
        ];
    
        $columnHeaders = array_shift($rows);
        $sources = Source::pluck('name', 'id')->map(function($name) {
            return trim($name);
          })->toArray();
          
          $agents = User::where('role', '=', 'agent')->pluck('name', 'id')->map(function($name) {
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
            if (!$agentId|| !$sourceId) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }
         
           // Add entry to results
            $entry = [
                'source_id' => $sourceId,
                'name' =>$data['Name'],
                'date' => $formattedDate,
                'number' => $data['Number'],
                'language' => $data['Language'],
                'idName' => $data['ID NAME'],
                'agent_id' => $agentId,
                'manager_id'=>$manager->id,
            ];
            Lead::create($entry);

            $entries[] = $entry;
            $existingEntries[$entryKey] = true;
            $addedCount++;
        }
    
        return  redirect('/leads')->with([
            'msg-success'=>"Imported Successfully",
            'added' => $addedCount,
            'skippedCount' => $skippedCount,
            'skipped' => $skipped,
            'errors' => $errors,
            'error_count' => $errorCount,
        ]);
    }
    public function submitStatus(Request $req)
    {
        
            $lead_id = $req->leadId;
            $statusId = $req->status;
            $date = $req->date;
            $amount = $req->amount;
            $remark = $req->remark;
            $statusValue=LeadStatusOption::find($statusId);
            if (($statusValue->name == "Follow Up" || $statusValue->name == "Busy") && $date == '') {
                return ['dateError' => "Date is mandatory"];
            }
            else
            {
                $lead=Lead::find($lead_id);
                $lead->remark = $remark;
                $lead->current_status = $statusValue->name;
                $lead->followup_date = $date??null;
                $lead->updated_at = $date??null;
                $lead->amount=$amount??null;
                
                $lead->update();
             
                
                $lead_status =new  LeadStatus();
                $lead_status->lead_id=$lead_id; 
                $lead_status->status_id=$statusId; 
                $lead_status->remark=$remark; 
                $lead_status->followup_date=$date??null;
                $lead_status->amount=$amount??null;
                $result=$lead_status->save();
                if($result)
                {
                    return redirect()->back()->with(['msg-success' => "Status has been changed"]);
                }
                else
                {
                    return redirect()->back()->with(['msg-error' => "Somthing went wrong"]);
                }
            }
        }



        
    public function downloadfile()
    {
        $file = public_path('assets/Sample.xlsx');
        $headers = [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        return response()->download($file, 'Sample.xlsx', $headers);
    }
    
}
