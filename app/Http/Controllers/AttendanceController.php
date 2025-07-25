<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendances;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\Addon\HrDailyAttendence;
use App\Models\Addon\Hr_user_list;
use App\Models\Addon\Hr_roles;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use PDF;
use DateTime;

class AttendanceController extends Controller
{
    /**
     * Get Attendance from incoming URL, process it and record attendance
     */
    
    public function getAttendance(Request $request)
    {
        $url = $request->getRequestUri();
        Log::info('Incoming URL: ' . $url);
    
        if (!preg_match('/\$(.*?)\*/', $url, $matches)) {
            Log::error('❌ Pattern match failed. URL: ' . $url);
            return response('Invalid URL format. Data must be between $ and *.', 400);
        }
    
        Log::info('📌 Matched data: ' . $matches[1]);
        $data = explode('&', $matches[1]);
    
        if (count($data) < 4) {
            Log::error('❌ Invalid data count. Extracted data: ' . json_encode($data));
            return response('Invalid URL format. Expected at least 4 parts.', 400);
        }
    
        $school_id = (int) $data[0];
        $device_id = $data[1];
    
        Log::info("📌 Extracted School ID: $school_id");
        Log::info("📌 Extracted Device ID: $device_id");
    
        // ✅ Correct way to fetch session_id now
        $session_id = get_school_settings($school_id)->value('running_session');
        if (!$session_id) {
            Log::error('❌ No active session found for School ID: ' . $school_id);
            return response('No active session found.', 400);
        }
    
        $recordString = implode('&', array_slice($data, 2));
        $bioid_time_pairs = explode(',', $recordString);
    
        Log::info("✅ Processing multiple records. Total Records: " . count($bioid_time_pairs));
    
        foreach ($bioid_time_pairs as $pair) {
            DB::beginTransaction();
            try {
                $entry_data = explode('&', trim($pair));
    
                if (count($entry_data) !== 2) {
                    throw new \Exception("Invalid record format: " . json_encode($pair));
                }
    
                $bioid = trim($entry_data[0]);
                $date = trim($entry_data[1]);
    
                if (strlen($date) !== 14 || !ctype_digit($date)) {
                    throw new \Exception("Invalid date format for BioID $bioid: $date");
                }
    
                $dateTime = \DateTime::createFromFormat('dmYHis', $date);
                if (!$dateTime) {
                    throw new \Exception("Failed to parse date for BioID $bioid: $date");
                }
    
                $formattedDate = $dateTime->format('Y-m-d H:i:s');
                $attendanceDate = $dateTime->format('Y-m-d');
    
                $enrollment = Enrollment::where('stu_bioid', $bioid)
                    ->where('school_id', $school_id)
                    ->first();
    
                if ($enrollment) {
                    Log::info("✅ Student BioID $bioid found (Student ID: {$enrollment->user_id})");
    
                    $attendance = DailyAttendances::where('school_id', $school_id)
                        ->where('student_id', $enrollment->user_id)
                        ->whereDate('stu_intime', $attendanceDate)
                        ->first();
    
                    if (!$attendance) {
                        DailyAttendances::create([
                            'school_id'   => $school_id,
                            'section_id'  => $enrollment->section_id,
                            'student_id'  => $enrollment->user_id,
                            'class_id'    => $enrollment->class_id,
                            'device_id'   => $device_id,
                            'stu_intime'  => $formattedDate,
                         // 'timestamp'   => $attendanceDate,
                           'timestamp'   => $formattedDate,
                            'stu_outtime' => null,
                            'punchstatus' => 1,
                            'session_id'  => $session_id,  // ✅ using session_id here
                        ]);
                        Log::info("🆕 Creating attendance with stu_intime = $formattedDate for BioID $bioid");
                    } else {
                        $attendance->update([
                            'stu_outtime' => $formattedDate,
                            //'timestamp'   => $attendanceDate,
                            'punchstatus' => 1,
                        ]);
                        Log::info("✅ Student attendance updated for BioID $bioid");
                    }
                } else {
                    $hrUser = Hr_user_list::where('emp_bioid', $bioid)
                        ->where('school_id', $school_id)
                        ->first();
    
                    if ($hrUser) {
                        Log::info("✅ Employee BioID $bioid found (User ID: {$hrUser->id})");
    
                        $attendance = HrDailyAttendence::where('school_id', $school_id)
                            ->where('user_id', $hrUser->id)
                            ->whereDate('emp_intime', $attendanceDate)
                            ->first();
    
                        $check = Hr_roles::where('id', $hrUser->role_id)
                            ->where('school_id', $school_id)
                            ->first();
    
                        if ($check && $check->permanent === 'yes') {
                            if (!$attendance) {
                                HrDailyAttendence::create([
                                    'school_id'         => $school_id,
                                    'role_id'           => $hrUser->role_id,
                                    'hr_roles_role_id'  => $check->id,
                                    'emp_intime'        => $formattedDate,
                                    'emp_outtime'       => null,
                                    'device_id'         => $device_id,
                                    'punchstatus'       => 1,
                                    'session_id'        => $session_id, // ✅ fixed
                                    'user_id'           => $hrUser->id,
                                //  'created_at'        => $attendanceDate,
                                    'created_at'        => $formattedDate,
                                ]);
                                Log::info("✅ New employee attendance recorded for BioID $bioid");
                            } else {
                                $attendance->update([
                                    'emp_outtime' => $formattedDate,
                                    
                                    'punchstatus' => 1,
                                ]);
                                Log::info("✅ Employee attendance updated for BioID $bioid");
                            }
                        } else {
                            throw new \Exception("Invalid or non-permanent hr_roles for BioID $bioid");
                        }
                    } else {
                        throw new \Exception("BioID $bioid not found in enrollment or HR.");
                    }
                }
    
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("❌ Error processing attendance for BioID: " . $e->getMessage());
            }
        }
    
        return response('$RFID=0#', 200)->header('Content-Type', 'text/plain');
    }
    
    
        
        
        
