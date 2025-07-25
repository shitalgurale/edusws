<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Models\DailyAttendances;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class MonthlyAttendanceDispatcher
{
    public static function sendReports($admin)
{
    \Log::info("📤 MonthlyAttendanceDispatcher::sendReports triggered by {$admin->name}");

    $students = User::where('role_id', 7)->whereNotNull('parent_id')->get();

    foreach ($students as $student) {
        $parent = User::find($student->parent_id);
        if (!$parent) continue;

        $month = now()->subMonth()->format('m');
        $year = now()->subMonth()->format('Y');

        $attendances = \App\Models\DailyAttendances::where('student_id', $student->id)
            ->whereMonth('timestamp', $month)
            ->whereYear('timestamp', $year)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly_attendance', [
            'student' => $student,
            'attendances' => $attendances,
            'month' => $month,
            'year' => $year,
        ]);

        $filename = "attendance_{$student->id}_{$month}_{$year}.pdf";
        \Storage::disk('public')->put("attendance_reports/{$filename}", $pdf->output());

        \App\Models\Message::create([
            'from_user_id' => $admin->id,
            'to_user_id' => $parent->id,
            'school_id' => $student->school_id,
            'subject' => "Monthly Attendance Report - {$month}/{$year}",
            'body' => "Attached is the attendance report of your child: {$student->name} for {$month}/{$year}.",
            'attachment_path' => "attendance_reports/{$filename}",
        ]);
    }
}
}