<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function startBreak(AttendanceService $attendanceService)
    {
        $attendanceService->startBreak(session('user')->id);
        return redirect()->back();
    }
        
    
    
    public function endBreak(AttendanceService $attendanceService)
    {
        $attendanceService->endBreak(session('user')->id);
        return redirect()->back();
    }
}
