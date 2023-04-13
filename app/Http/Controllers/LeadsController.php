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
            ->when($agent, function ($query, $agent) {
                $query->where(function ($query) use ($agent) {
                    $query->where('leads.agent_id', '=', $agent->id);
                });
            })
            ->when($Filterstatus, function ($query, $Filterstatus) {
                $query->where(function ($query) use ($Filterstatus) {
                    $query->where('leads.status_name', '=', $Filterstatus);
                });
            })
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('sources.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('users.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('leads.number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->select('leads.*','sources.name as source_name', 'users.name as agent_name')
            ->paginate(10);
        return view('Admin.Leads.list', compact('leads', 'searchTerm', 'Filterstatus', 'statuses'));
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
        $sources = Source::pluck('name','id')->toArray();
        $agents = User::where('role', '=', 'agent')->pluck('name', 'id')->toArray(); // flip the keys and values
        
        $manager = User::where('role', '=', 'manager')->where('email', '=', session('user')->email)->first();

        foreach ($rows as $row) {
           
            $data = array_combine($columnHeaders, $row);
            $validator = Validator::make($data, $validationRules);
    
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
                
            // Search for the agent name in the $agents array
            $agentId = array_search($data['Agent'], $agents);
            $sourceId = array_search($data['Sources'], $sources);
            // If agent is not found in the $agents array, skip the entry
            if (!$agentId|| !$sourceId) {
                $skipped[] = $data;
                $skippedCount++;
                continue;
            }
         
            $DateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
            $unixTimestamp = ($DateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
            $formattedDate = $date->format('d-m-Y'); // format the date in the desired format
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
    
        // TODO: Save $entries to database or perform any other desired action
    
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
            $statusValue = $req->status;
            $date = $req->date;
            $remark = $req->remark;
            if (($statusValue == "Follow Up" || $statusValue == "Busy") && $date == '') {
                return ['dateError' => "Date is mandatory"];
            }
            else
            {
                $lead=Lead::find($lead_id);
                $lead->remark = $remark;
                $lead->status_name = $statusValue;
                $lead->followup_date = $date??null;
                $lead->updated_at = $date??null;
                $lead->update();
             
                $lead_status =new  LeadStatus();
                $lead_status->lead_id=$lead_id; 
                $lead_status->name=$statusValue; 
                $lead_status->remark=$remark; 
                $lead_status->followup_date=$date??null;
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
}
