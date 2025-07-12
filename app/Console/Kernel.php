<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DailyAttendances;
use App\Models\Enrollment;
use App\Models\Classes;
use App\Services\FcmHttpV1Service;




class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Monthly Attendance Report
        $schedule->command('attendance:send-monthly-report')->monthlyOn(1, '11:00');

        // ✅ Attendance Notification - moved full logic here
        $schedule->call(function () {
            DB::beginTransaction();

            try {
                $todayDate = (new \DateTime())->format('Y-m-d');

                $newAttendances = DailyAttendances::whereDate('stu_intime', $todayDate)
                    ->where('notification_sent', 0)
                    ->with('enrollment.user.parent', 'enrollment.class', 'enrollment.section')
                    ->get();

                if ($newAttendances->isEmpty()) {
                   // Log::info('No new attendances to notify for today.');
                    DB::commit();
                    return;
                }

                $fcm = new FcmHttpV1Service();

                foreach ($newAttendances as $attendance) {
                    if ($attendance->enrollment && $attendance->enrollment->user && $attendance->enrollment->user->parent) {
                        $parent = $attendance->enrollment->user->parent;
                        $student = $attendance->enrollment->user;

                        $className = $attendance->enrollment->class->name ?? '';
                        $sectionName = $attendance->enrollment->section->name ?? '';

                        $fcmTokens = [];

                        for ($i = 1; $i <= 5; $i++) {
                            $tokenField = 'fcm_token' . $i;
                            if (!empty($parent->$tokenField)) {
                                $fcmTokens[] = $parent->$tokenField;
                            }
                        }

                        if (empty($fcmTokens)) {
                            Log::info('No FCM token found for parent ID: ' . $parent->id);
                            continue;
                        }

                        $dateTime = new \DateTime($attendance->stu_intime);
                        $formattedTime = $dateTime->format('g:i A');
                        $formattedDate = $dateTime->format('d M Y');

                        $title = "Attendance Update";
                        $classSection = trim($className . ' ' . $sectionName);
                        $body = "Your child {$student->name} ({$classSection}) has checked in at {$formattedTime} on {$formattedDate}.";

                        Log::info('📣 Sending notification to parent ID ' . $parent->id);

                        $fcm->sendToSpecificUsers($fcmTokens, $title, $body);

                        Log::info('✅ Notification sent to parent ID ' . $parent->id);
                    }
                }

                // ✅ After sending, mark notification_sent = 1
                DailyAttendances::whereIn('id', $newAttendances->pluck('id'))->update([
                    'notification_sent' => 1
                ]);

                DB::commit();
                Log::info('✅ Attendance notifications for today sent successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('❌ Attendance Notification Error: ' . $e->getMessage());
            }
        })->everyTenMinutes(); // ⏰ Run every 10 mins
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}