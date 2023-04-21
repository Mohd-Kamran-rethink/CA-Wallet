<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class ClientController extends Controller
{
    public function list()
    {
       $currentUser=session('user');
       $clients=Client::where('isDeleted','=','No')->where('agent_id','=',$currentUser->id)->paginate(10);
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
        $client=new Client();
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
        $client=Client::find($req->userId);
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
    public function redeposit(Request $req)
    {
        $deposit=Deposit::find($req->depositId);
        $deposit->deposit_amount=$deposit->deposit_amount+$req->amount;
        $deposit->type='redeposit';
        $result=$deposit->update();
        if ($result) 
        {
                return redirect('/clients')->with(['msg-success' => 'Amount has been redeposited.']);
        } 
        else 
        {
            return redirect('/clients')->with(['msg-error'=>'Something went wrong could not redeposit amount.']);   
        }
    }
                
           
           
           
        
}
