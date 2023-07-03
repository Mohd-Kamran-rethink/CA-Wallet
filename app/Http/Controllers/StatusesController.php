<?php

namespace App\Http\Controllers;

use App\LeadStatusOption;
use Illuminate\Http\Request;

class StatusesController extends Controller
{
    public function list(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $statuses = LeadStatusOption::when($searchTerm, function ($query, $searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        })
            ->where("isDeleted", "=", "No")
            ->paginate(10);
        return view('Admin.Statuses.list', compact('statuses'));
    }
    public function addView(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $status = LeadStatusOption::find($id);
            return view('Admin.Statuses.add', compact('status'));
        }

        return view('Admin.Statuses.add');
    }

    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:lead_status_options,name'
        ]);
        $status = new LeadStatusOption();
        $status->name = $req->name;
        $status->manager_id = session('user')->id;
        $result = $status->save();
        if ($result) {
            return redirect('/statuses')->with(['msg-success' => 'Status has been added.']);
        } else {
            return redirect('/statuses')->with(['msg-error' => 'Something went wrong could not add status.']);
        }
    }
    public function edit(Request $req)
    {
        $status = LeadStatusOption::find($req->statusId);
        $req->validate([
            'name' => 'required|unique:lead_status_options,name'
        ]);
        $status->name = $req->name;
        $result = $status->save();
        if ($result) {
            return redirect('/statuses')->with(['msg-success' => 'Status has been updated.']);
        } else {
            return redirect('/statuses')->with(['msg-error' => 'Something went wrong could not update status.']);
        }
    }
    public function delete(Request $req)
    {
        $status = LeadStatusOption::find($req->deleteId);
        $status->isDeleted = "Yes";
        $result = $status->update();
        if ($result) {
            return redirect('/statuses')->with(['msg-success' => 'Status has been deleted.']);
        } else {
            return redirect('/statuses')->with(['msg-error' => 'Something went wrong could not delete status.']);
        }
    }
}
