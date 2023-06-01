<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Manager;
use App\MasterAttendance as AppMasterAttendance;
use App\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MasterAttendance;

class AuthController extends Controller
{
    public function loginView()
    {
        if(session()->has('user'))
        {
            return redirect('/dashboard');
        }
        else
        {
            $settings=Setting::first();
            return view('Admin.Auth.Login',compact('settings'));
        }
    }

    public function login(Request $req)
    {
        // Get the current date
        $allowedRoles = ['manager', 'agent'];
       
        $date = Carbon::today();
        // Check if there is an entry for today and the current user
        
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', '=', $req->email)->first();
        if ($user && in_array($user->role, $allowedRoles)) {
            if (Hash::check($req->password, $user->password)) {
                session()->put('user', $user);
                $attendanceMaster = AppMasterAttendance::where('user_id', session('user')->id)
                ->whereDate('created_at', $date)
                ->latest()
                ->first();
                if (!$attendanceMaster) {
                    $time = '00:00:00';
                    $masterattendance=new AppMasterAttendance();
                    $masterattendance->hours=$time;
                    $masterattendance->user_id=session('user')->id;
                    $masterattendance->actions="login";
                    
                    $masterattendance->save();
                } else {
                    
                    $attendanceMaster->actions="login";
                    $attendanceMaster->update();
                }
                $attendance=new Attendance();
                $attendance->user_id=session('user')->id;
                $attendance->action='login';
                $attendance->time=Carbon::now('Asia/Kolkata')->toTimeString();
                $attendance->save();
                return redirect('/dashboard');
                
            } else {
                return redirect('/')->with(['msg-error-password' => 'Invalid password']);
            }
        }
        else
        {
            return redirect('/')->with(['msg-error-username' => "Email is not registered with us"]);
        }
    }
    public function logout()
    {
        
        $lastEntry = AppMasterAttendance::where('user_id', session('user')->id)->whereDate('master_attendances.created_at', now()->format('Y-m-d') )->latest()->first();
        
        if ($lastEntry && $lastEntry->created_at->isToday()) {
           // If there is an entry for today, increment the hours based on the last entry
           $lastEntryTime = Carbon::parse($lastEntry->updated_at);
           $now = Carbon::now();
       
           $hours = $lastEntryTime->diffInHours($now);
           $minutes = $lastEntryTime->diff($now)->format('%I');
       
           // Parse the existing hours to get the total hours and minutes
           $existingHours = explode(':', $lastEntry->hours);
           $existingTotalMinutes = ($existingHours[0] * 60) + $existingHours[1];
       
           // Add the newly calculated hours and minutes
           $totalMinutes = $existingTotalMinutes + ($hours * 60) + (int)$minutes;
           $newHours = str_pad(floor($totalMinutes / 60), 2, '0', STR_PAD_LEFT);
           $newMinutes = str_pad($totalMinutes % 60, 2, '0', STR_PAD_LEFT);
           // Store the result as a string in the same format
           $lastEntry->hours = $newHours . ':' . $newMinutes . ':00';
           $lastEntry->actions="logout";
           $lastEntry->update();
           $attendance=new Attendance();
           $attendance->user_id=session('user')->id;
           $attendance->action='logout';
           $attendance->time=Carbon::now('Asia/Kolkata')->toTimeString();
           $attendance->save();
           session()->remove('user');
        }
        return redirect('/');
    }
}
