<?php

namespace App\Http\Controllers;

use App\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function list(Request $req)
    {
        $searchTerm = $req->query('table_search');

        if ($searchTerm) {
            $sources = Source::when($searchTerm, function ($query, $searchTerm) {
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(10);
        }
        else
        {
            $sources=Source::paginate(10);
        }
        return view('Admin.Sources.list',compact('sources','searchTerm'));

    }
    public function addView(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $source = Source::find($id);
            return view('Admin.Sources.add', compact('source'));
        } 
        return view('Admin.Sources.add');
    }
    public function add(Request $req)
    {
        $req->validate([ 'name' => 'required|unique:sources,name',]);
        $source=new Source();
        $source->name=$req->name;
        $result=$source->save();
        if ($result) {
                return redirect('/sources')->with(['msg-success' => 'Source has been added.']);
        } else {
            return redirect('/sources')->with(['msg-error' => 'Something went wrong could not add source.']);
        }
            
    }
    public function delete(Request $req)
    {
        $source=Source::find($req->deleteId);
        $result= $source->delete();
        if ($result) {
            return redirect('/sources')->with(['msg-success' => 'Source has been deleted.']);
        } else {
            return redirect('/sources')->with(['msg-error' => 'Something went wrong could not delete source.']);
            }
    }
    public function edit(Request $req)
    {
        $req->validate([ 'name' => 'required|unique:sources,name',]);
        $source=Source::find($req->sourceId);
        $source->name=$req->name;
        $result=$source->update();
        if ($result) {
            return redirect('/sources')->with(['msg-success' => 'Source has been updated.']);
        } else {
            return redirect('/sources')->with(['msg-error' => 'Something went wrong could not update source.']);
        }
    }
        
}
            
            
