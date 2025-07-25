<?php
namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use App\Models\FeeInstallment;
use App\Models\Admin;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Addon\Hr_user_list;
use App\Models\Addon\Hr_roles;
use App\Models\Addon\HrDailyAttendence;
use App\Models\User;
use App\Models\Session;
use App\Models\School;
use App\Models\Subscription;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Gradebook;
use App\Models\Grade;
use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\ClassList;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\Routine;
use App\Models\Syllabus;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\Package;
use App\Models\PaymentMethods;
use App\Models\Currency;
use App\Models\PaymentHistory;
use App\Models\TeacherPermission;
use App\Models\Payments;
use App\Models\Feedback;
use App\Models\MessageThrade;
use App\Models\Chat;
use App\Models\AdmitCard;
use App\Models\Role;
use App\Models\AdmissionDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use \DateTime;
use App\Helpers\NumberToWordsHelper;
//use Mail;
use App\Mail\FreeEmail;
use App\Mail\StudentsEmail;
use App\Mail\NewUserEmail;
use App\Services\FcmHttpV1Service;


use PDF;


use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{

    private $user;
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth()->user();
            $this->check_subscription_status(Auth()->user()->school_id);
            $this->insert_gateways();
            return $next($request);
        });


    }

    function check_subscription_status($school_id = ""){
        $current_route = Route::currentRouteName();
        $has_subscription = Subscription::where('school_id', $school_id)->where('status', 1)->get()->count();
        $active_subscription = Subscription::where('school_id', $school_id)->where('active', 1)->first();
        
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        
        if($has_subscription != 0) {
            if($active_subscription['expire_date'] == '0'){
                $expiry_status = '0';
            }else{
                $expiry_status = (int)$active_subscription['expire_date'] < $today_time;
            }     
             
            if(
                ($current_route != 'admin.subscription' && $expiry_status) &&
                ($current_route != 'admin.subscription.purchase' && $expiry_status) &&
                ($current_route != 'admin.subscription.payment' && $expiry_status) &&
                ($current_route != 'admin.subscription.offline_payment' && $expiry_status)
            )
            {
                redirect()->route('admin.subscription')->send();
            }

        } else {

            if(
                ($current_route != 'admin.subscription' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.purchase' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.payment' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.offline_payment' && $has_subscription == 0)
            )
            {
                redirect()->route('admin.subscription')->send();
            }
        }
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function check_admin_subscription($school_id)
     {
         $validity_of_current_package=Subscription::where('school_id',$school_id)->where('active',1)->first();
         if(!empty($validity_of_current_package))
         {
             $validity_of_current_package= $validity_of_current_package->toArray();
 
 
             $today = date("Y-m-d");
             $today_time = strtotime($today);
 
             if((int)$validity_of_current_package['expire_date'] < $today_time  )
             {
                 $this->adminDashboard();
 
             }
             else
             {
 
                
             }
 
 
         }
         else
         {
 
 
         }
 
     }



    public function adminDashboard()
    {
        $account_status = auth()->user()->account_status;
        if(auth()->user()->role_id != "") {
            return view('admin.dashboard');
        } else {
            redirect()->route('login')
                ->with('error','You are not logged in.');
        }
    }

 /**
     * Show the Compose -Outbox.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function adminCompose()
     {
         $school_id = auth()->user()->school_id;
     
         // Subscription check like adminDashboard
         $validity_of_current_package = Subscription::where('school_id', $school_id)->where('active', 1)->first();
     
         $today = date("Y-m-d");
         $today_time = strtotime($today);
     
         if ($validity_of_current_package) {
             if ((int)$validity_of_current_package->expire_date < $today_time) {
                 return redirect()->route('admin.subscription')->with('error', 'Your subscription has expired.');
             }
         } else {
             return redirect()->route('admin.subscription')->with('error', 'You do not have an active subscription.');
         }
     
         // Fetch recipients for compose form filtered by admin's school_id
         $classes = Classes::where('school_id', $school_id)->get();
         $students = User::where('role_id', 7)->where('school_id', $school_id)->get(); // Students with the same school_id
         $parents = User::where('role_id', 6)->where('school_id', $school_id)->whereHas('children')->get(); // Parents with the same school_id
     
         return view('admin.compose.compose', compact('classes', 'students', 'parents'));
     }
     

    
 /**
     * Show the sending mails to parents / students.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */ 
    public function sendMail(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => 'required',
            'subject'        => 'required|string',
            'message'        => 'required|string',
            'attachment'     => 'nullable|file|max:2048',
        ]);
    
        $admin      = auth()->user();
        $school_id  = $admin->school_id;
        $recipients = collect();
    
        // ✅ Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $filename       = time() . '_' . $request->file('attachment')->getClientOriginalName();
            $attachmentPath = $request->file('attachment')->storeAs('attachments', $filename, 'public');
        }
    
        // ✅ Handle Students
        if (in_array($request->recipient_type, ['student', 'both'])) {
            if ($request->student_id === 'all') {
                \Log::info("🔍 Fetching students for Class ID: {$request->class_id}, Section ID: {$request->section_id}, School ID: {$school_id}");
    
                $studentIds = \App\Models\Enrollment::where('school_id', $school_id)
                    ->when($request->class_id !== 'all', fn($q) => $q->where('class_id', $request->class_id))
                    ->when($request->section_id !== 'all', fn($q) => $q->where('section_id', $request->section_id))
                    ->pluck('user_id');
    
                $students = \App\Models\User::whereIn('id', $studentIds)->where('role_id', 7)->get();
    
                \Log::info("✅ Students fetched", $students->pluck('id', 'name')->toArray());
                $recipients = $recipients->merge($students);
            } elseif ($request->filled('student_id')) {
                \Log::info("📌 Single student selected", [$request->student_id]);
                $student = \App\Models\User::find($request->student_id);
                if ($student) $recipients->push($student);
            }
        }
    
        // ✅ Handle Parents
        if (in_array($request->recipient_type, ['parent', 'both'])) {
            if ($request->parent_id === 'all') {
                $studentIds = \App\Models\Enrollment::where('school_id', $school_id)
                    ->when($request->class_id !== 'all', fn($q) => $q->where('class_id', $request->class_id))
                    ->when($request->section_id !== 'all', fn($q) => $q->where('section_id', $request->section_id))
                    ->pluck('user_id');
    
                $parentIds = \App\Models\User::where('role_id', 7)
                    ->where('school_id', $school_id)
                    ->whereIn('id', $studentIds)
                    ->pluck('parent_id')
                    ->unique()
                    ->filter();
    
                $parents = \App\Models\User::whereIn('id', $parentIds)
                    ->where('role_id', 6)
                    ->get();
    
                \Log::info("✅ Parents fetched", $parents->pluck('id', 'name')->toArray());
                $recipients = $recipients->merge($parents);
            } elseif ($request->filled('parent_id')) {
                \Log::info("📌 Single parent selected", [$request->parent_id]);
                $parent = \App\Models\User::find($request->parent_id);
                if ($parent) $recipients->push($parent);
            }
        }

        //Handle Teachers
        if (in_array($request->recipient_type, ['teacher'])) {
            if ($request->teacher_id === 'all') {
                $teachers = User::where('role_id', 3)
                    ->where('school_id', $school_id)
                    ->get();
                $recipients = $recipients->merge($teachers);
            } elseif ($request->filled('teacher_id')) {
                $teacher = User::find($request->teacher_id);
                if ($teacher) $recipients->push($teacher);
            }
        }
        
    
        // ✅ Final Check
        $recipientIds = $recipients->pluck('id')->unique()->values();
        if ($recipientIds->isEmpty()) {
            \Log::warning("⚠️ No recipients found for message.");
            return redirect()->back()->with('error', 'No valid recipients found.');
        }
    
        \Log::info("📬 Final merged recipient user IDs", $recipientIds->toArray());
    
        // ✅ Save the message
        \App\Models\Message::create([
            'from_user_id'    => $admin->id,
            'to_user_id'      => $recipientIds->implode(','),
            'school_id'       => $school_id,
            'subject'         => $request->subject,
            'body'            => $request->message,
            'attachment_path' => $attachmentPath,
            'recipient_type'  => $request->recipient_type,
            'role_id'         => $recipients->pluck('role_id')->unique()->implode(','),
            'class_id'        => $request->class_id !== 'all' ? $request->class_id : null,
        ]);
    
        return redirect()->back()->with('message', 'Message sent to inbox successfully.');
    }

    public function inlinePreview($id)
{
    $message = Message::findOrFail($id);

    if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
        abort(404, 'File not found');
    }

    $mimeType = Storage::disk('public')->mimeType($message->attachment_path);
    $content = Storage::disk('public')->get($message->attachment_path);

    return response($content, 200)->header('Content-Type', $mimeType);
}

public function downloadAttachment($id)
{
    $message = Message::findOrFail($id);

    if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
        abort(404, 'File not found');
    }

    return Storage::disk('public')->download($message->attachment_path);
}


        
    public function getTeachersByClassAndSection($class_id, $section_id)
{
    \Log::info("📥 Fetching teachers for Class ID: $class_id, Section ID: $section_id");

    $school_id = auth()->user()->school_id;

    // ✅ If both are "all", fetch all teachers from the school
    if ($class_id === 'all' && $section_id === 'all') {
        $teachers = \App\Models\User::where('school_id', $school_id)
            ->where('role_id', 3) // role_id = 3 for teachers
            ->get(['id', 'name']);

        \Log::info("✅ Teachers fetched:", $teachers->toArray());
        return response()->json(['teachers' => $teachers]);
    }

    // Else: fetch based on teacher assignments table
    $teacherIds = \DB::table('select_teachers')
        ->when($class_id !== 'all', fn($q) => $q->where('class_id', $class_id))
        ->when($section_id !== 'all', fn($q) => $q->where('section_id', $section_id))
        ->pluck('teacher_id');

    \Log::info("👨‍🏫 Teacher User IDs:", $teacherIds->toArray());

    $teachers = \App\Models\User::whereIn('id', $teacherIds)
        ->where('school_id', $school_id)
        ->where('role_id', 3)
        ->get(['id', 'name']);

    \Log::info("✅ Teachers fetched:", $teachers->toArray());

    return response()->json(['teachers' => $teachers]);
}




     /*
    public function getParentsByClass($class_id)
{
    // Get the school ID of the logged-in admin
    $school_id = auth()->user()->school_id;
    
    // Log the class ID and school ID for debugging
    Log::info('Fetching parents for class ID: ' . $class_id);
    Log::info('Logged-in Admin School ID: ' . $school_id);
    
    // Get all students for the selected class and ensure they belong to the logged-in admin's school
    $students = Enrollment::where('class_id', $class_id)
        ->whereHas('student', function($query) use ($school_id) {
            $query->where('school_id', $school_id);
        })
        ->with('student') // Eager load the related student data
        ->get();
    
    // Log the number of students found
    Log::info('Number of students found for class ' . $class_id . ': ' . $students->count());
    
    // Get the unique parent_ids of the students
    $parentIds = $students->pluck('student.parent_id')->unique();
    
    // Log the parent IDs found
    Log::info('Parent IDs: ' . $parentIds->implode(', '));
    
    // Fetch the parents by parent_ids and ensure they belong to the same school as the logged-in admin
    $parents = User::whereIn('id', $parentIds)
                   ->where('role_id', 6) // Only parents
                   ->where('school_id', $school_id) // Ensure parents belong to the same school as the logged-in admin
                   ->get();
    
    // Log the parent names found
    Log::info('Parents Found for class ' . $class_id . ': ' . $parents->pluck('name')->implode(', '));
    
    // Add "All Parents" option
    $parentsList = $parents->prepend((object) ['id' => 'all', 'name' => 'All Parents']);
    
    return response()->json(['parents' => $parentsList]);
}
public function getSectionsByClass($id)
{
    $school_id = auth()->user()->school_id;

    // Fetch sections that belong to the selected class and the logged-in school
    $sections = \App\Models\Section::where('class_id', $id)
        ->where('school_id', $school_id)
        ->get(['id', 'name']);

    return response()->json(['sections' => $sections]);
}

*/

public function getSectionsByClass($class_id)
{
    \Log::info("📥 Request received: getSectionsByClass for class_id = $class_id");

    $sections = \App\Models\Section::where('class_id', $class_id)
        ->get(['id', 'name']); // ✅ Removed ->where('school_id', ...)

    \Log::info("✅ Sections fetched:", $sections->toArray());

    return response()->json(['sections' => $sections]);
}

        // Controller Method
public function getAllTeachersBySchool()
{
    $school_id = auth()->user()->school_id;

    \Log::info("📥 Request received: getAllTeachersBySchool");

    // Join teacher_permissions and users to get unique teachers per school
    $teacherIds = DB::table('teacher_permissions')
        ->join('users', 'teacher_permissions.teacher_id', '=', 'users.id')
        ->where('users.school_id', $school_id)
        ->pluck('users.id')
        ->unique();

    $teachers = User::whereIn('id', $teacherIds)
        ->where('role_id', 3)
        ->select('id', 'name')
        ->get();

    \Log::info("✅ All Teachers Fetched:", $teachers->toArray());

    return response()->json(['teachers' => $teachers]);
}



// Fetch sections by class
public function getAllSectionsBySchool()
{
    \Log::info("📥 Request received: getAllSectionsBySchool");

    // Removed school_id filter since it's not in the sections table
    $sections = \App\Models\Section::get(['id', 'name']);

    \Log::info("✅ All Sections:", $sections->toArray());

    return response()->json(['sections' => $sections]);
}

public function getAllStudentsBySchool()
{
    \Log::info("📥 Request received: getAllStudentsBySchool");

    $students = User::where('role_id', 7)
        ->where('school_id', auth()->user()->school_id)
        ->get(['id', 'name']);

    \Log::info("✅ All Students:", $students->toArray());

    return response()->json(['students' => $students]);
}
public function getAllParentsBySchool()
{
    \Log::info("📥 Request received: getAllParentsBySchool");

    $parents = User::where('role_id', 6)
        ->where('school_id', auth()->user()->school_id)
        ->get(['id', 'name']);

    \Log::info("✅ All Parents:", $parents->toArray());

    return response()->json(['parents' => $parents]);
}

public function getStudentsByClassAndSection($class_id, $section_id)
{
    $school_id = auth()->user()->school_id;

    \Log::info("📥 getStudentsByClassAndSection for class_id = $class_id, section_id = $section_id");

    $enrollmentQuery = \App\Models\Enrollment::where('school_id', $school_id);

    if ($class_id !== 'all') {
        $enrollmentQuery->where('class_id', $class_id);
    }

    if ($section_id !== 'all') {
        $enrollmentQuery->where('section_id', $section_id);
    }

    $studentIds = $enrollmentQuery->pluck('user_id');
    \Log::info("👨‍🎓 Enrolled Student IDs:", $studentIds->toArray());

    $students = \App\Models\User::where('role_id', 7)
        ->where('school_id', $school_id)
        ->whereIn('id', $studentIds)
        ->select('id', 'name')
        ->get();

    \Log::info("✅ Students fetched:", $students->toArray());

    return response()->json(['students' => $students]);
}

public function getParentsByClassAndSection($class_id, $section_id)
{
    $school_id = auth()->user()->school_id;
    \Log::info("📥 getParentsByClassAndSection for class_id = $class_id, section_id = $section_id");

    $query = \App\Models\Enrollment::query()->where('school_id', $school_id);

    if ($class_id !== 'all') {
        $query->where('class_id', $class_id);
    }

    if ($section_id !== 'all') {
        $query->where('section_id', $section_id);
    }

    $studentIds = $query->pluck('user_id');
    \Log::info("👨‍🎓 Student User IDs:", $studentIds->toArray());

    $parentIds = \App\Models\User::where('role_id', 7)
        ->where('school_id', $school_id)
        ->whereIn('id', $studentIds)
        ->pluck('parent_id')
        ->unique()
        ->filter();

    \Log::info("🆔 Unique Parent IDs:", $parentIds->toArray());

    $parents = \App\Models\User::whereIn('id', $parentIds)
        ->where('role_id', 6)
        ->where('school_id', $school_id)
        ->select('id', 'name')
        ->get();

    \Log::info("✅ Parents fetched:", $parents->toArray());

    return response()->json(['parents' => $parents]);
}


    /**
     * Show the admin list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function adminList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $admins = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 2);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 2);
                })->paginate(10);

        } else {
            $admins = User::where('role_id', 2)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.admin.admin_list', compact('admins', 'search'));
    }



        /**
         * Show the admin add modal.
         *
         * @return \Illuminate\Contracts\Support\Renderable
         */
        public function adminOutbox()
{
    $messages = \App\Models\Message::where('from_user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('subject');

    $messageGroups = $messages->map(function ($group) {
        $first = $group->first();

        $receiverLabel = 'Unknown';

        if ($first->recipient_type === 'all_parents') {
            $receiverLabel = 'All Parents';
        } elseif ($first->recipient_type === 'all_students') {
            $receiverLabel = 'All Students';
        } elseif ($first->recipient_type === 'all_classes') {
            $receiverLabel = 'All Classes';
        } elseif ($first->recipient_type === 'all_students_parents') {
            $receiverLabel = 'All Students & Parents';
        } elseif ($first->recipient_type === 'class' && !empty($first->class_id)) {
            $class = \App\Models\ClassModel::find($first->class_id);
            $receiverLabel = 'Class ' . ($class->name ?? 'Unknown');
        } elseif (!empty($first->to_user_id)) {
            $recipientIds = explode(',', $first->to_user_id);
            $recipients = \App\Models\User::whereIn('id', $recipientIds)->get();

            if ($recipients->isNotEmpty()) {
                $names = $recipients->pluck('name')->take(3)->join(', ');
                $moreCount = $recipients->count() > 3 ? ' +' . ($recipients->count() - 3) . ' more' : '';

                $studentCount = $recipients->where('role_id', 7)->count();
                $parentCount  = $recipients->where('role_id', 6)->count();

                $receiverLabel = $names . $moreCount . " (" .
                    ($studentCount > 0 ? "$studentCount student" . ($studentCount > 1 ? 's' : '') : '') .
                    ($studentCount > 0 && $parentCount > 0 ? ', ' : '') .
                    ($parentCount > 0 ? "$parentCount parent" . ($parentCount > 1 ? 's' : '') : '') . ")";
            }
        }

        return (object)[
            'message_id'      => $first->id,
            'to_user_id'      => $first->to_user_id,
            'role_id'         => $first->role_id,
            'class_id'        => $first->class_id,
            'receiver_label'  => $receiverLabel,
            'subject'         => $first->subject,
            'body'            => $first->body,
            'attachment_path' => $first->attachment_path,
            'created_at'      => $first->created_at,
        ];
    });

    return view('admin.compose.outbox', compact('messageGroups'));
}


    //Message Detail
    public function messageDetails($id)
    {
        $message = Message::findOrFail($id);
        return view('admin.compose.details', compact('message'));
    }




    /**
     * Show the admin add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createModal()
    {
        return view('admin.admin.add_admin');
    }



    

    public function adminCreate(Request $request)
{
    Log::info('adminCreate function started.');
    $data = $request->all();

    // Handle photo upload
    if (!empty($data['photo'])) {
        $imageName = time().'.'.$data['photo']->extension();
        $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
        $photo = $imageName;
    } else {
        $photo = '';
    }

    // Prepare user_information JSON
    $info = array(
        'gender' => $data['gender'],
        'blood_group' => $data['blood_group'],
        'birthday' => strtotime($data['birthday']),
        'phone' => $data['phone'],
        'address' => $data['address'],
        'photo' => $photo,
        'school_role' => 0,
    );
    $data['user_information'] = json_encode($info);

    // Check for duplicate email
    $duplicate_user_check = User::get()->where('email', $data['email']);

    if (count($duplicate_user_check) == 0) {

        // Create user in users table
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '2',  // Admin role
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);

        // Get role name for role_id = 2 (Admin)
        $roleName = Role::where('role_id', '2')->value('name');

        // Find matching hr_roles.id for role name and school
        $hrRole = Hr_roles::where('school_id', auth()->user()->school_id)
                          ->whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                          ->first();
        $hr_roles_role_id = $hrRole ? $hrRole->id : null;

        // Create hr_user_list entry with hr_roles_role_id
        Hr_user_list::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => '2',
            'hr_roles_role_id' => $hr_roles_role_id,
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'joining_salary' => $data['joining_salary'],
            'school_id' => auth()->user()->school_id,
            'emp_bioid' => $data['emp_bioid']
        ]);

        // Send email if SMTP settings exist
        if (!empty(get_settings('smtp_user')) && !empty(get_settings('smtp_pass')) &&
            !empty(get_settings('smtp_host')) && !empty(get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }

        return redirect()->back()->with('message', 'Admin added successfully');

    } else {
        return redirect()->back()->with('error', 'Email was already taken.');
    }
}



  /*  
    public function adminCreate(Request $request)

    {

        Log::info('adminCreate function started.');
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
            'school_role' => 0,
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);


        if(count($duplicate_user_check) == 0) {
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '2',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);

            Hr_user_list::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => '2',
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'joining_salary' => $data['joining_salary'],
                'school_id' => auth()->user()->school_id,
                'emp_bioid' => $data['emp_bioid']
            ]);
            return redirect()->back()->with('message', 'Admin added successfully');
   
        } 
        else {
            return redirect()->back()->with('error','Email was already taken.');
        }
        if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message','You have successfully add user.');
    }
*/

/*
    public function editModal($id)
    {
        $user = User::find($id);
        return view('admin.admin.edit_admin', ['user' => $user]);
    }
*/



public function editModal($id)
{
    $user = User::find($id);  // Fetch user details from the users table
    $hrUser = Hr_user_list::where('user_id', $id)->first(); // Fetch HR details

    return view('admin.admin.edit_admin', ['user' => $user, 'hrUser' => $hrUser]);
}

  

/*    
    public function adminUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        return redirect()->back()->with('message','You have successfully update user.');
    }


    */
    
    
    public function adminUpdate(Request $request, $id)
    {
        Log::info("Updating user with ID: " . $id);
    
        $data = $request->all();
        $photo = ''; // default
    
        // ✅ Handle image upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $imageName = time().'.'.$request->file('photo')->extension();
            $request->file('photo')->move(public_path('assets/uploads/user-images/'), $imageName);
            $photo = $imageName;
        } else {
            // ✅ Retain old photo if exists
            $user_information = User::where('id', $id)->value('user_information');
            $decoded = json_decode($user_information, true);
            $photo = $decoded['photo'] ?? '';
        }
    
        // ✅ Create user_information array
        $info = [
            'gender' => $data['gender'] ?? '',
            'blood_group' => $data['blood_group'] ?? '',
            'birthday' => !empty($data['birthday']) ? strtotime($data['birthday']) : null,
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'photo' => $photo
        ];
    
        // ✅ Update User Table
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => json_encode($info),
        ]);
    
        // ✅ Update or Insert into HR Table
        Hr_user_list::updateOrCreate(
            ['user_id' => $id],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'] ?? '',
                'blood_group' => $data['blood_group'] ?? '',
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'joining_salary' => $data['joining_salary'] ?? '',
                'emp_bioid' => $data['emp_bioid'] ?? ''
            ]
        );
    
        Log::info("User update successful for ID: " . $id);
        return redirect()->back()->with('message', 'User updated successfully.');
    }

    
    
    

