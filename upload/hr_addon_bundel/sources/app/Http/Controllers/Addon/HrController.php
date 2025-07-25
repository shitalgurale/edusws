<?php

namespace App\Http\Controllers\Addon;

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

        $check = Hr_roles::where('id', $data['role_id'])->where('school_id', auth()->user()->school_id)->first();
        if($check->permanent == 'yes') {

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

            User::create([
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
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'joining_salary' => $data['joining_salary'],
            'school_id' => auth()->user()->school_id,


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
        $users = Hr_user_list::where('school_id', auth()->user()->school_id)->where('role_id', $data['role_id'])->get();

        return view('hr_user_list.list', ['role_id' => $role_id, 'users' => $users]);
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

        $data = $request->all();
        return view('hr_daily_attendence.take_attendence');
    }

    public function roleWiseUserlist(Request $request)
    {

        $data = $request->all();
        $users = Hr_user_list::get()->where('role_id', $data['role_id'])->toArray();
        return view('hr_daily_attendence.attendence_view', ['users' => $users, 'date' => $data['date'], 'role_id' => $data['role_id']]);
    }

    public function hr_take_attendance(Request $request)
    {
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

                $attendance_id = $att_data['attendance_id'];


                HrDailyAttendence::where('id', $attendance_id[$key])->update($data);

            endforeach;
        } else {
            foreach ($users as $user) :
                $data['status'] = $att_data['status-' . $user];
                $data['user_id'] = $user;

                HrDailyAttendence::create($data);

            endforeach;
        }


        return redirect()->back()->with('message', ' attendance updated successfully.');
    }

    public function hrdailyAttendanceFilter(Request $request)
    {


        $data = $request->all();
        $active_session = Session::where('status', 1)->first();

        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";

        $date = '01 ' . $data['month'] . ' ' . $data['year'];
        $first_date = strtotime($date);
        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;


        if (auth()->user()->role_id == 2) {

            $no_of_users = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');
            $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->get()->toArray();



            return view('hr_daily_attendence.load_table', ['test' => 1, 'roleName' => $roleName, 'loaddata' => 1, 'page_data' => $page_data, 'attendance_of_students' => $attendance_of_students, 'role_id' => $data['role_id'], 'no_of_users' => $no_of_users]);
        } else {

            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);


            if ($hr_user != "no_user") {
                $no_of_users = $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');

                $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->get()->toArray();
                $userName = Hr_user_list::find($hr_user['id']);
                return view('hr_daily_attendence.load_table', ['userName' => $userName, 'roleName' => $roleName, 'loaddata' => 1, 'page_data' => $page_data, 'attendance_of_students' => $attendance_of_students, 'role_id' => $hr_user['role_id'], 'no_of_users' => $no_of_users]);
            }
        }
    }

    public function convert_user_to_hr_user($user_id, $school_id)
    {
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

        $data = $request->all();
        $to;
        $form;
        $hr_searched_role_id = 0;

        if (isset($data['datetimes'])) {

            $f = Str::substr($data['datetimes'], 0, 10);
            $t = Str::substr($data['datetimes'], 13, 25);
            $form = strtotime($f);
            $to = strtotime($t) + 8600;
        } else {
            $to = strtotime(date("m/d/Y")) + 86400;
            $f = date('m/d/Y', strtotime("-31 days"));
            $form = strtotime($f);
        }


        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";



        if (auth()->user()->role_id == 2) {

            if (isset($data['role_id'])) {


                $type = $data['role_id'];
                $hr_searched_role_id = $data['role_id'];
                $list_of_pending_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 0, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 1, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where(array('role_id' => $type, 'status' => 2, 'school_id' => auth()->user()->school_id))->orderBy('created_at', 'DESC')->get();
            } else {


                $list_of_pending_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 0)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 1)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::whereBetween('created_at', [$form, $to])->where('status', 2)->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();
            }
        } else {

            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);
            $list_of_pending_leaves = array();
            $list_of_approve_leaves = array();
            $list_of_decline_leaves = array();

            if ($hr_user != "no_user") {
                $list_of_pending_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 0))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
                $list_of_approve_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 1))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
                $list_of_decline_leaves = Leavelist::where(array('user_id' =>  $hr_user['id'], 'school_id' => $hr_user['school_id'], 'status' => 2))->whereBetween('created_at', [$form, $to])->orderBy('created_at', 'DESC')->get();
            }
        }

        return view('hr_leave.list', ['list_of_pending_leaves' => $list_of_pending_leaves, 'list_of_approve_leaves' => $list_of_approve_leaves, 'list_of_decline_leaves' => $list_of_decline_leaves, 'roleName' => $roleName, 'role' => $role['name'], 'hr_searched_role_id' => $hr_searched_role_id]);
    }

    public function show_leave_request_modal(Request $request)
    {
        $data = $request->all();
        return view('hr_leave.create');
    }



    public function show_leave_request_modal_for_admin(Request $request)
    {
        $data = $request->all();
        return view('hr_leave.admin_leave_create');
    }

    public function add_update_delete_leave_request(Request $request, $action, $id)
    {
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
        $data = $request->all();


        $leave = Leavelist::where(array('id' => $id, 'status' => 0))->first();

        return view('hr_leave.edit', ['leave' => $leave->toArray()]);
    }


    public function delete_leave_request(Request $request, $id)
    {
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
        return view('hr_payroll.list_of_payrolls', ['filtered_month' => $filtered_month, 'filtered_year' => $filtered_year]);
    }


    public function payrolls_details(Request $request)
    {

        $data = $request->all();


        $from = strtotime(date('01-' . $data['month'] . '-' . $data['year']));
        $to = strtotime(date('t-' . $data['month'] . '-' . $data['year']));




        $list_of_payrolls  = HrPayroll::whereBetween('created_at', [$from, $to])->where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get()->toArray();


        return view('hr_payroll.payroll_detail', ['list_of_payrolls' => $list_of_payrolls]);
    }

    public function payslip(Request $request, $id)
    {

        $data = $request->all();

        $payslip  = HrPayroll::where('school_id', auth()->user()->school_id)->where('id', $id)->get()->toArray();



        return view('hr_payroll.payslip', ['payslip' => $payslip]);
    }

    public function print_invoice(Request $request, $id)
    {

        $data = $request->all();

        $payslip  = HrPayroll::where('school_id', auth()->user()->school_id)->where('id', $id)->get()->toArray();

        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";



        return view('hr_payroll.print_ivoice', ['payslip' => $payslip,'roleName'=>$roleName]);
    }



    public function update_payroll_status(Request $request, $id, $date)
    {

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
    {

        $data = $request->all();
        $roles = Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();

        return view('hr_payroll.create_payslip.index', ['roles' => $roles]);
    }


    public function get_user_by_role(Request $request)
    {

        $data = $request->all();
        $users = Hr_user_list::where('role_id', $data['role_id'])->get()->toArray();

        foreach ($users as $row)
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
    }

    public function payroll_add_view(Request $request)
    {

        $data = $request->all();

        $users = Hr_user_list::where('role_id', $data['role_id'])->get()->toArray();

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



        return view('hr_payroll.users.list', ['roleName' => $roleName, 'payroll' => $payroll]);
    }




    public function user_payroll_print_details(Request $request, $payroll_id)
    {

        $edit_data = HrPayroll::where(array('id' => $payroll_id, 'school_id' => auth()->user()->school_id))->get()->toArray();


        return view('hr_payroll.users.payroll_details', ['edit_data' => $edit_data]);
    }

    public function user_payroll_print_details_print(Request $request, $payroll_id)
    {


        $edit_data = HrPayroll::where(array('id' => $payroll_id, 'school_id' => auth()->user()->school_id))->get()->toArray();


        return view('hr_payroll.users.payroll_details_print', ['edit_data' => $edit_data]);
    }

    public function hrdailyAttendanceFilter_csv(Request $request)
    {

        $data = $request->all();

        $store_get_data=array_keys($data);


        $data['month']= substr($store_get_data[0],0,3);
        $data['year']= substr($store_get_data[0],4,4);
        $data['role_id']=substr($store_get_data[0],9,5);

        $active_session = Session::where('status', 1)->first();

        $role = Role::where('role_id', auth()->user()->role_id)->first()->toArray();
        $roleName = $role['name'] . ".navigation";

        $date = '01 ' . $data['month'] . ' ' . $data['year'];


        $first_date = strtotime($date);

        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;




        if (auth()->user()->role_id == 2) {

            $no_of_users = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');
            $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'role_id' => $data['role_id'], 'session_id' => $active_session->id])->get()->toArray();
              }
              else {

            $hr_user = $this->convert_user_to_hr_user(auth()->user()->id, auth()->user()->school_id);


            if ($hr_user != "no_user") {
                $no_of_users = $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->distinct()->count('user_id');

                $attendance_of_students = HrDailyAttendence::whereBetween('created_at', [$first_date, $last_date])->where(['user_id' => $hr_user['id'], 'school_id' => $hr_user['school_id'], 'role_id' => $hr_user['role_id'], 'session_id' => $active_session->id])->get()->toArray();
                $userName = Hr_user_list::find($hr_user['id']);
                      }
        }


        $csv_content =$role['name']."/".get_phrase('Date');
        $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
        for ($i = 1; $i <= $number_of_days; $i++)
        {
            $csv_content .=','.get_phrase($i);

        }


        $file = "Attendence_report.csv";


        $student_id_count = 0;


            foreach(array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student ){
                $csv_content .= "\n";

             $user_details = $this->get_user_by_id_from_hr_userlist_table($attendance_of_student['user_id']);
           if(date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['created_at'])) {



             if($student_id_count != $attendance_of_student['user_id'])
             {


                 $csv_content .= $user_details['name'] . ',';


             for ($i = 1; $i <= $number_of_days; $i++)
             {
             $page_data['date'] = $i.' '.$page_data['month'].' '.$page_data['year'];
             $timestamp = strtotime($page_data['date']);

                 $attendance_by_id = HrDailyAttendence::where([ 'user_id' => $attendance_of_student['user_id'], 'school_id' => auth()->user()->school_id, 'created_at' => $timestamp])->first();
                 if(isset($attendance_by_id->status) && $attendance_by_id->status == 1){
                    $csv_content .= "P,";
                 }elseif(isset($attendance_by_id->status) && $attendance_by_id->status == 0){
                    $csv_content .= "A,";
                 }
                 else
                 {
                    $csv_content .= ",";

                 }


                 if($i==$number_of_days)
                 {
                    $csv_content= substr_replace($csv_content,"", -1);
                 }

             }

            }

             $student_id_count = $attendance_of_student['user_id'];
        }
    }

        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv_content);
        fclose($txt);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        header("Content-type: text/csv");
        readfile($file);
    }

    public function roleCheck($id='')
    {
        $check = Hr_roles::where('id', $id)->where('permanent', 'yes')->where('school_id', auth()->user()->school_id)->value('permanent');
        echo $check;
    }
}
