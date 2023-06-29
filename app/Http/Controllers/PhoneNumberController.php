<?php

namespace App\Http\Controllers;

use App\PhoneAgent;
use App\PhoneNumber;
use App\User;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    public function list() {
        $numbers=PhoneNumber::get();
        foreach ($numbers as $number) {
            $WatiAgent = PhoneAgent::where('number_id', $number->id)
                                   ->where('status', 'active')
                                   ->where('platform', 'wati')
                                   ->first();
            $whatsAppAgent = PhoneAgent::where('number_id', $number->id)
                                   ->where('status', 'active')
                                   ->where('platform', 'whatsapp')
                                   ->first();
            if ($WatiAgent) {
                $agent = User::find($WatiAgent->agent_id);
                $number->watiAgent=$agent->name??'';
                // Do whatever you need with the $phoneAgent and $agent data
            }
           
                
                if ($whatsAppAgent) {
                    $agent = User::find($whatsAppAgent->agent_id);
                    $number->WhatsAppAgent=$agent->name??'';
                    // Do whatever you need with the $phoneAgent and $agent data
                }
               
        }
        $agents=User::where('role','=','agent')->get();
        return view('Admin.Phone.list',compact('numbers','agents'));
    }
    public function addForm(Request $req) {
        $id=$req->query('id');
        if($id)
        {
            $number=PhoneNumber::find($id);
            return view('Admin.Phone.add',compact('number'));
        }
        return view('Admin.Phone.add');
    }
        
    public function add(Request $req) {
        $req->validate(['number'=>'required']);
        $number=new PhoneNumber();
        $number->number=$req->number;
        $number->status="active";
        $result=$number->save();
        if ($result) {
            return redirect('/phone-numbers')->with(['msg-success' => 'Phone Number added successfully.']);
        } else {
            return redirect('/phone-numbers')->with(['msg-error' => 'Something went wrong could not add phone number.']);
            }

        
    }
   
    public function edit(Request $req) {
        $number=PhoneNumber::find($req->hiddenId);
        $number->number=$req->number;
        $result=$number->update();

        if ($result) {
            return redirect('/phone-numbers')->with(['msg-success' => 'Phone Number udpate successfully.']);
        } else {
            return redirect('/phone-numbers')->with(['msg-error' => 'Something went wrong could not update phone number.']);
            }
    }
    public function statusChange(Request $req)
    {
        $number=PhoneNumber::find($req->id);
        $number->status=$req->status;
        $result=$number->update();
        if ($result) {
            return redirect('/phone-numbers')->with(['msg-success' => 'Phone Number updated successfully.']);
        } else {
            return redirect('/phone-numbers')->with(['msg-error' => 'Something went wrong could not update phone number.']);
            }
    }
    public function reassign(Request $req) {
        $number=PhoneNumber::find($req->id);
        $agent=User::find($req->agent);
        $platform=$req->platform;
        $phoneAgent=PhoneAgent::where('number_id','=',$number->id)->where('platform','=',$platform)->where('status','=','active')->first();
        if($phoneAgent)
        {
            
            $phoneAgent->status='inactive';
            $phoneAgent->update();
        }
        $newPhoneAgent=new PhoneAgent();
        $newPhoneAgent->agent_id = $agent->id;
        $newPhoneAgent->number_id = $number->id;
        $newPhoneAgent->platform = $platform;
        $newPhoneAgent->status ='active';
        $result= $newPhoneAgent->save();
        if ($result) {
            return redirect('/phone-numbers')->with(['msg-success' => 'Phone Number udpate successfully.']);
        } else {
            return redirect('/phone-numbers')->with(['msg-error' => 'Something went wrong could not update phone number.']);
            }
    } 
     public function history(Request $req) {
        $numberid=$req->query('id');
        $history=PhoneAgent::leftJoin('users', 'phone_agents.agent_id', '=', 'users.id')->where('number_id','=',$numberid)
            ->select('phone_agents.*','users.name as useranme')
            ->orderBy('id','desc')
            ->get();
        return view('Admin.Phone.History',compact('history'));

        
    }
}