/*
    public function adminUpdate(Request $request, $id)
{
    Log::info("Updating user with ID: " . $id);

    $data = $request->all();
    if(!empty($data['photo'])){

        $imageName = time().'.'.$data['photo']->extension();

        $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

        $photo  = $imageName;
    } else {
        $user_information = User::where('id', $id)->value('user_information');
        $file_name = json_decode($user_information)->photo;

        if($file_name != ''){
            $photo = $file_name;
        } else {
            $photo = '';
        }
    }
    $info = array(
        'gender' => $data['gender'],
        'blood_group' => $data['blood_group'],
        'birthday' => strtotime($data['birthday']),
        'phone' => $data['phone'],
        'address' => $data['address'],
        'photo' => $photo
    );
    $data['user_information'] = json_encode($info);
    // Update User Table
    $user = User::where('id', $id)->update([
        'name' => $data['name'],
        'email' => $data['email'],
        'user_information' => json_encode([
            'gender' => $data['gender'] ?? '',
            'blood_group' => $data['blood_group'] ?? '',
            'birthday' => !empty($data['birthday']) ? strtotime($data['birthday']) : null,
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'photo' => $request->hasFile('photo') ? $request->file('photo')->store('uploads/user-images') : ''
        ]),
    ]);

    // Update or Insert into HR Table
    $hrUser = Hr_user_list::updateOrCreate(
        ['user_id' => $id],  // Condition to check if HR record exists
        [
            'name' => $data['name'],
            'email' => $data['email'],
            'gender' => $data['gender'] ?? '',
            'blood_group' => $data['blood_group'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
          //  'role_id' => $data['role_id'] ?? null,
            'joining_salary' => $data['joining_salary'] ?? '',
            'emp_bioid' => $data['emp_bioid'] ?? ''
        ]
    );

    Log::info("User update successful for ID: " . $id);
    return redirect()->back()->with('message', 'User updated successfully.');
}*/


    public function adminDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 2);
        return redirect()->route('admin.admin')->with('message','You have successfully deleted user.');
    }

    public function menuSettingsView($id)
     {
        $user = User::find($id);
        return view('admin.admin.menu_permission', ['user' => $user]);
     }


    public function menuPermissionUpdate(Request $request, $id)
    {

        User::where('id', $id)->update([
            'menu_permission' => json_encode($request->permissions),
        ]);

        return redirect()->back()->with('message', 'You have successfully updated user permissions.');
    }


    public function adminProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.admin.admin_profile', ['user_details' => $user_details]);
        // return view('admin.admin.admin_profile');
    }
    
    function school_user_password(Request $request){

        $userId = $request->input('user_id');

        $data['password'] = Hash::make($request->password);
        User::where('id', $userId)->update($data);

        return redirect()->back()->with('message', 'You have successfully update password.');
    }

    public function adminDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.admin.documents', ['user_details' => $user_details]);
    }

    public function accountantDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.accountant.documents', ['user_details' => $user_details]);
    }

    public function librarianDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.librarian.documents', ['user_details' => $user_details]);
    }

    public function parentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.parent.documents', ['user_details' => $user_details]);
    }

    public function studentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.student.documents', ['user_details' => $user_details]);
    }

    public function teacherDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.teacher.documents', ['user_details' => $user_details]);
    }

    public function documentsUpload(Request $request, $id="")
    {
        // Validate the request
        $request->validate([
            'file_name' => 'required',
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Get the current user
        $user = User::find($id);

        $filePath = $file->move(public_path('assets/uploads/user-docs/'.$user->id.'/'), $fileName);

        // Get existing documents or initialize as an empty array
        $documents = $user->documents ? json_decode($user->documents, true) : [];

        // Add the new document with the provided file name
        $documents[slugify($request->input('file_name'))] = $fileName;

        // Update the user's documents
        $user->update(['documents' => json_encode($documents)]);
        
        return redirect()->back()->with('message', 'File uploaded successfully.');
    }

    public function documentsRemove($id = "", $file_name="")
    {
        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Get the documents as an array
            $documents = json_decode($user->documents, true);

            // Check if the file with the given file_name exists
            if (isset($documents[$file_name])) {
                $file_path = public_path('assets/uploads/user-docs/'.$user->id.'/'.$documents[$file_name]);
                
                // Check if the file exists
                if (file_exists($file_path)) {
                    // Delete the file
                    unlink($file_path);

                    // Remove the file entry from the documents array
                    unset($documents[$file_name]);

                    // Update the user's documents column
                    $user->update(['documents' => json_encode($documents)]);

                    return redirect()->back()->with('message', 'File removed successfully.');
                } else {
                    return redirect()->back()->with('error', 'File not found.');
                }
            } else {
                return redirect()->back()->with('error', 'File not found.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }


    /**
     * Show the teacher list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $teachers = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 3);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 3);
                })->paginate(10);

        } else {
            $teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.teacher.teacher_list', compact('teachers', 'search'));
    }

    /**
     * Show the teacher add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTeacherModal()
    {
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.add_teacher', ['departments' => $departments]);
    }

    public function adminTeacherCreate(Request $request)
    {
        $data = $request->all();
    
             // Handle photo upload
    if (!empty($data['photo'])) {
        $imageName = time().'.'.$data['photo']->extension();
        $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
        $photo = $imageName;
    } else {
        $photo = '';
    }

    
        // Prepare user_information JSON
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);
    
        // Check for duplicate email
        $duplicate_user_check = User::get()->where('email', $data['email']);
    
        if (count($duplicate_user_check) == 0) {
    
            // Create user in users table
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '3',  // Teacher role
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
                'department_id' => $data['department_id'],
                'designation' => $data['designation'],
            ]);
    
            // Get role name for role_id = 3 (Teacher)
            $roleName = Role::where('role_id', '3')->value('name');
    
            // Find matching hr_roles.id for role name and school
            $hrRole = Hr_roles::where('school_id', auth()->user()->school_id)
                              ->whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                              ->first();
            $hr_roles_role_id = $hrRole ? $hrRole->id : null;
    
            // Create hr_user_list entry with hr_roles_role_id
            Hr_user_list::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => '3',
                'hr_roles_role_id' => $hr_roles_role_id,
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'joining_salary' => $data['joining_salary'],
                'school_id' => auth()->user()->school_id,
                'emp_bioid' => $data['emp_bioid']
            ]);
    
            // Send email if SMTP settings are configured
            if (!empty(get_settings('smtp_user')) && !empty(get_settings('smtp_pass')) &&
                !empty(get_settings('smtp_host')) && !empty(get_settings('smtp_port'))) {
                Mail::to($data['email'])->send(new NewUserEmail($data));
            }
    
            return redirect()->back()->with('message', 'You have successfully add teacher.');
    
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
    }
    
    public function teacherEditModal($id)
    {
        $user = User::find($id);
        $hrUser = Hr_user_list::where('user_id', $id)->first(); // Fetch HR details
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.edit_teacher', ['user' => $user, 'hrUser' => $hrUser,  'departments' => $departments]);
    }

    public function teacherUpdate(Request $request, $id)
    {
        \Log::info('Starting teacher update process', ['teacher_id' => $id]);
    
        try {
            $photo = '';
            
            // ✅ Handle the file upload properly
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                \Log::info('🟢 File passed validation');
                $file = $request->file('photo');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/user-images/'), $imageName);
                $photo = $imageName;
                \Log::info('🟢 Photo uploaded', ['photo' => $photo]);
            } else {
                \Log::info('📌 No photo uploaded or invalid file');
                $user_information = User::where('id', $id)->value('user_information');
                $existing = json_decode($user_information);
                $photo = $existing->photo ?? '';
                \Log::info('Retaining existing photo', ['photo' => $photo]);
            }
    
            // ✅ Now safely get all other request data
            $data = $request->only([
                'name', 'email', 'gender', 'blood_group', 'birthday', 'phone',
                'address', 'joining_salary', 'emp_bioid'
            ]);
    
            // ✅ Prepare updated info array
            $info = [
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'birthday' => strtotime($data['birthday']),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'photo' => $photo
            ];
            \Log::info('Preparing user info array', ['info' => $info]);
    
            // ✅ Update User
            User::where('id', $id)->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_information' => json_encode($info),
            ]);
            \Log::info('✅ User table updated');
    
            // ✅ Update or Create HR user
            Hr_user_list::updateOrCreate(
                ['user_id' => $id],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'gender' => $data['gender'] ?? '',
                    'blood_group' => $data['blood_group'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? '',
                    'joining_salary' => $data['joining_salary'] ?? '',
                    'emp_bioid' => $data['emp_bioid'] ?? ''
                ]
            );
            \Log::info('✅ HR user list updated');
    
            return redirect()->back()->with('message', 'You have successfully updated teacher.');
        } catch (\Exception $e) {
            \Log::error('❌ Error during teacher update', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong while updating teacher.');
        }
    }
    
    public function teacherDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 3);
        return redirect()->route('admin.teacher')->with('message','You have successfully deleted teacher.');
    }
    public function teacherProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.teacher.teacher_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the accountant list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function accountantList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $accountants = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 4);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 4);
                })->paginate(10);

        } else {
            $accountants = User::where('role_id', 4)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.accountant.accountant_list', compact('accountants', 'search'));
    }

    public function createAccountantModal()
    {
        return view('admin.accountant.add_accountant');
    }

    public function accountantCreate(Request $request)
{
    $data = $request->all();

    // Handle photo upload
    if (!empty($data['photo'])) {
        $imageName = time().'.'.$data['photo']->extension();
        $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
        $photo = $imageName;
    } else {
        $photo = '';
    }

    // Prepare user_information JSON
    $info = array(
        'gender' => $data['gender'],
        'blood_group' => $data['blood_group'],
        'birthday' => strtotime($data['birthday']),
        'phone' => $data['phone'],
        'address' => $data['address'],
        'photo' => $photo
    );
    $data['user_information'] = json_encode($info);

    // Check for duplicate email
    $duplicate_user_check = User::get()->where('email', $data['email']);

    if (count($duplicate_user_check) == 0) {

        // Create user in users table
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '4',  // Accountant role
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);

        // Get role name for role_id = 4 (Accountant)
        $roleName = Role::where('role_id', '4')->value('name');

        // Find matching hr_roles.id for role name and school_id
        $hrRole = Hr_roles::where('school_id', auth()->user()->school_id)
                          ->whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                          ->first();
        $hr_roles_role_id = $hrRole ? $hrRole->id : null;

        // Create hr_user_list with hr_roles_role_id
        Hr_user_list::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => '4',
            'hr_roles_role_id' => $hr_roles_role_id,
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'joining_salary' => $data['joining_salary'],
            'school_id' => auth()->user()->school_id,
            'emp_bioid' => $data['emp_bioid']
        ]);

        // Send email if SMTP settings are configured
        if (!empty(get_settings('smtp_user')) && !empty(get_settings('smtp_pass')) &&
            !empty(get_settings('smtp_host')) && !empty(get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }

        return redirect()->back()->with('message', 'You have successfully add accountant.');

    } else {
        return redirect()->back()->with('error', 'Email was already taken.');
    }
}

    public function accountantEditModal($id)
    {
        $user = User::find($id);
        $hrUser = Hr_user_list::where('user_id', $id)->first(); // Fetch HR details
        return view('admin.accountant.edit_accountant', ['user' => $user, 'hrUser' => $hrUser]);
    }

    public function accountantUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);
        // Update or Insert into HR Table
    $hrUser = Hr_user_list::updateOrCreate(
        ['user_id' => $id],  // Ensures the record exists
        [
            'name' => $data['name'],
            'email' => $data['email'],
            'gender' => $data['gender'] ?? '',
            'blood_group' => $data['blood_group'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
          //  'role_id' => $data['role_id'] ?? '',
            'joining_salary' => $data['joining_salary'] ?? '',
            'emp_bioid' => $data['emp_bioid'] ?? ''
        ]
    );

        return redirect()->back()->with('message','You have successfully update accountant.');
    }

    public function accountantDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 4);
        return redirect()->route('admin.accountant')->with('message','You have successfully deleted accountant.');
    }

    public function accountantProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.accountant.accountant_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the librarian list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function librarianList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $librarians = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 5);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 5);
                })->paginate(10);

        } else {
            $librarians = User::where('role_id', 5)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.librarian.librarian_list', compact('librarians', 'search'));
    }

    public function createLibrarianModal()
    {
        return view('admin.librarian.add_librarian');
    }

    public function librarianCreate(Request $request)
    {
        $data = $request->all();
    
        // Handle photo upload
        if (!empty($data['photo'])) {
            $imageName = time().'.'.$data['photo']->extension();
            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
            $photo = $imageName;
        } else {
            $photo = '';
        }
    
        // Prepare user_information JSON
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);
    
        // Check for duplicate email
        $duplicate_user_check = User::get()->where('email', $data['email']);
    
        if (count($duplicate_user_check) == 0) {
    
            // Create user in users table
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '5',  // Librarian role
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
    
            // Get role name for role_id = 5 (Librarian)
            $roleName = Role::where('role_id', '5')->value('name');
    
            // Find matching hr_roles.id for role name and school_id
            $hrRole = Hr_roles::where('school_id', auth()->user()->school_id)
                              ->whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                              ->first();
            $hr_roles_role_id = $hrRole ? $hrRole->id : null;
    
            // Create hr_user_list with hr_roles_role_id
            Hr_user_list::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => '5',
                'hr_roles_role_id' => $hr_roles_role_id,
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'joining_salary' => $data['joining_salary'],
                'school_id' => auth()->user()->school_id,
                'emp_bioid' => $data['emp_bioid']
            ]);
    
            // Send email if SMTP settings are configured
            if (!empty(get_settings('smtp_user')) && !empty(get_settings('smtp_pass')) &&
                !empty(get_settings('smtp_host')) && !empty(get_settings('smtp_port'))) {
                Mail::to($data['email'])->send(new NewUserEmail($data));
            }
    
            return redirect()->back()->with('message', 'You have successfully add librarian.');
    
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
    }
    
    public function librarianEditModal($id)
    {
        $user = User::find($id);
        $hrUser = Hr_user_list::where('user_id', $id)->first(); // Fetch HR details

        return view('admin.librarian.edit_librarian', ['user' => $user,'hrUser' => $hrUser]);
    }

    public function librarianUpdate(Request $request, $id)
    {
        $data = $request->all();

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);// Update or Insert into HR Table
        $hrUser = Hr_user_list::updateOrCreate(
            ['user_id' => $id],  // Ensures the record exists
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'] ?? '',
                'blood_group' => $data['blood_group'] ?? '',
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
               // 'role_id' => $data['role_id'] ?? null,
                'joining_salary' => $data['joining_salary'] ?? '',
                'emp_bioid' => $data['emp_bioid'] ?? ''
            ]
        );

        return redirect()->back()->with('message','You have successfully update librarian.');
    }

    public function librarianDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.librarian')->with('message','You have successfully deleted librarian.');
    }

    public function librarianProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.librarian.librarian_profile', ['user_details' => $user_details]);
    }


    /**
     * Show the parent list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function parentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $parents = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 6);
                })->orWhere(function ($query) use($search) {
                    $query->where('email', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 6);
                })->paginate(10);

        } else {
            $parents = User::where('role_id', 6)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.parent.parent_list', compact('parents', 'search'));
    }

    public function createParent()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.add_parent', ['classes' => $classes]);
    }


    public function parentCreate(Request $request)
    {
        $data = $request->all();
        
        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        $parent = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => '6',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
        $students = $data['student_id'];
        $class_id = $data['class_id'];
        $section_id = $data['student_id'];

        foreach($students as $student){
            $users = User::where('id', $student)->get();

            if(count($users) == 1) {
                User::where('id', $student)->update([
                    'parent_id' => $parent->id,
                ]);
            } else {
                if(count($users) > 1) {
                    foreach($users as $user){
                        $data = Enrollment::where('class_id', $class_id)->where('section_id', $section_id)->where('user_id', $user->id)->where('school_id', auth()->user()->school_id)->first();

                        if($data != '') {
                            User::where('id', $user->id)->update([
                                'parent_id' => $parent->id,
                            ]);
                        }
                    }
                }
            }
        }
        if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        
        return redirect()->back()->with('message','You have successfully add parent.');
    }


    public function parentEditModal($id)
    {
        $user = User::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.edit_parent', ['user' => $user, 'classes' => $classes]);
    }
    
    
    
/*
    public function parentUpdate(Request $request, $id)
    {
        $data = $request->all();


        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {

            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);


        //Previous parent has been empty
        User::where('parent_id', $id)->update(['parent_id' => null]);


        $students = $data['student_id'];
        foreach($students as $student){
            if($student != '') {
                $user = User::where('id', $student)->first();

                if($user != '') {
                    User::where('id', $user->id)->update([
                        'parent_id' => $id,
                    ]);
                }
            }
        }

        return redirect()->back()->with('message','You have successfully update parent.');
    }
*/



public function parentUpdate(Request $request, $id)
{
    Log::info("Parent update started for user ID: {$id}");

    $data = $request->all();
    Log::info("Request data received:", $data);

    // ✅ Handle photo upload
    if ($request->hasFile('photo')) {
        Log::info("New photo uploaded. Processing...");
        $imageName = time().'.'.$request->file('photo')->extension();
        $request->file('photo')->move(public_path('assets/uploads/user-images/'), $imageName);
        $photo = $imageName;
        Log::info("Photo saved as: {$photo}");
    } else {
        Log::info("No new photo uploaded. Checking existing photo.");
        $user_information = User::where('id', $id)->value('user_information');
        $decoded_info = json_decode($user_information, true);
        $photo = $decoded_info['photo'] ?? '';
        Log::info("Retained existing photo: {$photo}");
    }

    // ✅ Build user_information
    $info = [
        'gender' => $data['gender'] ?? '',
        'blood_group' => $data['blood_group'] ?? '',
        'birthday' => !empty($data['birthday']) ? strtotime($data['birthday']) : null,
        'phone' => $data['phone'] ?? '',
        'address' => $data['address'] ?? '',
        'photo' => $photo
    ];

    Log::info("Final user_information to store:", $info);

    // ✅ Update parent user
    User::where('id', $id)->update([
        'name' => $data['name'],
        'email' => $data['email'],
        'user_information' => json_encode($info),
    ]);
    Log::info("Parent basic info updated in users table for ID: {$id}");

    // ✅ Unlink old children
    $affected = User::where('parent_id', $id)->update(['parent_id' => null]);
    Log::info("Cleared previous children for parent ID {$id}. Affected rows: {$affected}");

    // ✅ Assign new children
$students = array_filter($request->input('student_id', []));
Log::info("Student list received:", $students);

// Clear existing parent links only if you're updating child assignments.
User::where('parent_id', $id)->update(['parent_id' => null]);

foreach ($students as $studentId) {
    if (!empty($studentId)) {
        $student = User::find($studentId);
        if ($student) {
            $student->update(['parent_id' => $id]);
            Log::info("Linked student ID {$studentId} to parent ID {$id}");
        }
    }
}


    Log::info("Parent update completed successfully for ID: {$id}");

    return redirect()->back()->with('message', 'You have successfully updated the parent.');
}



    public function parentDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.parent')->with('message','You have successfully deleted parent.');
    }

    
    public function parentProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.parent.parent_profile', ['user_details' => $user_details]);
    }
    /**
     * Show the student list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    
    
public function studentList(Request $request)
{
    $search     = $request->search ?? '';
    $class_id   = $request->class_id ?? '';
    $section_id = $request->section_id ?? '';
    $school_id  = auth()->user()->school_id;

    $students = \App\Models\Enrollment::query()
        ->where('enrollments.school_id', $school_id)
        ->join('users', function ($join) use ($search) {
            $join->on('enrollments.user_id', '=', 'users.id')
                 ->where('users.role_id', 7);

            if (!empty($search)) {
                $join->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', "%{$search}%")
                          ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
        })
        ->select('enrollments.*')
        ->paginate(10);

    $classes   = \App\Models\Classes::where('school_id', $school_id)->get();
    $sessions  = \App\Models\Session::where('school_id', $school_id)->get(); // ✅ Add this

    return view('admin.student.student_list', [
        'students'    => $students,
        'search'      => $search,
        'class_id'    => $class_id,
        'section_id'  => $section_id,
        'session_id'  => '', // Optional: blank on initial load
        'classes'     => $classes,
        'sessions'    => $sessions // ✅ Pass sessions
    ]);
}

    
    /*
    public function studentList(Request $request)
    {
        $search = $request['search'] ?? "";
        $class_id = $request['class_id'] ?? "";
        $section_id = $request['section_id'] ?? "";

        $users = User::where(function ($query) use($search) {
            $query->where('users.name', 'LIKE', "%{$search}%")
                ->orWhere('users.email', 'LIKE', "%{$search}%");
        });

        $users->where('users.school_id', auth()->user()->school_id)
        ->where('users.role_id', 7);

        if($section_id == 'all' || $section_id != ""){
            $users->where('section_id', $section_id);
        }

        if($class_id == 'all' || $class_id != ""){
            $users->where('class_id', $class_id);
        }

        $students = $users->join('enrollments', 'users.id', '=', 'enrollments.user_id')->select('enrollments.*')->paginate(10);
        
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);

        return view('admin.student.student_list', compact('students', 'search', 'classes', 'class_id', 'section_id'));
    }
    
    */
    
    
/*
//  Without SESSION
public function filterStudents(Request $request)
{
    $class_id = $request->class_id ?? '';
    $section_id = $request->section_id ?? '';
    $school_id = auth()->user()->school_id;

    Log::info("Class/Section Filter Request", [
        'class_id' => $class_id,
        'section_id' => $section_id,
        'school_id' => $school_id,
        'user_id' => auth()->id()
    ]);

    $students = \App\Models\Enrollment::query()
        ->where('enrollments.school_id', $school_id)
        ->when($class_id != '', fn($q) => $q->where('enrollments.class_id', $class_id))
        ->when($section_id != '', fn($q) => $q->where('enrollments.section_id', $section_id))
        ->join('users', 'enrollments.user_id', '=', 'users.id')
        ->where('users.role_id', 7)
        ->select('enrollments.*')
        ->orderBy('users.name')
        ->paginate(10);

    Log::info("Filtered students found", ['count' => $students->total()]);

    $classes = \App\Models\Classes::where('school_id', $school_id)->get();

    return view('admin.student.student_list', compact(
        'students',
        'class_id',
        'section_id',
        'classes'
    ))->with('search', ''); 
}

*/

// With SESSION

/*
public function filterStudents(Request $request)
{
    $school_id   = auth()->user()->school_id;
    $class_id    = $request->class_id ?? '';
    $section_id  = $request->section_id ?? '';
    $session_id  = $request->session_id ?? '';
    $search      = $request->search ?? '';

    \Log::info('Student List Filter Request', [
        'search'     => $search,
        'class_id'   => $class_id,
        'section_id' => $section_id,
        'session_id' => $session_id,
        'school_id'  => $school_id,
        'user_id'    => auth()->user()->id
    ]);

    $students = \App\Models\Enrollment::query()
        ->where('enrollments.school_id', $school_id)
        ->when($class_id, fn($q) => $q->where('enrollments.class_id', $class_id))
        ->when($section_id, fn($q) => $q->where('enrollments.section_id', $section_id))
        ->when($session_id, fn($q) => $q->where('enrollments.session_id', $session_id))
        ->join('users', function ($join) use ($search) {
            $join->on('enrollments.user_id', '=', 'users.id')
                 ->where('users.role_id', 7);

            if (!empty($search)) {
                $join->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', "%{$search}%")
                          ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
        })
        ->select('enrollments.*')
        ->paginate(10);

    \Log::info('Filtered students fetched', ['total' => $students->total()]);

    $classes   = \App\Models\Classes::where('school_id', $school_id)->get();
    $sessions  = \App\Models\Session::where('school_id', $school_id)->get();
    $session   = \App\Models\Session::where('id', $session_id)->first();

    return view('admin.student.student_list', [
        'students'      => $students,
        'class_id'      => $class_id,
        'section_id'    => $section_id,
        'session_id'    => $session_id,
        'session_title' => $session?->session_title ?? '',
        'search'        => $search,
        'classes'       => $classes,
        'sessions'      => $sessions,
    ]);
}
*/

public function filterStudents(Request $request)
{
    $school_id    = auth()->user()->school_id;
    $class_id     = $request->class_id ?? '';
    $section_id   = $request->section_id ?? '';
    $session_id   = $request->session_id ?? '';
    $search       = $request->search ?? '';
    $filter_type  = $request->filter_type ?? '';
    $filter_value = $request->filter_value ?? '';

    \Log::info('Student List Filter Request', [
        'search'       => $search,
        'class_id'     => $class_id,
        'section_id'   => $section_id,
        'session_id'   => $session_id,
        'filter_type'  => $filter_type,
        'filter_value' => $filter_value,
        'school_id'    => $school_id,
        'user_id'      => auth()->user()->id
    ]);

    $students = \App\Models\Enrollment::query()
        ->where('enrollments.school_id', $school_id)
        ->when($class_id, fn($q) => $q->where('enrollments.class_id', $class_id))
        ->when($section_id, fn($q) => $q->where('enrollments.section_id', $section_id))
        ->when($session_id, fn($q) => $q->where('enrollments.session_id', $session_id))

        // Join users
        ->join('users', function ($join) use ($search) {
            $join->on('enrollments.user_id', '=', 'users.id')
                 ->where('users.role_id', 7);

            if (!empty($search)) {
                $join->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', "%{$search}%")
                          ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
        })

        // Always join admission_details
        ->leftJoin('admission_details', 'enrollments.user_id', '=', 'admission_details.user_id')

        // Apply conditional filtering
        ->when($filter_type && $filter_value, function ($query) use ($filter_type, $filter_value) {
            if (in_array($filter_type, ['religion', 'caste'])) {
                $query->where("admission_details.$filter_type", $filter_value);
            } elseif ($filter_type === 'gender') {
                $query->where("users.gender", $filter_value);
            }
        })

        ->select('enrollments.*')
        ->paginate(10);

    \Log::info('Filtered students fetched', ['total' => $students->total()]);

    $classes   = \App\Models\Classes::where('school_id', $school_id)->get();
    $sessions  = \App\Models\Session::where('school_id', $school_id)->get();
    $session   = \App\Models\Session::where('id', $session_id)->first();

    return view('admin.student.student_list', [
        'students'      => $students,
        'class_id'      => $class_id,
        'section_id'    => $section_id,
        'session_id'    => $session_id,
        'session_title' => $session?->session_title ?? '',
        'search'        => $search,
        'classes'       => $classes,
        'sessions'      => $sessions,
        'filter_type'   => $filter_type,
        'filter_value'  => $filter_value,
    ]);
}




    public function createStudentModal()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.add_student', ['classes' => $classes]);
    }

    public function studentCreate(Request $request)
    {
        $data = $request->all();
        $code = student_code();
        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
           
        );

        $data['user_information'] = json_encode($info);
        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'code' => student_code(),
            'role_id' => '7',
            'school_id' => auth()->user()->school_id,
            'user_information' => $data['user_information'],
            'status' => 1,
        ]);
    } else {
        return redirect()->back()->with('error','Email was already taken.');
    }
    if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
        Mail::to($data['email'])->send(new NewUserEmail($data));
    }
        return redirect()->back()->with('message','You have successfully add student.');
    }

    public function studentIdCardGenerate($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('admin.student.id_card', ['student_details' => $student_details]);
    }
    
    


