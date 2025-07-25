<?php

namespace App\Observers;

use App\Models\DailyAttendances;
use App\Http\Controllers\Addon\SmsController;
use Illuminate\Support\Facades\Log;
use DateTime;

class DailyAttendancesObserver
{
    public function created(DailyAttendances $attendance)
    {
        Log::info("🟢 Created: Attendance ID {$attendance->id}");

        $attendance->load('enrollment.user.parent', 'enrollment.class', 'enrollment.section');

        if (!empty($attendance->stu_intime)) {
            $this->sendFlowSMS($attendance, 'in');
        }
    }

    public function updated(DailyAttendances $attendance)
    {
        Log::info("🟡 Updated: Attendance ID {$attendance->id}");

        $attendance->load('enrollment.user.parent', 'enrollment.class', 'enrollment.section');

        if ($attendance->isDirty('stu_outtime') && !empty($attendance->stu_outtime)) {
            $this->sendFlowSMS($attendance, 'out');
        }
    }

    private function sendFlowSMS(DailyAttendances $attendance, $type)
    {
        $enrollment = $attendance->enrollment;

        if (
            !$enrollment ||
            !$enrollment->user ||
            !$enrollment->user->parent
        ) {
            Log::warning("❌ Missing student/parent info for attendance ID: {$attendance->id}");
            return;
        }

        $student = $enrollment->user;
        $parent = $student->parent;

        $parentInfo = json_decode($parent->user_information, true);
        $parentPhone = $parentInfo['phone'] ?? null;

        if (empty($parentPhone)) {
            Log::warning("❌ Parent phone not found in user_information JSON for parent ID: {$parent->id}");
            return;
        }

        $phone = '91' . $parentPhone;
        $studentName = $student->name;

        if ($type === 'in') {
            $dt = new DateTime($attendance->stu_intime);
            $dateTimeFormatted = $dt->format('d-m-Y h:i A');
            $workedHours = '00:00:00';
        } else {
            $dt = new DateTime($attendance->stu_outtime);
            $dateTimeFormatted = $dt->format('d-m-Y h:i A');

            if ($attendance->stu_intime) {
                $in  = new DateTime($attendance->stu_intime);
                $interval = $in->diff($dt);
                $workedHours = $interval->format('%H:%I:%S');
            } else {
                $workedHours = '00:00:00';
            }
        }

        Log::info("📤 Sending {$type}-time messages → Name: {$studentName}, Time: {$dateTimeFormatted}, Hours: {$workedHours}");

        $sms = new SmsController();
        
        // Send SMS
        $sms->msg91($phone, $studentName, $dateTimeFormatted, $workedHours);
        
        // Send WhatsApp message
        $sms->sendWhatsappMessage($phone, $studentName, $dateTimeFormatted, $workedHours);

    }
}



// Option2
/* 

class DailyAttendancesObserver
{
    public function created(DailyAttendances $attendance)
    {
        Log::info("🟢 Created: Attendance ID {$attendance->id}");

        $attendance->load('enrollment.user.parent', 'enrollment.class', 'enrollment.section');

        if (!empty($attendance->stu_intime)) {
            $this->sendFlowSMS($attendance, 'in');
        }
    }

    public function updated(DailyAttendances $attendance)
    {
        Log::info("🟡 Updated: Attendance ID {$attendance->id}");

        $attendance->load('enrollment.user.parent', 'enrollment.class', 'enrollment.section');

        if ($attendance->isDirty('stu_outtime') && !empty($attendance->stu_outtime)) {
            $this->sendFlowSMS($attendance, 'out');
        }
    }

    private function sendFlowSMS(DailyAttendances $attendance, $type)
    {
        $enrollment = $attendance->enrollment;

        if (
            !$enrollment ||
            !$enrollment->user ||
            !$enrollment->user->parent
        ) {
            Log::warning("❌ Missing student/parent info for attendance ID: {$attendance->id}");
            return;
        }

        $student = $enrollment->user;
        $parent = $student->parent;

        $parentInfo = json_decode($parent->user_information, true);
        $parentPhone = $parentInfo['phone'] ?? null;

        if (empty($parentPhone)) {
            Log::warning("❌ Parent phone not found in user_information JSON for parent ID: {$parent->id}");
            return;
        }

        $phone = '91' . $parentPhone;

        Log::info("📞 Parent details resolved → Parent ID: {$parent->id}, Phone: {$phone} for Student ID: {$student->id}, Attendance ID: {$attendance->id}");

        $studentName = $student->name;
        $className = $enrollment->class->name ?? '';
        $sectionName = $enrollment->section->name ?? '';
        $classSection = trim("{$className} {$sectionName}");

        if ($type === 'in') {
            $dt = new DateTime($attendance->stu_intime);
            $formattedTime = $dt->format('g:i A');
            $formattedDate = $dt->format('d M Y');
            $message = "Your child {$studentName} ({$classSection}) has checked in at {$formattedTime} on {$formattedDate}.";
        } else {
            $dt = new DateTime($attendance->stu_outtime);
            $formattedTime = $dt->format('g:i A');
            $formattedDate = $dt->format('d M Y');
            $message = "Your child {$studentName} ({$classSection}) has checked out at {$formattedTime} on {$formattedDate}.";
        }

        Log::info("📤 Sending {$type}-time Flow SMS to {$phone} for {$studentName}");

        $sms = new SmsController();
        $sms->msg91($phone, $studentName, $formattedTime, $formattedDate, $type);
    }
}
*/