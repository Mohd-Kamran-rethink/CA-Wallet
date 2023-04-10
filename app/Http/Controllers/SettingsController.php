<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function view()
    {
        $isAlready= Setting::first();
        return view('Admin.Settings.index',compact('isAlready'));
    }
    public function add(Request $req)
    {
        $req->validate(['project_name'=>'required']);
        $isAlready= Setting::first();
        if($isAlready)
        {
            $isAlready->delete();
        }
        $setting=new Setting();
        $setting->project_name=$req->project_name;
        $timestamp = time();
        if($req->is_dark_mode=="Enabled")
        {
            $setting->darkmode=$req->is_dark_mode;
        }
        if ($req->projectLogo) {
            $LogoRequest = $req->file('projectLogo');
            $originalThumnail = $LogoRequest->getClientOriginalName();
            $LogoFilename = $timestamp . '_' . $originalThumnail;
            $LogoRequest->move(public_path('Data/Project'), $LogoFilename);
            $setting->project_logo=$LogoFilename;
          }
        $setting->save();
         return redirect('/dashboard');   
    }
}