public function studentProfile($id)
{
    \Log::info('▶️ studentProfile() called for user_id: ' . $id);

    // Fetch student details (CommonController pulls full profile)
    $student_details = (new CommonController)->get_student_details_by_id($id);

    // Fetch admission and enrollment details
    $admission_details = AdmissionDetail::where('user_id', $id)->first();
    $enrollment = Enrollment::where('user_id', $id)->first();

    // Fallback values from admission → enrollment
    $class_id = $admission_details->class_id ?? $enrollment->class_id ?? null;
    $section_id = $admission_details->section_id ?? $enrollment->section_id ?? null;
    $session_id = $admission_details->session_id ?? $enrollment->session_id ?? null;

    // Fetch actual names
    $class = Classes::find($class_id);
    $section = Section::find($section_id);
    $session = Session::find($session_id);

    $class_name = $class ? $class->name : 'Unknown Class';
    $section_name = $section ? $section->name : 'Unknown Section';
    $session_title = $session ? $session->session_title : 'Unknown Session';

    \Log::info('[Student Profile Info]', [
        'student_id' => $id,
        'class_id' => $class_id,
        'section_id' => $section_id,
        'session_id' => $session_id,
        'class_name' => $class_name,
        'section_name' => $section_name,
        'session_title' => $session_title,
    ]);

    return view('admin.student.student_profile', [
        'student_details' => $student_details,
        'admission_details' => $admission_details,
        'class_name' => $class_name,
        'section_name' => $section_name,
        'session_title' => $session_title,
    ]);
}


   
    /*
    public function studentProfile($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('admin.student.student_profile', ['student_details' => $student_details]);
    }
*/
    public function studentEditModal($id)
    {
        $user = User::find($id);
        $student_details = (new CommonController)->get_student_details_by_id($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.edit_student', ['user' => $user, 'student_details' => $student_details, 'classes' => $classes]);
    }  


    public function studentUpdate(Request $request, $id)
    {
        $data = $request->all();
        $data['student_info'] = json_encode(array_filter($request->student_info));
         if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if($file_name != ''){
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
            'student_info'     => $data['student_info'],
        ]);

        Enrollment::where('user_id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'stu_bioid' => $data['stu_bioid']
        
        ]);

        return redirect()->back()->with('message','You have successfully update student.');
    }

    public function studentDelete($id)
    {
        $enroll = Enrollment::where('user_id', $id)->first();
        $enroll->delete();

        $fee_history = StudentFeeManager::get()->where('student_id', $id);
        $fee_history->map->delete();

        $attendances = DailyAttendances::get()->where('student_id', $id);
        $attendances->map->delete();

        $book_issues = BookIssue::get()->where('student_id', $id);
        $book_issues->map->delete();

        $gradebooks = Gradebook::get()->where('student_id', $id);
        $gradebooks->map->delete();

        $payments = Payments::get()->where('user_id', $id);
        $payments->map->delete();

        $payment_history = PaymentHistory::get()->where('user_id', $id);
        $payment_history->map->delete();


        $user = User::find($id);
        $user->delete();

        $students = User::get()->where('role_id', 7);
        return redirect()->back()->with('message','Student removed successfully.');
    }


    /**
     * Show the teacher permission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherPermission()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where('role_id', 3)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        return view('admin.permission.index', ['classes' => $classes, 'teachers' => $teachers]);
    }

    public function teacherPermissionList($value = "")
    {
        $data = explode('-', $value);
        $class_id = $data[0];
        $section_id = $data[1];
        $teachers = User::where('role_id', 3)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        return view('admin.permission.list', ['teachers' => $teachers, 'class_id' => $class_id, 'section_id' => $section_id]);
    }

    public function teacherPermissionUpdate(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $teacher_id = $data['teacher_id'];
        $column_name = $data['column_name'];
        $value = $data['value'];

         $check_row = TeacherPermission::where('class_id', $class_id)
                    ->where('section_id', $section_id)
                    ->where('teacher_id', $teacher_id)
                    ->where('school_id', auth()->user()->school_id)
                    ->get();

        if(count($check_row) > 0){

            TeacherPermission::where('class_id', $class_id)
                    ->where('section_id', $section_id)
                    ->where('teacher_id', $teacher_id)
                    ->where('school_id', auth()->user()->school_id)
                    ->update([
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'school_id' => auth()->user()->school_id,
                        'teacher_id' => $teacher_id,
                        $column_name => $data['value'],
                    ]);

            
        } else {
            TeacherPermission::create([
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => auth()->user()->school_id,
                'teacher_id' => $teacher_id,
                $column_name => 1,
            ]);
            
        }
        
    }


    /**
     * Show the offline_admission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineAdmissionForm($type = '')
    {   
        $data['parents'] = User::where(['role_id' => 6,'school_id' => 1])->get();
        $data['departments'] = Department::get()->where('school_id', auth()->user()->school_id);
        $data['classes'] = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.offline_admission.offline_admission', ['aria_expand' => $type, 'data' => $data]);
    }

    public function offlineAdmissionCreate(Request $request)
    {
        $package = Subscription::where('school_id', auth()->user()->school_id)->latest()->first();
        
        
        $student_limit = $package->studentLimit;
        
        $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();

        
        
        if ($student_limit == 'unlimited' || $student_limit > $student_count) {
        
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(!empty($data['photo'])){

            $imageName = time().'.'.$data['photo']->extension();

            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

            $photo  = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['eDefaultDateRange']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);

        $data['student_info'] = json_encode(array_filter($request->student_info));

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if(count($duplicate_user_check) == 0) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'password' => Hash::make($data['password']),
                'code' => student_code(),
                'role_id' => '7',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'student_info'     => $data['student_info'],
                'status' => 1,
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
                'stu_bioid'=>$data['bioid'],
            ]);
            
            AdmissionDetail::create([
                'user_id'          =>   $user->id,
                'class_id'         =>   $data['class_id'],
                'section_id'       =>   $data['section_id'],
                'school_id'        =>   auth()->user()->school_id,
                'session_id'       =>   $active_session,
                'mother_name'      =>   $data['mother_name'],
                'father_name'      =>   $data['father_name'],
                'nationality'      =>   $data['nationality'],
                'caste'            =>   $data['cast'],
                //'admission_date'   =>   $data['admission_date'],
                'admission_date'   =>   strtotime($data['admission_date']),
                'user_information' =>   $data['user_information'],
                'birthday'         =>   strtotime($data['eDefaultDateRange']),
                'religion'         =>   $data['religion'],
            ]);
            
           

            if(!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))){
                Mail::to($data['email'])->send(new NewUserEmail($data));
            }
            return redirect()->back()->with('message','Admission successfully done.');

        } else {

            return redirect()->back()->with('error','Sorry this email has been taken');
        }
     } else{
        return redirect()->back()->with('error','Your students limit out.Please upgrade to add more students');
     }
    }


/*
    public function offlineAdmissionBulkCreate(Request $request)
{
    $data = $request->all();

    $duplication_counter = 0;
    $successful_entries = 0;

    $class_id = $data['class_id'];
    $section_id = $data['section_id'];
    $department_id = $data['department_id'];

    $students_name = $data['name'];
    $students_email = $data['email'];
    $students_password = $data['password'];
    $students_gender = $data['gender'];
    $students_parent = $data['parent_id'];
    $students_phone = $data['phone'];
    $students_bioid = $data['stu_bioid'];

    $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

    foreach ($students_name as $key => $value) {
        $duplicate_user_check = User::where('email', $students_email[$key])->exists();
        $duplicate_bioid_check = Enrollment::where('stu_bioid', $students_bioid[$key])->exists();
        $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $students_bioid[$key])->exists();

        if ($duplicate_user_check) {
            $duplication_counter++;
        } elseif ($duplicate_bioid_check) {
            return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} already exists in Enrollments.");
        } elseif ($duplicate_hr_bioid_check) {
            return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} is already assigned as an Employee Bio ID.");
        } else {
            $info = [
                'gender' => $students_gender[$key],
                'blood_group' => '',
                'birthday' => '',
                'address' => '',
                'photo' => ''
            ];

            $data['user_information'] = json_encode($info);

            $user = User::create([
                'name' => $students_name[$key],
                'email' => $students_email[$key],
                'password' => Hash::make($students_password[$key]),
                'code' => student_code(),
                'role_id' => 7,
                'parent_id' => $students_parent[$key],
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
                'phone' => $students_phone[$key],
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => auth()->user()->school_id,
                'department_id' => $department_id,
                'session_id' => $active_session,
                'stu_bioid' => $students_bioid[$key],
            ]);

            $successful_entries++;
        }
    }

    if ($successful_entries > 0 && $duplication_counter == 0) {
        return redirect()->back()->with('message', 'Students added successfully.');
    } elseif ($successful_entries > 0 && $duplication_counter > 0) {
        return redirect()->back()->with('warning', 'Some of the emails have been taken.');
    } else {
        return redirect()->back()->with('error', 'No students were added. All emails were duplicates.');
    }
}
    */







public function offlineAdmissionBulkCreate(Request $request)
{
    Log::info('🚀 offlineAdmissionBulkCreate started');

    $data = $request->all();
    Log::info('📅 Received form data', $data);

    $duplication_counter = 0;
    $successful_entries = 0;

    $class_id = $data['class_id'];
    $section_id = $data['section_id'];
    $school_id = auth()->user()->school_id;
    $session_id = get_school_settings($school_id)->value('running_session');

    $students_name      = $data['name'];
    $students_email     = $data['email'];
    $students_password  = $data['password'];
    $students_phone     = $data['phone'];
    $students_bioid     = $data['stu_bioid'];
    $students_gender    = $data['gender'];
    $students_father    = $data['father_name'];
    $students_mother    = $data['mother_name'];
    $students_dob       = $data['date_of_birth'];
    $students_adm_date  = $data['admission_date'];
    $students_caste     = $data['caste'];
    $students_nationality = $data['nationality'];
    $students_blood_group = $data['blood_group'];
    $students_address   = $data['address'];
    $students_religion     = $data['religion'];
    foreach ($students_name as $key => $student_name) {
        Log::info("🔄 Processing student: {$student_name}");

        $slugified_name = preg_replace('/[^a-z0-9]/', '', strtolower($student_name));
        $email = !empty($students_email[$key]) ? $students_email[$key] : $slugified_name . '_' . $class_id . '@student.xyz';
        $password = !empty($students_password[$key]) ? $students_password[$key] : $slugified_name;

        Log::info("✉️ Email: {$email}, 🔐 Password: {$password}");

        $duplicate_user_check = User::where('email', $email)->exists();
        $duplicate_bioid_check = Enrollment::where('stu_bioid', $students_bioid[$key])->exists();
        $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $students_bioid[$key])->exists();

        if ($duplicate_user_check || $duplicate_bioid_check || $duplicate_hr_bioid_check) {
            Log::warning("⚠️ Duplicate detected: {$email} or Bio ID");
            $duplication_counter++;
            continue;
        }

        $birthday_ts = strtotime(str_replace('/', '-', $students_dob[$key]));
        $admission_ts = strtotime(str_replace('/', '-', $students_adm_date[$key]));

        $info = [
            'gender' => $students_gender[$key],
            'blood_group' => $students_blood_group[$key] ?? '',
            'birthday' => $birthday_ts,
            'phone'=> $students_phone[$key] ?? '',
            'address' => $students_address[$key] ?? '',
            'photo' => ''
        ];

        $user = User::create([
            'name' => $student_name,
            'email' => $email,
            'password' => Hash::make($password),
            'code' => student_code(),
            'role_id' => 7,
            'school_id' => $school_id,
            'user_information' => json_encode($info),
            'status' => 1,
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'school_id' => $school_id,
            'session_id' => $session_id,
            'stu_bioid' => $students_bioid[$key],
        ]);

        AdmissionDetail::create([
            'user_id' => $user->id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'school_id' => $school_id,
            'session_id' => $session_id,
            'mother_name' => $students_mother[$key],
            'father_name' => $students_father[$key],
            'nationality' => $students_nationality[$key],
            'caste' => $students_caste[$key],
            'admission_date' => $admission_ts,
            'birthday' => $birthday_ts,
            'user_information' => json_encode($info),
            'religion' => $students_religion[$key],
        ]);

        $successful_entries++;
    }

    Log::info("📊 Summary: {$successful_entries} students added, {$duplication_counter} duplicates");

    if ($successful_entries > 0 && $duplication_counter == 0) {
        return redirect()->back()->with('message', 'Students added successfully.');
    } elseif ($successful_entries > 0 && $duplication_counter > 0) {
        return redirect()->back()->with('warning', 'Some of the emails or Bio IDs have been taken.');
    } else {
        return redirect()->back()->with('error', 'No students were added. All entries were duplicates.');
    }
}



public function offlineAdmissionExcelCreate(Request $request)
{
    \Log::info('🚀 offlineAdmissionExcelCreate started');

    ini_set('max_execution_time', 300);
    set_time_limit(300);

    $class_id   = $request->input('class_id');
    $section_id = $request->input('section_id');
    $school_id  = auth()->user()->school_id;
    $session_id = get_school_settings($school_id)->value('running_session');
    $package    = Subscription::where('school_id', $school_id)->first();

    $student_limit = $package->studentLimit;
    $student_count = User::where(['role_id' => 7, 'school_id' => $school_id])->count();

    $file = $request->file('csv_file');
    if (!$file) return redirect()->back()->with('error', 'No file uploaded.');

    $filename = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path('assets/csv_file/'), $filename);
    $filepath = public_path('assets/csv_file/' . $filename);
    \Log::info("📁 File uploaded: {$filename}");

    $successful_entries = 0;
    $duplication_counter = 0;

    if (($handle = fopen($filepath, 'r')) !== FALSE) {
        $count = 0;

        while (($all_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($count++ === 0) continue;

            list($name, $bio_id, $phone, $blood_group, $gender, $dob_raw, $address,
                 $father_name, $mother_name, $admission_raw, $nationality, $caste) = $all_data;

            $dob_ts = strtotime(str_replace('/', '-', $dob_raw));
            $adm_ts = strtotime(str_replace('/', '-', $admission_raw));

            $slugified_name = preg_replace('/[^a-z0-9]/', '', strtolower($name));
            $email = $slugified_name . '_' . $class_id . '@student.xyz';
            $password = $slugified_name;

            if (User::where('email', $email)->exists() ||
                Enrollment::where('stu_bioid', $bio_id)->exists() ||
                Hr_user_list::where('emp_bioid', $bio_id)->exists()) {
                $duplication_counter++;
                continue;
            }

            $info = [
                'gender' => $gender,
                'blood_group' => $blood_group,
                'birthday' => $dob_ts,
                'phone' => $phone,
                'address' => $address,
                'photo' => ''
            ];

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'code' => student_code(),
                'role_id' => 7,
                'school_id' => $school_id,
                'user_information' => json_encode($info),
                'status' => 1,
                'phone' => $phone
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => $school_id,
                'session_id' => $session_id,
                'stu_bioid' => intval($bio_id),
            ]);

            AdmissionDetail::create([
                'user_id' => $user->id,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => $school_id,
                'session_id' => $session_id,
                'mother_name' => $mother_name,
                'father_name' => $father_name,
                'nationality' => $nationality,
                'caste' => $caste,
                'admission_date' => $adm_ts,
                'birthday' => $dob_ts,
                'user_information' => json_encode($info),
            ]);

            $successful_entries++;
            $student_count++;
        }

        fclose($handle);
    }

    \Log::info("📊 Import completed: {$successful_entries} added, {$duplication_counter} duplicates");

    if ($successful_entries > 0 && $duplication_counter == 0) {
        return redirect()->back()->with('message', 'Students added successfully.');
    } elseif ($successful_entries > 0) {
        return redirect()->back()->with('warning', 'Some students were skipped due to duplication.');
    } else {
        return redirect()->back()->with('error', 'No students were added.');
    }
}








/*

//Date:23-06-25 code



    public function offlineAdmissionBulkCreate(Request $request)
    {
        Log::info('🚀 offlineAdmissionBulkCreate started');
    
        $data = $request->all();
        Log::info('📥 Received form data', $data);
    
        $duplication_counter = 0;
        $successful_entries = 0;
    
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $department_id = $data['department_id'];
    
        $students_name     = $data['name'];
        $students_email    = $data['email'];
        $students_password = $data['password'];
        $students_gender   = $data['gender'];
        $students_parent   = $data['parent_id'];
        $students_phone    = $data['phone'];
        $students_bioid    = $data['stu_bioid'];
    
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
    
        foreach ($students_name as $key => $student_name) {
            Log::info("🔄 Processing student: {$student_name}");
    
            $slugified_name = preg_replace('/[^a-z0-9]/', '', strtolower($student_name));
            $email = !empty($students_email[$key]) ? $students_email[$key] : $slugified_name . '_' . $class_id . '@student.xyz';
            $password = !empty($students_password[$key]) ? $students_password[$key] : $slugified_name;
    
            Log::info("✉️ Email: {$email}, 🔐 Password: {$password}");
    
            $duplicate_user_check = User::where('email', $email)->exists();
            $duplicate_bioid_check = Enrollment::where('stu_bioid', $students_bioid[$key])->exists();
            $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $students_bioid[$key])->exists();
    
            if ($duplicate_user_check) {
                Log::warning("⚠️ Duplicate email detected: {$email}");
                $duplication_counter++;
            } elseif ($duplicate_bioid_check) {
                Log::error("❌ Duplicate Bio ID in Enrollments: {$students_bioid[$key]}");
                return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} already exists in Enrollments.");
            } elseif ($duplicate_hr_bioid_check) {
                Log::error("❌ Bio ID already used in HR: {$students_bioid[$key]}");
                return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} is already assigned as an Employee Bio ID.");
            } else {
                $info = [
                    'gender' => $students_gender[$key],
                    'blood_group' => '',
                    'birthday' => '',
                    'address' => '',
                    'photo' => ''
                ];
    
                $data['user_information'] = json_encode($info);
    
                $user = User::create([
                    'name' => $student_name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'code' => student_code(),
                    'role_id' => 7,
                    'parent_id' => $students_parent[$key],
                    'school_id' => auth()->user()->school_id,
                    'user_information' => $data['user_information'],
                    'status' => 1,
                    'phone' => $students_phone[$key],
                ]);
    
                Log::info("✅ User created: ID {$user->id}");
    
                Enrollment::create([
                    'user_id' => $user->id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'school_id' => auth()->user()->school_id,
                    'department_id' => $department_id,
                    'session_id' => $active_session,
                    'stu_bioid' => $students_bioid[$key],
                ]);
    
                Log::info("✅ Enrollment created for user ID {$user->id}");
    
                $successful_entries++;
            }
        }
    
        Log::info("📊 Summary: {$successful_entries} students added, {$duplication_counter} duplicates");
    
        if ($successful_entries > 0 && $duplication_counter == 0) {
            return redirect()->back()->with('message', 'Students added successfully.');
        } elseif ($successful_entries > 0 && $duplication_counter > 0) {
            return redirect()->back()->with('warning', 'Some of the emails have been taken.');
        } else {
            return redirect()->back()->with('error', 'No students were added. All emails were duplicates.');
        }
    }
    


    public function offlineAdmissionExcelCreate(Request $request)
    {
        Log::info('🚀 offlineAdmissionExcelCreate started');
    
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        $data = $request->all();
    
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $school_id = auth()->user()->school_id;
        $session_id = get_school_settings($school_id)->value('running_session');
        $package = Subscription::where('school_id', $school_id)->first();
    
        $student_limit = $package->studentLimit;
        $student_count = User::where(['role_id' => 7, 'school_id' => $school_id])->count();
    
        $file = $request->file('csv_file');
        if ($file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/csv_file/'), $filename);
            $filepath = public_path('assets/csv_file/' . $filename);
            Log::info("📁 File uploaded: {$filename}");
        }
    
        $successful_entries = 0;
        $duplication_counter = 0;
    
        if (($handle = fopen($filepath, 'r')) !== FALSE) {
            $count = 0;
    
            while (($all_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($count === 0) {
                    $count++;
                    continue; // Skip header
                }
    
                Log::info("🧾 Processing row #{$count}", $all_data);
    
                if ($student_limit !== 'unlimited' && $student_count >= $student_limit) {
                    Log::error("🚫 Student limit exceeded.");
                    return redirect()->back()->with('error', 'Your student limit is exceeded. Upgrade your package.');
                }
    
                $student_name   = $all_data[0];
                $bio_id         = $all_data[1];
                $phone          = $all_data[2];
                $blood_group    = $all_data[3];
                $gender         = $all_data[4];
                $birthday       = strtotime($all_data[5]);
                $address        = $all_data[6];
    
                $slugified_name = preg_replace('/[^a-z0-9]/', '', strtolower($student_name));
                $email = $slugified_name . '_' . $class_id . '@student.xyz';
                $password = $slugified_name;
    
                Log::info("✉️ Email: {$email}, 🔐 Password: {$password}");
    
                $duplicate_user_check = User::where('email', $email)->exists();
                $duplicate_bioid_check = Enrollment::where('stu_bioid', $bio_id)->exists();
                $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $bio_id)->exists();
    
                if ($duplicate_user_check) {
                    Log::warning("⚠️ Duplicate email: {$email}");
                    $duplication_counter++;
                } elseif ($duplicate_bioid_check) {
                    Log::error("❌ Duplicate Bio ID in Enrollments: {$bio_id}");
                    return redirect()->back()->with('error', "Student Bio ID {$bio_id} already exists in Enrollments.");
                } elseif ($duplicate_hr_bioid_check) {
                    Log::error("❌ Bio ID already used in HR: {$bio_id}");
                    return redirect()->back()->with('error', "Student Bio ID {$bio_id} is already assigned as an Employee Bio ID.");
                } else {
                    $info = [
                        'gender' => $gender,
                        'blood_group' => $blood_group,
                        'birthday' => $birthday,
                        'address' => $address,
                        'photo' => ''
                    ];
    
                    $user = User::create([
                        'name' => $student_name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'code' => student_code(),
                        'role_id' => 7,
                        'school_id' => $school_id,
                        'user_information' => json_encode($info),
                        'status' => 1,
                        'phone' => $phone,
                    ]);
    
                    Log::info("✅ Created user ID: {$user->id}");
    
                    Enrollment::create([
                        'user_id' => $user->id,
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'school_id' => $school_id,
                        'session_id' => $session_id,
                        'stu_bioid' => intval($bio_id),
                    ]);
    
                    Log::info("✅ Enrollment added for user ID {$user->id}");
    
                    $successful_entries++;
                    $student_count++;
                }
    
                $count++;
            }
    
            fclose($handle);
        }
    
        Log::info("📊 Import completed: {$successful_entries} students added, {$duplication_counter} duplicates");
    
        if ($successful_entries > 0 && $duplication_counter == 0) {
            return redirect()->back()->with('message', 'Students added successfully.');
        } elseif ($successful_entries > 0 && $duplication_counter > 0) {
            return redirect()->back()->with('warning', 'Some of the emails have been taken.');
        } else {
            return redirect()->back()->with('error', 'No students were added. All emails were duplicates.');
        }
    }
    
*/



