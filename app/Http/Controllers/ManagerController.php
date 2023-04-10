<?php

namespace App\Http\Controllers;

use App\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function addView(Request $req)
    {
        $id = $req->query('id');
        if($id)
        {
            $manager=Manager::find($id);
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
            'name' => 'required|unique:managers,name',
            'phone' => 'required|unique:managers,phone', 
            'email' => 'required|email|unique:managers,email',
            'password' => 'required|min:8|same:confirmPassword',
            'confirmPassword' => 'required|'
        ]);

        $manager=new Manager();
        $manager->name=$req->name;
        $manager->phone=$req->phone;
        $manager->email=$req->email;
        $manager->password=Hash::make($req->password);
        $result= $manager->save();
        if($result)
        {
            return redirect('/managers')->with(['msg-success'=>'Manager has been added.']);   
        }

    }
    public function list()
    { 
        $managers=Manager::orderBy('id',"desc")->paginate(10);
        return view('Admin.Manager.list',compact('managers'));
    }
    public function delete(Request $req)
    {
        $manager=Manager::find($req->deleteId);
        $result= $manager->delete();
        if($result)
        {
            return redirect()->back()->with(['msg-success'=>"Manager has been deleted."]);
        }
    }
    public function edit(Request $req)
    {
        $manager=Manager::find($req->managerId);
        $req->validate([
            'name' => 'required',
            'phone' => 'required', 
            'email' => 'required',
            'password' => 'sometimes|min:8|same:confirmPassword',
            'confirmPassword' => 'sometimes|required_with:password|same:password'
        ]);

        $manager->name=$req->name;
        $manager->phone=$req->phone;
        $manager->email=$req->email;
        if($req->password)
        {
            $manager->password=Hash::make($req->password);
        }
        $result= $manager->save();
        if($result)
        {
            return redirect('/managers')->with(['msg-success'=>'Manager has been upadated.']);   
        }

    }


}