        /**
         * Post attendance data via a POST request.
         */
        public function postAttendance(Request $request)
    {
        // 🔹 Fetch Raw Input
        $rawData = file_get_contents('php://input');
        Log::info("🔹 Raw Request Data", ['raw_request' => $rawData]);
    
        // 🔹 Decode JSON manually
        $data = json_decode($rawData, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("❌ JSON Decode Error: " . json_last_error_msg());
            return response()->json(['DeviceResponse' => 1], 400);
        }
    
        // 🔹 Log Parsed Data
        Log::info("🔹 Parsed Request Data", ['request_data' => $data]);
    
        if (!isset($data['MachineID']) || !isset($data['Attendance']) || !is_array($data['Attendance'])) {
            Log::error("❌ Invalid data format: Missing MachineID or Attendance array.", ['data' => $data]);
            return response()->json(['DeviceResponse' => 1], 400);
        }
    
        // 🔹 Extract school_id and device_id
        $machineID = $data['MachineID'];
        Log::info("🔹 Extracted MachineID: $machineID");
    
        if (strlen($machineID) < 7) {
            Log::error("❌ Invalid MachineID format: $machineID");
            return response()->json(['DeviceResponse' => 1], 400);
        }
    
        $school_id = substr($machineID, 0, 5);
        $device_id = substr($machineID, -2);
    
        Log::info("✅ Parsed school_id: $school_id, device_id: $device_id");
    
        // ✅ Fetch session_id using get_school_settings (correct method)
        $session_id = get_school_settings($school_id)->value('running_session');
        if (!$session_id) {
            Log::error("❌ No active session found for School ID: $school_id");
            return response()->json(['DeviceResponse' => 1], 400);
        }
    
        DB::beginTransaction();
    
        try {
            foreach ($data['Attendance'] as $attendanceData) {
                Log::info("🔹 Processing attendance record", ['attendanceData' => $attendanceData]);
    
                if (!isset($attendanceData['PunchID'], $attendanceData['PunchTime'], $attendanceData['PunchStatus'])) {
                    Log::error("❌ Invalid attendance record structure", ['record' => $attendanceData]);
                    continue;
                }
    
                $bioid = $attendanceData['PunchID'];
                $punchStatus = $attendanceData['PunchStatus'];
                $punchTime = $attendanceData['PunchTime'];
    
                Log::info("🔹 Extracted BioID: $bioid, PunchStatus: $punchStatus, PunchTime: $punchTime");
    
                if (strlen($punchTime) !== 14 || !ctype_digit($punchTime)) {
                    Log::error("❌ Invalid date format received for BioID $bioid. Date: $punchTime");
                    continue;
                }
    
                $dateTime = \DateTime::createFromFormat('dmYHis', $punchTime);
                if (!$dateTime) {
                    Log::error("❌ Failed to parse PunchTime for BioID $bioid. Received date: $punchTime");
                    continue;
                }
    
                $formattedDate = $dateTime->format('Y-m-d H:i:s');
                $attendanceDate = $dateTime->format('Y-m-d');
    
                Log::info("✅ Formatted DateTime: $formattedDate, Attendance Date: $attendanceDate");
    
                // 1️⃣ Check Student Enrollment
                $enrollment = Enrollment::where('stu_bioid', $bioid)
                                        ->where('school_id', $school_id)
                                        ->first();
    
                if ($enrollment) {
                    Log::info("✅ BioID $bioid found in enrollments (Student ID: {$enrollment->user_id})");
    
                    $attendance = DailyAttendances::where('school_id', $school_id)
                                                  ->where('student_id', $enrollment->user_id)
                                                  ->whereDate('stu_intime', $attendanceDate)
                                                  ->first();
    
                    if (!$attendance) {
                        DailyAttendances::create([
                            'school_id'   => $school_id,
                            'section_id'  => $enrollment->section_id,
                            'student_id'  => $enrollment->user_id,
                            'class_id'    => $enrollment->class_id,
                            'device_id'   => $device_id,
                            'stu_intime'  => $formattedDate,
                            'timestamp'   => $formattedDate,
                            'stu_outtime' => null,
                            'punchstatus' => $punchStatus,
                            'session_id'  => $session_id, // ✅ Correct session id
                        ]);
                        Log::info("🆕 Creating attendance with stu_intime = $formattedDate for BioID $bioid");
                    } else {
                        $attendance->update([
                            'stu_outtime' => $formattedDate,
                           //'timestamp'   => $attendanceDate,
                            'device_id'   => $device_id,
                            'punchstatus' => $punchStatus,
                        ]);
                        Log::info("✅ Updated stu_outtime for BioID: $bioid");
                    }
                } else {
                    // 2️⃣ If not found in Students, check Employee
                    $hrUser = Hr_user_list::where('emp_bioid', $bioid)
                                          ->where('school_id', $school_id)
                                          ->first();
    
                    if ($hrUser) {
                        Log::info("✅ Employee BioID $bioid found in hr_user_list (User ID: {$hrUser->id})");
    
                        $check = Hr_roles::where('id', $hrUser->role_id)
                                         ->where('school_id', $school_id)
                                         ->first();
    
                        if ($check && $check->permanent === 'yes') {
                            $attendance = HrDailyAttendence::where('school_id', $school_id)
                                                           ->where('user_id', $hrUser->id)
                                                           ->whereDate('emp_intime', $attendanceDate)
                                                           ->first();
    
                            if (!$attendance) {
                                HrDailyAttendence::create([
                                    'school_id'         => $school_id,
                                    'role_id'           => $hrUser->role_id,
                                    'hr_roles_role_id'  => $check->id,
                                    'emp_intime'        => $formattedDate,
                                    'created_at'        => $formattedDate,
                                    'emp_outtime'       => null,
                                    'device_id'         => $device_id,
                                    'punchstatus'       => $punchStatus,
                                    'session_id'        => $session_id, // ✅ Correct session id
                                    'user_id'           => $hrUser->id,
                                ]);
                                Log::info("✅ Recorded new emp_intime for BioID: $bioid");
                            } else {
                                $attendance->update([
                                    'emp_outtime' => $formattedDate,
                                   // 'created_at'  => $attendanceDate,
                                    'device_id'   => $device_id,
                                    'punchstatus' => $punchStatus,
                                ]);
                                Log::info("✅ Updated emp_outtime for BioID: $bioid");
                            }
                        } else {
                            Log::error("❌ No valid HR role or not permanent for BioID $bioid");
                        }
                    } else {
                        Log::error("❌ BioID $bioid not found in students or employees.");
                    }
                }
            }
    
            DB::commit();
            Log::info("✅ Attendance processing completed successfully.");
            return response()->json(['DeviceResponse' => 0], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error recording attendance: " . $e->getMessage());
            return response()->json(['DeviceResponse' => 1], 500);
        }
    }


    public function studentDailyAttendancePage()
    { 
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
         return view('admin.attendance.student_daily_attendance', [
        'classes' => $classes
    ]);
    }



        public function getDailyStudentAttendance(Request $request)
    {
        $class_id   = $request->class_id;
        $section_id = $request->section_id;
        $date       = date('Y-m-d', strtotime($request->date));
    
        // Get students in selected class & section
        $students = Enrollment::with('user')
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
           
            ->get();
    
        $attendanceData = [];
foreach ($students as $student) {
    // ✅ Skip only if name is missing (allow empty bioid or attendance)
    if (!$student->user || empty($student->user->name)) {
        continue;
    }

    $attendance = DailyAttendances::where('student_id', $student->user_id)
        ->whereDate('timestamp', $date)
        ->first();
            $attendanceData[] = [
                'name'        => $student->user->name ?? 'N/A',
                'bioid'       => $student->stu_bioid,
                'school_id'   => $attendance ? $attendance->school_id  : null,
                'device_id'   => $attendance && $attendance->device_id !== null ? $attendance->device_id : '0',
                'in_time'     => $attendance ? $attendance->stu_intime : null,
                'out_time'    => $attendance ? $attendance->stu_outtime : null,
                'remark'      => $attendance
                        ? ($attendance->device_id ? 'Automatic' : 'Manual')
                        : 'N/A',
            ];
        }
    
        return view('admin.attendance.student_daily_attendance_table', compact('attendanceData'));
    }



public function exportStudentDailyCSV(Request $request)
{
    \Log::info('🚀 exportStudentDailyCSV triggered.', $request->all());

    $date       = $request->input('date');
    $class_id   = $request->input('class_id');
    $section_id = $request->input('section_id');

    if (!$date || !$class_id || !$section_id) {
        \Log::error('❌ Missing required filters.');
        return response()->json(['error' => 'Missing required filters.']);
    }

    // Parse m/d/Y format to standard Y-m-d
    $parsedDate = \DateTime::createFromFormat('m/d/Y', $date);
    if (!$parsedDate) {
        \Log::error('❌ Invalid date format.', ['date' => $date]);
        return response()->json(['error' => 'Invalid date format.']);
    }

    $startDate = $parsedDate->format('Y-m-d 00:00:00');
    $endDate   = $parsedDate->format('Y-m-d 23:59:59');

    \Log::info('📅 Parsed Date:', ['start' => $startDate, 'end' => $endDate]);

    $class = \App\Models\Classes::find($class_id);
    $className = $class ? preg_replace('/\s+/', '_', $class->name) : 'Class';

    $attendances = \DB::table('daily_attendances')
        ->join('users', 'daily_attendances.student_id', '=', 'users.id')
        ->join('enrollments', 'users.id', '=', 'enrollments.user_id')
        ->where('daily_attendances.class_id', $class_id)
        ->where('daily_attendances.section_id', $section_id)
        ->whereBetween('daily_attendances.timestamp', [$startDate, $endDate])
        ->select(
            'users.name as student_name',
            'enrollments.stu_bioid',
            'daily_attendances.timestamp',
            'daily_attendances.status',
            'daily_attendances.stu_intime',
            'daily_attendances.stu_outtime'
        )
        ->get();

    \Log::info('✅ Records Fetched:', ['count' => $attendances->count()]);

    $filename = "Daily_Attendance_{$className}_" . $parsedDate->format('Ymd') . ".csv";

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $columns = ['Student Name', 'Student BioID', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($attendances, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($attendances as $row) {
            $dateOnly  = date('d-m-Y', strtotime($row->timestamp));
            $inTime    = $row->stu_intime ? date('H:i:s', strtotime($row->stu_intime)) : '';
            $outTime   = $row->stu_outtime ? date('H:i:s', strtotime($row->stu_outtime)) : '';
            $statusTxt = $row->status == 1 ? 'Present' : 'Absent';

            fputcsv($file, [
                $row->student_name,
                $row->stu_bioid,
                $dateOnly,
                $inTime,
                $outTime,
                $statusTxt
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




/* Daily Attendance - staff
*/
public function dailyAttendancePage(Request $request)
{
    Log::info('📥 dailyAttendancePage called', $request->all());

    $role = Role::where('role_id', auth()->user()->role_id)->first();
    $roleName = $role ? $role->name . ".navigation" : 'admin.navigation';

    Log::info("🔍 Resolved role name: " . ($role ? $role->name : 'None') . ", Using layout: " . $roleName);

    $no_user = 1;

    $userEmail = auth()->user()->email;
    Log::info("🔐 Auth user email: {$userEmail}");

    $user_record = Hr_user_list::where('email', $userEmail)->first();

    if (!empty($user_record)) {
        $user_role = $user_record->role_id;
        Log::info("✅ HR user found with role_id: {$user_role}");
    } else {
        $user_role = auth()->user()->role_id - 1;
        Log::warning("⚠️ HR user NOT found, using fallback role_id: {$user_role}");

        if (auth()->user()->role_id != 2) {
            $no_user = 0;
            Log::warning("❌ No user matched and not admin. Setting no_user = 0");
        }
    }

    Log::info('📤 Rendering view admin.attendance.hr_daily_attendance with:', [
        'roleName'  => $roleName,
        'user_role' => $user_role,
        'no_user'   => $no_user
    ]);

    return view('admin.attendance.hr_daily_attendance', [
        'roleName'  => $roleName,
        'user_role' => $user_role,
        'no_user'   => $no_user
    ]);
}


public function filterHrDailyAttendance(Request $request)
{
    Log::info('📥 filterHrDailyAttendance called with params:', $request->all());

    $dateInput = $request->date;
    $roleId = $request->role_id;
    $schoolId = auth()->user()->school_id;

    Log::info("🔍 Raw input date: $dateInput");
    Log::info("🏫 School ID: $schoolId, Role ID: $roleId");

    // Convert m/d/Y to Y-m-d format
    $dateObj = DateTime::createFromFormat('m/d/Y', $dateInput);
    $formattedDate = $dateObj ? $dateObj->format('Y-m-d') : null;

    if (!$formattedDate) {
        Log::warning('❌ Date parsing failed for input: ' . $dateInput);
        return response()->json(['error' => 'Invalid date format'], 400);
    }

    Log::info("📆 Filter date converted: $formattedDate");

    // 1. Get all employees by school and role

    $employees = Hr_user_list::with('hrRole')
        ->where('school_id', $schoolId)
        ->when($roleId !== 'All', function ($q) use ($roleId) {
            return $q->where('hr_roles_role_id', $roleId);
        })
        ->get();

    Log::info('👥 Total employees fetched: ' . $employees->count());

    // 2. Get attendance for the selected date
    $attendanceMap = HrDailyAttendence::whereDate('emp_intime', $formattedDate)
        ->where('school_id', $schoolId)
        ->get()
        ->keyBy('user_id');

    Log::info('🕒 Attendance records fetched: ' . $attendanceMap->count());

    // 3. Merge employees with their attendance
    $records = $employees->map(function ($emp) use ($attendanceMap) {
        $att = $attendanceMap[$emp->id] ?? null;
        return (object) [
            'name'       => $emp->name,
            'role'       => $emp->hrRole->name ?? 'N/A',
            'bioid'      => $emp->emp_bioid ?? 'N/A',
            'device_id'  => $att->device_id ?? '-',
            'emp_intime' => $att->emp_intime ?? null,
            'emp_outtime'=> $att->emp_outtime ?? null,
            'remark'     => $att ? ($att->device_id ? 'Automatic' : 'Manual') : 'N/A',
        ];
    });

    Log::info('📦 Final merged record count: ' . $records->count());

    $roleName = $roleId === 'All'
        ? 'All Roles'
        : (Hr_roles::find($roleId) ? Hr_roles::find($roleId)->name : 'N/A');

    Log::info("📤 Rendering partial view with role: $roleName");

    return view('admin.attendance.hr_daily_attendance_table', [
        'records'   => $records,
        'date'      => $formattedDate,
        'role_name' => $roleName,
        'is_pdf'    => false,
    ]);
}



public function exportEmployeeDailyAttendancePDF(Request $request)
{
    \Log::info('📄 Exporting Employee Attendance PDF');

    $date = $request->input('date');
    $roleId = $request->input('role_id');
    $schoolId = auth()->user()->school_id;

    // Convert to Y-m-d format
    $formattedDate = \Carbon\Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');

    // Step 1: Get all employees (with roles)

$employees = Hr_user_list::with('hrRole')
    ->where('school_id', $schoolId)
    ->when($roleId !== 'All', function ($q) use ($roleId) {
        return $q->where('hr_roles_role_id', $roleId);
    })
    ->get();

    // Step 2: Get attendance of that day
    $attendanceMap = HrDailyAttendence::whereDate('emp_intime', $formattedDate)
        ->where('school_id', $schoolId)
        ->get()
        ->keyBy('user_id'); // Make sure this matches Hr_user_list.id

    // Step 3: Merge employee info with attendance
    $records = $employees->map(function ($emp) use ($attendanceMap) {
        $att = $attendanceMap[$emp->id] ?? null;
        return (object) [
            'name'       => $emp->name,
            'role'       => $emp->hrRole->name ?? 'N/A',
            'bioid'      => $emp->emp_bioid ?? 'N/A',
            'device_id'  => $att->device_id ?? '-',
            'emp_intime' => $att->emp_intime ?? null,
            'emp_outtime'=> $att->emp_outtime ?? null,
            'remark'     => $att ? ($att->device_id ? 'Automatic' : 'Manual') : 'N/A',
        ];
    });

    // Step 4: Get role name
    $role_name = $roleId === 'All'
        ? 'All Roles'
        : (Hr_roles::find($roleId)->name ?? 'N/A');

    // Step 5: Return PDF
    return \PDF::loadView('admin.attendance.hr_daily_attendance_table', [
        'records'   => $records,
        'date'      => $formattedDate,
        'role_name' => $role_name,
        'pdf'       => 1, // this is required in Blade for heading logic
    ])
    ->setPaper('a4', 'portrait')
    ->download('Employee_Attendance_Report_' . date('d-m-Y', strtotime($formattedDate)) . '.pdf');
}




/*
public function exportEmployeeDailyAttendancePDF(Request $request)
{
    \Log::info('📄 Exporting Daily Attendance PDF');

    $date = $request->input('date');
    $role_id = $request->input('role_id');
    $school_id = auth()->user()->school_id;

    $formattedDate = date('Y-m-d', strtotime($date));

    // 1. Fetch all employees of selected role
    $employees = Hr_user_list::with('hrRole')
        ->where('school_id', $school_id)
        ->when($role_id !== 'All', function ($q) use ($role_id) {
            return $q->where('role_id', $role_id);
        })
        ->get();

    // 2. Fetch all attendances of the date
    $attendanceMap = HrDailyAttendence::whereDate('emp_intime', $formattedDate)
        ->where('school_id', $school_id)
        ->get()
        ->keyBy('user_id'); // user_id in HrDailyAttendence matches Hr_user_list.id

    // 3. Merge: Combine employee and attendance
    $records = $employees->map(function ($emp) use ($attendanceMap) {
        $att = isset($attendanceMap[$emp->id]) ? $attendanceMap[$emp->id] : null;
        return (object) [
            'name'       => $emp->name,
            'role'       => $emp->hrRole->name ?? 'N/A',
            'bioid'      => $emp->emp_bioid ?? 'N/A',
            'device_id'  => $att->device_id ?? '-',
            'emp_intime' => $att->emp_intime ?? null,
            'emp_outtime'=> $att->emp_outtime ?? null,
            'remark'     => $att ? ($att->device_id ? 'Automatic' : 'Manual') : 'N/A',
        ];
    });

    $role_name = $role_id === 'All'
        ? 'All Roles'
        : (Hr_roles::find($role_id) ? Hr_roles::find($role_id)->name : 'N/A');

    $pdf = Pdf::loadView('admin.attendance.hr_daily_attendance_table', [
        'records'   => $records,
        'date'      => $date,
        'role_name' => ucfirst($role_name),
        'is_pdf'    => true,
    ])->setPaper('A4', 'portrait');

    return $pdf->download('Employee_Daily_Attendance_' . date('d-m-Y', strtotime($date)) . '.pdf');
}
*/

public function exportHrDailyCSV(Request $request)
{
    \Log::info('📥 exportHrDailyCSV called', $request->all());

    $date    = $request->input('date');
    $role_id = $request->input('role_id');

    // Validate date
    $parsedDate = \DateTime::createFromFormat('m/d/Y', $date);
    if (!$parsedDate) {
        \Log::error('❌ Invalid date format', ['input' => $date]);
        return response()->json(['error' => 'Invalid date format']);
    }

    $formattedDate = $parsedDate->format('Y-m-d');
    \Log::info('📅 Parsed export date', ['formatted' => $formattedDate]);

    // Get attendance records
    $attendances = HrDailyAttendence::with(['user', 'role'])
        ->whereDate('created_at', $formattedDate)
        ->when($role_id !== 'All', function ($query) use ($role_id) {
            $query->where('hr_roles_role_id', $role_id);
        })
        ->get();

    \Log::info('✅ Attendance records fetched', ['count' => $attendances->count()]);

    // Prepare filename
    $roleName = 'All_Roles';
    if ($role_id !== 'All') {
        $roleModel = \App\Models\Addon\Hr_roles::find($role_id);
        $roleName = $roleModel ? str_replace(' ', '_', $roleModel->name) : 'Role';
    }

    $filename = "HR_Attendance_{$roleName}_" . $parsedDate->format('Ymd') . ".csv";

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $columns = ['Employee Name', 'Role', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($attendances, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($attendances as $row) {
            $user      = $row->user;
            $role      = $row->role;
            $empName   = $user ? $user->name : 'N/A';
            $roleName  = $role ? $role->name : 'Unknown';
            $statusStr = $row->status == 1 ? 'Present' : 'Absent';
            $dateOnly  = date('d-m-Y', strtotime($row->created_at));
            $inTime    = $row->emp_intime ? date('H:i:s', strtotime($row->emp_intime)) : '';
            $outTime   = $row->emp_outtime ? date('H:i:s', strtotime($row->emp_outtime)) : '';

            fputcsv($file, [
                $empName,
                $roleName,
                $dateOnly,
                $inTime,
                $outTime,
                $statusStr
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




    /**
     * Display attendance records for both students and employees.
     */

     /*
    public function showStudentAttendance()
    {
        $studentAttendances = DailyAttendances::join('enrollments', 'daily_attendances.student_id', '=', 'enrollments.user_id')
            ->select(
                'daily_attendances.id',
                'daily_attendances.class_id',
                'daily_attendances.section_id',
                'daily_attendances.student_id',
                'daily_attendances.status',
                'daily_attendances.session_id',
                'daily_attendances.school_id',
                'daily_attendances.device_id',
                'daily_attendances.stu_intime',
                'daily_attendances.stu_outtime',
                'enrollments.stu_bioid'
            )
            ->orderBy('daily_attendances.stu_intime', 'ASC')
            ->get();
    
        return view('attendance.student_daily_attendance', compact('studentAttendances'));
    }

public function showEmployeeAttendance()
{
    $employeeAttendances = HrDailyAttendence::join('hr_user_list', 'hr_daily_attendences.user_id', '=', 'hr_user_list.id')
        ->select(
            'hr_daily_attendences.id',
            'hr_daily_attendences.role_id',
            'hr_daily_attendences.school_id',
            'hr_daily_attendences.device_id',
            'hr_daily_attendences.emp_intime',
            'hr_daily_attendences.emp_outtime',
            'hr_daily_attendences.session_id',
            'hr_daily_attendences.status',
            'hr_user_list.emp_bioid'
        )
        ->orderBy('hr_daily_attendences.emp_intime', 'ASC')
        ->get();

    return view('attendance.employee_attendance_report', compact('employeeAttendances'));
}
*/



public function updateStudentAttendanceData(Request $request)
{
    $month      = $request->input('month');
    $year       = $request->input('year');
    $class_id   = $request->input('class_id');
    $section_id = $request->input('section_id');

    session([
        'student_attendance_filter' => [
            'month'      => $month,
            'year'       => $year,
            'class_id'   => $class_id,
            'section_id' => $section_id,
        ]
    ]);

    return response()->json(['success' => true, 'message' => 'Student attendance data updated.']);
}



public function exportStudentAttendancePDF(Request $request)
{
       ini_set('memory_limit', '512M'); // or '1G'
    //ini_set('max_execution_time', 300); // 5 minutes
    // Get filter values
    $filters = session('student_attendance_filter', [
        'month'      => date('M'),
        'year'       => date('Y'),
        'class_id'   => null,
        'section_id' => null
    ]);

    $filters['month'] = $request->input('month', $filters['month']);
    $filters['year'] = $request->input('year', $filters['year']);
    $filters['class_id'] = $request->input('class_id', $filters['class_id']);
    $filters['section_id'] = $request->input('section_id', $filters['section_id']);

    // Convert month name to numeric format
    $monthNumeric = date('m', strtotime('01 ' . $filters['month'] . ' ' . $filters['year']));
    $year = $filters['year'];
    $startDate = "$year-$monthNumeric-01 00:00:00";
    $endDate   = date('Y-m-t 23:59:59', strtotime($startDate));

    // Fetch class & section
    $class = Classes::find($filters['class_id']);
    $section = Section::find($filters['section_id']);
    $class_name = $class ? $class->name : 'Unknown Class';
    $section_name = $section ? $section->name : 'Unknown Section';

    // Get attendance
    $studentAttendances = DailyAttendances::join('enrollments', 'daily_attendances.student_id', '=', 'enrollments.user_id')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->select([
            'daily_attendances.*',
            'enrollments.stu_bioid',
            'users.name as student_name'
        ])
        ->where('daily_attendances.school_id', auth()->user()->school_id)
        ->where('daily_attendances.session_id', get_school_settings(auth()->user()->school_id)->value('running_session'))
        ->where('daily_attendances.class_id', $filters['class_id'])
        ->where('daily_attendances.section_id', $filters['section_id'])
        ->whereBetween('daily_attendances.timestamp', [$startDate, $endDate])
        ->orderBy('daily_attendances.timestamp', 'asc')
        ->get();

    if ($studentAttendances->isEmpty()) {
        return back()->with('error', '⚠ No attendance records found for the selected filters.');
    }

    // Grouped & summarized
    $groupedAttendances = $studentAttendances->groupBy('student_name');
    $summary = $studentAttendances->groupBy('student_name')->map(function ($records) {
        return [
            'bio_id'  => $records->first()->stu_bioid,
            'present' => $records->where('status', 1)->count(),
            'leave'   => $records->where('status', 0)->count(),
        ];
    });

    // ✅ Define $selectedMonthAbbrev and $selectedYear
    $selectedMonthAbbrev = $filters['month'];
    $selectedYear = $filters['year'];
    //$lastRecord = DailyAttendances::latest('updated_at')->first();

    return PDF::loadView('attendance.student_attendance_report', compact(
        'groupedAttendances',
        'summary',
        'class_name',
        'section_name',
        'selectedMonthAbbrev',
        'selectedYear'
        //'lastRecord'
    ))->setPaper('a4', 'landscape')
      ->download('Student_Attendance_Report_' . $selectedMonthAbbrev . '_' . $selectedYear . '.pdf');
}


        
public function updateStudentAttendanceDataTeacher(Request $request)
{
    $month      = $request->input('month');
    $year       = $request->input('year');
    $class_id   = $request->input('class_id');
    $section_id = $request->input('section_id');

    session([
        'teacher_student_attendance_filter' => [
            'month'      => $month,
            'year'       => $year,
            'class_id'   => $class_id,
            'section_id' => $section_id,
        ]
    ]);

    return response()->json(['success' => true, 'message' => 'Student attendance data for teacher updated.']);
}



public function exportStudentAttendancePDFTeacher(Request $request)
{    Log::error("exportStudentAttendancePDFTeacher function started");

    ini_set('memory_limit', '512M'); 
    ini_set('max_execution_time', 300); 

    $filters = session('teacher_student_attendance_filter', [
        'month'      => date('M'),
        'year'       => date('Y'),
        'class_id'   => null,
        'section_id' => null
    ]);

    $filters['month'] = $request->input('month', $filters['month']);
    $filters['year'] = $request->input('year', $filters['year']);
    $filters['class_id'] = $request->input('class_id', $filters['class_id']);
    $filters['section_id'] = $request->input('section_id', $filters['section_id']);

    $monthNumeric = date('m', strtotime('01 ' . $filters['month'] . ' ' . $filters['year']));
    $year = $filters['year'];
    $startDate = "$year-$monthNumeric-01 00:00:00";
    $endDate   = date('Y-m-t 23:59:59', strtotime($startDate));

    $class = Classes::find($filters['class_id']);
    $section = Section::find($filters['section_id']);
    $class_name = $class ? $class->name : 'Unknown Class';
    $section_name = $section ? $section->name : 'Unknown Section';

    $studentAttendances = DailyAttendances::join('enrollments', 'daily_attendances.student_id', '=', 'enrollments.user_id')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->select([
            'daily_attendances.*',
            'enrollments.stu_bioid',
            'users.name as student_name'
        ])
        ->where('daily_attendances.school_id', auth()->user()->school_id)
        ->where('daily_attendances.session_id', get_school_settings(auth()->user()->school_id)->value('running_session'))
        ->where('daily_attendances.class_id', $filters['class_id'])
        ->where('daily_attendances.section_id', $filters['section_id'])
        ->whereBetween('daily_attendances.timestamp', [$startDate, $endDate])
        ->orderBy('daily_attendances.timestamp', 'asc')
        ->get();

    if ($studentAttendances->isEmpty()) {
        return back()->with('error', '⚠ No attendance records found for the selected filters.');
    }

    $groupedAttendances = $studentAttendances->groupBy('student_name');
    $summary = $studentAttendances->groupBy('student_name')->map(function ($records) {
        return [
            'bio_id'  => $records->first()->stu_bioid,
            'present' => $records->where('status', 1)->count(),
            'leave'   => $records->where('status', 0)->count(),
        ];
    });

    $selectedMonthAbbrev = $filters['month'];
    $selectedYear = $filters['year'];

    return PDF::loadView('attendance.student_attendance_report_teacher', compact(
        'groupedAttendances',
        'summary',
        'class_name',
        'section_name',
        'selectedMonthAbbrev',
        'selectedYear'
    ))->setPaper('a4', 'landscape')
      ->download('Student_Attendance_Report_' . $selectedMonthAbbrev . '_' . $selectedYear . '.pdf');
}

public function exportStudentAttendanceCSVTeacher(Request $request)
{
    \Log::info("📥 exportStudentAttendanceCSVTeacher() started", $request->all());

    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);

    $filters = session('teacher_student_attendance_filter', [
        'month'      => date('M'),
        'year'       => date('Y'),
        'class_id'   => null,
        'section_id' => null
    ]);

    $filters['month']      = $request->input('month', $filters['month']);
    $filters['year']       = $request->input('year', $filters['year']);
    $filters['class_id']   = $request->input('class_id', $filters['class_id']);
    $filters['section_id'] = $request->input('section_id', $filters['section_id']);

    $monthNumeric = date('m', strtotime("01 {$filters['month']} {$filters['year']}"));
    $year = $filters['year'];
    $startDate = "$year-$monthNumeric-01 00:00:00";
    $endDate   = date('Y-m-t 23:59:59', strtotime($startDate));

    \Log::info("📅 Date range", ['start' => $startDate, 'end' => $endDate]);

    $school_id = auth()->user()->school_id;
    $session_id = get_school_settings($school_id)->value('running_session');

    $records = DailyAttendances::join('enrollments', 'daily_attendances.student_id', '=', 'enrollments.user_id')
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->select([
            'users.name as student_name',
            'enrollments.stu_bioid',
            'daily_attendances.timestamp',
            'daily_attendances.status',
            'daily_attendances.stu_intime',
            'daily_attendances.stu_outtime',
        ])
        ->where('daily_attendances.school_id', $school_id)
        ->where('daily_attendances.session_id', $session_id)
        ->where('daily_attendances.class_id', $filters['class_id'])
        ->where('daily_attendances.section_id', $filters['section_id'])
        ->whereBetween('daily_attendances.timestamp', [$startDate, $endDate])
        ->orderBy('daily_attendances.timestamp', 'asc')
        ->get();

    \Log::info("✅ Attendance records fetched", ['count' => $records->count()]);

    if ($records->isEmpty()) {
        return back()->with('error', '⚠ No attendance records found for selected filters.');
    }

    $filename = "Student_Attendance_{$filters['month']}_{$filters['year']}.csv";
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate',
        'Expires' => '0'
    ];

    $columns = ['Student Name', 'Student ID', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($records, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($records as $row) {
            fputcsv($file, [
                $row->student_name ?? 'N/A',
                $row->stu_bioid ?? 'N/A',
                date('d-m-Y', strtotime($row->timestamp)),
                $row->stu_intime ? date('H:i:s', strtotime($row->stu_intime)) : '',
                $row->stu_outtime ? date('H:i:s', strtotime($row->stu_outtime)) : '',
                $row->status == 1 ? 'Present' : 'Absent'
            ]);
        }

        fclose($file);
    };

    \Log::info("📤 CSV generation complete. Downloading...");

    return response()->stream($callback, 200, $headers);
}




    /**
     * 🔹 Update Employee Attendance Data in Session (For PDF Export)
     */
        
public function updateEmployeeAttendanceData(Request $request)
{
    $month   = $request->input('month');
    $year    = $request->input('year');
    $role_id = $request->input('role_id');

    // Store filters in session
    session([
        'employee_attendance_filter' => [
            'month'   => $month,
            'year'    => $year,
            'role_id' => $role_id,
        ]
    ]);

    \Log::info('🔹 Employee Attendance Data Updated in Session', ['filters' => session('employee_attendance_filter')]);

    return response()->json(['success' => true, 'message' => 'Employee attendance data updated successfully.']);
}


    /**
     * 🔹 Export Employee Attendance Report as PDF
     */
    
public function exportEmployeeAttendancePDF(Request $request)
{
    \Log::info('🚀 Step 1: Export PDF triggered');
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);

    $filters = [
        'month'   => $request->input('month', date('M')),
        'year'    => $request->input('year', date('Y')),
        'role_id' => $request->input('role_id', null),
    ];
    \Log::info('✅ Filters retrieved', $filters);

    $monthNumeric = date('m', strtotime('01-' . $filters['month'] . '-' . $filters['year']));
    $year = $filters['year'];
    $startDate = date('Y-m-01 00:00:00', strtotime("$year-$monthNumeric-01"));
    $endDate   = date('Y-m-t 23:59:59', strtotime($startDate));

    $school_id = auth()->user()->school_id;
    $runningSession = get_school_settings($school_id)->value('running_session');

    \Log::info('📌 Date Range:', ['from' => $startDate, 'to' => $endDate]);
    \Log::info('🏫 School & Session:', ['school_id' => $school_id, 'session_id' => $runningSession]);

    $query = HrDailyAttendence::leftJoin('hr_user_list', 'hr_daily_attendences.user_id', '=', 'hr_user_list.id')
        ->leftJoin('users', 'hr_user_list.user_id', '=', 'users.id')
        ->leftJoin('hr_roles', 'hr_daily_attendences.hr_roles_role_id', '=', 'hr_roles.id')
        ->select([
            'hr_daily_attendences.*',
            'hr_roles.name as role_name',
            'users.name as username',
            'hr_user_list.emp_bioid',
            'hr_user_list.name as employee_name'
        ])
        ->where('hr_daily_attendences.school_id', $school_id)
        ->where('hr_daily_attendences.session_id', $runningSession)
        ->whereBetween('hr_daily_attendences.created_at', [$startDate, $endDate])
        ->whereIn('hr_daily_attendences.status', [0, 1]); // only Present/Absent

    // ✅ Apply role-based filter using hr_user_list.role_id (NOT hr_roles.id)
    if (!empty($filters['role_id']) && $filters['role_id'] !== "All") {
        $filteredUserIds = \App\Models\Addon\Hr_user_list::where('school_id', $school_id)
            ->where('role_id', $filters['role_id'])
            ->pluck('id');

        \Log::info('📌 Filtering by HR role_id:', ['hr_user_list.role_id' => $filters['role_id'], 'user_ids' => $filteredUserIds]);

        $query->whereIn('hr_daily_attendences.user_id', $filteredUserIds);
    } else {
        \Log::info('📌 No specific role filter applied.');
    }

    // ✅ Restrict for non-admin user
    if (auth()->user()->role_id != 2) {
        $hr_user = \App\Models\Addon\Hr_user_list::where('user_id', auth()->id())
            ->where('school_id', $school_id)
            ->first();

        if ($hr_user) {
            \Log::info('👤 Non-admin user; restricting to own HR record', ['hr_user_id' => $hr_user->id]);
            $query->where('hr_daily_attendences.user_id', $hr_user->id);
        } else {
            \Log::warning('⚠️ HR user mapping not found for current user.', ['user_id' => auth()->id()]);
            return back()->withErrors(['error' => 'HR user mapping not found.']);
        }
    }

    \Log::debug('🔍 Final SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

    $employeeAttendances = $query->get();

    \Log::info('📋 Attendance record count:', ['records' => $employeeAttendances->count()]);

    if ($employeeAttendances->isEmpty()) {
        \Log::warning('⚠️ No attendance records found for PDF export.', compact('filters', 'startDate', 'endDate', 'runningSession'));
        return back()->with('error', '⚠️ No attendance records found for selected filters.');
    }

    $role_name = (!empty($filters['role_id']) && $filters['role_id'] !== 'All') ?
        Hr_roles::where('id', $filters['role_id'])->value('name') :
        'All Roles';

    \Log::info('📄 Generating PDF with role:', ['role_name' => $role_name]);

    $pdf = PDF::loadView('attendance.employee_attendance_report', compact('employeeAttendances', 'role_name', 'filters'))
        ->setPaper('a4', 'landscape');

    \Log::info('✅ PDF generation successful. Download ready.');

    return $pdf->download('employee-attendance-report.pdf');
}
   
   
   
   
   public function exportEmployeeAttendanceCSV(Request $request)
{
    \Log::info('📥 CSV Export triggered', $request->all());

    $month = $request->input('month', date('M'));
    $year = $request->input('year', date('Y'));
    $role_id = $request->input('role_id', null);

    $monthNumeric = date('m', strtotime('01-' . $month . '-' . $year));
    $startDate = date('Y-m-01 00:00:00', strtotime("$year-$monthNumeric-01"));
    $endDate = date('Y-m-t 23:59:59', strtotime($startDate));

    $school_id = auth()->user()->school_id;
    $session_id = get_school_settings($school_id)->value('running_session');

    $query = HrDailyAttendence::leftJoin('hr_user_list', 'hr_daily_attendences.user_id', '=', 'hr_user_list.id')
        ->leftJoin('users', 'hr_user_list.user_id', '=', 'users.id')
        ->leftJoin('hr_roles', 'hr_daily_attendences.hr_roles_role_id', '=', 'hr_roles.id')
        ->select([
            'users.name as username',
            'hr_user_list.name as employee_name',
            'hr_roles.name as role_name',
            'hr_daily_attendences.created_at',
            'hr_daily_attendences.emp_intime',
            'hr_daily_attendences.emp_outtime',
            'hr_daily_attendences.status'
        ])
        ->where('hr_daily_attendences.school_id', $school_id)
        ->where('hr_daily_attendences.session_id', $session_id)
        ->whereBetween('hr_daily_attendences.created_at', [$startDate, $endDate])
        ->whereIn('hr_daily_attendences.status', [0, 1]);

    if (!empty($role_id) && $role_id !== 'All') {
        $filteredUserIds = \App\Models\Addon\Hr_user_list::where('school_id', $school_id)
            ->where('role_id', $role_id)
            ->pluck('id');
        $query->whereIn('hr_daily_attendences.user_id', $filteredUserIds);
    }

    if (auth()->user()->role_id != 2) {
        $hr_user = \App\Models\Addon\Hr_user_list::where('user_id', auth()->id())
            ->where('school_id', $school_id)
            ->first();

        if ($hr_user) {
            $query->where('hr_daily_attendences.user_id', $hr_user->id);
        } else {
            \Log::warning('⚠️ HR user mapping not found for CSV export.', ['user_id' => auth()->id()]);
            return back()->withErrors(['error' => 'HR user mapping not found.']);
        }
    }

    $records = $query->get();

    if ($records->isEmpty()) {
        \Log::info('⚠️ No records to export.');
        return back()->withErrors(['error' => 'No attendance records found.']);
    }

    $filename = "employee_attendance_report_{$month}_{$year}.csv";

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate',
        'Expires' => '0',
    ];

    $columns = ['Employee Name', 'Role', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($records, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($records as $row) {
            fputcsv($file, [
                $row->employee_name ?? $row->username ?? 'N/A',
                $row->role_name ?? 'N/A',
                date('d-m-Y', strtotime($row->created_at)),
                $row->emp_intime ? date('H:i:s', strtotime($row->emp_intime)) : '',
                $row->emp_outtime ? date('H:i:s', strtotime($row->emp_outtime)) : '',
                $row->status == 1 ? 'Present' : 'Absent'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


/*

    public function viewEmployeeAttendanceReport(Request $request)
    {
        Log::info('🔹 Starting Employee Attendance Report Retrieval');
    
        $role_id = $request->input('role_id'); // Get role_id from request (if any)
    
        // Fetch employee attendance records with role name and employee name
        $query = HrDailyAttendence::join('hr_user_list', 'hr_daily_attendences.user_id', '=', 'hr_user_list.id')
            ->join('hr_roles', 'hr_daily_attendences.role_id', '=', 'hr_roles.id') // Join to get role name
            ->join('users', 'hr_user_list.user_id', '=', 'users.id') // Join to get employee name
            ->select([
                'hr_daily_attendences.id',
                'hr_roles.name as role_name', // Fetch role name
                'users.name as employee_name', // Fetch employee name
                'hr_daily_attendences.school_id',
                'hr_daily_attendences.device_id',
                'hr_daily_attendences.emp_intime',
                'hr_daily_attendences.emp_outtime',
                'hr_daily_attendences.session_id',
                'hr_daily_attendences.status',
                'hr_user_list.emp_bioid',
                'hr_daily_attendences.created_at'
            ])
            ->where('hr_daily_attendences.school_id', auth()->user()->school_id)
            ->where('hr_daily_attendences.session_id', get_school_settings(auth()->user()->school_id)->value('running_session'));
    
        // **Filter by Role if Selected**
        if (!empty($role_id)) {
            $query->where('hr_daily_attendences.role_id', $role_id);
            Log::info("🔹 Filtering Employee Attendance for Role ID: $role_id");
        }
    
        // Get results
        $employeeAttendances = $query->orderBy('hr_daily_attendences.created_at', 'desc')->get();
    
        // Log retrieved records
        Log::info('🔹 Retrieved Employee Attendance Data:', ['total_records' => count($employeeAttendances)]);
    
        if ($employeeAttendances->isEmpty()) {
            Log::warning('⚠ No attendance records found.');
        }
    
        // Fetch all roles for filter dropdown
        $roles = Hr_roles::all();
    
        // Return view with data
        return view('attendance.employee_attendance_report', compact('employeeAttendances', 'roles', 'role_id'));
    }
    */
    
}