/*
    public function offlineAdmissionBulkCreate(Request $request)
    {
        $data = $request->all();
    
        $duplication_counter = 0;
        $successful_entries = 0;
    
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $department_id = $data['department_id'];
    
        $students_name = $data['name'];
        $students_email = $data['email'];
        $students_password = $data['password'];
        $students_gender = $data['gender'];
        $students_parent = $data['parent_id'];
        $students_phone = $data['phone'];
        $students_bioid = $data['stu_bioid'];
    
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
    
        foreach ($students_name as $key => $value) {
            // Slugify the student name for generating email/password if missing
            $slugified_name = strtolower(preg_replace('/[^a-z0-9]/', '', $value));
            $email = !empty($students_email[$key]) ? $students_email[$key] : $slugified_name . '_' . $class_id . '@student.xyz';
            $password = !empty($students_password[$key]) ? $students_password[$key] : $slugified_name;
    
            $duplicate_user_check = User::where('email', $email)->exists();
            $duplicate_bioid_check = Enrollment::where('stu_bioid', $students_bioid[$key])->exists();
            $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $students_bioid[$key])->exists();
    
            if ($duplicate_user_check) {
                $duplication_counter++;
            } elseif ($duplicate_bioid_check) {
                return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} already exists in Enrollments.");
            } elseif ($duplicate_hr_bioid_check) {
                return redirect()->back()->with('error', "Student Bio ID {$students_bioid[$key]} is already assigned as an Employee Bio ID.");
            } else {
                $info = [
                    'gender' => $students_gender[$key],
                    'blood_group' => '',
                    'birthday' => '',
                    'address' => '',
                    'photo' => ''
                ];
    
                $data['user_information'] = json_encode($info);
    
                $user = User::create([
                    'name' => $value,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'code' => student_code(),
                    'role_id' => 7,
                    'parent_id' => $students_parent[$key],
                    'school_id' => auth()->user()->school_id,
                    'user_information' => $data['user_information'],
                    'status' => 1,
                    'phone' => $students_phone[$key],
                ]);
    
                Enrollment::create([
                    'user_id' => $user->id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'school_id' => auth()->user()->school_id,
                    'department_id' => $department_id,
                    'session_id' => $active_session,
                    'stu_bioid' => $students_bioid[$key],
                ]);
    
                $successful_entries++;
            }
        }
    
        if ($successful_entries > 0 && $duplication_counter == 0) {
            return redirect()->back()->with('message', 'Students added successfully.');
        } elseif ($successful_entries > 0 && $duplication_counter > 0) {
            return redirect()->back()->with('warning', 'Some of the emails have been taken.');
        } else {
            return redirect()->back()->with('error', 'No students were added. All emails were duplicates.');
        }
    }
    
      


public function offlineAdmissionExcelCreate(Request $request) 
{
    ini_set('max_execution_time', 300);
    set_time_limit(300);
    $data = $request->all();

    $class_id = $data['class_id'];
    $section_id = $data['section_id'];
    $school_id = auth()->user()->school_id;
    $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
    $package = Subscription::where('school_id', auth()->user()->school_id)->first();
    
    $student_limit = $package->studentLimit;
    $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();

    $file = $request->file('csv_file');
    if ($file) {
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('assets/csv_file/'), $filename);
        $filepath = public_path('assets/csv_file/' . $filename);
    }

    $successful_entries = 0;
    $duplication_counter = 0;

    if (($handle = fopen($filepath, 'r')) !== FALSE) {
        $count = 0;

        while (($all_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($student_limit == 'unlimited' || $student_limit > $student_count) {
                if ($count > 0) {
                    $duplicate_user_check = User::where('email', $all_data[1])->exists();
                    $duplicate_bioid_check = Enrollment::where('stu_bioid', $all_data[3])->exists();
                    $duplicate_hr_bioid_check = Hr_user_list::where('emp_bioid', $all_data[3])->exists();

                    if ($duplicate_user_check) {
                        $duplication_counter++;
                    } elseif ($duplicate_bioid_check) {
                        return redirect()->back()->with('error', "Student Bio ID {$all_data[3]} already exists in Enrollments.");
                    } elseif ($duplicate_hr_bioid_check) {
                        return redirect()->back()->with('error', "Student Bio ID {$all_data[3]} is already assigned as an Employee Bio ID.");
                    } else {
                        $info = [
                            'gender' => $all_data[5],
                            'blood_group' => '',
                            'birthday' => strtotime($all_data[6]),
                            'address' => '',
                            'photo' => ''
                        ];

                        $user = User::create([
                            'name' => $all_data[0],
                            'email' => $all_data[1],
                            'password' => Hash::make($all_data[2]),
                            'code' => student_code(),
                            'role_id' => 7,
                            'school_id' => $school_id,
                            'user_information' => json_encode($info),
                            'status' => 1,
                            'phone' => $all_data[4],  
                        ]);

                        Enrollment::create([
                            'user_id' => $user->id,
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'school_id' => $school_id,
                            'session_id' => $session_id,
                            'stu_bioid' => intval($all_data[3]),  
                        ]);

                        $successful_entries++;
                        $student_count++;  
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Your student limit is exceeded. Upgrade your package.');
            }
            $count++;
        }

        fclose($handle);
    }

    if ($successful_entries > 0 && $duplication_counter == 0) {
        return redirect()->back()->with('message', 'Students added successfully.');
    } elseif ($successful_entries > 0 && $duplication_counter > 0) {
        return redirect()->back()->with('warning', 'Some of the emails have been taken.');
    } else {
        return redirect()->back()->with('error', 'No students were added. All emails were duplicates.');
    }
}
*/




    /**
     * Show the exam category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function examCategoryList()
    {
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.exam_category.exam_category', ['exam_categories' => $exam_categories]);
    }

    public function createExamCategory()
    {
        return view('admin.exam_category.create');
    }

    public function examCategoryCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::create([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message','Exam category created successfully.');
    }

    public function editExamCategory($id='')
    {
        $exam_category = ExamCategory::find($id);
        return view('admin.exam_category.edit', ['exam_category' => $exam_category]);
    }

    public function examCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::where('id', $id)->update([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message','Exam category updated successfully.');
    }

    public function examCategoryDelete($id='')
    {
        $exam_category = ExamCategory::find($id);
        $exam_category->delete();
        return redirect()->back()->with('message','You have successfully delete exam category.');
    }


    /**
     * Show the exam list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineExamList()
    {
        $id = "all";
        $exams = Exam::get()->where('exam_type', 'offline')->where('school_id', auth()->user()->school_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function offlineExamExport($id="")
    {
        if($id != "all") {
            $exams = Exam::where([
               'exam_type' => 'offline',
               'class_id' => $id
            ])->get();
        } else {
            $exams = Exam::get()->where('exam_type', 'offline');
        }
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_export', ['exams' => $exams, 'classes' => $classes]);
    }

    public function classWiseOfflineExam($id)
    {
        $exams = Exam::where([
           'exam_type' => 'offline',
           'class_id' => $id
        ])->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function createOfflineExam()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.add_offline_exam', ['classes' => $classes,'exam_categories' => $exam_categories]);
    }

    public function classWiseSubject($id)
    {
        $subjects = Subject::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a subject'.'</option>';
        foreach ($subjects as $subject):
            $options .= '<option value="'.$subject->id.'">'.$subject->name.'</option>';
        endforeach;
        echo $options;
    }

    public function offlineExamCreate(Request $request)
    {

            // Retrieve request data
        $data = $request->input('class_room_id');
        $startingTime =  strtotime($request->starting_date.''.$request->starting_time);
        $endingTime = strtotime($request->ending_date.''.$request->ending_time);


           // Check if the room is occupied for the specified time range
        $occupiedExams = Exam::where('room_number', $data)
        ->where(function ($query) use ($startingTime, $endingTime) {
            $query->whereBetween('starting_time', [$startingTime, $endingTime])
            ->orWhereBetween('ending_time', [$startingTime, $endingTime])
            ->orWhere(function ($query) use ($startingTime, $endingTime) {
              $query->where('starting_time', '<=', $startingTime)
              ->where('ending_time', '>=', $endingTime);
          });
        })
        ->get();
                // Return response based on room availability
        if (count($occupiedExams) != 0) {
            return redirect()->back()->with(['warning' => 'The room is occupied for the specified time range'], 409);
        } else {
            $data = $request->all();
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
            $exam_category = ExamCategory::find($data['exam_category_id']);
            Exam::create([
                'name' => $exam_category->name,
                'exam_category_id' => $data['exam_category_id'],
                'exam_type' => 'offline',
                'room_number' => $data['class_room_id'],
                'starting_time' => strtotime($data['starting_date'].''.$data['starting_time']),
                'ending_time' => strtotime($data['ending_date'].''.$data['ending_time']),
                'total_marks' => $data['total_marks'],
                'status' => 'pending',
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

           return redirect()->back()->with(['message' => 'You have successfully create exam'], 200);
       }

   }

   public function editOfflineExam($id){
        $exam = Exam::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $subjects = Subject::get()->where('class_id', $exam->class_id);
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.edit_offline_exam', ['exam' => $exam, 'classes' => $classes, 'subjects' => $subjects, 'exam_categories' => $exam_categories]);
    }

    public function offlineExamUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $exam_category = ExamCategory::find($data['exam_category_id']);
        Exam::where('id', $id)->update([
            'name' => $exam_category->name,
            'exam_category_id' => $data['exam_category_id'],
            'exam_type' => 'offline',
            'room_number' => $data['class_room_id'],
            'starting_time' => strtotime($data['starting_date'].''.$data['starting_time']),
            'ending_time' => strtotime($data['ending_date'].''.$data['ending_time']),
            'total_marks' => $data['total_marks'],
            'status' => 'pending',
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully update exam.');
    }

    public function offlineExamDelete($id)
    {
        $exam = Exam::find($id);
        $exam->delete();
        $exams = Exam::get()->where('exam_type', 'offline');
        return redirect()->back()->with('message','You have successfully delete exam.');
    }

    /**
     * Show the grade daily attendance.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */



     



    public function dailyAttendance()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $attendance_of_students = array();
        $no_of_users = 0;
        
        return view('admin.attendance.daily_attendance', ['classes' => $classes, 'attendance_of_students' => $attendance_of_students, 'no_of_users' => $no_of_users]);
    }

   
    public function dailyAttendanceFilter(Request $request)
    {
        Log::info('dailyAttendanceFilter function started.');
        
        $data = $request->all();
        Log::info('Request data received:', $data);
        
        // Process the date range (ensure MySQL-compatible DATETIME format)
        // Use the same logic as in the HR version by constructing the date from day, month, and year.
        $date = '01 ' . $data['month'] . ' ' . $data['year'];
        $timestamp = strtotime($date);
        
        if ($timestamp === false) {
            Log::error('Invalid date format', ['date' => $date]);
            return back()->withErrors(['error' => 'Invalid date format.']);
        }
        
        $first_date = date('Y-m-01 00:00:00', $timestamp);  // e.g., "2025-02-01 00:00:00"
        $last_date  = date('Y-m-t 23:59:59', $timestamp);      // e.g., "2025-02-28 23:59:59"
        
        Log::info('Processed date range:', [
            'first_date' => $first_date,
            'last_date'  => $last_date
        ]);
        
        // Prepare page data – use the full DATETIME string for attendance_date
        $page_data = [
            'attendance_date' => $first_date,  // Store the DATETIME string directly
            'class_id'        => $data['class_id'],
            'section_id'      => $data['section_id'],
            'month'           => $data['month'],
            'year'            => $data['year']
        ];
        Log::info('Page data prepared:', $page_data);

        
        // Get active session
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        Log::info('Active session:', ['session_id' => $active_session]);
        
        
        // Fetch class name
        $class = Classes::find($data['class_id']);
        $class_name = $class ? $class->name : 'Unknown Class';
         
        // Fetch section name
        $section = Section::find($data['section_id']);
        $section_name = $section ? $section->name : 'Unknown Section';
        
        // Fetch attendance data using correct DATETIME filtering
        $attendance_of_students = DailyAttendances::where([
            'class_id'   => $data['class_id'],
            'section_id' => $data['section_id'],
            'school_id'  => auth()->user()->school_id,
            'session_id' => $active_session
        ])
        ->whereBetween('timestamp', [$first_date, $last_date])
        ->get()
        ->toArray();


        $studentAttendances = $attendance_of_students;

        
        Log::info('Attendance data fetched.', ['count' => count($attendance_of_students)]);
        
        // Fetch student details
        $students_details = Enrollment::where('class_id', $page_data['class_id'])
            ->where('section_id', $page_data['section_id'])
            ->get();
        
        Log::info('Student details fetched.', ['count' => count($students_details)]);
        
        // Get the number of unique students
        $no_of_users = DailyAttendances::where([
            'class_id'   => $data['class_id'],
            'section_id' => $data['section_id'],
            'school_id'  => auth()->user()->school_id,
            'session_id' => $active_session
        ])->distinct()->count('student_id');
        
        Log::info('Number of unique students fetched.', ['no_of_users' => $no_of_users]);
        
        // Fetch classes
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        Log::info('Classes fetched.', ['count' => count($classes)]);
        
        // Log final query for debugging
        Log::info('Final SQL Query:', [
            'query'    => DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->toSql(),
            'bindings' => DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->getBindings()
        ]);
        
        Log::info('Prepare to return view');
        
        
        return view('admin.attendance.attendance_list', [
            'page_data'              => $page_data,
            'class_name'             => $class_name,
            'loaddata'               => 1,
            'classes'                => $classes,
            'attendance_of_students' => $attendance_of_students,
            'students_details'       => $students_details,
            'section_name'           => $section_name,
            'no_of_users'            => $no_of_users,
            'studentAttendances'     => collect($attendance_of_students)->groupBy('name'), // Group by student name
            'attendanceDate'         => $page_data['attendance_date'] ?? now()->format('Y-m-d'), // Ensure attendance date is set
            'lastRecord'             => !empty($attendance_of_students) ? end($attendance_of_students) : null, // Get last record
            'className'              => $class_name,
            'sectionName'            => $section_name,
            'class_id'               => $data['class_id'], // Ensure this is passed
            'section_id'             => $data['section_id'] // Ensure this is passed
           
]);
        
}

