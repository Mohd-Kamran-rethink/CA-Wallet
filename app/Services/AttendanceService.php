<?php

namespace App\Services;
use App\Attendance;
use App\MasterAttendance as AppMasterAttendance;
use Carbon\Carbon;
use MasterAttendance;

class AttendanceService
{
    
    public function getLastEntry($userId)
    {
        return  AppMasterAttendance::where('user_id', session('user')->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function isOnBreak($userId)
    {
        $lastEntry = $this->getLastEntry($userId);

        return $lastEntry && $lastEntry->actions === 'break';
    }

    public function startBreak($userId)
    {
        $lastEntry = AppMasterAttendance::where('user_id', session('user')->id)->whereDate('created_at', Carbon::today())->first();

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
           $lastEntry->actions="break";
           $lastEntry->save();
           $attendance=new Attendance();
           $attendance->user_id=session('user')->id;
           $attendance->action='Break Start';
           $attendance->time=Carbon::now('Asia/Kolkata')->toTimeString();
           $attendance->save();
        }
        
        

    }
    

    public function endBreak($userId)
    {
        $lastEntry = AppMasterAttendance::where('user_id', session('user')->id)->whereDate('created_at', Carbon::today())->first();
        
            $lastEntryTime = Carbon::parse($lastEntry->updated_at);
            $now = Carbon::now();
        
            $hours = $lastEntryTime->diffInHours($now);
            $minutes = $lastEntryTime->diff($now)->format('%I');
             // Parse the existing hours to get the total hours and minutes
            $existingHours = explode(':', $lastEntry->breaktime);
            $existingTotalMinutes = ($existingHours[0] * 60) + $existingHours[1];
       
            // Add the newly calculated hours and minutes
            $totalMinutes = $existingTotalMinutes + ($hours * 60) + (int)$minutes;
            $newHours = str_pad(floor($totalMinutes / 60), 2, '0', STR_PAD_LEFT);
            $newMinutes = str_pad($totalMinutes % 60, 2, '0', STR_PAD_LEFT);
            // Store the result as a string in the same format
            $lastEntry->breaktime = $newHours . ':' . $newMinutes . ':00';
        $lastEntry->actions="break_end";
        $lastEntry->update();
        $attendance=new Attendance();
        $attendance->user_id=session('user')->id;
        $attendance->action='Break End';
        $attendance->time=Carbon::now('Asia/Kolkata')->toTimeString();
        $attendance->save();
    }
        
}
