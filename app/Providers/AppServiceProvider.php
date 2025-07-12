<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Models\Message;
use App\Models\DailyAttendances;
use App\Models\Classes;
use App\Models\Section;

// ✅ Add this use statement
use App\Observers\DailyAttendancesObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // ✅ Register the Observer FIRST (safe to place anywhere in boot)
        DailyAttendances::observe(DailyAttendancesObserver::class);

        Blade::component('admin.compose.mail-form-fields', 'mail-fields');
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $school_id = $user->school_id;

                // ✅ Inbox Notification Badge Count
                $unreadMessageCount = Message::where('school_id', $school_id)
                    ->whereRaw("FIND_IN_SET(?, to_user_id)", [$user->id])
                    ->where('is_read', 0)
                    ->count();

                // ✅ Attendance Block (unchanged logic)
                $active_session = get_school_settings($school_id)->value('running_session');

                $attendance_of_students = DailyAttendances::where([
                    'school_id'  => $school_id,
                    'session_id' => $active_session,
                ])->get()->groupBy('name')->toArray();

                $lastRecord = !empty($attendance_of_students) ? end($attendance_of_students) : null;

                $page_data = [
                    'attendance_date' => now()->format('Y-m-d'),
                    'class_id'        => request()->input('class_id', null),
                    'section_id'      => request()->input('section_id', null),
                    'month'           => request()->input('month', date('m')),
                    'year'            => request()->input('year', date('Y')),
                ];

                $class_name   = optional(Classes::find($page_data['class_id']))->name ?? 'N/A';
                $section_name = optional(Section::find($page_data['section_id']))->name ?? 'N/A';

                // ✅ Share all data globally to views
                $view->with([
                    'studentAttendances'     => $attendance_of_students,
                    'attendanceDate'         => $page_data['attendance_date'],
                    'lastRecord'             => $lastRecord,
                    'page_data'              => $page_data,
                    'class_name'             => $class_name,
                    'section_name'           => $section_name,
                    'unreadMessageCount'     => $unreadMessageCount,
                ]);
            }
        });
    }
}
