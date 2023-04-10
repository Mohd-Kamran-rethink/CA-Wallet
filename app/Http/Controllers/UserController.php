<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function addView(Request $req)
    {
        $id = $req->query('id');
        if($id)
        {
            $manager=User::where("role",'=','manager')->find($id);
            return view('Admin.Manager.add',compact('manager'));
        }
        else
        {
            return view('Admin.Manager.add');
        }
    }
    public function add(Request $req)
    {
       
        $req->validate([
            'name' => 'required|unique:users,name',
            'phone' => 'required|unique:users,phone', 
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|same:confirmPassword',
            'confirmPassword' => 'required|'
        ]);

        $manager=new User();
        $manager->name=$req->name;
        $manager->phone=$req->phone;
        $manager->email=$req->email;
        $manager->password=Hash::make($req->password);
        $manager->role='manager';
        $result= $manager->save();
        if($result)
        {
            return redirect('/managers')->with(['msg-success'=>'Manager has been added.']);   
        }
        else
        {
            return redirect('/managers')->with(['msg-error'=>'Something went wrong could not add manager.']);   
        }
            

    }
    public function list(Request $req)
    { 
       $searchTerm = $req->query('table_search');
       
       if($searchTerm)
       {
        $managers = User::where('role', 'manager')
        ->when($searchTerm, function($query, $searchTerm) {
           $query->where(function($query) use($searchTerm) {
               $query->where('name', 'like', '%' . $searchTerm . '%')
                     ->orWhere('email', 'like', '%' . $searchTerm . '%')
                     ->orWhere('phone', 'like', '%' . $searchTerm . '%');
           });
        })
        ->orderBy('id', 'desc')
        ->paginate(10);    
       }
       else
       {
           $managers=User::where("role",'=','manager')->orderBy('id',"desc")->paginate(10);
        }

        return view('Admin.Manager.list',compact('managers'));
    }
    public function delete(Request $req)
    {
        $manager=User::where("role",'=','manager')->find($req->deleteId);
        $result= $manager->delete();
        if($result)
        {
            return redirect()->back()->with(['msg-success'=>"Manager has been deleted."]);
        }
        else
        {
            return redirect('/managers')->with(['msg-error'=>'Something went wrong could not delete manager.']);   
        }
    }
    public function edit(Request $req)
    {
        $currentManager=User::where("role",'=','manager')->find($req->managerId);

        $rules = [
            'name' => 'required|unique:users,name,' . $currentManager->id,
            'phone' => 'required|unique:users,phone,' . $currentManager->id,
            'email' => 'required|email|unique:users,email,' . $currentManager->id,
            'confirmPassword' => 'required_with:password',
        ];
        
        $conditionalRules = [
            'password' => 'nullable|min:8|same:confirmPassword',
        ];
        
        $req->validate(array_merge($rules, $conditionalRules));

        $currentManager->name=$req->name;
        $currentManager->phone=$req->phone;
        $currentManager->email=$req->email;
        if($req->password)
        {
            $currentManager->password=Hash::make($req->password);
        }
        $result= $currentManager->save();
        if($result)
        {
            return redirect('/managers')->with(['msg-success'=>'Manager has been upadated.']);   
        }

    }


}
