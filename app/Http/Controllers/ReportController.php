<?php

namespace App\Http\Controllers;

use App\Client;
use App\Exports\DepositExport;
use App\Exports\LeadsReportExport;
use App\Lead;
use App\LeadStatusOption;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\VarDumper\Cloner\Data;

class ReportController extends Controller
{
    public function leadsReport(Request $req)
    {
        $agents = User::where('role', '=', 'agent')->get();
        
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        $currentUser = session('user');
        $statuses = LeadStatusOption::get();
        
        $data = [];
        foreach ($agents as $key => $value) {
            # code...
            $row = [];
            array_push($row, $value->name);

            $totalLeads = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
            ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate))
            ])
                            ->get()->count();

            array_push($row, $totalLeads);
            $notProcessed = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
            ->where("current_status", '=', NUll)
            ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate))
            ])
            ->get()->count();
            
            array_push($row, $notProcessed);
            foreach ($statuses as $status) {
                $leadsCount = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
                
                    ->where("current_status", '=', $status->name)
                    ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                        date('Y-m-d', strtotime($startDate)),
                        date('Y-m-d', strtotime($endDate))
                    ])
                    ->get()->count();
                array_push($row, $leadsCount);
            }
            array_push($data, $row);
        }
        $startDate= $startDate->toDateString();
        $endDate= $endDate->toDateString();
        return view('Admin.Reports.leadsReport', compact('statuses', 'data', 'totalLeads', 'startDate', 'endDate'));
    }
    public function exportLeads(Request $req)
    {
       
        $startDate = $req->date_from;
        $endDate = $req->date_to;
        $header = ['Name', 'Total Leads', 'Not Processed Leads'];
        $statuses = LeadStatusOption::get();
        foreach ($statuses as $statusValue) {
            array_push($header, $statusValue->name);
        }
        
        
            $agents = User::where('role', '=', 'agent')->get();
        
       
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }

        $currentUser = session('user');
        $data = [];
        foreach ($agents as $key => $value) {
            # code...
            $row = [];
            array_push($row, $value->name);
            $totalLeads = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
            ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate))
            ])
                ->get()->count();

            array_push($row, $totalLeads === 0 ? '0' : ($totalLeads ?: ''));
            $notProcessed = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
                ->where("current_status", '=', NUll)
                ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                    date('Y-m-d', strtotime($startDate)),
                    date('Y-m-d', strtotime($endDate))
                ])
                ->get()->count();
            array_push($row, $notProcessed === 0 ? '0' : ($notProcessed ?: ''));
            foreach ($statuses as $status) {
                $leadsCount = Lead::where('agent_id', '=', $value->id)->where('is_approved', '=', 'Yes')
                    ->where("current_status", '=', $status->name)
                    ->whereBetween(DB::raw("STR_TO_DATE(date, '%d-%m-%Y')"), [
                        date('Y-m-d', strtotime($startDate)),
                        date('Y-m-d', strtotime($endDate))
                    ])
                    ->get()->count();
                array_push($row, $leadsCount === 0 ? '0' : ($leadsCount ?: ''));
            }
            array_push($data, $row);
        }
        $totalRow = ['Total'];
        for ($i = 1; $i < count($header); $i++) {
            $total = collect($data)->sum($i);
            array_push($totalRow, $total === 0 ? '0' : ($total ?: ''));
        }
        array_push($data, $totalRow);
        $filename = 'leads-from-' . $startDate . 'to-' . $endDate . '.xlsx';
        $export = new LeadsReportExport($data, $header);
        return Excel::download($export, $filename);
    }
    // deposits report and exports
    public function deposits(Request $req)
    {
        $agent=null;
       
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        
        $clients = Client::with(['depositHistories' => function ($query) use ($startDate, $endDate) {
            $query->where('deposit_histories.created_at', '>=', $startDate)
            ->where('deposit_histories.created_at', '<=', $endDate);
        }])
        ->when($agent, function ($query, $agent) {
            $query->where(function ($query) use ($agent) {
                $query->where('agent_id', '=', $agent->id);
            });
        })
        ->where('isDeleted','=','No')
        ->get();

        // Initialize the two-dimensional array
        $data = [];

        // Add the header row with the date range
        $headerRow = ['Name','ID Name','Number'];
        $currentDate = Carbon::parse($startDate);
        while ($currentDate <= Carbon::parse($endDate)) {
            $headerRow[] = $currentDate->format('d-m-Y');
            $currentDate->addDay();
        }
        $data[] = $headerRow;

        // Add the data rows with the client names and deposit totals for each date
        foreach ($clients as $client) {
            $dataRow = [$client->name,$client->ca_id,$client->number];
            $currentDate = Carbon::parse($startDate);
            while ($currentDate <= Carbon::parse($endDate)) {
                $depositTotal = $client->depositHistories
                    ->where('created_at', '>=', $currentDate->toDateString())
                    ->where('created_at', '<=', $currentDate->toDateString() . ' 23:59:59')
                    ->sum('amount');
                $dataRow[] = $depositTotal;
                $currentDate->addDay();
            }
            $data[] = $dataRow;
        }
       $startDate= $startDate->toDateString();
       $endDate= $endDate->toDateString();
        return view('Admin.Reports.depositsReport',compact('data','headerRow','startDate','endDate'));
    }
    public function exportDeposit(Request $req)
    {
        $agent=null;
        
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        
        $clients = Client::with(['depositHistories' => function ($query) use ($startDate, $endDate) {
            $query->where('deposit_histories.created_at', '>=', $startDate)
            ->where('deposit_histories.created_at', '<=', $endDate);
        }])
        ->when($agent, function ($query, $agent) {
            $query->where(function ($query) use ($agent) {
                $query->where('agent_id', '=', $agent->id);
            });
        })
        ->where('isDeleted','=','No')
        ->get();

        // Initialize the two-dimensional array
        $data = [];

        // Add the header row with the date range
        $headerRow = ['Name','ID Name','Number'];
        $currentDate = Carbon::parse($startDate);
        while ($currentDate <= Carbon::parse($endDate)) {
            $headerRow[] = $currentDate->format('d-m-Y');
            $currentDate->addDay();
        }
        $data[] = $headerRow;

        // Add the data rows with the client names and deposit totals for each date
        foreach ($clients as $client) {
            $dataRow = [$client->name,$client->ca_id,$client->number];
            $currentDate = Carbon::parse($startDate);
            while ($currentDate <= Carbon::parse($endDate)) {
                $depositTotal = $client->depositHistories
                    ->where('created_at', '>=', $currentDate->toDateString())
                    ->where('created_at', '<=', $currentDate->toDateString() . ' 23:59:59')
                    ->sum('amount');
                $dataRow[] = $depositTotal=== 0 ? '0' : ($depositTotal ?: '');
                $currentDate->addDay();
            }
            $data[] = $dataRow;
        }
        $totalSum=['TOTAL','-','-'];
        for ($i = 3; $i < count($headerRow); $i++) {
            $columnSum = 0;
            foreach ($data as $row) {
                if ($row[$i] !== '-') {
                    if (is_numeric($row[$i])) {
                        $columnSum += $row[$i];
                    }
                }
            }
            $totalSum[] = $columnSum;
        }
        
        // Add the total row to the data array
        $data[] = $totalSum;
        $filename = 'deposit-from-' . $startDate . 'to-' . $endDate . '.xlsx';
        $export = new DepositExport($data, $data[0]);
        return Excel::download($export, $filename);
    }
}
