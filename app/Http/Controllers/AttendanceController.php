<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Services\AttendanceService;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function list(Request $req)
    {

        $querryId = $req->query('id') ?? null;
        $querryDate = $req->query('date') ?? now()->format('Y-m-d');


        $agents = User::where('role', '=', 'agent')->get();
        if (session('user')->role === "manager") {
            $users = DB::table('users')
                ->leftJoin('master_attendances', 'users.id', '=', 'master_attendances.user_id')
                ->when($querryId, function ($query, $querryId) {
                    $query->where('users.id', '=', $querryId)
                        ->where('master_attendances.user_id', '=', $querryId);
                })
                ->whereDate('master_attendances.created_at', $querryDate)
                ->orderBy('users.id', 'desc')
                ->get();
        } else {
            $users = DB::table('users')
                ->leftJoin('master_attendances', 'users.id', '=', 'master_attendances.user_id')
                ->where('master_attendances.user_id', '=', session('user')->id)
                ->whereDate('master_attendances.created_at', $querryDate)
                ->orderBy('users.id', 'desc')
                ->get();
        }


        return view('Admin.Attendance.list', compact("agents", 'users', 'querryDate', 'querryId'));
    }

    public function viewActivity(Request $req)
    {
        $querryId = $req->id ?? null;
        $querryDate = $req->date != "null" ? $req->date : now()->format('Y-m-d');
        $attendances = Attendance::where('user_id', '=', $querryId)
            ->whereDate('created_at', $querryDate)
            ->get();
        return ["status" => true, 'data' => $attendances];
    }
}
