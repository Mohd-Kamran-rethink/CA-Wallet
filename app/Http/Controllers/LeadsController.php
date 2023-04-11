<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Lead;
use App\Source;
use App\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadsController extends Controller
{
    
    public function list(Request $req)
    {
        $leads=Lead::paginate(10);
        return view('Admin.Leads.list',compact('leads'));
    }
    public function addView(Request $req)
    {
        $sources=Source::get();
        return view('Admin.Leads.add',compact('sources'));
    }
    // public function add(Request $req)
    // {
    //     $req->validate([
    //         'name'=>'required',
    //         'number'=>'required',
    //         'idName'=>'required',
    //         'date'=>'required',
    //         'agent'=>'required',
    //         'language'=>'required',
    //         'source'=>'required|not_in:0',
    //     ]);
    //     $lead=new Lead();
    //     $lead->source_id=$req->name;
    //     $lead->name=$req->name;
    //     $lead->date=$req->date;
    //     $lead->number=$req->number;
    //     $lead->language=$req->language;
    //     $lead->idName=$req->name;
    //     $lead->agent_id=$req->agent;
    //     $lead->name=$req->name;
        
    // }
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

    // Get column names from the first row of the Excel data
    $columns = array_map('strtolower', $data[0][0]);

    // Remove the first row (column names) from the data
    array_shift($data[0]);

    $sources = Source::pluck('id', 'name');
    $agents = User::where('role','=','agent')->pluck('id', 'name');

    $insertData = [];

    foreach ($data[0] as $row) {
        $date=$row[array_search('date', $columns)];
        
        
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
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('leads')->insert($insertData);
    
        return redirect()->back()->with('success', 'Excel file imported successfully.');
   
            
    }
}
