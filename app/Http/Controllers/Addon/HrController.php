<?php

namespace App\Http\Controllers\Addon;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addon\LiveClassSettings;
use App\Models\User;
use App\Models\Session;
use App\Models\Addon\LiveClasses;
use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Addon\Leavelist;
use Illuminate\Support\Str;
use App\Models\Addon\HrDailyAttendence;
use App\Models\Gradebook;
use App\Models\Addon\HrPayroll;
use App\Models\Addon\Hr_user_list;
use App\Models\Addon\Hr_roles;
use Illuminate\Support\Facades\Route;
use App\Models\Subscription;
use PDF;


class HrController extends Controller
{
    //userlist

     function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth()->user();
            $this->check_subscription_status(Auth()->user()->school_id);
            $this->check_newschool_status(Auth()->user()->school_id);
            return $next($request);
        });


    }

        function check_subscription_status($school_id = ""){
        $current_route = Route::currentRouteName();
        $active_subscription = Subscription::where('school_id', $school_id)->where('status', 1)->get()->count();

        if(
            ($current_route != 'admin.subscription' && $active_subscription == 0) &&
            ($current_route != 'admin.subscription.purchase' && $active_subscription == 0) &&
            ($current_route != 'admin.subscription.payment' && $active_subscription == 0) &&
            ($current_route != 'admin.subscription.offline_payment' && $active_subscription == 0)
        )
        {
            redirect()->route('admin.subscription')->send();
        }
    }

    function check_newschool_status($school_id = "") {
        $school = DB::table('schools')->latest()->first();

        $admin_role = DB::table('hr_roles')->where('name', 'admin')->where('permanent', 'yes')->where('school_id', $school->id);
    
        if($admin_role->get()->count() == 0){
            DB::table('hr_roles')->insert([
                'name' => 'admin',
                'permanent' => 'yes',
                'school_id' => $school->id,
                'created_at' => '0',
            ]);
        }
    
        $teacher_role = DB::table('hr_roles')->where('name', 'teacher')->where('permanent', 'yes')->where('school_id', $school->id);
    
        if($teacher_role->get()->count() == 0){
            DB::table('hr_roles')->insert([
                'name' => 'teacher',
                'permanent' => 'yes',
                'school_id' => $school->id,
                'created_at' => '0',
            ]);
        }
    
        $accountant_role = DB::table('hr_roles')->where('name', 'accountant')->where('permanent', 'yes')->where('school_id', $school->id);
    
        if($accountant_role->get()->count() == 0){
            DB::table('hr_roles')->insert([
                'name' => 'accountant',
                'permanent' => 'yes',
                'school_id' => $school->id,
                'created_at' => '0',
            ]);
        }
    
        $librarian_role = DB::table('hr_roles')->where('name', 'librarian')->where('permanent', 'yes')->where('school_id', $school->id);
    
        if($librarian_role->get()->count() == 0){
            DB::table('hr_roles')->insert([
                'name' => 'librarian',
                'permanent' => 'yes',
                'school_id' => $school->id,
                'created_at' => '0',
            ]);
        }	
    }

    

    
    public function user_role_index(Request $request)
    {

        $data = $request->all();
        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();


        return view('hr_roles.index', ['roles' => $roles]);
    }

    public function user_role_edit(Request $request, $id)
    {

        $data = $request->all();
        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->where('id', $id)->first()->toArray();


        return view('hr_roles.edit_role', ['roles' => $roles]);
    }

    public function user_role_update(Request $request, $id)
    {


        $data = $request->all();


        Hr_roles::where('id', $id)->where('school_id', auth()->user()->school_id)->update([
            'name' => $data['name'],
        ]);

        return redirect()->back()->with('message', 'role updated successfully');
    }

    public function user_role_delete(Request $request, $id)
    {

        $data = $request->all();

        $role = Hr_roles::find($id);
        $role->delete();

        return redirect()->back()->with('message', 'role deleted successfully');
    }

    public function user_role_create(Request $request)
    {

        $data = $request->all();

        return view('hr_roles.create_role');
    }

    public function user_role_create_post(Request $request)
    {

        $data = $request->all();

        Hr_roles::create([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'created_at' => strtotime(date('d-M-Y')),
        ]);

        return redirect()->back()->with('message', 'role created successfully');
    }

    // user list

    public function userlist_index(Request $request)
    {
        $data = $request->all();
        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();

        return view('hr_user_list.index', ['roles' => $roles, 'data' => $data]);
    }

    public function create_user(Request $request)
    {
        $data = $request->all();


        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();

        return view('hr_user_list.create', ['roles' => $roles]);
    }

    public function create_user_post(Request $request)
    {
        $data = $request->all();

          // 1️⃣ Get Hr_roles record for the selected role_id and school_id
    $check = Hr_roles::where('id', $data['role_id'])
                     ->where('school_id', auth()->user()->school_id)
                     ->first();

    if ($check && $check->permanent == 'yes') {

        // 2️⃣ Map hr_roles.name to Role table for getting Laravel role_id
        $role = Role::where('name', $check->name)->first();

            $info = array(
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'birthday' => strtotime(date('d-m-Y')),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'photo' => "",
            );
            $data['user_information'] = json_encode($info);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $role->role_id,
                'created_at' => strtotime(date('d-m-Y')),
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
            ]);
        }

        Hr_user_list::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'], 
            'hr_roles_role_id' => $check->id,
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'joining_salary' => $data['joining_salary'],
            'school_id' => auth()->user()->school_id,
            'emp_bioid' => $data['emp_bioid']
          


        ]);


        return redirect()->back()->with('message', 'user created successfully');
    }

    public function userlist_import(Request $request)
    {
        $data = $request->all();
        $roles = Hr_roles::where('permanent', 'yes')->where('school_id', auth()->user()->school_id)->get()->toArray();

        return view('hr_user_list.import', ['roles' => $roles]);
    }

    public function userlist_import_post(Request $request)
    {

        $data = $request->all();
        $asked_for_import = $data['role_name'];

        foreach ($asked_for_import as $role) {

            $role_info = Role::where('name', $role)->first()->toArray();

            $role_wise_users = User::where('role_id', $role_info['role_id'])->where('school_id', auth()->user()->school_id)->get()->toArray();

            foreach ($role_wise_users as $user) {
                $user_info = json_decode($user['user_information']);
                $hr_roll = Hr_roles::where('name', $role)->where('school_id', auth()->user()->school_id)->first()->toArray();
                $check_duplication = Hr_user_list::where('email', $user['email'])->get();
                $messege = 0;



                if (count($check_duplication) == 0) {

                    Hr_user_list::create([
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'gender' =>  $user_info->gender??"",
                        'blood_group' => $user_info->blood_group??"",
                        'phone' => $user_info->phone??"",
                        'address' =>  $user_info->address??"",
                        'role_id' =>  $hr_roll['id'],
                        'school_id' => $user['school_id'],
                    ]);
                    $messege = '1';
                }
            }
        }

        if ($messege == 1) {
            return redirect()->back()->with('message', 'user imported successfully');
        } else {
            return redirect()->back()->with('message', 'user imported successfully');
        }
    }

    
    public function userlist_show(Request $request)
    {
        $data = $request->all();
        $role_id = $data['role_id'];

        if ($role_id === 'All') {
            // Fetch all users from all roles in the current school
            $users = Hr_user_list::where('school_id', auth()->user()->school_id)->get();
        } else {
            // Fetch users for selected role
            $users = Hr_user_list::where('school_id', auth()->user()->school_id)
                                ->where('role_id', $role_id)
                                ->get();
        }

        return view('hr_user_list.list', ['role_id' => $role_id, 'users' => $users]);
    }

    // ✅ HrController Method to Show All Staff

    public function showAllStaff()
    {
        $school_id = auth()->user()->school_id;
        $users = Hr_user_list::where('school_id', $school_id)->get();

        return view('hr_user_list.staff_list', compact('users'));
    }
    
    public function user_lists_user_edit_post(Request $request, $id)
    {

        $data = $request->all();

        Hr_user_list::where('id', $id)->where('school_id', auth()->user()->school_id)->update([

            'name' => $data['name'],
            'email' => $data['email'],
            'gender' =>  $data['gender'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'address' =>  $data['address'],
            'role_id' =>  $data['role_id'],
            'joining_salary' =>  $data['joining_salary'],
            'emp_bioid' => $data['emp_bioid']

        ]);

        return $data;
    }


    public function user_lists_user_edit(Request $request, $id)
    {



        $data = $request->all();
        $user = Hr_user_list::where('school_id', auth()->user()->school_id)->where('id', $id)->first()->toArray();

        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get();





        return view('hr_user_list.edit', ['roles' => $roles, 'user' => $user]);
    }



    public function user_lists_user_delete(Request $request, $id)
    {

        $data = $request->all();

        $user = Hr_user_list::find($id);

        $details_in_attendence = HrDailyAttendence::where(array('user_id' =>  $user->id, 'school_id' => $user->school_id))->get();



        if (count($details_in_attendence) > 0) {
            $details_in_attendence = $details_in_attendence->toArray();
            for ($i = 0; $i < count($details_in_attendence); $i++) {
                $delete_attendence = HrDailyAttendence::find($details_in_attendence[$i]['id']);
                $delete_attendence->delete();
            }
        }




        $details_in_leavelist = Leavelist::where(array('user_id' =>  $user->id, 'school_id' => $user->school_id))->get();

        if (count($details_in_leavelist) > 0) {
            $details_in_leavelist = $details_in_leavelist->toArray();
            for ($i = 0; $i < count($details_in_leavelist); $i++) {
                $delete_leave = Leavelist::find($details_in_leavelist[$i]['id']);
                $delete_leave->delete();
            }
        }


        $details_in_payroll = HrPayroll::where(array('user_id' =>  $user->id, 'school_id' => $user->school_id))->get();

        if (count($details_in_payroll) > 0) {
            $details_in_payroll = $details_in_payroll->toArray();
            for ($i = 0; $i < count($details_in_payroll); $i++) {
                $delete_payroll = HrPayroll::find($details_in_payroll[$i]['id']);
                $delete_payroll->delete();
            }
        }


        $user->delete();

        return "data deleted successfully";
    }



    public function list_of_attendence(Request $request)
    {

        $data = $request->all();
        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";
        $no_user=1;



        $user_role = Hr_user_list::where('email', auth()->user()->email)->first();
        if(!empty($user_role))
        {
            $user_role=$user_role->toArray();
            $user_role = $user_role['role_id'];

        }
        else
        {
            $user_role=auth()->user()->role_id;
            $user_role=$user_role-1;

            if(auth()->user()->role_id!=2)
            {
                $no_user=0;
            }

        }






        return view('hr_daily_attendence.list_of_attendence', ['roleName' => $roleName, 'loaddata' => 0,'user_role'=>$user_role,'no_user'=>$no_user]);
    }

    public function show_take_attendence_modal(Request $request) // leave list
    {
        \Log::info('Hello World - show_take_attendence_modal function started.');
       
        $data = $request->all();
        return view('hr_daily_attendence.take_attendence');
    }

    /*
    public function roleWiseUserlist(Request $request)
    {

        $data = $request->all();
        $users = Hr_user_list::get()->where('role_id', $data['role_id'])->toArray();
        return view('hr_daily_attendence.attendence_view', ['users' => $users, 'date' => $data['date'], 'role_id' => $data['role_id']]);
    }
        */




        public function roleWiseUserlist(Request $request)
        {
            \Log::info('==> roleWiseUserlist started');
        
            $data = $request->all();
            \Log::info('Received request data:', $data);
        
            $user = auth()->user();
            \Log::info('Authenticated user ID: ' . $user->id . ', School ID: ' . $user->school_id);
        
            if (!isset($data['role_id']) || !isset($data['date'])) {
                \Log::warning('Missing required parameters: role_id or date');
                return response()->json(['error' => 'Missing role_id or date'], 400);
            }
        
            \Log::info('Requested role_id: ' . $data['role_id'] . ', Date: ' . $data['date']);
        
            try {
                // ✅ FIX: Query by hr_roles_role_id instead of role_id
                $users = Hr_user_list::where([
                    'hr_roles_role_id' => $data['role_id'],   // Fixed line
                    'school_id' => $user->school_id
                ])->get();
        
                $userCount = $users->count();
                \Log::info("Users fetched count: " . $userCount);
                \Log::info('User data (first user): ', $userCount > 0 ? [$users->first()->toArray()] : []);
            } catch (\Exception $e) {
                \Log::error('Error fetching users: ' . $e->getMessage());
                return response()->json(['error' => 'Error fetching users'], 500);
            }
        
            \Log::info('Rendering view hr_daily_attendence.attendence_view with data');
        
            try {
                return view('hr_daily_attendence.attendence_view', [
                    'users' => $users->toArray(), 
                    'date' => $data['date'], 
                    'role_id' => $data['role_id']
                ]);
            } catch (\Exception $e) {
                \Log::error('Error rendering view: ' . $e->getMessage());
                return response()->json(['error' => 'Error rendering view'], 500);
            }
        }
        
/*
    public function hr_take_attendance(Request $request)
    {
        \Log::info('Hello World - hr_take_attendance function started.');
        Log::info('Incoming request data: ', $request->all());
        $att_data = $request->all();



        $users = $att_data['user_id'];


        $active_session = Session::where('status', 1)->first();

        $data['created_at'] = strtotime($att_data['date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session->id;
        $data['role_id'] =  $att_data['role_id'];



        $check_data = HrDailyAttendence::where(['created_at' => $data['created_at'], 'role_id' => $data['role_id'], 'session_id' => $active_session->id, 'school_id' => auth()->user()->school_id])->get();

        if (count($check_data) > 0) {
            foreach ($users as $key => $user) :
                $data['status'] = $att_data['status-' . $user];

                $data['user_id'] = $user;

               // $attendance_id = $att_data['attendance_id'];


               // HrDailyAttendence::where('id', $attendance_id[$key])->update($data);

               $attendance_id = $att_data['attendance_id'][$key];  // Get the corresponding attendance ID
               HrDailyAttendence::where('id', $attendance_id)->update($data);  // Update the attendance record
            endforeach;
        } else {
            foreach ($users as $user) :
                $data['status'] = $att_data['status-' . $user];
                $data['user_id'] = $user;

                HrDailyAttendence::create($data);

            endforeach;
        }


        return redirect()->back()->with('message', ' attendance updated successfully.');
    }*/


    public function dailyAttendancePage()
    {
        // Fetch data required by the view, e.g. employee list, roles, etc.
        return view('hr_daily_attendence.hr_daily_attendance'); // Adjust path as per your folder structure
    }    
    
    
    
    
    public function hr_take_attendance(Request $request)
{
    Log::info('✅ hr_take_attendance function started.');
    Log::info('📥 Incoming request data:', $request->all());

    $att_data = $request->all();
    $users = $att_data['user_id'];

    $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
    $attendance_date = date('Y-m-d', strtotime($att_data['date']));
    $school_id = auth()->user()->school_id;

    // Map hr_roles.id (frontend) → roles.role_id
    $hrRole = Hr_roles::find($att_data['role_id']);
    $roleName = $hrRole ? $hrRole->name : null;

    $rolesTableRoleId = Role::whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                            ->value('role_id');

    Log::info("🔁 Mapped hr_roles.id = {$att_data['role_id']} → roles.role_id = {$rolesTableRoleId}");

    foreach ($users as $key => $user_id) {
        $status = $att_data['status-' . $user_id];

        // Always use 00:00:00 for manual attendance
        $intime = $attendance_date . ' 00:00:00';
        $outtime = $attendance_date . ' 00:00:00';
        $created_at = $intime;

        Log::info("✅ Setting emp_intime = $intime, emp_outtime = $outtime for user_id: $user_id");

        $data = [
            'user_id'           => $user_id,
            'status'            => $status,
            'emp_intime'        => $intime,
            'emp_outtime'       => $outtime,
            'created_at'        => $created_at,
            'updated_at'        => now(),
            'school_id'         => $school_id,
            'session_id'        => $session_id,
            'role_id'           => $rolesTableRoleId,
            'hr_roles_role_id'  => $att_data['role_id'],
        ];

        if (!empty($att_data['attendance_id'][$key])) {
            HrDailyAttendence::where('id', $att_data['attendance_id'][$key])->update($data);
            Log::info("📝 Attendance updated for user_id: $user_id");
        } else {
            HrDailyAttendence::create($data);
            Log::info("🆕 New attendance created for user_id: $user_id");
        }
    }

    return redirect()->back()->with('message', '✅ Attendance updated successfully.');
}

    
    
    
    
    
    
    
       
 /*      
       //Added default timmings for emp_intime, emp_outtime and created_at
    public function hr_take_attendance(Request $request)
    {
        Log::info('✅ hr_take_attendance function started.');
        Log::info('📥 Incoming request data:', $request->all());
    
        $att_data = $request->all();
        $users = $att_data['user_id'];
    
        // Get session_id
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $attendance_date = date('Y-m-d', strtotime($att_data['date']));
        $school_id = auth()->user()->school_id;
    
        // Map hr_roles.id (frontend) → roles.role_id
        $hrRole = Hr_roles::find($att_data['role_id']);
        $roleName = $hrRole ? $hrRole->name : null;
    
        $rolesTableRoleId = Role::whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                                ->value('role_id');
    
        Log::info("🔁 Mapped hr_roles.id = {$att_data['role_id']} → roles.role_id = {$rolesTableRoleId}");
    
        foreach ($users as $key => $user_id) {
            // Get attendance status
            $status = $att_data['status-' . $user_id];
    
            // Default timing logic
            $intime  = $att_data['emp_intime'][$key] ?? ($attendance_date . ' 09:30:00');
            $outtime = $att_data['emp_outtime'][$key] ?? ($attendance_date . ' 17:30:00');
    
            // Use intime as created_at timestamp
            $created_at = $intime;
    
            $data = [
                'user_id'           => $user_id,
                'status'            => $status,
                'emp_intime'        => $intime,
                'emp_outtime'       => $outtime,
                'created_at'        => $created_at,
                'updated_at'        => now(),
                'school_id'         => $school_id,
                'session_id'        => $session_id,
                'role_id'           => $rolesTableRoleId,
                'hr_roles_role_id'  => $att_data['role_id'],
            ];
    
            if (!empty($att_data['attendance_id'][$key])) {
                HrDailyAttendence::where('id', $att_data['attendance_id'][$key])->update($data);
                Log::info("📝 Attendance updated for user_id: $user_id");
            } else {
                HrDailyAttendence::create($data);
                Log::info("🆕 New attendance created for user_id: $user_id");
            }
        }
    
        return redirect()->back()->with('message', '✅ Attendance updated successfully.');
    }

 */      
       
       
       
       //Shital 
        
        /*

    public function hr_take_attendance(Request $request)
    {
        Log::info('Hello World - hr_take_attendance function started.');
        Log::info('Incoming request data:', $request->all());
    
        $att_data = $request->all();
        $users = $att_data['user_id'];
    
        // Get session_id
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $attendance_date = date('Y-m-d', strtotime($att_data['date']));
        $school_id = auth()->user()->school_id;
    
        // Map hr_roles.id (frontend) → roles.role_id
        $hrRole = Hr_roles::find($att_data['role_id']);
        $roleName = $hrRole ? $hrRole->name : null;
    
        $rolesTableRoleId = Role::whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                                ->value('role_id');
    
        Log::info("Mapped hr_roles.id = {$att_data['role_id']} → roles.role_id = {$rolesTableRoleId}");
    
        $commonData = [
            'created_at' => $attendance_date . ' 00:00:00',
            'school_id' => $school_id,
            'session_id' => $session_id,
            'role_id' => $rolesTableRoleId,
            'hr_roles_role_id' => $att_data['role_id'],
        ];
    
        foreach ($users as $key => $user) {
            $data = $commonData;
            $data['status'] = $att_data['status-' . $user];
            $data['user_id'] = $user;
    
            // Check explicitly for attendance_id in request to handle both updates and new inserts
            if (isset($att_data['attendance_id'][$key])) {
                HrDailyAttendence::where('id', $att_data['attendance_id'][$key])->update($data);
                Log::info("Attendance updated for user_id: $user");
            } else {
                HrDailyAttendence::create($data);
                Log::info("New attendance created for user_id: $user");
            }
        }
    
        return redirect()->back()->with('message', 'Attendance updated successfully.');
    }
    
    
    */

/*
    public function hr_take_attendance(Request $request)
    {
        Log::info('Hello World - hr_take_attendance function started.');
        Log::info('Incoming request data: ', $request->all());
        $att_data = $request->all();
    
        $users = $att_data['user_id'];
    
        // ✅ Use same session_id fetch method as hrdailyAttendanceFilter()
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $data['created_at'] = strtotime($att_data['date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $session_id;
        $data['role_id'] = $att_data['role_id'];
    
        $check_data = HrDailyAttendence::where([
            'created_at' => $data['created_at'],
            'role_id' => $data['role_id'],
            'session_id' => $session_id,
            'school_id' => auth()->user()->school_id
        ])->get();
    
        if (count($check_data) > 0) {
            foreach ($users as $key => $user) {
                $data['status'] = $att_data['status-' . $user];
                $data['user_id'] = $user;
                $attendance_id = $att_data['attendance_id'][$key];
                HrDailyAttendence::where('id', $attendance_id)->update($data);
            }
        } else {
            foreach ($users as $user) {
                $data['status'] = $att_data['status-' . $user];
                $data['user_id'] = $user;
                HrDailyAttendence::create($data);
            }
        }
    
        return redirect()->back()->with('message', 'Attendance updated successfully.');
    }
    
*/
/*
    public function hrdailyAttendanceFilter(Request $request)
    {
        \Log::info('Hello World - hrdailyAttendanceFilter function started.');
        \Log::info('Filter request received', ['month' => $request->month, 'year' => $request->year, 'role_id' => $request->role_id]);
        Log::info('Filter request received', $request->all());


        $data = $request->all();
        $active_session = Session::where('status', 1)->first();

        \Log::info('Active session:', ['session_id' => $active_session->id ?? 'No active session']);


        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        if (!$role) {
            Log::error('Role not found for user', ['user_id' => auth()->user()->id]);
            return back()->withErrors(['error' => 'Role not found.']);
        }
        
        
        $roleName = $role['name'] . ".navigation";
        Log::info('Role retrieved', ['role_name' => $roleName]);

        $date = '01 ' . $data['month'] . ' ' . $data['year'];
       // $first_date = strtotime($date);
       // $last_date = date("Y-m-t", strtotime($date));
       // $last_date = strtotime($last_date);
      
      $timestamp = strtotime($date);

      if ($timestamp === false) {
          Log::error('Invalid date format', ['date' => $date]);
          return back()->withErrors(['error' => 'Invalid date format.']);
      }
      
      $first_date = date('Y-m-d H:i:s', $timestamp); // Corrected format
      $last_date = date('Y-m-t 23:59:59', strtotime($date));
      Log::info('Date Range', ['first_date' => $first_date, 'last_date' => $last_date]);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;


        if (auth()->user()->role_id == 2) {
            Log::info('HR Admin detected, fetching attendance data');


           
            $no_of_users = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');
            $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->get()->toArray();
            Log::info('Attendance data retrieved', ['records' => count($attendance_of_students)]);



            return view('hr_daily_attendence.load_table', ['test' => 1, 'roleName' => $roleName, 'loaddata' => 1, 'page_data' => $page_data, 'attendance_of_students' => $attendance_of_students, 'role_id' => $data['role_id'], 'no_of_users' => $no_of_users]);
        } else {

            Log::info('Non-HR user detected, fetching specific user attendance');
            
            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);
            Log::info('HR User conversion result', ['hr_user' => $hr_user]);


            if ($hr_user != "no_user") {
                $no_of_users = $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');
                Log::info('Number of users fetched for HR user', ['no_of_users' => $no_of_users]);

                $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->get()->toArray();
                Log::info('Attendance data retrieved for HR user', ['records' => count($attendance_of_students)]);

                $userName = Hr_user_list::find($hr_user['id']);
                Log::info('HR user details fetched', ['userName' => $userName]);

                return view('hr_daily_attendence.load_table', [
                'userName' => $userName, 
                'roleName' => $roleName, 
                'loaddata' => 1, 
                'page_data' => $page_data, 
                'attendance_of_students' => $attendance_of_students, 
                'role_id' => $hr_user['role_id'], 
                'no_of_users' => $no_of_users]);
            }
        }

        Log::warning('No valid HR user found');
        return back()->withErrors(['error' => 'No valid HR user found.']);
    }
    
*/
    /**
     * 🔹 Filter Employee Attendance and Load Report
     */
    public function hrdailyAttendanceFilter(Request $request)
    {
        Log::info('✅ hrdailyAttendanceFilter function started.');
        Log::info('📌 Filter request received:', $request->all());
    
        $data = $request->all();
    
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        if (!$session_id) {
            Log::error('❌ No active session found.');
            return back()->withErrors(['error' => 'No active session found.']);
        }
    
        Log::info('📌 Active session:', ['session_id' => $session_id]);
    
        $role = Role::where('role_id', auth()->user()->role_id)->first();
        if (!$role) {
            Log::error('❌ Role not found for user', ['user_id' => auth()->user()->id]);
            return back()->withErrors(['error' => 'Role not found.']);
        }
    
        $roleName = $role->name . ".navigation";
        Log::info('📌 Role retrieved:', ['role_name' => $roleName]);
    
        $date = '01 ' . $data['month'] . ' ' . $data['year'];
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            Log::error('❌ Invalid date format', ['date' => $date]);
            return back()->withErrors(['error' => 'Invalid date format.']);
        }
    
        $first_date = date('Y-m-01 00:00:00', $timestamp);
        $last_date = date('Y-m-t 23:59:59', $timestamp);
        Log::info('📌 Date Range:', ['first_date' => $first_date, 'last_date' => $last_date]);
    
        $page_data = [
            'attendance_date' => $first_date,
            'role_id'         => $data['role_id'],
            'month'           => $data['month'],
            'year'            => $data['year']
        ];
    
        $no_of_users = 0;
    
        if (auth()->user()->role_id == 2) {
            Log::info('✅ HR Admin detected, fetching attendance data.');
    
            $query = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $session_id);
    
            if ($data['role_id'] !== "All") {
                $query->where('hr_roles_role_id', $data['role_id']);
                Log::info('📌 Filtering attendance for hr_roles_role_id:', ['hr_roles_role_id' => $data['role_id']]);
            } else {
                Log::info('📌 Fetching attendance for ALL roles (no hr_roles_role_id filtering).');
            }
    
            $attendance_of_students = $query->get()->toArray();


                        $approvedLeaves = Leavelist::where('status', 1) // only approved
                ->where('school_id', auth()->user()->school_id)
                ->where(function ($q) use ($first_date, $last_date) {
                    // Convert datetime to UNIX timestamp for comparison
                    $first = strtotime($first_date);
                    $last = strtotime($last_date);

                    $q->where(function ($query) use ($first, $last) {
                        $query->whereBetween('start_date', [$first, $last])
                            ->orWhereBetween('end_date', [$first, $last])
                            ->orWhere(function ($sub) use ($first, $last) {
                                $sub->where('start_date', '<', $first)
                                    ->where('end_date', '>', $last);
                            });
                    });
                })
                ->get();



                $leaveMap = [];

            foreach ($approvedLeaves as $leave) {
                $fromTimestamp = $leave->start_date;
                $toTimestamp = $leave->end_date;

                for ($day = $fromTimestamp; $day <= $toTimestamp; $day += 86400) { // increment by 1 day
                    $dateString = date('Y-m-d', $day);
                    $leaveMap[$leave->user_id][$dateString] = 'AL';
                }
            }

            foreach ($leaveMap as $userId => $dates) {
                foreach ($dates as $date => $status) {
                    // Check if a record already exists for this user and date
                    $exists = HrDailyAttendence::where('user_id', $userId)
                        ->where('school_id', auth()->user()->school_id)
                        ->whereDate('created_at', $date)
                        ->exists();
            
                    if (!$exists) {
                        // Get user details (role_id etc.)
                        $user = \App\Models\Addon\Hr_user_list::find($userId);
            
                        HrDailyAttendence::create([
                            'user_id'           => $userId,
                            'school_id'         => auth()->user()->school_id,
                            'session_id'        => $session_id,
                            'hr_roles_role_id'  => $user->role_id ?? null,
                            'emp_intime'        => null,
                            'emp_outtime'       => null,
                            'status'            => 2, // 2 = Approved Leave
                            'created_at'        => $date . ' 00:00:00',
                            'updated_at'        => now(),
                            'role_id'           => $data['role_id'],
                        ]);
            
                        Log::info('✅ Inserted AL record', [
                            'user_id' => $userId,
                            'date'    => $date,
                            'status'  => 2,
                        ]);
                    }
                }
            }
            

          
            // Inject AL entries
            foreach ($leaveMap as $userId => $dates) {
                foreach ($dates as $date => $status) {
                    $exists = collect($attendance_of_students)->first(function ($record) use ($userId, $date) {
                        $recordDate = date('Y-m-d', strtotime($record['created_at']));
                        return $record['user_id'] == $userId && $recordDate === $date;
                    });

                    $user = \App\Models\Addon\Hr_user_list::find($userId);
                    if (!$exists && $user && $user->role_id == $data['role_id']) {
                        $user = \App\Models\Addon\Hr_user_list::find($userId);

                        $attendance_of_students[] = [
                            'user_id' => $userId,
                            'name' => $user->name ?? 'Unknown',
                            'created_at' => $date . ' 00:00:00',
                            'updated_at' => now()->toDateTimeString(), // Add this line
                            'status' => 'AL',
                            'hr_roles_role_id' => $user->role_id ?? null,
                        ];
                    }
                }
            }


            $no_of_users = collect($attendance_of_students)->pluck('user_id')->unique()->count();
    
            $employeeAttendances = $attendance_of_students;
    
            Log::info('📌 Attendance data retrieved:', ['records' => count($attendance_of_students)]);
                
                
    
            return view('hr_daily_attendence.load_table', [
                'test'                   => 1,
                'roleName'               => $role->name . ".navigation",
                'loaddata'               => 1,
                'page_data'              => $page_data,
                'attendance_of_students' => $attendance_of_students,
                'role_id'                => $data['role_id'],
                'no_of_users'            => $no_of_users,
                'employeeAttendances'    => collect($attendance_of_students)->groupBy('name'),
                'attendanceDate'         => $page_data['attendance_date'] ?? now()->format('Y-m-d'),
                'lastRecord'             => !empty($attendance_of_students) ? end($attendance_of_students) : null,
            ]);
        } else {
            $user = auth()->user();
            Log::info('👤 Non-admin user detected, fetching own attendance only.', ['user_id' => $user->id]);
        
            Log::info('➡️ Checking user email:', ['email' => $user->email]);
            Log::info('➡️ School ID:', ['school_id' => $user->school_id]);
            Log::info('➡️ Session ID:', ['session_id' => $session_id]);
            Log::info('➡️ Date range for attendance:', ['from' => $first_date, 'to' => $last_date]);
        
            //$attendance_of_students = HrDailyAttendence::where('user_id', $user->id)

            $hr_user = Hr_user_list::where('email', auth()->user()->email)
            ->where('school_id', auth()->user()->school_id)
            ->first();
        
        if (!$hr_user) {
            Log::warning('❌ No HR mapping found for user');
            return back()->withErrors(['error' => 'HR user mapping not found.']);
        }
        
        // ✅ Correct: Use $hr_user->id
        $attendance_of_students = HrDailyAttendence::where('user_id', $hr_user->id)
            ->whereBetween('created_at', [$first_date, $last_date])
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $session_id)
            ->get();

        
            Log::info('📌 Raw attendance query result:', [
                'records_count' => $attendance_of_students->count(),
                'records_preview' => $attendance_of_students->take(3)->toArray()
            ]);
        
            // Additional HR mapping check
            $hr_user = Hr_user_list::where('email', $user->email)
                ->where('school_id', $user->school_id)
                ->first();
        
            if (!$hr_user) {
                Log::warning('⚠️ No HR user mapping found in hr_user_list for this user.');
            } else {
                Log::info('✅ HR user mapping found:', ['user_id' => $hr_user->id, 'role_id' => $hr_user->role_id]);
            }
        
            $attendance_of_students = $attendance_of_students->toArray();
            $no_of_users = 1;
            $employeeAttendances = $attendance_of_students;
        
            Log::info('📌 Final attendance data prepared:', ['records' => count($attendance_of_students)]);
        
            return view('hr_daily_attendence.load_table', [
                'test'                   => 1,
                'roleName'               => $role->name . ".navigation",
                'loaddata'               => 1,
                'page_data'              => $page_data,
                'attendance_of_students' => $attendance_of_students,
                'role_id'                => $data['role_id'],
                'no_of_users'            => $no_of_users,
                'employeeAttendances'    => collect($attendance_of_students)->groupBy('name'),
                'attendanceDate'         => $page_data['attendance_date'] ?? now()->format('Y-m-d'),
                'lastRecord'             => !empty($attendance_of_students) ? end($attendance_of_students) : null,
            ]);
        }
    }        


    public function hrdailyAttendanceFilter_csv(Request $request)
{
    \Log::info('📥 hrdailyAttendanceFilter_csv started', $request->all());

    $month   = $request->input('month');
    $year    = $request->input('year');
    $role_id = $request->input('role_id');

    if (!$month || !$year || !$role_id) {
        return response()->json(['error' => 'Missing filters']);
    }

    // Date range for selected month
    $start = date('Y-m-01 00:00:00', strtotime("01-$month-$year"));
    $end   = date('Y-m-t 23:59:59', strtotime("01-$month-$year"));

    \Log::info('📅 Date range', ['start' => $start, 'end' => $end]);

    $attendances = HrDailyAttendence::with(['user', 'role'])
        ->whereBetween('created_at', [$start, $end])
        ->when($role_id !== 'All', function ($q) use ($role_id) {
            $q->where('hr_roles_role_id', $role_id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    \Log::info('✅ Records fetched', ['count' => $attendances->count()]);

    // Role name for filename
    $roleName = ($role_id !== 'All') 
        ? (Hr_roles::find($role_id) ? Hr_roles::find($role_id)->name : 'Role') 
        : 'All_Roles';

    $filename = "HR_Monthly_Attendance_" . str_replace(' ', '_', $roleName) . "_{$month}_{$year}.csv";

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0'
    ];

    $columns = ['Employee Name', 'Role', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($attendances, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($attendances as $row) {
            fputcsv($file, [
                $row->user->name ?? 'N/A',
                $row->role->name ?? 'N/A',
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



    public function convert_user_to_hr_user($user_id, $school_id)
    {
        \Log::info('Hello World - convert_user_to_hr_user function started.');
        $user_table = User::where(array('id' => $user_id, 'school_id' => $school_id))->first()->toArray();


        $Hr_user = Hr_user_list::where(array('email' => $user_table['email'], 'school_id' => $user_table['school_id']))->first();


        if (empty($Hr_user)) {
            $Hr_user = "no_user";
        } else {
            $Hr_user = $Hr_user->toArray();
        }


        return $Hr_user;
    }

    public function get_user_by_id_from_hr_userlist_table($id)
    {
        $user = Hr_user_list::find($id);

        return $user;
    }



    public function get_user_by_id_from_user_table($id)
    {
        $user = User::find($id);

        return $user;
    }



    public function list_of_leaves(Request $request)
    {
        Log::info("Entered method: list_of_leaves");
        $data = $request->all();
        Log::info("Retrieved request data", $data);

        $to;
        $form;
        $hr_searched_role_id = 0;

        if (isset($data['datetimes'])) {

            $f = Str::substr($data['datetimes'], 0, 10);
            $t = Str::substr($data['datetimes'], 13, 25);
            $form = strtotime($f);
            $to = strtotime($t) + 8600;
            Log::info("Parsed date range from input", ['from' => $f, 'to' => $t]);
        } else {
            $to = strtotime(date("m/d/Y")) + 86400;
            $f = date('m/d/Y', strtotime("-31 days"));
            $form = strtotime($f);
            Log::info("Default date range used", ['from' => $f, 'to' => date("m/d/Y")]);
        }


        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";
        Log::info("Fetched user role details", $role);



        if (auth()->user()->role_id == 2) {

            if (isset($data['role_id'])) {


                $type = $data['role_id'];
                $hr_searched_role_id = $data['role_id'];
                Log::info("Role filter applied by HR", ['role_id' => $type]);
                $list_of_pending_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 0, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 1, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 2, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
            } else {

                Log::info("No role filter applied by HR");
                $list_of_pending_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 0)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 1)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 2)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
            }
        } else {
            Log::info("User role is not HR");
            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);
            $list_of_pending_leaves = array();
            $list_of_approve_leaves = array();
            $list_of_decline_leaves = array();

            if ($hr_user != "no_user") {
                Log::info("HR user found", $hr_user);
                $list_of_pending_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 0))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 1))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 2))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
            }
            else {
                Log::warning("No HR user found for current user.");
            }
        }
        Log::info("Returning view with leave data", [
            'pending' => count($list_of_pending_leaves),
            'approved' => count($list_of_approve_leaves),
            'declined' => count($list_of_decline_leaves),
            'role_view' => $roleName
        ]);

        return view('hr_leave.list', ['list_of_pending_leaves' => $list_of_pending_leaves, 'list_of_approve_leaves' => $list_of_approve_leaves, 'list_of_decline_leaves' => $list_of_decline_leaves, 'roleName' => $roleName, 'role' => $role['name'], 'hr_searched_role_id' => $hr_searched_role_id]);
    }

    public function show_leave_request_modal(Request $request)
    {
        $data = $request->all();
        Log::info('Showing leave request modal');
        return view('hr_leave.create');
    }



    public function show_leave_request_modal_for_admin(Request $request)
    {
        $data = $request->all();
        Log::info('Showing leave request modal');
        return view('hr_leave.admin_leave_create');
    }

    public function add_update_delete_leave_request(Request $request, $action, $id)
    {
        Log::info('Processing leave request', ['action' => $action, 'id' => $id ?? 'N/A']);

        $data = $request->all();
        $to = strtotime(date("m/d/Y")) + 86000;
        $f = date('m/d/Y', strtotime("-31 days"));
        $form = strtotime(date('m/d/Y'), strtotime($f));

        if ($action == "add") {

            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);
            $Insert_in_table = new Leavelist;

            $Insert_in_table['start_date'] = strtotime($data['start_date']);
            $Insert_in_table['end_date']  = strtotime($data['end_date']);
            $Insert_in_table['reason'] = $data['reason'];
            $Insert_in_table['status'] = 0;
            $Insert_in_table['user_id'] = $hr_user['id'];
            $Insert_in_table['role_id'] = $hr_user['role_id'];
            $Insert_in_table['created_at'] = strtotime(date('d-M-Y'));
            $Insert_in_table['updated_at'] = strtotime(date('d-M-Y'));
            $Insert_in_table['school_id'] = $hr_user['school_id'];

            $Insert_in_table->save();

            return redirect()->route('hr.list_of_leaves', ['from' => $form, 'to' => $to, 'type' => 'type'])->with('message', ' added');
        } elseif ($action == "update") {

            Leavelist::where('id', $id)->update([
                'start_date' => strtotime($data['start_date']),
                'end_date' => strtotime($data['end_date']),
                'reason' => $data['reason'],
                'status' => 0,
                'created_at' => strtotime(date('d-M-Y')),
                'updated_at' => strtotime(date('d-M-Y'))
            ]);
            return redirect()->route('hr.list_of_leaves', ['from' => $form, 'to' => $to, 'type' => 'type'])->with('message', ' updated');
        } elseif ($action == "add_by_admin") {

            $Insert_in_table = new Leavelist;

            $Insert_in_table['start_date'] = strtotime($data['start_date']);
            $Insert_in_table['end_date']  = strtotime($data['end_date']);
            $Insert_in_table['reason'] = $data['reason'];
            $Insert_in_table['status'] = 0;
            $Insert_in_table['user_id'] = $data['user_id'];
            $Insert_in_table['role_id'] = $data['role_id'];
            $Insert_in_table['created_at'] = strtotime(date('d-M-Y'));
            $Insert_in_table['updated_at'] = strtotime(date('d-M-Y'));
            $Insert_in_table['school_id'] = auth()->user()->school_id;

            $Insert_in_table->save();

            return redirect()->route('hr.list_of_leaves', ['from' => $form, 'to' => $to, 'type' => 'type'])->with('message', ' added');
        }
    }

    public function show_leave_update_request_modal(Request $request, $id)
    {   
        Log::info('Fetching leave record to edit', ['id' => $id]);

        $data = $request->all();


        $leave = Leavelist::where(array('id' => $id, 'status' => 0))->first();

        return view('hr_leave.edit', ['leave' => $leave->toArray()]);
    }


    public function delete_leave_request(Request $request, $id)
    {
        Log::info('Attempting to delete leave', ['id' => $id]);

        $data = $request->all();

        $delete_leave = Leavelist::find($id);
        if ($delete_leave['status'] == 0) {
            $delete_leave->delete();
            return redirect()->route('hr.list_of_leaves')->with('message', ' deleted');
        }

        return redirect()->route('hr.list_of_leaves')->with('message', ' Sorry , can not delete that ');
    }


    public function actions_on_employee_leave(Request $request, $id, $action)
    {  
         Log::info('Action on leave request', ['id' => $id, 'action' => $action]);

        $data = $request->all();

        if ($action == 'approve') {
            Leavelist::where('id', $id)->update([
                'status' => 1
            ]);

            return redirect()->route('hr.list_of_leaves')->with('message', ' Leave approved');
        } elseif ($action == 'decline') {
            Leavelist::where('id', $id)->update([
                'status' => 2
            ]);
            return redirect()->route('hr.list_of_leaves')->with('message', ' Leave declined');
        } elseif ($action == 'delete') {
            $delete_leave = Leavelist::find($id);
            $delete_leave->delete();
            return redirect()->route('hr.list_of_leaves')->with('message', ' Leave deleted');
        }
    }

    public function roleWiseUser($id)
    {
        Log::info('Fetching users by role_id', ['role_id' => $id]);

        $users = Hr_user_list::get()->where('role_id', $id);
        $options = '<option value="">' . 'Select a user' . '</option>';
        foreach ($users as $user) :
            $options .= '<option value="' . $user->id . '">' . $user->name . '</option>';
        endforeach;
        echo $options;
    }



    public function list_of_payrolls(Request $request)
    {
        

        $data = $request->all();
        $filtered_month;
        $filtered_year;
        if (isset($data['month'])) {
            $filtered_month = $data['month'];
            $filtered_year = $data['year'];
        } else {
            $filtered_month = date("m");
            $filtered_year = date("Y");
        }
        Log::info('Listing payrolls', ['month' => $filtered_month, 'year' => $filtered_year]);
        return view('hr_payroll.list_of_payrolls', ['filtered_month' => $filtered_month, 'filtered_year' => $filtered_year]);
    }


    public function payrolls_details(Request $request)
    {

        
        $data = $request->all();


        $from = strtotime(date('01-' . $data['month'] . '-' . $data['year']));
        $to = strtotime(date('t-' . $data['month'] . '-' . $data['year']));




        $list_of_payrolls  = HrPayroll::whereBetween('created_at', [$from, $to])->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get()->toArray();

        Log::info('Fetching payrolls details', ['from' => $from, 'to' => $to]);
        return view('hr_payroll.payroll_detail', ['list_of_payrolls' => $list_of_payrolls]);
    }

    public function payslip(Request $request, $id)
    {
        Log::info('Viewing payslip', ['id' => $id]);
        $data = $request->all();

        $payslip  = HrPayroll::where('school_id', auth()->user()->school_id)->where('id', $id)->get()->toArray();



        return view('hr_payroll.payslip', ['payslip' => $payslip]);
    }

    public function print_invoice(Request $request, $id)
    {
       
        Log::info('Printing payslip invoice', ['id' => $id]);
        $data = $request->all();

        $payslip  = HrPayroll::where('school_id', auth()->user()->school_id)->where('id', $id)->get()->toArray();

        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";



        return view('hr_payroll.print_ivoice', ['payslip' => $payslip,'roleName'=>$roleName]);
    }



    public function update_payroll_status(Request $request, $id, $date)
    {
        Log::info('Updating payroll status', ['id' => $id, 'date' => $date]);

        $data = $request->all();

        $month = date('m', $date);
        $year = date('Y', $date);

        HrPayroll::where(array('id' => $id, 'created_at' => $date))->update([
            'status' => 1,
            'updated_at' => strtotime(date('Y-m-d')),

        ]);



        return redirect()->route('hr.list_of_payrolls', ['month' => $month, 'year' => $year]);
    }


    public function create_payslip(Request $request)
    {   Log::info('Opening payslip creation form');

        $data = $request->all();
        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();

        return view('hr_payroll.create_payslip.index', ['roles' => $roles]);
    }


    public function get_user_by_role(Request $request)
    {   

        $data = $request->all();
        $users = Hr_user_list::where('role_id', $data['role_id'])->get()->toArray();
        Log::info('Getting users by role', ['role_id' => $data['role_id']]);

        foreach ($users as $row)
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }

    public function payroll_add_view(Request $request)
    {
        
        $data = $request->all();

        $users = Hr_user_list::where('role_id', $data['role_id'])->get()->toArray();
        Log::info('Loading payroll add view', ['user_id' => $data['user_id']]);

        $joining_salary = Hr_user_list::where('id', $data['user_id'])->where('role_id', $data['role_id'])->first()->toArray();
        $joining_salary = $joining_salary['joining_salary'];




        return view('hr_payroll.create_payslip.payroll_add_view', ['users' => $users, 'joining_salary' => $joining_salary, 'user_id' => $data['user_id'], 'month' => $data['month'], 'year' => $data['year']]);
    }

    public function insert_payslip_to_db(Request $request)
    {   

        $info_from_view = $request->all();

        $data = new HrPayroll;

        $data['user_id']        = $info_from_view['user_id'];

        $allowances             = array();
        $allowance_types        = $info_from_view['allowance_type'];
        $allowance_amounts      = $info_from_view['allowance_amount'];
        if (is_array($allowance_types)) {
            $number_of_entries      = sizeof($allowance_types);
        } else {
            $number_of_entries      = 1;
            $allowance_types        = array($info_from_view['allowance_type']);
            $allowance_amounts      = array($info_from_view['allowance_amount']);
        }

        for ($i = 0; $i < $number_of_entries; $i++) {
            if ($allowance_types[$i] != "" && $allowance_amounts[$i] != "") {
                $new_entry = array('type' => $allowance_types[$i], 'amount' => $allowance_amounts[$i]);
                array_push($allowances, $new_entry);
            }
        }

        $data['allowances']     = $info_from_view['total_allowance'];

        $deductions             = array();
        $deduction_types        = $info_from_view['deduction_type'];
        $deduction_amounts      = $info_from_view['deduction_amount'];

        if (is_array($deduction_types)) {
            $number_of_entries      = sizeof($deduction_types);
        } else {
            $number_of_entries      = 1;
            $deduction_types        = array($info_from_view['deduction_type']);
            $deduction_amounts        = array($info_from_view['deduction_amount']);
        }

        for ($i = 0; $i < $number_of_entries; $i++) {
            if ($deduction_types[$i] != "" && $deduction_amounts[$i] != "") {
                $new_entry = array('type' => $deduction_types[$i], 'amount' => $deduction_amounts[$i]);
                array_push($deductions, $new_entry);
            }
        }

        $data['deducition']     = $info_from_view['total_deduction'];
        $data['created_at']     = strtotime(date('d-' . $info_from_view['month'] . '-' . $info_from_view['year']));
        $data['status']         = $info_from_view['status'];
        $data['school_id']       = auth()->user()->school_id;




        $data->save();


        $month = $info_from_view['month'];
        $year = $info_from_view['year'];
        Log::info('Inserting payslip into DB', ['user_id' => $info_from_view['user_id']]);
        return redirect()->route('hr.list_of_payrolls', ['month' => $month, 'year' => $year])->with('message', 'payslip created successfully');
    }

    


    public function user_list_of_payrolls(Request $request)
    {
            
        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";
        $payroll = array();


        $user_data = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);



        if ($user_data != "no_user") {

            $user = Hr_user_list::where(array('email' => $user_data['email'], 'school_id' => $user_data['school_id']))->first()->toArray();

            $payroll = HrPayroll::where(array('user_id' => $user['id'], 'school_id' => $user_data['school_id']))->orderBy('id', 'DESC')->get()->toArray();
        }


        Log::info('Fetching payroll list for user');
        return view('hr_payroll.users.list', ['roleName' => $roleName, 'payroll' => $payroll]);
    }




    public function user_payroll_print_details(Request $request, $payroll_id)
    {
        
        $edit_data = HrPayroll::where(array('id' => $payroll_id, 'school_id' => auth()->user()->school_id))->get()->toArray();

        Log::info('Viewing user payroll details', ['payroll_id' => $payroll_id]);
        return view('hr_payroll.users.payroll_details', ['edit_data' => $edit_data]);
    }

    public function user_payroll_print_details_print(Request $request, $payroll_id)
    {
        
        $edit_data = HrPayroll::where(array('id' => $payroll_id, 'school_id' => auth()->user()->school_id))->get()->toArray();

        Log::info('Printing user payroll details', ['payroll_id' => $payroll_id]);
        return view('hr_payroll.users.payroll_details_print', ['edit_data' => $edit_data]);
    }

    

    public function roleCheck($id='')
    {
        $check = Hr_roles::where('id', $id)->where('permanent', 'yes')->where('school_id', auth()->user()->school_id)->value('permanent');
        echo $check;
    }
}
