<?php

namespace App\Http\Controllers;

use App\client;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class ClientController extends Controller
{
    public function list()
    {
       $currentUser=session('user');
       $clients=client::where('isDeleted','=','No')->where('agent_id','=',$currentUser->id)->paginate(10);
       return view('Admin.Clients.list',compact('clients'));
    }
    public function addView(Request $req)
    {
        $client=null;
        $id=$req->query('id');
        $client=Client::find($id);
        return view('Admin.Clients.add',compact('client'));
    }
    public function add(Request $req)
    {
        $req->validate([
            'name'=>'required',
            'ca_id'=>'required|unique:clients,ca_id',
            'number'=>'required|unique:clients,number',
        ]);
        $Agent=session('user');
        $client=new client();
        $client->name=$req->name;
        $client->number=$req->number;
        $client->ca_id=$req->ca_id;
        $client->agent_id=$Agent->id;
        $result=$client->save();
        if ($result) 
        {
                return redirect('/clients')->with(['msg-success' => 'Client has been added.']);
        } 
        else 
        {
            return redirect('/clients')->with(['msg-error'=>'Something went wrong could not add client.']);   
        }
    }
    public function delete(Request $req)
    {
        $client=Client::find($req->deleteId);
        $client->isDeleted="Yes";
        $result=$client->update();
        if ($result) 
        {
                return redirect('/clients')->with(['msg-success' => 'Client has been deleted.']);
        } 
        else 
        {
            return redirect('/clients')->with(['msg-error'=>'Something went wrong could not delete client.']);   
        }
    }
    public function edit(Request $req)
    {
        $req->validate([
            'name'=>'required',
            'ca_id'=>'required',
            'number'=>'required',
        ]);
        $Agent=session('user');
        $client=client::find($req->userId);
        $client->name=$req->name;
        $client->number=$req->number;
        $client->ca_id=$req->ca_id;
        $client->agent_id=$Agent->id;
        $result=$client->update();
        if ($result) 
        {
                return redirect('/clients')->with(['msg-success' => 'Client has been updated.']);
        } 
        else 
        {
            return redirect('/clients')->with(['msg-error'=>'Something went wrong could not update client.']);   
        }
    }
                
           
           
           
        
}
