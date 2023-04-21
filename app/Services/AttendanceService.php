<?php

namespace App\Services;
use App\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    
    public function getLastEntry($userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function isOnBreak($userId)
    {
        $lastEntry = $this->getLastEntry($userId);

        return $lastEntry && $lastEntry->action === 'break';
    }

    public function startBreak($userId)
    {
        Attendance::create([
            'user_id' => $userId,
            'action' => 'break',
        ]);
    }

    public function endBreak($userId)
    {
        Attendance::create([
            'user_id' => $userId,
            'action' => 'end_break',
        ]);
    }
}
