<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // uncommon functions for search
    public function ManagerView(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $manager = User::where("role", '=', 'manager')->find($id);
            return view('Admin.Manager.add', compact('manager'));
        } else {
            return view('Admin.Manager.add');
        }
    }
    public function listManager(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $managers = User::where('role', 'manager')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('Admin.Manager.list', compact('managers','searchTerm'));
    }
    public function AgentView(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $agent = User::where("role", '=', 'agent')->find($id);
            return view('Admin.Agents.add', compact('agent'));
        } else {
            return view('Admin.Agents.add');
        }
    }

        
        

    public function listAgents(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $agents = User::where('role', 'agent')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('Admin.Agents.list', compact('agents','searchTerm'));
    }
    // common functions for manager and agents
    public function add(Request $req)
    {

        $req->validate([
            'name' => 'required|unique:users,name',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|same:confirmPassword',
            'confirmPassword' => 'required|'
        ]);

        $user = new User();
        $user->name = $req->name;
        $user->phone = $req->phone;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        $user->role = $req->role;
        $result = $user->save();
        if ($result) {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-success' => 'Manager has been added.']);
            } else {
                return redirect('/agents')->with(['msg-success'=>'Agent has been added.']);   
            }
        } else {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-error' => 'Something went wrong could not add manager.']);
            } else {
                return redirect('/agents')->with(['msg-error'=>'Something went wrong could not add manager.']);   
            }
        }
    }
    public function edit(Request $req)
    {
        $currentManager = User::where("role", '=', $req->role)->find($req->userId);

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

        $currentManager->name = $req->name;
        $currentManager->phone = $req->phone;
        $currentManager->email = $req->email;
        if ($req->password) {
            $currentManager->password = Hash::make($req->password);
        }
        $result = $currentManager->save();
        if ($result) {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-success' => 'Manager has been updated.']);
            } else {
                return redirect('/agents')->with(['msg-success'=>'Agent has been added.']);   
            }
        } else {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-error' => 'Something went wrong could not update manager.']);
            } else {
                return redirect('/agents')->with(['msg-error'=>'Something went wrong could not update manager.']);   
            }
        }
    }
    public function delete(Request $req)
    {

        $User = User::where("role", '=', $req->role)->find($req->deleteId);
        $result = $User->delete();
        if ($result) {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-success' => 'Manager has been deleted.']);
            } else {
                return redirect('/agents')->with(['msg-success'=>'Agent has been deleted.']);   
            }
        } else {
            if ($req->role === 'manager') {
                return redirect('/managers')->with(['msg-error' => 'Something went wrong could not delete manager.']);
            } else {
                return redirect('/agents')->with(['msg-error'=>'Something went wrong could not delete manager.']);   
            }
        }
    }
}

        
        

    
    