public function dailyAttendanceFilter_csv(Request $request)
{
    \Log::info('dailyAttendanceFilter_csv function started.');
    \Log::info('CSV export request data: ', $request->all());

    $month      = $request->input('month');
    $year       = $request->input('year');
    $class_id   = $request->input('class_id');
    $section_id = $request->input('section_id');

    if (!$month || !$year || !$class_id || !$section_id) {
        return response()->json(['error' => 'Missing required filters.']);
    }

    $start_date = (new \DateTime("01-$month-$year"))->format('Y-m-d 00:00:00');
    $end_date   = (new \DateTime("last day of $month $year"))->format('Y-m-d 23:59:59');

    \Log::info('📅 Date range:', ['start_date' => $start_date, 'end_date' => $end_date]);

    // Fetch attendance records with intime/outtime
    $attendances = \DB::table('daily_attendances')
        ->join('users', 'daily_attendances.student_id', '=', 'users.id')
        ->join('enrollments', 'enrollments.user_id', '=', 'users.id')
        ->select(
            'users.name as student_name',
            'enrollments.stu_bioid',
            'daily_attendances.timestamp',
            'daily_attendances.stu_intime',
            'daily_attendances.stu_outtime',
            'daily_attendances.status'
        )
        ->whereBetween('daily_attendances.timestamp', [$start_date, $end_date])
        ->where('daily_attendances.class_id', $class_id)
        ->where('daily_attendances.section_id', $section_id)
        ->get();

    \Log::info('✅ Fetched attendance rows:', ['count' => $attendances->count()]);

    $class = \App\Models\Classes::find($class_id);
    $class_name = $class ? preg_replace('/\s+/', '_', strtolower($class->name)) : 'class';

    $filename = "student_attendance_report_{$class_name}_{$month}_{$year}.csv";
    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=$filename",
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];

    $columns = ['Student Name', 'Student BioID', 'Date', 'In Time', 'Out Time', 'Status'];

    $callback = function () use ($attendances, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($attendances as $row) {
            $statusText = $row->status == 1 ? 'Present' : 'Absent';

            $date = date('d-m-Y', strtotime($row->timestamp));
            $inTime = $row->stu_intime ? date('H:i:s', strtotime($row->stu_intime)) : '';
            $outTime = $row->stu_outtime ? date('H:i:s', strtotime($row->stu_outtime)) : '';

            fputcsv($file, [
                $row->student_name,
                $row->stu_bioid,
                $date,
                $inTime,
                $outTime,
                $statusText
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}







    
    public function takeAttendance()
    {
        Log::Info('takeAttendance Function Started');
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.attendance.take_attendance', ['classes' => $classes]);
    }

        
            public function studentListAttendance(Request $request)
    {
        Log::Info('studentListAttendance Function Started');
        $data = $request->all();
        
        $school_id = auth()->user()->school_id;
        $date = date('Y-m-d', strtotime($data['date'])); // then this
        $page_data['attendance_date'] = $data['date'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];    // Get attendance records for the date
        $attendance = \App\Models\DailyAttendances::whereDate('timestamp', $date)
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('school_id', $school_id)
            ->get()
            ->keyBy('student_id'); // so you can easily find attendance by student_id
    
        return view('admin.attendance.student', [
            'page_data' => $page_data,
            'attendance' => $attendance,
        ]);
    }
   
    /*
  
   public function studentListAttendance(Request $request)
   {
       Log::info('studentListAttendance Function Started');
       $data = $request->all();
   
       $school_id = auth()->user()->school_id;
       $date = date('Y-m-d', strtotime($data['date']));
       $page_data['attendance_date'] = $data['date'];
       $page_data['class_id'] = $data['class_id'];
       $page_data['section_id'] = $data['section_id'];
   
       $attendance = \App\Models\DailyAttendances::whereDate('timestamp', $date)
           ->where('class_id', $data['class_id'])
           ->where('section_id', $data['section_id'])
           ->where('school_id', $school_id)
           ->get()
           ->keyBy('student_id');
   
       // 🔥 Add logs here
       Log::info('Passing page_data to Blade', $page_data);
       Log::info('Attendance Data:', $attendance->toArray());
   
       return view('admin.attendance.student', [
           'page_data' => $page_data,
           'attendance' => $attendance,
       ]);
   }
   */



    public function attendanceTake(Request $request)
    {
        $att_data = $request->all();
        $students = $att_data['student_id'];
        $school_id = auth()->user()->school_id;
        $active_session = get_school_settings($school_id)->value('running_session');
    
        // Common timestamp: date only with 00:00:00 time
        $date_only = date('Y-m-d 00:00:00', strtotime($att_data['date']));
    
        $data['timestamp'] = $date_only;
        $data['class_id'] = $att_data['class_id'];
        $data['section_id'] = $att_data['section_id'];
        $data['school_id'] = $school_id;
        $data['session_id'] = $active_session;
    
        foreach ($students as $student) {
            $data['status'] = $att_data['status-' . $student];
            $data['student_id'] = $student;
    
            // Check if attendance already exists
            $existing = DailyAttendances::where('student_id', $student)
                ->where('school_id', $school_id)
                ->where('class_id', $data['class_id'])
                ->where('section_id', $data['section_id'])
                ->where('session_id', $active_session)
                ->where(function ($query) use ($date_only) {
                    $query->whereDate('stu_intime', $date_only)
                        ->orWhere(function ($q) use ($date_only) {
                            $q->whereNull('stu_intime')
                              ->whereDate('timestamp', $date_only);
                        });
                })
                ->first();
    
            if (!$existing) {
                // Set all times to date with 00:00:00
                $data['stu_intime'] = $date_only;
                $data['stu_outtime'] = $date_only;
                $data['timestamp'] = $date_only;
    
                DailyAttendances::create($data);
            }
        }
    
        return redirect()->back()->with('message', 'Student attendance updated successfully.');
    }



    

    
/* Correct code with Default Times Added for stu_intime and stu_outtime - Shital

    public function attendanceTake(Request $request)
    {
     //   Log::Info('attendanceTake Function started');
        $att_data = $request->all();

        $students = $att_data['student_id'];
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        
        //$data['timestamp'] = strtotime($att_data['date']);
        $data['timestamp'] = date('Y-m-d H:i:s', strtotime($att_data['date']));
        $data['class_id'] = $att_data['class_id'];
        $data['section_id'] = $att_data['section_id'];
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        $check_data = DailyAttendances::where(['timestamp' => $data['timestamp'], 'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'session_id' => $active_session, 'school_id' => auth()->user()->school_id])->get();
        foreach ($students as $key => $student) {
            $data['status'] = $att_data['status-' . $student];
            $data['student_id'] = $student;
        
            $existing = DailyAttendances::where('student_id', $student)
                ->where('school_id', auth()->user()->school_id)
                ->where('class_id', $data['class_id'])
                ->where('section_id', $data['section_id'])
                ->where('session_id', $data['session_id'])
                ->where(function ($query) use ($data) {
                    $query->whereDate('stu_intime', date('Y-m-d', strtotime($data['timestamp'])))
                        ->orWhere(function ($q) use ($data) {
                            $q->whereNull('stu_intime')
                              ->whereDate('timestamp', date('Y-m-d', strtotime($data['timestamp'])));
                        });
                })
                ->first();
        
               if ($existing) {
        // Log::info("Attendance already exists for student {$student} on " . date('Y-m-d', strtotime($data['timestamp'])) . ". Skipping creation.");
    } else {
        // Set stu_intime, stu_outtime, and also match timestamp
        $data['stu_intime'] = date('Y-m-d 09:30:00', strtotime($att_data['date']));
        $data['stu_outtime'] = date('Y-m-d 17:30:00', strtotime($att_data['date']));
        $data['timestamp'] = $data['stu_intime'];  // 👈 Ensure timestamp is same as stu_intime
    
        DailyAttendances::create($data);
        // Log::info("Attendance created for student {$student} on " . date('Y-m-d', strtotime($data['timestamp'])));
    }
            }
            
          //    \Log::info('Triggering attendance notification manually...');
       // \Artisan::call('attendance:notify-parents');
    
       // \Log::info('Attendance notification triggered.');
    
            
            // ✅ Always return success message after loop
            return redirect()->back()->with('message','Student attendance updated successfully.');
        }
    */
    


    /**
     * Show the routine.
     *
     * @return \Illuminate\Contracts\upport\Renderable
     */
    public function routine()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.routine.routine', ['classes' => $classes]);
    }

    public function routineList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.routine.routine_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addRoutine()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.add_routine', ['classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::create([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/routine/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully create a class routine.');
    }

    public function routineEditModal($id){
        $routine = Routine::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.edit_routine', ['routine' => $routine, 'classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::where('id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect()->back()->with('message','You have successfully update routine.');
    }

    public function routineDelete($id)
    {
        $routine = Routine::find($id);
        $routine->delete();
        return redirect()->back()->with('message','You have successfully delete routine.');
    }

    /**
     * Show the syllabus.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function syllabus()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.syllabus.syllabus', ['classes' => $classes]);
    }


    public function syllabusList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.syllabus.syllabus_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addSyllabus()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.add_syllabus', ['classes' => $classes]);
    }

    public function syllabusAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->move(public_path('assets/uploads/syllabus/'), $filename);

            $filepath = asset('assets/uploads/syllabus/'.$filename);
        }

        Syllabus::create([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/syllabus/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully create a syllabus.');
    }

    public function syllabusEditModal($id){
        $syllabus = Syllabus::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.edit_syllabus', ['syllabus' => $syllabus, 'classes' => $classes]);
    }

    public function syllabusUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->move(public_path('assets/uploads/syllabus/'), $filename);

            $filepath = asset('assets/uploads/syllabus/'.$filename);
        }

        Syllabus::where('id', $id)->update([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/syllabus/list?class_id='.$data['class_id'].'&section_id='.$data['section_id'])->with('message','You have successfully update a syllabus.');
    }

    public function syllabusDelete($id)
    {
        $syllabus = Syllabus::find($id);
        $syllabus->delete();
        return redirect()->back()->with('message','You have successfully delete syllabus.');
    }

    /**
     * Show the gradebook.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradebook(Request $request)
    {
        \Log::info('Gradebook method called.');

        $school_id = auth()->user()->school_id;
        \Log::info('Authenticated school_id:', ['school_id' => $school_id]);

        $classes = Classes::where('school_id', $school_id)->get();
        $exam_categories = ExamCategory::where('school_id', $school_id)->get();

        \Log::info('Fetched classes and exam categories.', [
            'classes_count' => $classes->count(),
            'exam_categories_count' => $exam_categories->count()
        ]);

        $active_session = get_school_settings($school_id)->value('running_session');
        \Log::info('Active session ID:', ['active_session' => $active_session]);

        if ($request->all()) {
            $data = $request->all();
            \Log::info('Request data received:', $data);

            $filter_list = Gradebook::where([
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'exam_category_id' => $data['exam_category_id'],
                'school_id' => $school_id,
                'session_id' => $active_session
            ])->get();

            \Log::info('Filtered gradebooks count:', ['count' => $filter_list->count()]);

            $class_id = $data['class_id'];
            $section_id = $data['section_id'];
            $exam_category_id = $data['exam_category_id'];

            $subjects = Subject::where([
                'class_id' => $class_id,
                'school_id' => $school_id
            ])->get();

            \Log::info('Fetched subjects count:', ['count' => $subjects->count()]);
        } else {
            \Log::info('No request data. Setting default empty values.');
            $filter_list = [];
            $class_id = '';
            $section_id = '';
            $exam_category_id = '';
            $subjects = collect(); // Better than empty string for collections
        }

        \Log::info('Returning gradebook view with data.', [
            'filter_list_count' => count($filter_list),
            'class_id' => $class_id,
            'section_id' => $section_id,
            'exam_category_id' => $exam_category_id
        ]);

        return view('admin.gradebook.gradebook', [
            'filter_list' => $filter_list,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'exam_category_id' => $exam_category_id,
            'classes' => $classes,
            'exam_categories' => $exam_categories,
            'subjects' => $subjects
        ]);
    }


    public function gradebookList(Request $request)
    {
        \Log::info('gradebookList method called.');

        $data = $request->all();
        \Log::info('Request data received:', $data);

        $school_id = auth()->user()->school_id;
        $active_session = get_school_settings($school_id)->value('running_session');

        \Log::info('School ID and active session:', [
            'school_id' => $school_id,
            'active_session' => $active_session
        ]);

        $exam_wise_student_list = Gradebook::where([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'exam_category_id' => $data['exam_category_id'],
            'school_id' => $school_id,
            'session_id' => $active_session
        ])->get();

        \Log::info('Exam-wise student list count:', ['count' => $exam_wise_student_list->count()]);

        echo view('admin.gradebook.list', [
            'exam_wise_student_list' => $exam_wise_student_list,
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'exam_category_id' => $data['exam_category_id'],
            'school_id' => $school_id,
            'session_id' => $active_session
        ]);
    }

    public function addMark()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $exam_categories = ExamCategory::get()->where('school_id', auth()->user()->school_id);
        return view('admin.gradebook.add_mark', ['classes' => $classes, 'exam_categories' => $exam_categories]);
    }

    public function markAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $subject_wise_mark_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'student_id' => $data['student_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

        $result = $subject_wise_mark_list->count();

        if($result > 0){

            return redirect()->back()->with('message','Mark added successfully.');

        } else {

            $mark = array($data['subject_id'] => $data['mark']);

            $marks = json_encode($mark);

            $data['marks'] = $marks;
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('Y-m-d'));

            Gradebook::create($data);

            return redirect()->back()->with('message','Mark added successfully.');
        }
    }
    
    
    
    
public function uploadMarks(Request $request)
{
    \Log::info('uploadMarks() called.', ['data' => $request->all()]);

    $request->validate([
        'csv_file' => 'required|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $path = $file->getRealPath();
    $data = array_map('str_getcsv', file($path));

    // Assuming first row is header
    unset($data[0]);

    foreach ($data as $row) {
        $studentName = trim($row[0]);
        $mark        = trim($row[1]);
        $comment     = trim($row[2]);

        $student = \App\Models\User::where('name', $studentName)->where('role_id', 7)->first();

        if ($student) {
            $gradebook = \App\Models\Gradebook::firstOrNew([
                'student_id'       => $student->id,
                'class_id'         => $request->class_id,
                'section_id'       => $request->section_id,
                'session_id'       => $request->session_id,
                'exam_category_id' => $request->exam_category_id,
            ]);

            $existingMarks = json_decode($gradebook->marks ?? '{}', true);
            $existingMarks[$request->subject_id] = $mark;

            $gradebook->marks   = json_encode($existingMarks);
            $gradebook->comment = $comment;
            $gradebook->save();
        }
    }

    return redirect()->back()->with('success', 'Marks uploaded successfully.');
}






    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function marks($value='')
    {
        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
        $page_data['sessions'] = Session::where('school_id', auth()->user()->school_id)->get();

        return view('admin.marks.index', $page_data);
    }
    
    
        
    public function marksFilter(Request $request)
    {
        Log::info('marksFilter() function started.', ['request_data' => $request->all()]);
    
        $data = $request->all();
    
        $marks_data['exam_category_id'] = $data['exam_category_id'];
        $marks_data['class_id'] = $data['class_id'];
        $marks_data['section_id'] = $data['section_id'];
        $marks_data['subject_id'] = $data['subject_id'];
        $marks_data['session_id'] = $data['session_id'];
    
        Log::info('Assigned request filters to $marks_data.', $marks_data);
    
        $class = Classes::find($data['class_id']);
        $section = Section::find($data['section_id']);
        $subject = Subject::find($data['subject_id']);
        $session = Session::find($data['session_id']);
    
        $marks_data['class_name'] = $class ? $class->name : 'N/A';
        $marks_data['section_name'] = $section ? $section->name : 'N/A';
        $marks_data['subject_name'] = $subject ? $subject->name : 'N/A';
        $marks_data['session_title'] = $session ? $session->session_title : 'N/A';
    
        Log::info('Resolved class/section/subject/session names.', [
            'class_name' => $marks_data['class_name'],
            'section_name' => $marks_data['section_name'],
            'subject_name' => $marks_data['subject_name'],
            'session_title' => $marks_data['session_title'],
        ]);
    
        $enroll_students = Enrollment::where('class_id', $marks_data['class_id'])
            ->where('section_id', $marks_data['section_id'])
            ->get();
    
        Log::info('Fetched enrolled students.', ['count' => $enroll_students->count()]);
    
        $marks_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $marks_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
    
        Log::info('Refetched exam categories and classes.', [
            'exam_categories_count' => $marks_data['exam_categories']->count(),
            'classes_count' => $marks_data['classes']->count()
        ]);
    
        $exam = Exam::where('exam_type', 'offline')
            ->where('class_id', $data['class_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('session_id', $data['session_id'])
            ->where('exam_category_id', $data['exam_category_id'])
            ->where('school_id', auth()->user()->school_id)
            ->first();
    
        if ($exam) {
            Log::info('Exam found.', ['exam_id' => $exam->id]);
    
            $response = view('admin.marks.marks_list', [
                'enroll_students' => $enroll_students,
                'marks_data' => $marks_data
            ])->render();
    
            Log::info('Rendered marks_list view successfully.');
            return response()->json(['status' => 'success', 'html' => $response]);
        } else {
            Log::warning('No exam found for the provided filters.', [
                'filters' => $data
            ]);
            return response()->json([
                'status' => 'error', 
                'message' => 'No records found for the specified filter. First create exam for the selected filter.'
            ]);
        }
    }
    
    
     
    public function marksPdf($section_id = "", $class_id ="", $session_id ="", $exam_category_id="", $subject_id ="")
    {
        $enroll_students = Enrollment::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->get();
    
        $class = Classes::find($class_id);
        $section = Section::find($section_id);
        $subject = Subject::find($subject_id);
        $session = Session::find($session_id);
    
        $marks_data = [
            'class_name' => $class ? $class->name : 'N/A',
            'section_name' => $section ? $section->name : 'N/A',
            'subject_name' => $subject ? $subject->name : 'N/A',
            'session_title' => $session ? $session->session_title : 'N/A',
            'exam_category_id' => $exam_category_id,
            'subject_id' => $subject_id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'session_id' => $session_id,
        ];
    
        $data = [
            'enroll_students' => $enroll_students,
            'marks_data' => $marks_data
        ];
        // Generate dynamic filename
        $class_name_safe = str_replace(' ', '_', $marks_data['class_name']);
        $subject_name_safe = str_replace(' ', '_', $marks_data['subject_name']);
        $filename = 'Marklist_' . $class_name_safe . '_' . $subject_name_safe . '.pdf';
    
    
        $pdf = PDF::loadView('admin.marks.markPdf', $data);
    
        
        return $pdf->download($filename);
    }

    
    
    
    
/*
    public function marksFilter(Request $request)
    {
        $data = $request->all();

        $page_data['exam_category_id'] = $data['exam_category_id'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['subject_id'] = $data['subject_id'];
        $page_data['session_id'] = $data['session_id'];

        $page_data['class_name'] = Classes::find($data['class_id'])->name;
        $page_data['section_name'] = Section::find($data['section_id'])->name;
        $page_data['subject_name'] = Subject::find($data['subject_id'])->name;
        $page_data['session_title'] = Session::find($data['session_id'])->session_title;


        $enroll_students = Enrollment::where('class_id', $page_data['class_id'])
        ->where('section_id', $page_data['section_id'])
        ->get();

        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();

        $exam = Exam::where('exam_type', 'offline')
        ->where('class_id', $data['class_id'])
        ->where('subject_id', $data['subject_id'])
        ->where('session_id', $data['session_id'])
        ->where('exam_category_id', $data['exam_category_id'])
        ->where('school_id', auth()->user()->school_id)
        ->first();

        if ($exam) {
            $response = view('admin.marks.marks_list', ['enroll_students' => $enroll_students, 'page_data' => $page_data])->render();
            return response()->json(['status' => 'success', 'html' => $response]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No records found for the specified filter. First create exam for the selected filter.']);
        }
    }
    
    */
    
    
    /*
    public function marksPdf($section_id = "", $class_id ="", $session_id ="", $exam_category_id="", $subject_id ="")
    {
       
        $enroll_students = Enrollment::where('class_id', $class_id)
        ->where('section_id', $section_id)
        ->get();

        $data = [
            'enroll_students' => $enroll_students,
            'section_id' => $section_id,
            'class_id' => $class_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id,
            'subject_id' => $subject_id
        ];

        $pdf = PDF::loadView('admin.marks.markPdf', $data);

        return $pdf->download('webappfix.pdf');

        // return $pdf->stream('webappfix.pdf');
    }*/
    
       
       
       
       
       
    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradeList()
    {
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return view('admin.grade.grade_list', ['grades' => $grades]);
    }

    public function createGrade()
    {
        return view('admin.grade.add_grade');
    }

    public function gradeCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_grade_check = Grade::get()->where('name', $data['grade'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_grade_check) == 0) {
            Grade::create([
                'name' => $data['grade'],
                'grade_point' => $data['grade_point'],
                'mark_from' => $data['mark_from'],
                'mark_upto' => $data['mark_upto'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new grade.');

        } else {
            return back()
            ->with('error','Sorry this grade already exists');
        }
    }

    public function editGrade($id)
    {
        $grade = Grade::find($id);
        return view('admin.grade.edit_grade', ['grade' => $grade]);
    }

    public function gradeUpdate(Request $request, $id)
    {
        $data = $request->all();
        Grade::where('id', $id)->update([
            'name' => $data['grade'],
            'grade_point' => $data['grade_point'],
            'mark_from' => $data['mark_from'],
            'mark_upto' => $data['mark_upto'],
            'school_id' => auth()->user()->school_id,
        ]);
        
        return redirect()->back()->with('message','You have successfully update grade.');
    }

    public function gradeDelete($id)
    {
        $grade = Grade::find($id);
        $grade->delete();
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message','You have successfully delete grade.');
    }


    /*
    public function generateReportCard(Request $request, $student_id, $exam_category_id)
    {    
        // Fetch student details
        $student = User::findOrFail($student_id);
        $school = School::where('id', $student->school_id)->first();
        
        // Fetch the current session title safely
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $session = $session_id ? \App\Models\Session::find($session_id) : null;
        $session_title = $session ? $session->session_title : 'Unknown Session';
    
        \Log::info('Session Title:', ['session_id' => $session_id, 'session_title' => $session_title]);
    
        // Fetch gradebook entry
        $gradebook = Gradebook::where([
            'student_id' => $student_id,
            'school_id' => $student->school_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id
        ])->first();
    
        if (!$gradebook) {
            return back()->with('error', 'No marks found for this student.');
        }
    
        // Fetch marks
        $marks = json_decode($gradebook->marks, true) ?? [];
        $total_marks_obtained = array_sum($marks);
    
        // Fetch total marks per subject from the Exam table
        $subject_total_marks = [];
        $max_total_marks = 0;
        foreach ($marks as $subject_id => $mark) {
            $exam = Exam::where([
                'subject_id' => $subject_id,
                'exam_category_id' => $exam_category_id,
                'class_id' => $gradebook->class_id,
                'school_id' => $student->school_id,
                'session_id' => $session_id
            ])->first();
    
            $subject_mark = $exam ? $exam->total_marks : 100; // Default 100
            $subject_total_marks[$subject_id] = $subject_mark;
            $max_total_marks += $subject_mark;
        }
    
        // Calculate percentage based on total max marks
        $percentage = $max_total_marks > 0 ? ($total_marks_obtained / $max_total_marks) * 100 : 0;
    
        // Fetch exam category name
        $exam_category = ExamCategory::find($exam_category_id);
        $exam_name = $exam_category ? $exam_category->name : 'Unknown Exam';
    
        \Log::info('Exam name fetched:', ['exam_name' => $exam_name]);
    
        // Class and section
        $class = Classes::find($gradebook->class_id ?? null);
        $section = Section::find($gradebook->section_id ?? null);
        $class_name = $class ? $class->name : 'Unknown Class';
        $section_name = $section ? $section->name : 'Unknown Section';
    
        // School logo fix for PDF
        $school_logo = null;
        if ($school && $school->school_logo) {
            $logo_path = public_path('assets/uploads/school_logo/' . $school->school_logo);
            if (file_exists($logo_path)) {
                $school_logo = str_replace('\\', '/', $logo_path);
            }
        }
    
        // Grade calculation
        $grade = $this->calculateGrade($percentage);
    
        // Prepare data for Blade view
        $data = [
            'school_name' => $school->title ?? 'Unknown School',
            'school_logo' => $school_logo,
            'session_title' => $session_title,
            'student_name' => $student->name,
            'class_name' => $class_name,
            'section_name' => $section_name,
            'exam_name' => $exam_name,
            'marks' => $marks,
            'subject_total_marks' => $subject_total_marks, // ✅ Per subject total marks
            'total_marks_obtained' => $total_marks_obtained,
            'total_marks' => $max_total_marks,             // ✅ Total max marks across subjects
            'percentage' => round($percentage, 2),
            'grade' => $grade,
            'current_date' => date('d-m-Y'),
        ];
    
        \Log::info('Data sent to Blade:', $data);
    
        $pdf = Pdf::loadView('admin.gradebook.report_card', $data);
        return $pdf->download('report_card_' . str_replace(' ', '_', $student->name) . '.pdf');
    }
    
    */
    

    public function generateReportCard(Request $request, $student_id, $exam_category_id)
    {
        // Step 1: Get student & enrollment
        $student = User::findOrFail($student_id);
        $enrollment = Enrollment::where('user_id', $student_id)->first();
        $school = School::find($student->school_id);
    
        // Step 2: Get session details
        $school_id = $school->id;
        $session_id = get_school_settings($school_id)->value('running_session');
        $session = Session::find($session_id);
        $session_title = $session ? $session->session_title : 'Unknown Session';
    
        // Step 3: Get exam info
        $exam_category = ExamCategory::find($exam_category_id);
        $exam_name = $exam_category ? $exam_category->name : 'Unknown Exam';
    
        // Step 4: Get marks
        $gradebook = Gradebook::where([
            'student_id' => $student_id,
            'school_id' => $school_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id,
        ])->first();
    
        if (!$gradebook) {
            return back()->with('error', 'No marks found for this student.');
        }
    
        $marks = json_decode($gradebook->marks, true) ?? [];
        $total_marks_obtained = 0;
        $subject_total_marks = [];
        $max_total_marks = 0;
    
        foreach ($marks as $subject_id => $mark) {
            $exam = Exam::where([
                'subject_id' => $subject_id,
                'exam_category_id' => $exam_category_id,
                'class_id' => $gradebook->class_id,
                'school_id' => $school_id,
                'session_id' => $session_id
            ])->first();
    
            $subject_mark = $exam ? (int) $exam->total_marks : 100;
            $subject_total_marks[$subject_id] = $subject_mark;
    
            $total_marks_obtained += (int) $mark;
            $max_total_marks += $subject_mark;
        }
    
        $percentage = $max_total_marks > 0 ? round(($total_marks_obtained / $max_total_marks) * 100, 2) : 0;
        $grade = $this->calculateGrade($percentage); // Make sure this method exists
    
        // Step 5: Class and section (fallback from enrollment if needed)
        $class_id = $gradebook->class_id ?? $enrollment->class_id ?? null;
        $section_id = $gradebook->section_id ?? $enrollment->section_id ?? null;
    
        $class = Classes::find($class_id);
        $section = Section::find($section_id);
    
        \Log::info('Class & Section check', [
            'class_id' => $class_id,
            'class_found' => $class ? $class->name : 'Not found',
            'section_id' => $section_id,
            'section_found' => $section ? $section->name : 'Not found',
        ]);
    
        $class_name = $class ? $class->name : 'N/A';
        $section_name = $section ? $section->name : 'N/A';
    
        // Step 6: Prepare school logo for PDF
        $school_logo = null;
        if ($school && $school->school_logo) {
            $logo_path = public_path('assets/uploads/school_logo/' . $school->school_logo);
            if (file_exists($logo_path)) {
                $school_logo = str_replace('\\', '/', $logo_path); // for dompdf compatibility
            }
        }
    
        // Step 7: Send to PDF view
        $data = [
            'school_name' => $school->title ?? 'Unknown School',
            'school_logo' => $school_logo,
            'session_title' => $session_title,
            'student_name' => $student->name,
            'class_name' => $class_name,
            'section_name' => $section_name,
            'exam_name' => $exam_name,
            'marks' => $marks,
            'subject_total_marks' => $subject_total_marks,
            'total_marks_obtained' => $total_marks_obtained,
            'total_marks' => $max_total_marks,
            'percentage' => $percentage,
            'grade' => $grade,
            'current_date' => now()->format('d-m-Y'),
            'gradebook' => $gradebook,
        ];
    
        \Log::info('Report card data sent to view', $data);
    
        $pdf = PDF::loadView('admin.gradebook.report_card', $data);
        return $pdf->download('report_card_' . str_replace(' ', '_', $student->name) . '.pdf');
    }



    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) {
            return 'A+';
        } elseif ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B';
        } elseif ($percentage >= 60) {
            return 'C';
        } elseif ($percentage >= 50) {
            return 'D';
        } elseif ($percentage >= 40) {
            return 'E';
        } else {
            return 'F';
        }
    }








    /**
     * Show the promotion list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function promotionFilter()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.promotion.promotion', ['sessions' => $sessions, 'classes' => $classes]);
    }

    public function promotionList(Request $request)
    {
        $data = $request->all();
        $promotion_list = Enrollment::where(['session_id' => $data['session_id_from'], 'class_id' => $data['class_id_from'], 'section_id' => $data['section_id_from']])->get();
        echo view('admin.promotion.promotion_list', ['promotion_list' => $promotion_list, 'class_id_to' => $data['class_id_to'], 'section_id_to' => $data['section_id_to'], 'session_id_to' => $data['session_id_to'], 'class_id_from' => $data['class_id_from'], 'section_id_from' => $data['section_id_from']]);
    }

    public function promote($promotion_data = '')
    {
        $promotion_data = explode('-', $promotion_data);
        $enroll_id = $promotion_data[0];
        $class_id = $promotion_data[1];
        $section_id = $promotion_data[2];
        $session_id = $promotion_data[3];

        $enroll = Enrollment::find($enroll_id);

        Enrollment::where('id', $enroll_id)->update([
            'class_id' => $class_id,
            'section_id' => $section_id,
            'session_id' => $session_id,
        ]);

        return true;
    }

    public function classWiseSections($id)
    {
        $sections = Section::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a section'.'</option>';
        foreach ($sections as $section):
            $options .= '<option value="'.$section->id.'">'.$section->name.'</option>';
        endforeach;
        echo $options;
    }

    /**
     * Show the subject list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function subjectList(Request $request)
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if(count($request->all()) > 0 && $request->class_id != ''){

            $data = $request->all();
            $class_id = $data['class_id'] ?? '';
            $subjects = Subject::where('class_id', $class_id)->paginate(10);

        } else {
            $subjects = Subject::where('school_id', auth()->user()->school_id)->paginate(10);

            $class_id = '';
        }

        return view('admin.subject.subject_list', compact('subjects', 'classes', 'class_id'));
    }

    public function createSubject()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.add_subject', ['classes' => $classes]);
    }

    public function subjectCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Subject::create([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);
        
        return redirect('/admin/subject?class_id='.$data['class_id'])->with('message','You have successfully create subject.');
    }

    public function editSubject($id)
    {
        $subject = Subject::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.edit_subject', ['subject' => $subject, 'classes' => $classes]);
    }

    public function subjectUpdate(Request $request, $id)
    {
        $data = $request->all();
        Subject::where('id', $id)->update([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
        ]);
        
        return redirect('/admin/subject?class_id='.$data['class_id'])->with('message','You have successfully update subject.');
    }

    public function subjectDelete($id)
    {
        $subject = Subject::find($id);
        $subject->delete();
        $subjects = Subject::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message','You have successfully delete subject.');
    }

    /**
     * Show the department list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function departmentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $departments = Department::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $departments = Department::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.department.department_list', compact('departments', 'search'));
    }

    public function createDepartment()
    {
        return view('admin.department.add_department');
    }

    public function departmentCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_department_check) == 0) {
            Department::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new department.');

        } else {
            return back()
            ->with('error','Sorry this department already exists');
        }
    }

    public function editDepartment($id)
    {
        $department = Department::find($id);
        return view('admin.department.edit_department', ['department' => $department]);
    }

    public function departmentUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_department_check) == 0) {
            Department::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update subject.');
        } else {
            return back()
            ->with('error','Sorry this department already exists');
        }
    }

    public function departmentDelete($id)
    {
        $department = Department::find($id);
        $department->delete();
        return redirect()->back()->with('message','You have successfully delete department.');
    }


    /**
     * Show the class room list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classRoomList()
    {
        $class_rooms = ClassRoom::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.class_room.class_room_list', compact('class_rooms'));
    }

    public function createClassRoom()
    {
        return view('admin.class_room.add_class_room');
    }

    public function classRoomCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if(count($duplicate_class_room_check) == 0) {
            ClassRoom::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new class room.');

        } else {
            return back()
            ->with('error','Sorry this class room already exists');
        }
    }

    public function editClassRoom($id)
    {
        $class_room = ClassRoom::find($id);
        return view('admin.class_room.edit_class_room', ['class_room' => $class_room]);
    }

    public function classRoomUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if(count($duplicate_class_room_check) == 0) {
            ClassRoom::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update class room.');
        } else {
            return back()
            ->with('error','Sorry this class room already exists');
        }
    }

    public function classRoomDelete($id)
    {
        $department = ClassRoom::find($id);
        $department->delete();
        return redirect()->back()->with('message','You have successfully delete class room.');
    }

    /**
     * Show the class list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $class_lists = Classes::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $class_lists = Classes::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.class.class_list', compact('class_lists', 'search'));
    }

    public function createClass()
    {
        return view('admin.class.add_class');
    }

    public function classCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_class_check) == 0) {
            $id = Classes::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ])->id;

            Section::create([
                'name' => 'A',
                'class_id' => $id,
            ]);

            return redirect()->back()->with('message','You have successfully create a new class.');

        } else {
            return back()
            ->with('error','Sorry this class already exists');
        }
    }

    public function editClass($id)
    {
        $class = Classes::find($id);
        return view('admin.class.edit_class', ['class' => $class]);
    }

    public function classUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if(count($duplicate_class_check) == 0) {
            Classes::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);
            
            return redirect()->back()->with('message','You have successfully update class.');
        } else {
            return back()
            ->with('error','Sorry this class already exists');
        }
    }

    public function editSection($id)
    {
        $sections = Section::get()->where('class_id', $id);
        return view('admin.class.sections', ['class_id' => $id, 'sections' => $sections]);
    }

    public function sectionUpdate(Request $request, $id)
    {
        $data = $request->all();

        $section_id = $data['section_id'];
        $section_name = $data['name'];

        foreach($section_id as $key => $value){
            if($value == 0){
                Section::create([
                    'name' => $section_name[$key],
                    'class_id' => $id,
                ]);
            }
            if($value != 0 && is_numeric($value)){
                Section::where(['id' => $value, 'class_id' => $id])->update([
                    'name' => $section_name[$key],
                ]);
            }

            $section_value = null;
            if (strpos($value, 'delete') == true) {
                $section_value = str_replace('delete', '', $value);

                $section = Section::find(['id' => $section_value, 'class_id' => $id]);
                $section->map->delete();
            }
        }

        return redirect()->back()->with('message','You have successfully update sections.');
    }

    public function classDelete($id)
    {
        $class = Classes::find($id);
        $class->delete();
        $sections = Section::get()->where('class_id', $id);
        $sections->map->delete();
        $subjects = Subject::get()->where('class_id', $id);
        $subjects->map->delete();
        return redirect()->back()->with('message','You have successfully delete class.');
    }

    /**
     * Show the student fee manager.
     *
   
      * @return \Illuminate\Contracts\Support\Renderable
     */

    public function studentFeeManagerList(Request $request)
    {
        $school_id = auth()->user()->school_id;
        $active_session = get_school_settings($school_id)->value('running_session');
        \Log::info("📥 StudentFeeManagerList called by user ID: " . auth()->id());
     
        if (count($request->all()) > 0) {
            \Log::info("🔍 Request parameters:", $request->all());
     
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to = strtotime($date[1] . ' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = $data['status'];
     
            \Log::info("🗓️ Date range: From " . date('d-M-Y', $date_from) . " To " . date('d-M-Y', $date_to));
            \Log::info("🏷️ Class Filter: $selected_class | Status Filter: $selected_status");
     
            $query = StudentFeeManager::where('timestamp', '>=', $date_from)
                ->where('timestamp', '<=', $date_to)
                ->where('school_id', $school_id)
                ->where('session_id', $active_session);
     
            if ($selected_class != "all") {
                $query->where('class_id', $selected_class);
                \Log::info("✅ Class ID filter applied: $selected_class");
            }
     
            if ($selected_status != "all") {
                if ($selected_status == "partial") {
                    $query->whereColumn('paid_amount', '<', 'total_amount')
                          ->where('paid_amount', '>', 0);
                    \Log::info("✅ Partially Paid filter applied: paid_amount < total_amount AND > 0");
                } elseif ($selected_status == "paid") {
                    $query->whereColumn('paid_amount', '=', 'total_amount');
                    \Log::info("✅ Paid filter applied: paid_amount == total_amount");
                } elseif ($selected_status == "unpaid") {
                    $query->where(function ($q) {
                        $q->whereNull('paid_amount')->orWhere('paid_amount', '=', 0);
                    });
                    \Log::info("✅ Unpaid filter applied: paid_amount = 0 or NULL");
                } else {
                    \Log::info("⚠️ Unknown status value: $selected_status");
                }
            }
     
            $invoices = $query->get();
            \Log::info("📦 Invoices retrieved: " . $invoices->count());
     
            $classes = Classes::where('school_id', $school_id)->get();
     
            return view('admin.student_fee_manager.student_fee_manager', [
                'classes' => $classes,
                'invoices' => $invoices,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'selected_class' => $selected_class,
                'selected_status' => $selected_status
            ]);
     
        } else {
            \Log::info("🆕 No filters applied, loading default current month data");
     
            $classes = Classes::where('school_id', $school_id)->get();
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')) . ' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')) . ' 23:59:59');
            $selected_class = "";
            $selected_status = "";
     
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)
                ->where('timestamp', '<=', $date_to)
                ->where('school_id', $school_id)
                ->where('session_id', $active_session)
                ->get();
     
            \Log::info("📦 Default invoices retrieved: " . $invoices->count());
     
            return view('admin.student_fee_manager.student_fee_manager', [
                'classes' => $classes,
                'invoices' => $invoices,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'selected_class' => $selected_class,
                'selected_status' => $selected_status
            ]);
        }
    }
     



  /*
     public function studentFeeManagerList(Request $request)
    {
       
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = $data['status'];

            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all"){
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
         }
    }
*/
    public function feeManagerExport($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all"){
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();



        $file = "student_fee-".date('d-m-Y', $date_from).'-'.date('d-m-Y', $date_to).'-'.$selected_class.'-'.$selected_status.".csv";

        $csv_content = get_phrase('Invoice No') . ', ' . get_phrase('Student') . ', ' . get_phrase('Class') . ', ' . get_phrase('Invoice Title') . ', ' . get_phrase('Total Amount') . ', ' . get_phrase('Created At') . ', ' . get_phrase('Paid Amount') . ', ' . get_phrase('Status');

        foreach ($invoices as $invoice) {
            $csv_content .= "\n";

            $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']);
            $invoice_no = sprintf('%08d', $invoice['id']);

            $csv_content .= $invoice_no . ', ' . $student_details['name'] . ', ' . $student_details['class_name'] . ', ' . $invoice['title'] . ', ' . currency($invoice['total_amount']) . ', ' . date('d-M-Y', $invoice['timestamp']) . ', ' . currency($invoice['paid_amount']) . ', ' . $invoice['status'];
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


    public function feeManagerExportPdfPrint($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all"){
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }


        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.student_fee_manager.pdf_print', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);


    }

    public function createFeeManager($value="")
    {

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if($value == 'single'){
            return view('admin.student_fee_manager.single', ['classes' => $classes]);
        } else if($value == 'mass'){
            return view('admin.student_fee_manager.mass', ['classes' => $classes]);
        }
    }


    public function feeManagerCreate(Request $request, $value = "")
    {
        \Log::info("🚀 Starting feeManagerCreate", ['mode' => $value]);
    
        $data = $request->all();
        \Log::info("📥 Request Data", $data);
    
        if ($value == 'single') {
            // Calculate totals
            $data['total_amount'] = $data['amount'] - ($data['discounted_price'] ?? 0);
            $data['due_amount'] = $data['total_amount'] - $data['paid_amount'];
    
            \Log::info("🧮 Calculated Totals", [
                'total_amount' => $data['total_amount'],
                'due_amount' => $data['due_amount']
            ]);
    
            if ($data['paid_amount'] > $data['total_amount']) {
                \Log::error("❌ Paid amount exceeds total amount.");
                return back()->with('error', 'Paid amount cannot be greater than total amount');
            }
    
            // Handle status based on due/paid
            if ($data['due_amount'] == 0) {
                $data['status'] = 'paid';
            } elseif ($data['paid_amount'] > 0) {
                $data['status'] = 'partially_paid';
            } else {
                $data['status'] = 'unpaid';
            }
    
            // Fetch Parent & Session Info
            $data['parent_id'] = User::where('id', $data['student_id'])->value('parent_id');
            $data['timestamp'] = strtotime(date('d-M-Y'));
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = get_school_settings($data['school_id'])->value('running_session');
    
            // Insert Invoice
            $invoice = StudentFeeManager::create($data);
            \Log::info("✅ Invoice created", ['invoice_id' => $invoice->id]);
    
            // Store first installment if applicable
            if ($data['paid_amount'] > 0) {
                FeeInstallment::create([
                    'invoice_id' => $invoice->id,
                    'student_id' => $data['student_id'],
                    'amount_paid' => $data['paid_amount'],
                    'paid_at' => now(),
                    'payment_method' => $data['payment_method'] ?? 'cash',
                ]);
                \Log::info("💰 First installment stored", ['amount_paid' => $data['paid_amount']]);
            }
    
            return redirect()->back()->with('message', 'Invoice created successfully. Due Amount: ' . $data['due_amount']);
        }
    
        \Log::warning("⚠️ Invalid mode for feeManagerCreate");
        return back()->with('error', 'Invalid invoice mode selected.');
    }
    

    /*
    public function feeManagerCreate(Request $request, $value = "")
{
    \Log::info("🚀 Starting feeManagerCreate", ['mode' => $value]);

    $data = $request->all();
    \Log::info("📥 Request Data", $data);

    if ($value == 'single') {
        // Calculate totals
        $data['total_amount'] = $data['amount'] - ($data['discounted_price'] ?? 0);
        $data['due_amount'] = $data['total_amount'] - $data['paid_amount'];

        \Log::info("🧮 Calculated Totals", [
            'total_amount' => $data['total_amount'],
            'due_amount' => $data['due_amount']
        ]);

        if ($data['paid_amount'] > $data['total_amount']) {
            \Log::error("❌ Paid amount exceeds total amount.");
            return back()->with('error', 'Paid amount cannot be greater than total amount');
        }

        $data['status'] = $data['status'] ?? (($data['due_amount'] == 0) ? 'paid' : 'unpaid');
        $data['parent_id'] = User::where('id', $data['student_id'])->value('parent_id');
        $data['timestamp'] = strtotime(date('d-M-Y'));
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = get_school_settings($data['school_id'])->value('running_session');

        // Insert Invoice
        $invoice = StudentFeeManager::create($data);
        \Log::info("✅ Invoice created", ['invoice_id' => $invoice->id]);

        // Store first installment if applicable
        if ($data['paid_amount'] > 0) {
            FeeInstallment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $data['student_id'],
                'amount_paid' => $data['paid_amount'],
                'paid_at' => now(),
                'payment_method' => $data['payment_method'] ?? 'cash',
            ]);
            \Log::info("💰 First installment stored", ['amount_paid' => $data['paid_amount']]);
        }

        return redirect()->back()->with('message', 'Invoice created successfully. Due Amount: ' . $data['due_amount']);
    }

    // MASS INVOICE
    else if ($value == 'mass') {
        $data['timestamp'] = strtotime(date('d-M-Y'));
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = get_school_settings($data['school_id'])->value('running_session');

        $enrolments = Enrollment::where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->get();

        \Log::info("📦 Processing mass invoicing", ['total_students' => count($enrolments)]);

        foreach ($enrolments as $enrolment) {
            $data['student_id'] = $enrolment['user_id'];
            $data['parent_id'] = User::where('id', $data['student_id'])->value('parent_id');

            $data['total_amount'] = $data['amount'] - ($data['discounted_price'] ?? 0);
            $data['due_amount'] = $data['total_amount'] - $data['paid_amount'];
            $data['status'] = $data['status'] ?? (($data['due_amount'] == 0) ? 'paid' : 'unpaid');

            // Insert Invoice
            $invoice = StudentFeeManager::create($data);
            \Log::info("✅ Mass Invoice created", [
                'invoice_id' => $invoice->id,
                'student_id' => $data['student_id']
            ]);

            // Insert first installment if paid
            if ($data['paid_amount'] > 0) {
                FeeInstallment::create([
                    'invoice_id' => $invoice->id,
                    'student_id' => $data['student_id'],
                    'amount_paid' => $data['paid_amount'],
                    'paid_at' => now(),
                    'payment_method' => $data['payment_method'] ?? 'cash',
                ]);
                \Log::info("💰 First installment (mass) stored", [
                    'invoice_id' => $invoice->id,
                    'amount_paid' => $data['paid_amount']
                ]);
            }
        }

        return sizeof($enrolments) > 0
            ? redirect()->back()->with('message', 'Invoices created successfully. Due Amounts updated.')
            : back()->with('error', 'No student found');
    }

    \Log::warning("⚠️ Invalid mode for feeManagerCreate");
    return back()->with('error', 'Invalid invoice mode selected.');
}

*/


public function classWiseStudents($id = '')
{
    $enrollments = Enrollment::where('class_id', $id)->get();
    $options = '<option value="">Select a student</option>';

    foreach ($enrollments as $enrollment) {
        $student = User::find($enrollment->user_id);

        // Skip if student doesn't exist or has missing required fields
        if (!$student || empty($student->id) || empty($student->name)) {
            continue;
        }

        $options .= '<option value="'.$student->id.'">'.$student->name.'</option>';
    }

    echo $options;
}




/*
    public function classWiseStudents($id='')
    {
        $enrollments = Enrollment::get()->where('class_id', $id);
        $options = '<option value="">'.'Select a student'.'</option>';
        foreach ($enrollments as $enrollment):
            $student = User::find($enrollment->user_id);
            $options .= '<option value="'.$student->id.'">'.$student->name.'</option>';
        endforeach;
        echo $options;
    }
    */

        public function classWiseStudentsInvoice($id = '')
    {
        $enrollments = Enrollment::where('section_id', $id)->get();
    
        $options = '<option value="">Select a student</option>';
    
        foreach ($enrollments as $enrollment) {
            $student = User::find($enrollment->user_id);
    
            if ($student) {
                $options .= '<option value="'.$student->id.'">'.$student->name.'</option>';
            }
        }
    
        echo $options;
    }
    
    
    public function editFeeManager($id='')
    {
        $invoice_details = StudentFeeManager::find($id);
        $enrollments = Enrollment::get()->where('class_id', $invoice_details->class_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.student_fee_manager.edit', ['invoice_details' => $invoice_details, 'classes' => $classes, 'enrollments' => $enrollments]);
    }

    

    public function feeManagerUpdate(Request $request, $id = '')
    {
        \Log::info("✅ FeeManagerUpdate started for Invoice ID: {$id}");
    
        $data = $request->all();
        \Log::info("📥 Request Data", $data);
    
        $invoice = StudentFeeManager::findOrFail($id);
        \Log::info("📄 Existing Invoice", $invoice->toArray());
    
        $total_amount = $data['amount'] - $data['discounted_price'];
        \Log::info("🧮 Total Amount after discount", ['total_amount' => $total_amount]);
    
        $new_installment = $data['new_installment_amount'] ?? 0;
    
        if ($new_installment < 0) {
            \Log::error("❌ Installment cannot be negative.");
            return back()->with('error', 'Installment cannot be negative.');
        }
    
        // Add new installment
        if ($new_installment > 0) {
            FeeInstallment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $data['student_id'],
                'amount_paid' => $new_installment,
                'paid_at' => now(),
                'payment_method' => $data['payment_method'] ?? 'cash',
            ]);
            \Log::info("💰 New installment added", [
                'invoice_id' => $invoice->id,
                'student_id' => $data['student_id'],
                'amount_paid' => $new_installment
            ]);
        }
    
        // Recalculate total installments
        $installments_total = FeeInstallment::where('invoice_id', $invoice->id)->sum('amount_paid');
        \Log::info("🧾 Total installments sum:", ['sum' => $installments_total]);
    
        $updated_paid_amount = $installments_total;
    
        if ($updated_paid_amount > $total_amount) {
            \Log::error("❌ Total paid exceeds total amount", [
                'total_paid' => $updated_paid_amount,
                'total_amount' => $total_amount
            ]);
            return back()->with('error', 'Total paid amount exceeds total fee.');
        }
    
        $due_amount = $total_amount - $updated_paid_amount;
    
        // ✅ Updated logic for status
        if ($due_amount == 0) {
            $status = 'paid';
        } elseif ($updated_paid_amount > 0 && $due_amount > 0) {
            $status = 'partially_paid';
        } else {
            $status = 'unpaid';
        }
    
        // Update the invoice
        $invoice->update([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'discounted_price' => $data['discounted_price'],
            'total_amount' => $total_amount,
            'class_id' => $data['class_id'],
            'student_id' => $data['student_id'],
            'paid_amount' => $updated_paid_amount,
            'due_amount' => $due_amount,
            'payment_method' => $data['payment_method'],
            'status' => $status,
            'timestamp' => now()->timestamp,
            'session_id' => get_school_settings(auth()->user()->school_id)->value('running_session'),
            'school_id' => auth()->user()->school_id,
        ]);
    
        \Log::info("✅ Invoice updated", [
            'invoice_id' => $invoice->id,
            'paid_amount' => $updated_paid_amount,
            'due_amount' => $due_amount,
            'status' => $status
        ]);
    
        return back()->with('message', 'Installment added successfully.');
    }
     
    public function studentFeeDelete($id)
    {
        $invoice = StudentFeeManager::find($id);
        $invoice->delete();
        return redirect()->back()->with('message','You have successfully delete invoice.');
    }

    /**
     * Show the expense expense list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        if(count($request->all()) > 0){
            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $expense_category_id = $data['expense_category_id'];

            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = ExpenseCategory::find($expense_category_id);
            if($expense_category_id != 'all'){
                $expenses = Expense::where('expense_category_id', $expense_category_id)
                                ->where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            } else {
                $expenses = Expense::where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            }

            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);

        } else {
            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = "";
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $expenses = Expense::where('date', '>=', $date_from)
                                ->where('date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();
            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    public function createExpense()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.create', ['expense_categories' => $expense_categories]);
    }

    public function expenseCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::create([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully create a new expense.');
    }

    public function editExpense($id)
    {
        $expense_details = Expense::find($id);
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.edit', ['expense_categories' => $expense_categories, 'expense_details' => $expense_details]);
    }

    public function expenseUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::where('id', $id)->update([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message','You have successfully update expense.');
    }

    public function expenseDelete($id)
    {
        $expense = Expense::find($id);
        $expense->delete();
        return redirect()->back()->with('message','You have successfully delete expense.');
    }


    /**
     * Show the expense category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseCategoryList()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.expense_category.expense_category_list', compact('expense_categories'));
    }

    public function createExpenseCategory()
    {
        return view('admin.expense_category.create');
    }

    public function expenseCategoryCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if(count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message','You have successfully create a new expense category.');

        } else {
            return back()
            ->with('error','Sorry this expense category already exists');
        }
    }

    public function editExpenseCategory($id)
    {
        $expense_category = ExpenseCategory::find($id);
        return view('admin.expense_category.edit', ['expense_category' => $expense_category]);
    }

    public function expenseCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if(count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message','You have successfully update expense category.');

        } else {
            return back()
            ->with('error','Sorry this expense category already exists');
        }
    }

    public function expenseCategoryDelete($id)
    {
        $expense_category = ExpenseCategory::find($id);
        $expense_category->delete();
        return redirect()->back()->with('message','You have successfully delete expense category.');
    }

    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $books = Book::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->orWhere(function ($query) use($search) {
                    $query->where('author', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id);
                })->paginate(10);

        } else {
            $books = Book::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.book.list', compact('books', 'search'));
    }

    public function createBook()
    {
        return view('admin.book.create');
    }

    public function bookCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if(count($duplicate_book_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('d-M-Y'));

            Book::create($data);

            return redirect()->back()->with('message','You have successfully create a book.');

        } else {
            return back()
            ->with('error','Sorry this book already exists');
        }
    }

    public function editBook($id="")
    {
        $book_details = Book::find($id);
        return view('admin.book.edit', ['book_details' => $book_details]);
    }

    public function bookUpdate(Request $request, $id='')
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if(count($duplicate_book_check) == 0) {
            Book::where('id', $id)->update([
                'name' => $data['name'],
                'author' => $data['author'],
                'copies' => $data['copies'],
                'timestamp' => strtotime(date('d-M-Y')),
            ]);
            
            return redirect()->back()->with('message','You have successfully update book.');
        } else {
            return back()
            ->with('error','Sorry this book already exists');
        }
    }

    public function bookDelete($id)
    {
        $book = Book::find($id);
        $book->delete();
        return redirect()->back()->with('message','You have successfully delete book.');
    }


    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookIssueList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if(count($request->all()) > 0) {

            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                                    ->where('issue_date', '<=', $date_to)
                                    ->where('school_id', auth()->user()->school_id)
                                    ->where('session_id', $active_session)
                                    ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        } else {
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                                ->where('issue_date', '<=', $date_to)
                                ->where('school_id', auth()->user()->school_id)
                                ->where('session_id', $active_session)
                                ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);

        }
    }

    public function createBookIssue()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.create', ['classes' => $classes, 'books' => $books]);
    }

    public function bookIssueCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 0;
        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        BookIssue::create($data);

        return redirect()->back()->with('message','You have successfully issued a book.');
    }

    public function editBookIssue($id="")
    {
        $book_issue_details = BookIssue::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.edit', ['book_issue_details' => $book_issue_details, 'classes' => $classes, 'books' => $books]);
    }

    public function bookIssueUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        unset($data['_token']);

        BookIssue::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function bookIssueReturn($id)
    {
        BookIssue::where('id', $id)->update([
            'status' => 1,
            'timestamp' => strtotime(date('d-M-Y')),
        ]);

        return redirect()->back()->with('message','Return successfully.');
    }

    public function bookIssueDelete($id)
    {
        $book_issue = BookIssue::find($id);
        $book_issue->delete();
        return redirect()->back()->with('message','You have successfully delete a issued book.');
    }


    /**
     * Show the noticeboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function noticeboardList()
    {
        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);

        $events = array();

        foreach($notices as $notice) {
            if($notice['end_date'] !=""){
                if($notice['start_date'] != $notice['end_date']){
                    $end_date = strtotime($notice['end_date']) + 24*60*60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice['end_date']));
                }
            }

            if($notice['end_date'] =="" && $notice['start_time'] =="" && $notice['end_time'] ==""){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            } else if($notice['start_time'] !="" && ($notice['end_date'] =="" && $notice['end_time'] =="")){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])).'T'.$notice['start_time']
                );
            } else if($notice['end_date'] !="" && ($notice['start_time'] =="" && $notice['end_time'] =="")){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])),
                    'end' => $end_date
                );
            } else if($notice['end_date'] !="" && $notice['start_time'] !="" && $notice['end_time'] !=""){
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])).'T'.$notice['start_time'],
                    'end' => date('Y-m-d', strtotime($notice['end_date'])).'T'.$notice['end_time']
                );
            } else {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            }
            array_push($events, $info);
        }

        $events = json_encode($events);

        return view('admin.noticeboard.noticeboard', ['events' => $events]);
    }

    public function createNoticeboard()
    {
        return view('admin.noticeboard.create');
    }

    public function noticeboardCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if(!empty($data['image'])){

            $imageName = time().'.'.$data['image']->extension();

            $data['image']->move(public_path('assets/uploads/noticeboard/'), $imageName);

            $data['image']  = $imageName;
        }

        Noticeboard::create($data);

        return redirect()->back()->with('message','You have successfully create a notice.');
    }

    public function editNoticeboard($id="")
    {
        $notice = Noticeboard::find($id);
        return view('admin.noticeboard.edit', ['notice' => $notice]);
    }

    public function noticeboardUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if(!empty($data['image'])){

            $imageName = time().'.'.$data['image']->extension();

            $data['image']->move(public_path('assets/uploads/noticeboard/'), $imageName);

            $data['image']  = $imageName;
        }

        unset($data['_token']);

        Noticeboard::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function noticeboardDelete($id='')
    {
        $notice = Noticeboard::find($id);
        $notice->delete();
        return redirect()->back()->with('message','You have successfully delete a notice.');
    }


    /**
     * Show the subscription.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function subscription(Request $request)
    {

        $if_pending_payment=PaymentHistory::where('user_id',auth()->user()->id)->where('status','pending')->get()->count();

        if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        } else{
            $date_from = strtotime('first day of january this year');
            $date_to = strtotime('last day of december this year');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        }

        $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'active' => '1']);
        if($subscription_details->get()->count() > 0){
            $package_details = Package::find($subscription_details->first()->package_id);
        } else {
            $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'status' => '0']);
            if($subscription_details->get()->count() > 0){
                $package_details = Package::find($subscription_details->first()->package_id);
            } else {
                $package_details ='';
            }
        }
        return view('admin.subscription.subscription', ['if_pending_payment'=>$if_pending_payment,'subscriptions' => $subscriptions, 'subscription_details' => $subscription_details, 'package_details' => $package_details, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function subscriptionPurchase()
    {   
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.purchase', ['packages' => $packages]);
        
    }

    public function upgreadeSubscription()
    {   
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.upgrade_subscription', ['packages' => $packages]);
        
    }

    /**
     * Show the event list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function eventList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $events = FrontendEvent::where(function ($query) use($search) {
                    $query->where('title', 'LIKE', "%{$search}%");
                })->paginate(10);

        } else {
            $events = FrontendEvent::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.events.events', compact('events', 'search'));
    }

    public function createEvent()
    {
        return view('admin.events.create_event');
    }

    public function eventCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        FrontendEvent::create($data);

        return redirect()->back()->with('message','You have successfully create a event.');
    }

    public function editEvent($id="")
    {
        $event = FrontendEvent::find($id);
        return view('admin.events.edit_event', ['event' => $event]);
    }

    public function eventUpdate(Request $request, $id="")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        unset($data['_token']);

        FrontendEvent::where('id', $id)->update($data);

        return redirect()->back()->with('message','Updated successfully.');
    }

    public function eventDelete($id)
    {
        $event = FrontendEvent::find($id);
        $event->delete();
        return redirect()->back()->with('message','You have successfully delete a event.');
    }

    // Complain List
    function complainList(){
        return view('admin.complain.complainList');
    }
    


/*
|--------------------------------------------------------------------------
| CERIFICATES - BONAFIED, TRANSFER CERTIFICATE
|--------------------------------------------------------------------------
*/
public function showCertificateUI()
{
    $classes = \App\Models\Classes::where('school_id', auth()->user()->school_id)->get();
    return view('admin.certificate.index', compact('classes'));
}


public function generateBonafideUI(Request $request)
{
    $request->validate(['student_id' => 'required|exists:users,id']);
    return view('admin.certificate.bonafide_result', ['student_id' => $request->student_id]);
}

public function viewBonafide($id)
{
    $data = $this->prepareBonafideData($id);
    $data['is_pdf'] = false;
    return view('admin.certificate.bonafide_pdf', $data);
}
public function downloadBonafide($id)
{
    $data = $this->prepareBonafideData($id);
    $data['is_pdf'] = true;
    $pdf = PDF::loadView('admin.certificate.bonafide_pdf', $data);
    $pdf->setPaper('A4', 'portrait');
    return $pdf->download('Bonafide_Certificate_' . str_replace(' ', '_', $data['student_name']) . '.pdf');
}

/*
public function viewBonafide($id)
{
    $data = $this->prepareBonafideData($id);
    return view('admin.certificate.bonafide_pdf', $data);
}

public function downloadBonafide($id)
{
    $data = $this->prepareBonafideData($id);
    $pdf = PDF::loadView('admin.certificate.bonafide_pdf', $data);
    return $pdf->download('Bonafide_Certificate_' . str_replace(' ', '_', $data['student_name']) . '.pdf');
}
*/
public function generateTCUI(Request $request)
{
    $request->validate(['student_id' => 'required|exists:users,id']);
    return view('admin.certificate.tc_result', ['student_id' => $request->student_id]);
}

public function viewTC($id)
{
    $data = $this->prepareBonafideData($id);
    $data['is_pdf'] = false;

    return view('admin.certificate.tc_pdf', $data);
}

public function downloadTC($id)
{
    $data = $this->prepareBonafideData($id);
    $data['is_pdf'] = true;

    $pdf = PDF::loadView('admin.certificate.tc_pdf', $data);
    $pdf->setPaper('A4', 'portrait');

    return $pdf->download('Transfer_Certificate_' . str_replace(' ', '_', $data['student_name']) . '.pdf');
}
/*
public function viewTC($id)
{
    $data = $this->prepareBonafideData($id); // reuse for now
    return view('admin.certificate.tc_pdf', $data);
}


public function downloadTC($id)
{
    $data = $this->prepareBonafideData($id);
    $pdf = PDF::loadView('admin.certificate.tc_pdf', $data);
    $pdf->setPaper('A4', 'portrait');
    return $pdf->download('Transfer_Certificate_' . str_replace(' ', '_', $data['student_name']) . '.pdf');
}
*/






public function prepareBonafideData($id)
{
    $student = User::findOrFail($id);
    $admission = AdmissionDetail::where('user_id', $id)->first();
    $enrollment = Enrollment::where('user_id', $id)->first();
    $school = School::find($student->school_id);
    $session_id = get_school_settings($student->school_id)->value('running_session');
    $session = Session::find($session_id);

    $info = json_decode($admission->user_information ?? '{}', true);
    $birthday = isset($info['birthday']) ? (new DateTime("@{$info['birthday']}"))->format('d-m-Y') : 'N/A';
    $phone = $info['phone'] ?? 'N/A';
    $address = $info['address'] ?? 'N/A';
    $blood_group = $info['blood_group'] ?? 'N/A';
    $gender = $info['gender'] ?? 'N/A';

    $class = Classes::find($admission->class_id ?? $enrollment->class_id);
    $section = Section::find($admission->section_id ?? $enrollment->section_id);

    $startDate = new DateTime($session->start_date ?? date('Y-01-01'));
    $endDate = new DateTime($session->end_date ?? date('Y-m-d'));
    $endDate->modify('+1 day');

    $interval = new \DateInterval('P1D');
    $period = new \DatePeriod($startDate, $interval, $endDate);

    $working_days = 0;
    foreach ($period as $date) {
        if ($date->format('w') != 0) $working_days++;
    }

    $present_days = DailyAttendances::where('student_id', $id)
        ->where('status', 1)
        ->whereBetween('timestamp', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->count();

    return [
        'school_name'      => $school->title ?? 'N/A',
        'school_address'   => $school->address ?? 'N/A',
        'school_phone'     => $school->phone ?? 'N/A',
        'school_email'     => $school->email ?? 'N/A',
        'school_logo'      => $school->school_logo
            ? public_path('assets/uploads/school_logo/' . $school->school_logo)
            : null,
        'school_id'        => $school->id ?? 'N/A',

        'student_name'     => $student->name,
        'gender'           => ucfirst($gender),
        'phone'            => $phone,
        'address'          => $address,
        'blood_group'      => $blood_group,
        'dob'              => $birthday,
        'dob_words'        => $birthday !== 'N/A'
            ? \App\Helpers\NumberToWordsHelper::convertDateToWords($birthday)
            : 'N/A',

        'class'            => $class->name ?? 'N/A',
        'section'          => $section->name ?? 'N/A',
        'father_name'      => $admission->father_name ?? 'N/A',
        'mother_name'      => $admission->mother_name ?? 'N/A',
        'nationality'      => $admission->nationality ?? 'N/A',
        'caste'            => $admission->caste ?? 'N/A',

        'stu_bioid'        => $enrollment->stu_bioid ?? 'N/A',
        'student_id'       => $enrollment->user_id ?? $id,
        'admission_no'     => $admission->id ?? 'N/A',
        'session'          => $session->session_title ?? 'N/A',
        'issue_date'       => (new DateTime())->format('d-m-Y'),

        'working_days'     => $working_days,
        'present_days'     => $present_days,
    ];
}















    public function schoolSettings()
    {
        $school_details = School::find(auth()->user()->school_id);
        return view('admin.settings.school_settings', ['school_details' => $school_details]);
    }

    public function schoolUpdate(Request $request)
    {

        $data = $request->all();
        
        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();
        if($request->school_logoo){
                    
            $old_image = $school_data->school_logo;
            
            $ext = $request->school_logoo->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->school_logoo->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->school_logo =$newFileName;
            $school_data->save();
         }   

        if($request->email_logo){
                    
            $old_image = $school_data->email_logo;
            
            $ext = $request->email_logo->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->email_logo->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->email_logo =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo1){
                    
            $old_image = $school_data->socialLogo1;
            
            $ext = $request->socialLogo1->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo1->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo1 =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo2){
                    
            $old_image = $school_data->socialLogo2;
            
            $ext = $request->socialLogo2->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo2->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo2 =$newFileName;
            $school_data->save();
         }   
         if($request->socialLogo3){
                    
            $old_image = $school_data->socialLogo3;
            
            $ext = $request->socialLogo3->getClientOriginalExtension();
            $newFileName = random(8).'.'.$ext;
            $request->socialLogo3->move(public_path().'/assets/uploads/school_logo',$newFileName); // This will save file in a folder.  
            $school_data->socialLogo3 =$newFileName;
            $school_data->save();
         }   

        School::where('id', auth()->user()->school_id)->update([
            'title' => $data['school_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'email_title' => $data['email_title'],
            'email_details' => $data['email_details'],
            'warning_text' => $data['warning_text'],
            'socialLink1' => $data['socialLink1'],
            'socialLink2' => $data['socialLink2'],
            'socialLink3' => $data['socialLink3'],
            
        ]);


        return redirect()->back()->with('message','School details updated successfully.');
    }

            
    
    
    public function studentFeeinvoice($id)
{
    \Log::info("📥 studentFeeinvoice() called with Invoice ID: $id");

    // Step 1: Fetch invoice details
    $invoice = StudentFeeManager::find($id);
    if (!$invoice) {
        \Log::error("❌ Invoice not found for ID: $id");
        abort(404, 'Invoice not found');
    }

    $invoice_details = $invoice->toArray();
    \Log::info("✅ Invoice details loaded", $invoice_details);

    $payment_method = $invoice_details['payment_method'] ?? 'Not Available';

    // Step 2: Fetch student details
    $student_id = $invoice_details['student_id'] ?? null;
    \Log::info("🔍 Student ID from invoice: $student_id");

    if (!$student_id) {
        \Log::warning("⚠️ Student ID missing in invoice #$id");
        $student_details = [];
    } else {
        $student_details_obj = (new CommonController)->get_student_details_by_id($student_id);
        $student_details = json_decode(json_encode($student_details_obj), true);

        
if (empty($student_details)) {
    \Log::warning("⚠️ Student details not found for student ID: $student_id");
} else {
    \Log::info("✅ Student details retrieved", $student_details);

    // 🔍 Log individual fields
    \Log::info("🧾 Student Name: " . ($student_details['name'] ?? 'N/A'));
    \Log::info("📘 Class: " . ($student_details['class_name'] ?? 'N/A') . " | Section: " . ($student_details['section_name'] ?? 'N/A'));
    \Log::info("👨‍👩‍👧 Parent: " . ($student_details['parent_name'] ?? 'N/A'));
    \Log::info("📞 Phone: " . ($student_details['phone'] ?? 'N/A'));
    \Log::info("🎂 DOB: " . (isset($student_details['birthday']) ? date('d-M-Y', $student_details['birthday']) : 'N/A'));
    \Log::info("🩸 Blood Group: " . ($student_details['blood_group'] ?? 'N/A'));
}
    }

    // Step 3: Fetch school details
    $school_id = $student_details['school_id'] ?? auth()->user()->school_id;
    \Log::info("🏫 School ID to use: $school_id");

    $school = School::where('id', $school_id)->first();

    if (!$school) {
        \Log::warning("⚠️ School not found for ID: $school_id");
    }

    $school_name = $school->title ?? 'Unknown School';
    $school_logo = $school->school_logo ? asset('assets/uploads/school_logo/' . $school->school_logo) : null;
    $school_address = $school->address ?? 'Address Not Available';
    $school_phone = $school->phone ?? 'Phone Not Available';
    $school_email = $school->email ?? 'Email Not Available';

    \Log::info("🏫 School Info: Name = $school_name, Phone = $school_phone");

    // Step 4: Fetch running session
    $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
    $session = $session_id ? \App\Models\Session::find($session_id) : null;
    $session_title = $session ? $session->session_title : 'Unknown Session';

    \Log::info("📘 Running session ID: $session_id | Title: $session_title");

    // Step 5: Fetch installments
    $installments = FeeInstallment::where('invoice_id', $invoice_details['id'])->orderBy('paid_at')->get();
    \Log::info("💰 Installments count: " . $installments->count());

    return view('admin.student_fee_manager.invoice', [
        'invoice_details' => $invoice_details,
        'student_details' => $student_details,
        'school_name' => $school_name,
        'school_logo' => $school_logo,
        'school_address' => $school_address,
        'school_phone' => $school_phone,
        'school_email' => $school_email,
        'session_title' => $session_title,
        'payment_method' => $payment_method,
        'installments' => $installments
    ]);
}
        
            

/*
    public function studentFeeinvoice($id)
    {
        $invoice_details = StudentFeeManager::find($id)->toArray();
        $payment_method = $invoice_details['payment_method'] ?? 'Not Available'; 
        //$student_details = (new CommonController)->get_student_details_by_id($invoice_details['student_id'])->toArray();
        $student_details_obj = (new CommonController)->get_student_details_by_id($invoice_details['student_id']);
        $student_details = json_decode(json_encode($student_details_obj), true);
    
        $school = School::where('id', $student_details['school_id'])->first();
        $school_name = $school->title ?? 'Unknown School';
        $school_logo = $school->school_logo ? asset('assets/uploads/school_logo/' . $school->school_logo) : null;
        $school_address = $school->address ?? 'Address Not Available';
        $school_phone = $school->phone ?? 'Phone Not Available';
        $school_email = $school->email ?? 'Email Not Available';
    
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $session = $session_id ? \App\Models\Session::find($session_id) : null;
        $session_title = $session ? $session->session_title : 'Unknown Session';
    
        // ✅ Fetch installment records
        $installments = FeeInstallment::where('invoice_id', $invoice_details['id'])->orderBy('paid_at')->get();
    
        return view('admin.student_fee_manager.invoice', [
            'invoice_details' => $invoice_details,
            'student_details' => $student_details,
            'school_name' => $school_name,
            'school_logo' => $school_logo,
            'school_address' => $school_address,
            'school_phone' => $school_phone,
            'school_email' => $school_email,
            'session_title' => $session_title,
            'payment_method' => $payment_method,
            'installments' => $installments // ✅ Send to Blade
        ]);
    }
  */
  
    public function offline_payment_pending(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $school_id = auth()->user()->school_id;
    
        Log::info("Admin accessing pending offline payments | School ID: $school_id | Session: $active_session");
    
        $classes = Classes::where('school_id', $school_id)->get();
    
        if (count($request->all()) > 0) {
            $data = $request->all();
            Log::info("Received filter data: ", $data);
    
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to = strtotime($date[1].' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = 'pending';
    
            Log::info("Filtering pending payments from ".date('Y-m-d H:i:s', $date_from)." to ".date('Y-m-d H:i:s', $date_to).", Class: $selected_class");
    
            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                    ->where('class_id', $selected_class)
                    ->where('status', $selected_status)
                    ->where('school_id', $school_id)
                    ->where('session_id', $active_session)
                    ->get();
                Log::info("Filter: Class & Status | Invoices found: " . $invoices->count());
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                    ->where('class_id', $selected_class)
                    ->where('school_id', $school_id)
                    ->where('session_id', $active_session)
                    ->get();
                Log::info("Filter: Class only | Invoices found: " . $invoices->count());
            } else if ($selected_status != "all") {
                $invoices = StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                    ->where('status', $selected_status)
                    ->where('school_id', $school_id)
                    ->where('session_id', $active_session)
                    ->get();
                Log::info("Filter: Status only | Invoices found: " . $invoices->count());
            } else {
                $invoices = StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                    ->where('school_id', $school_id)
                    ->where('session_id', $active_session)
                    ->get();
                Log::info("No filters applied | Invoices found: " . $invoices->count());
            }
    
            return view('admin.student_fee_manager.student_fee_manager_pending', compact('classes', 'invoices', 'date_from', 'date_to', 'selected_class', 'selected_status'));
        } else {
            $date_from = strtotime(date('Y-m-01 00:00:00'));
            $date_to = strtotime(date('Y-m-t 23:59:59'));
            $selected_class = "";
            $selected_status = "";
    
            $invoices = StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                ->where('status', 'pending')
                ->where('school_id', $school_id)
                ->where('session_id', $active_session)
                ->get();
    
            Log::info("No filters submitted. Showing current month pending payments | Invoices found: " . $invoices->count());
    
            return view('admin.student_fee_manager.student_fee_manager_pending', compact('classes', 'invoices', 'date_from', 'date_to', 'selected_class', 'selected_status'));
        }
    }


    


/*
    public function offline_payment_pending(Request $request )
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
         if(count($request->all()) > 0){
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0].' 00:00:00');
            $date_to  = strtotime($date[1].' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = 'pending';



            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all"){
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);

         } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y',strtotime('first day of this month')).' 00:00:00');
            $date_to = strtotime(date('d-m-Y',strtotime('last day of this month')).' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status','pending')->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
         }


    }
    */

    public function update_offline_payment($id, $status)
    {
        $invoice = StudentFeeManager::find($id);
        $student_id = $invoice->student_id;
        $school_id = auth()->user()->school_id;
        $student_email = User::find($student_id)->email;
        $parent_email = User::find($invoice->parent_id)->email ?? null;
    
        // Amount being paid in this installment
        $installment_amount = $invoice->due_amount;
    
        if ($status == 'approve') {
            // ✅ Create a new installment record
            \DB::table('fee_installments')->insert([
                'invoice_id' => $id,
                'student_id' => $student_id,
                'amount_paid' => $installment_amount,
                'payment_method' => 'offline',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            // ✅ Recalculate total paid so far
            $total_paid = \DB::table('fee_installments')
                ->where('invoice_id', $id)
                ->sum('amount_paid');
    
            // ✅ Determine status
            $status_to_set = $total_paid >= $invoice->total_amount ? 'paid' : 'unpaid';
    
            // ✅ Update main invoice
            $invoice->update([
                'paid_amount' => $total_paid,
                'due_amount' => $invoice->total_amount - $total_paid,
                'status' => $status_to_set,
                'payment_method' => 'offline',
                'updated_at' => now()
            ]);
    
            // ✅ Send Emails if SMTP is configured
            if (!empty(get_settings('smtp_user')) && get_settings('smtp_pass') && get_settings('smtp_host') && get_settings('smtp_port')) {
                Mail::to($student_email)->send(new StudentsEmail($invoice));
                if ($parent_email) {
                    Mail::to($parent_email)->send(new StudentsEmail($invoice));
                }
            }
    
            return redirect()->back()->with('message', 'Installment Recorded & Payment Updated');
        }
    
        elseif ($status == 'decline') {
            // Optional: mark as declined (can also delete latest installment if needed)
            return redirect()->back()->with('message', 'Payment Declined');
        }
    }
    
    public function paymentSettings()
    {

        $payment_gateways = PaymentMethods::where('school_id', auth()->user()->school_id)->get();

        $school_currency=School::where('id', auth()->user()->school_id)->first()->toArray();
        $currencies=Currency::all()->toArray();
        $paypal="";
        $paypal_keys="";
        $stripe="";
        $stripe_keys="";
        $razorpay="";
        $razorpay_keys="";
        $paytm="";
        $paytm_keys="";
        $flutterwave="";
        $flutterwave_keys="";
        $paystack = "";
        $paystack_keys = "";
        
        foreach ($payment_gateways as  $single_gateway) {
           
            if($single_gateway->name=="paypal")
            {
                
                $paypal=$single_gateway->toArray();
                $paypal_keys=json_decode($paypal['payment_keys']);
            }
            elseif($single_gateway->name=="stripe")
            {
                $stripe=$single_gateway->toArray();
                $stripe_keys=json_decode($stripe['payment_keys']);
            }
            elseif($single_gateway->name=="razorpay")
            {
                $razorpay=$single_gateway->toArray();
                $razorpay_keys=json_decode($razorpay['payment_keys']);
            }
            elseif($single_gateway->name=="paytm")
            {
                $paytm=$single_gateway->toArray();
                $paytm_keys=json_decode($paytm['payment_keys']);
            }
            elseif($single_gateway->name=="flutterwave")
            {
                $flutterwave=$single_gateway->toArray();
                $flutterwave_keys=json_decode($flutterwave['payment_keys']);
            } elseif ($single_gateway->name == "paystack") {
                $paystack = $single_gateway->toArray();
                $paystack_keys = json_decode($paystack['payment_keys']);
            }


        }

        return view('admin.payment_settings.key_settings', ['paytm' => $paytm, 'paytm_keys' => $paytm_keys, 'razorpay' => $razorpay, 'razorpay_keys' => $razorpay_keys, 'stripe' => $stripe, 'stripe_keys' => $stripe_keys, 'paypal' => $paypal, 'paypal_keys' => $paypal_keys, 'flutterwave' => $flutterwave, 'flutterwave_keys' => $flutterwave_keys, 'paystack' => $paystack, 'paystack_keys' => $paystack_keys, 'school_currency' => $school_currency, 'currencies' => $currencies]);
    }

    public function install_paystack()
    {
        $keys = array();
        $paystack = new PaymentMethods;
        $paystack['name'] = "paystack";
        $paystack['image'] = "paystack.png";
        $paystack['status'] = 1;
        $paystack['mode'] = "test";
        $keys['test_key'] = "pk_test_xxxxxxxxxxxxx";
        $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxxx";
        $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
        $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
        $paystack['payment_keys'] = json_encode($keys);
        $paystack['school_id'] = auth()->user()->school_id;
        $paystack->save();
    }


    public function paymentSettings_post(Request $request)
    {
        $data=$request->all();

        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();

        if($request->off_pay_ins_file || $request->off_pay_ins_text){

            if($request->off_pay_ins_file){
                        
                $old_image = $school_data->off_pay_ins_file;
                
                $ext = $request->off_pay_ins_file->getClientOriginalExtension();
                $newFileName = random(8).'.'.$ext;
                $request->off_pay_ins_file->move(public_path().'/assets/uploads/offline_payment/',$newFileName); // This will save file in a folder.  
                $school_data->off_pay_ins_file =$newFileName;
                $school_data->save();
            }   

         School::where('id', auth()->user()->school_id)->update([
            'off_pay_ins_text' => $data['off_pay_ins_text'],
            
        ]);

        return redirect()->back()->with('message','Offline payment instruction update.');

        }
        $method=$data['method'];
        $update_id=$data['update_id'];


        if($method=='currency')
        {
            $Currency = School::find($update_id);
            $Currency['school_currency']= $data['school_currency'];
            $Currency['currency_position']=$data['currency_position'];
            $Currency->save();

        }
        elseif($method=='paypal')
        {

            $keys=array();
            $paypal=PaymentMethods::find($update_id);
            $paypal['status']=$data['status'];
            $paypal['mode']=$data['mode'];
            $keys['test_client_id']=$data['test_client_id'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['live_client_id']=$data['live_client_id'];
            $keys['live_secret_key']=$data['live_secret_key'];
            $paypal['payment_keys']=json_encode($keys);
            $paypal['school_id']=auth()->user()->school_id;
            $paypal->save();


        }
        elseif($method=='stripe')
        {
            $keys=array();
            $stripe=PaymentMethods::find($update_id);
            $stripe['status']=$data['status'];
            $stripe['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['public_live_key']=$data['public_live_key'];
            $keys['secret_live_key']=$data['secret_live_key'];
            $stripe['payment_keys']=json_encode($keys);
            $stripe['school_id']=auth()->user()->school_id;
            $stripe->save();
        }
        elseif($method=='razorpay')
        {
            $keys=array();
            $razorpay=PaymentMethods::find($update_id);
            $razorpay['status']=$data['status'];
            $razorpay['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['live_key']=$data['live_key'];
            $keys['live_secret_key']=$data['live_secret_key'];
            $keys['theme_color']=$data['theme_color'];
            $razorpay['payment_keys']=json_encode($keys);
            $razorpay['school_id']=auth()->user()->school_id;
            $razorpay->save();


        }
        elseif($method=='paytm')
        {
            $keys=array();
            $paytm=PaymentMethods::find($update_id);
            $paytm['status']=$data['status'];
            $paytm['mode']=$data['mode'];
            $keys['test_merchant_id']=$data['test_merchant_id'];
            $keys['test_merchant_key']=$data['test_merchant_key'];
            $keys['live_merchant_id']=$data['live_merchant_id'];
            $keys['live_merchant_key']=$data['live_merchant_key'];
            $keys['environment']=$data['environment'];
            $keys['merchant_website']=$data['merchant_website'];
            $keys['channel']=$data['channel'];
            $keys['industry_type']=$data['industry_type'];
            $paytm['payment_keys']=json_encode($keys);
            $paytm['school_id']=auth()->user()->school_id;
            $paytm->save();

        }
        elseif($method=='flutterwave')
        {
            $keys=array();
            $flutterwave=PaymentMethods::find($update_id);
            $flutterwave['status']=$data['status'];
            $flutterwave['mode']=$data['mode'];
            $keys['test_key']=$data['test_key'];
            $keys['test_secret_key']=$data['test_secret_key'];
            $keys['test_encryption_key']=$data['test_encryption_key'];
            $keys['public_live_key']=$data['public_live_key'];
            $keys['secret_live_key']=$data['secret_live_key'];
            $keys['encryption_live_key']=$data['encryption_live_key'];
            $flutterwave['payment_keys']=json_encode($keys);
            $flutterwave['school_id']=auth()->user()->school_id;
            $flutterwave->save();

        } elseif ($method == 'paystack') {
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();

        }

       return redirect()->route('admin.settings.payment')->with('message', 'key has been updated');



    }

    public function insert_gateways()
    {   
        $paypal=PaymentMethods::where(array('name' => 'paypal', 'school_id' => auth()->user()->school_id ))->first();

        if (empty($paypal)) {
            $keys=array();
            $paypal= new PaymentMethods;
            $paypal['name']="paypal";
            $paypal['image']="paypal.png";
            $paypal['status']=1;
            $paypal['mode']="test";
            $keys['test_client_id']="snd_cl_id_xxxxxxxxxxxxx";
            $keys['test_secret_key']="snd_cl_sid_xxxxxxxxxxxx";
            $keys['live_client_id']="lv_cl_id_xxxxxxxxxxxxxxx";
            $keys['live_secret_key']="lv_cl_sid_xxxxxxxxxxxxxx";
            $paypal['payment_keys']=json_encode($keys);
            $paypal['school_id']=auth()->user()->school_id;
            $paypal->save();
        }
        
        $stripe=PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($stripe)){
            $keys=array();
            $stripe= new PaymentMethods ;
            $stripe['name']="stripe";
            $stripe['image']="stripe.png";
            $stripe['status']=1;
            $stripe['mode']="test";
            $keys['test_key']="pk_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key']="pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key']="sk_live_xxxxxxxxxxxxxx";
            $stripe['payment_keys']=json_encode($keys);
            $stripe['school_id']=auth()->user()->school_id;
            $stripe->save();
        }
        

        $razorpay=PaymentMethods::where(array('name' => 'razorpay', 'school_id' => auth()->user()->school_id ))->first();

        if((empty($razorpay))){
            $keys=array();
            $razorpay= new PaymentMethods ;
            $razorpay['name']="razorpay";
            $razorpay['image']="razorpay.png";
            $razorpay['status']=1;
            $razorpay['mode']="test";
            $keys['test_key']="rzp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="rzs_test_xxxxxxxxxxxxx";
            $keys['live_key']="rzp_live_xxxxxxxxxxxxx";
            $keys['live_secret_key']="rzs_live_xxxxxxxxxxxxx";
            $keys['theme_color']="#c7a600";
            $razorpay['payment_keys']=json_encode($keys);
            $razorpay['school_id']=auth()->user()->school_id;
            $razorpay->save();
        }   
            
        $paytm=PaymentMethods::where(array('name' => 'paytm', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($paytm)){
            $keys=array();
            $paytm= new PaymentMethods ;
            $paytm['name']="paytm";
            $paytm['image']="paytm.png";
            $paytm['status']=1;
            $paytm['mode']="test";
            $keys['test_merchant_id']="tm_id_xxxxxxxxxxxx";
            $keys['test_merchant_key']="tm_key_xxxxxxxxxx";
            $keys['live_merchant_id']="lv_mid_xxxxxxxxxxx";
            $keys['live_merchant_key']="lv_key_xxxxxxxxxxx";
            $keys['environment']="provide-a-environment";
            $keys['merchant_website']="merchant-website";
            $keys['channel']="provide-channel-type";
            $keys['industry_type']="provide-industry-type";
            $paytm['payment_keys']=json_encode($keys);
            $paytm['school_id']=auth()->user()->school_id;
            $paytm->save();
        }
        
        $flutterwave=PaymentMethods::where(array('name' => 'flutterwave', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($flutterwave)){
            $keys=array();
            $flutterwave= new PaymentMethods ;
            $flutterwave['name']="flutterwave";
            $flutterwave['image']="flutterwave.png";
            $flutterwave['status']=1;
            $flutterwave['mode']="test";
            $keys['test_key']="flwp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key']="flws_test_xxxxxxxxxxxxx";
            $keys['test_encryption_key']="flwe_test_xxxxxxxxxxxxx";
            $keys['public_live_key']="flwp_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key']="flws_live_xxxxxxxxxxxxxx";
            $keys['encryption_live_key']="flwe_live_xxxxxxxxxxxxxx";
            $flutterwave['payment_keys']=json_encode($keys);
            $flutterwave['school_id']=auth()->user()->school_id;
            $flutterwave->save();
        }

        $paystack=PaymentMethods::where(array('name' => 'paystack', 'school_id' => auth()->user()->school_id ))->first();

        if(empty($paystack)){
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();
        }
        


    }

    public function subscriptionPayment($package_id)
    {
        
        $selected_package=Package::find($package_id)->toArray();
        $user_info=User::where('id',auth()->user()->id)->first()->toArray();
        
        if($selected_package['price']==0) 
        {
             $check_duplication=Subscription::where('package_id',$selected_package['id'])->where('school_id',auth()->user()->school_id)->get()->count();
             if($check_duplication==0)
               
             {
                return redirect()->route('admin_free_subcription',['user_id'=>auth()->user()->id,'package_id'=>$selected_package['id']]);
             }
             else
             {
                 return redirect()->back()->with('error', 'you can not subscribe the free trail twice');
             }


        }
         
        return view('admin.subscription.payment_gateway',['selected_package'=>$selected_package,'user_info'=>$user_info]);
    }

    public function admin_free_subcription(Request $request)
    {   
        $data=$request->all();

        $selected_package=Package::find($data['package_id'])->toArray();
        $user_info=User::where('id',$data['user_id'])->first()->toArray();
        $school_email=School::where('id', auth()->user()->school_id)->value('email');
        

        $data['document_file'] = "sample-payment.pdf";

        $transaction_keys = json_encode($data);
                if($selected_package['package_type'] =='life_time'){
                    $status = Subscription::create([
                        'package_id' => $selected_package['id'],
                        'school_id' => auth()->user()->school_id,
                        'paid_amount' => $selected_package['price'],
                        'payment_method' => 'free',
                        'transaction_keys' => $transaction_keys,
                        'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                        'expire_date' => 'life_time',
                        'studentLimit' => $selected_package['studentLimit'],
                        'status' => '1',
                        'active' => '1',
                    ]);
                }else{
                    $status = Subscription::create([
                        'package_id' => $selected_package['id'],
                        'school_id' => auth()->user()->school_id,
                        'paid_amount' => $selected_package['price'],
                        'payment_method' => 'free',
                        'transaction_keys' => $transaction_keys,
                        'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                        'expire_date' => strtotime('+'.$selected_package['days'].' days', strtotime(date("Y-m-d H:i:s")) ),
                        'studentLimit' => $selected_package['studentLimit'],
                        'status' => '1',
                        'active' => '1',
                    ]);
                }
       
        
        Mail::to($school_email)->send(new FreeEmail($status));


            return redirect()->route('admin.subscription')->with('message', 'Free Subscription Completed Successfully');


    }




    public function admin_subscription_offline_payment(Request $request, $id = "")
    {
    	$data = $request->all();

    	if ($data['amount'] > 0) {

			$file = $data['document_image'];

	    	if ($file) {
	            $filename = $file->getClientOriginalName();
	            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

	            
	            $file->move(public_path('assets/uploads/offline_payment'), $filename);
	            $data['document_image'] = $filename;

	        } else {
	        	$data['document_image'] = '';
	        }

           $pending_payment = new PaymentHistory;

           $pending_payment['payment_type']='subscription';
           $pending_payment['user_id']=auth()->user()->id;
           $pending_payment['package_id']=$id;
           $pending_payment['amount']=$data['amount'];
           $pending_payment['school_id']=auth()->user()->school_id;
           $pending_payment['transaction_keys']='[]';
           $pending_payment['document_image']=$data['document_image'];
           $pending_payment['paid_by']='offline';
           $pending_payment['status']='pending';
           $pending_payment['timestamp']=strtotime(date("Y-m-d H:i:s"));

           $pending_payment->save();


            return redirect()->route('admin.subscription')->with('message', 'offline payment requested successfully');
        }else{
            return redirect()->route('admin.subscription')->with('message', 'offline payment requested fail');
        }


    }

    public function offlinePayment(Request $request, $id = "")
    {
        $data = $request->all();

        if ($data['amount'] > 0) :

            $file = $data['document_image'];

            if ($file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

                
                $file->move(public_path('assets/uploads/offline_payment'), $filename);
                $data['document_image'] = $filename;
            } else {
                $data['document_image'] = '';
            }


            PaymentHistory::create([
                'payment_type' => 'subscription',
                'user_id' => auth()->user()->id,
                'amount' => $data['amount'],
                'school_id' => $id,
                'transaction_keys' => json_encode(array()),
                'document_image' => $data['document_image'],
                'paid_by' => 'offline',
                'status' => 'pending',
                'timestamp' => strtotime(date('Y-m-d')),
            ]);

            return redirect('admin/subscription')->with('message', 'Your document will be reviewd.');

        else :
            return redirect('admin/subscription')->with('warning', 'Session timed out. Please try again');
        endif;
    }


    function profile(){
        return view('admin.profile.view');
    }

    function profile_update(Request $request){
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['designation'] = $request->designation;
        
        $user_info['birthday'] = strtotime($request->eDefaultDateRange);
        $user_info['gender'] = $request->gender;
        $user_info['phone'] = $request->phone;
        $user_info['address'] = $request->address;


        if(empty($request->photo)){
            $user_info['photo'] = $request->old_photo;
        }else{
            $file_name = random(10).'.png';
            $user_info['photo'] = $file_name;

            $request->photo->move(public_path('assets/uploads/user-images/'), $file_name);
        }

        $data['user_information'] = json_encode($user_info);

        User::where('id', auth()->user()->id)->update($data);
        
        return redirect(route('admin.profile'))->with('message', get_phrase('Profile info updated successfully'));
    }

    function user_language(Request $request){
        $data['language'] = $request->language;
        User::where('id', auth()->user()->id)->update($data);
        
        return redirect()->back()->with('message', 'You have successfully transleted language.');
    }

    function password($action_type = null, Request $request){



        if($action_type == 'update'){

            

            if($request->new_password != $request->confirm_password){
                return back()->with("error", "Confirm Password Doesn't match!");
            }


            if(!Hash::check($request->old_password, auth()->user()->password)){
                return back()->with("error", "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update($data);

            return redirect(route('admin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('admin.profile.password');
    }



    /**
     * Show the session manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function sessionManager()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        return view('admin.session.session_manager', ['sessions' => $sessions]);
    }

    public function activeSession($id)
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        Session::where('id', $previous_session_id)->update([
            'status' => '0',
        ]);

        $session = Session::where('id', $id)->update([
            'status' => '1',
        ]);

        School::where('id', auth()->user()->school_id)->update([
            'running_session' => $id,
        ]);

        $response = array(
            'status' => true,
            'notification' => get_phrase('Session has been activated')
        );
        $response = json_encode($response);

        echo $response;
    }

    public function createSession()
    {
        return view('admin.session.create');
    }

    public function sessionCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_session_check = Session::get()->where('session_title', $data['session_title'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_session_check) == 0) {

            $data['status'] = '0';
            $data['school_id'] = auth()->user()->school_id;

            Session::create($data);

            return redirect()->back()->with('message', 'You have successfully create a session.');
        } else {
            return redirect()->back()->with('error', 'Sorry this session already exists');
        }
    }

    public function editSession($id = '')
    {
        $session = Session::find($id);
        return view('admin.session.edit', ['session' => $session]);
    }

    public function sessionUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Session::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update session.');
    }

    public function sessionDelete($id = '')
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        if($previous_session_id != $id){
            $session = Session::find($id);
            $session->delete();
            return redirect()->back()->with('message', 'You have successfully delete a session.');
        } else {
            return redirect()->back()->with('error', 'Can not delete active session.');
        }
    }

    // Account Disable
    public function account_disable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'disable',
        ]);
        return redirect()->back()->with('message','Account Disable Successfully');
    }

    // Account Enable
    public function account_enable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'enable',
        ]);
        return redirect()->back()->with('message','Account Enable Successfully');
    }

    public function feedback_list()
    {
        $feedbacks = Feedback::where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->paginate(20);
        return view('admin.feedback.feedback_list', ['feedbacks' => $feedbacks]);
    }

    public function create_feedback()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.create_feedback', ['classes' => $classes]);
    }

    public function upload_feedback(Request $request){
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        //$admin_id = auth()->user()->id;

        $feedbackData = [
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'student_id' => isset($data['student_id'][0]) ? $data['student_id'][0] : null, // Assuming single student for feedback
            'parent_id' => isset($data['parent_id'][0]) ? $data['parent_id'][0] : null, // Assuming single parent for feedback
            'feedback_text' => $data['feedback_text'],
            'school_id' => auth()->user()->school_id,
            'admin_id' => auth()->user()->id,
            'session_id' => $active_session,
            'title' => $data['title']

        ];
    
        // Create feedback entry
        Feedback::create($feedbackData);
    
        return redirect()->back()->with('message', 'Feedback Sent Successfully');
    }

    public function edit_feedback($id){

        $feedback = Feedback::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.edit_feedback', ['classes' => $classes],  ['feedback' => $feedback]);

    }

    public function update_feedback(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Feedback::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update feedback.');
    }

    public function delete_feedback($id)
     {
        Feedback::where('id', $id)->delete();
         return redirect()->back()->with('message', 'Delete successfully.');
     }

    //  Message

    public function allMessage(Request $request, $id)
    {

            $msg_user_details = DB::table('users')
            ->join('message_thrades', function ($join) {
                // Join where the user is the sender
                $join->on('users.id', '=', 'message_thrades.sender_id')
                    ->orWhere(function ($query) {
                        // Join where the user is the receiver
                        $query->on('users.id', '=', 'message_thrades.reciver_id');
                    });
            })
            ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
            ->where('message_thrades.id', $id)
            ->where('message_thrades.school_id', auth()->user()->school_id)
            ->where('users.id', '<>', auth()->user()->id) // Exclude the authenticated user
            ->first();

            
            
        if ($request->ajax()) {
            $query = $request->input('query');
            
            // Search users by name or any other criteria
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            // Prepare HTML response
            $html = '';

            // Check if any users were found
            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {
                
                if (!empty($user)) {
                    $userInfo = json_decode($user->user_information);
                    
                    $user_image = !empty($userInfo->photo) 
                        ? asset('assets/uploads/user-images/' . $userInfo->photo) 
                        : asset('assets/uploads/user-images/thumbnail.png');

                    $html .= '
                        <div class="user-item d-flex align-items-center msg_us_src_list">
                            <a href="' . route('admin.message.messagethrades', ['id' => $user->id]).'">
                                <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                                <span class="ms-3">' . $user->name . '</span>
                            </a>
                        </div>
                    ';
                }
            }

            return response()->json($html);
        }


        $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();

        $counter_condition = Chat::where('message_thrade', $id)->orderBy('id', 'desc')->first();

       
        if(!empty($counter_condition->sender_id)){
            if($counter_condition->sender_id != auth()->user()->id){
                Chat::where('message_thrade', $id)->update(['read_status' => 1]);
            }
       }
       
        
        return view('admin.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
    }

    public function messagethrades($id){

        $exists = MessageThrade::where('reciver_id', $id)
                            ->where('sender_id', auth()->user()->id)
                            ->exists();
        if( $id != auth()->user()->id){
            if (!$exists) {
                $message_thrades_data = [
                    'reciver_id' => $id,
                    'sender_id' => auth()->user()->id,
                    'school_id' => auth()->user()->school_id,
                ];
        
                MessageThrade::create($message_thrades_data);
        
                //return redirect()->back()->with('message', 'User added successfully');
            }
    
            
            $message_thrades = MessageThrade::where('reciver_id', $id)
                                         ->where('sender_id', auth()->user()->id)
                                         ->first();
            $msg_trd_id = $message_thrades->id;
            
            $msg_user_details = DB::table('users')
                ->join('message_thrades', 'users.id', '=', 'message_thrades.reciver_id')
                ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
                ->where('message_thrades.id', $msg_trd_id)
                ->first();
    
                $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();
    
                // Combine all data into a single array
                return view('admin.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
        }
        return redirect()->back()->with('error', 'You can not add you');
        
                        
    }


    public function chat_save(Request $request)
    {
        $data = $request->all();
        $chat_data = [
            'message_thrade' => $data['message_thrade'],
            'reciver_id' => $data['reciver_id'],
            'message' => $data['message'],
            'school_id' => auth()->user()->school_id,
            'sender_id' => auth()->user()->id,
            'read_status' => 0,

        ];
    
        // Create feedback entry
        Chat::create($chat_data);

        return redirect()->back();
    }

    public function chat_empty(Request $request)
    {

        if ($request->ajax()) {
            $query = $request->input('query');

            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            $html = '';

            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {
                $userInfo = json_decode($user->user_information);
                $user_image = !empty($userInfo->photo) 
                    ? asset('assets/uploads/user-images/' . $userInfo->photo) 
                    : asset('assets/uploads/user-images/thumbnail.png');

                $html .= '
                    <div class="user-item d-flex align-items-center msg_us_src_list">
                        <a href="' . route('admin.message.messagethrades', ['id' => $user->id]).'">
                            <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                            <span class="ms-3">' . $user->name . '</span>
                        </a>
                    </div>
                ';
            }

            return response()->json($html);
        }

        // Pass the data to the view only if msg_user_details is not null
        return view('admin.message.chat_empty');
    }

    public function admitCardList()
    {
        $admit_cards = AdmitCard::where('school_id', auth()->user()->school_id)->get();

        return view('admin.examination.admit_card_list', ['admit_cards' => $admit_cards]);
    }

    public function admitCardCreate()
    {
        
        return view('admin.examination.admit_card_create');
    }

    public function admitCardUpload(Request $request)
    {
        $data = $request->all();
    
        $admitCardData = [
            'template'    => $data['template'],
            'heading'     => $data['heading'],
            'title'       => $data['title'],
            'school_id'   => auth()->user()->school_id,
            'exam_center' => $data['exam_center'],
            'footer_text' => $data['footer_text'], 
        ];
    
        if ($request->hasFile('sign')) {
            $ext = $request->file('sign')->getClientOriginalExtension();
            $newFileName = time() . '.' . $ext;
            $request->file('sign')->move(public_path('assets/upload/user-docs/'), $newFileName);
            $admitCardData['sign'] = $newFileName; // Add the sign filename to $admitCardData
        }  

        AdmitCard::create($admitCardData);
    
        return redirect()->back()->with('message', 'You have successfully created an Admit Card');
    }
    
    public function admitCardEdit($id)
    {
        $admitCardEdit = AdmitCard::find($id);
        return view('admin.examination.admit_card_edit', ['admitCardEdit' => $admitCardEdit]);
    }

    public function admitCardUpdate(Request $request, $id)
    {
        $admitCard = AdmitCard::findOrFail($id);

        $admitCard->template = $request->template;
        $admitCard->heading = $request->heading;
        $admitCard->title = $request->title;
        $admitCard->exam_center = $request->exam_center;
        $admitCard->footer_text = $request->footer_text;

    
        // Check if a new image is uploaded
        if ($request->hasFile('sign')) {
            // Store the new image
            $newImage = $request->file('sign');
            $newFileName = time().'.'.$newImage->getClientOriginalExtension();
            $newImage->move(public_path('assets/upload/user-docs/'), $newFileName);
    
            // Delete the old image if it exists
            if ($admitCard->sign && file_exists(public_path().'assets/upload/user-docs/'.$admitCard->sign)) {
                unlink(public_path().'assets/upload/user-docs/'.$admitCard->sign);
            }
    
            // Update testimonial with the new image path
            $admitCard->sign = $newFileName;
        }
    
        // Save changes
        $admitCard->save();
         
        // Redirect back or wherever needed
        return redirect()->back()->with('message', 'Admit Card Updated Successfully');
    }

    public function admitCardDelete($id)
     {
        AdmitCard::where('id', $id)->delete();
         return redirect()->back()->with('message', 'Delete successfully.');
     }

     public function admitCardPrint()
     {
        $page_data['admit_cards'] = AdmitCard::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
        $page_data['sessions'] = Session::where('school_id', auth()->user()->school_id)->get();

        return view('admin.examination.admit_card_print', $page_data);
     }
/*
     public function admitCardFilter(Request $request)
     {
        $data = $request->all();

        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['session_id'] = $data['session_id'];

        $page_data['class_name'] = Classes::find($data['class_id'])->name;
        $page_data['section_name'] = Section::find($data['section_id'])->name;
        $page_data['session_title'] = Session::find($data['session_id'])->session_title;
        $admit_cards= AdmitCard::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();

        $enroll_students = Enrollment::where('class_id', $page_data['class_id'])
        ->where('section_id', $page_data['section_id'])
        ->paginate(10);

        $selected_admit_card = AdmitCard::where('id', $data['admit_card_id'])->first();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();

        
        return view('admin.examination.admitCardFilter', [
            'enroll_students' => $enroll_students,
            'page_data' => $page_data,
            'selected_admit_card' => $selected_admit_card,
            'admit_cards' => $admit_cards,
            'classes' => $classes,
            'sessions' => $sessions
        ]);
     }
     
*/

    public function admitCardFilter(Request $request)
{
    $data = $request->all();

    // Initialize page_data with default null-safe values
    $page_data = [
        'class_id' => $data['class_id'] ?? null,
        'section_id' => $data['section_id'] ?? null,
        'session_id' => $data['session_id'] ?? null,
        'class_name' => null,
        'section_name' => null,
        'session_title' => null,
    ];

    // Safely fetch related names only if IDs are present
    if ($page_data['class_id']) {
        $class = \App\Models\Classes::find($page_data['class_id']);
        $page_data['class_name'] = $class->name ?? 'Unknown';
    }

    if ($page_data['section_id']) {
        $section = \App\Models\Section::find($page_data['section_id']);
        $page_data['section_name'] = $section->name ?? 'Unknown';
    }

    if ($page_data['session_id']) {
        $session = \App\Models\Session::find($page_data['session_id']);
        $page_data['session_title'] = $session->session_title ?? 'Unknown';
    }

    $admit_cards = \App\Models\AdmitCard::where('school_id', auth()->user()->school_id)->get();
    $classes = \App\Models\Classes::where('school_id', auth()->user()->school_id)->get();
    $sessions = \App\Models\Session::where('school_id', auth()->user()->school_id)->get();

    // Enrollments only if class/section are selected
    $enroll_students = collect();
    if ($page_data['class_id'] && $page_data['section_id']) {
        $enroll_students = \App\Models\Enrollment::where('class_id', $page_data['class_id'])
            ->where('section_id', $page_data['section_id'])
            ->paginate(10);
    }

    $selected_admit_card = null;
    if (!empty($data['admit_card_id'])) {
        $selected_admit_card = \App\Models\AdmitCard::find($data['admit_card_id']);
    }

    return view('admin.examination.admitCardFilter', [
        'enroll_students' => $enroll_students,
        'page_data' => $page_data,
        'selected_admit_card' => $selected_admit_card,
        'admit_cards' => $admit_cards,
        'classes' => $classes,
        'sessions' => $sessions
    ]);
}




    public function broadcastNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'target' => 'required|in:all,teachers,parents,students,class,section',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
        ]);
    
        $fcm = new FcmHttpV1Service();
        $message = $request->message;
    
        switch ($request->target) {
            case 'teachers':
                $result = $fcm->sendToAllTeachers($message);
                break;
            case 'parents':
                $result = $fcm->sendToAllParents($message);
                break;
            case 'students':
                $result = $fcm->sendToAllStudents($message);
                break;
            case 'class':
                $result = $fcm->sendToParentsOfClass($request->class_id, $message);
                break;
            case 'section':
                $result = $fcm->sendToParentsOfSection($request->class_id, $request->section_id, $message);
                break;
            default:
                $result = $fcm->sendToAllUsers($message);
                break;
        }
    
        return response()->json([
            'success' => true,
            'summary' => "✅ Sent: {$result['success']} | ❌ Failed: {$result['failed']} | 🎯 Users targeted: {$result['total_users']}"
            
        ]);
    }

   
}
