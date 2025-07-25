<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Message;
use App\Models\Session;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use App\Models\Gradebook;
use App\Models\Subject;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Grade;
use App\Models\ClassList;
use App\Models\DailyAttendances;
use App\Models\Routine;
use App\Models\Syllabus;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\School;
use App\Models\Admin;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\Payments;
use App\Models\Feedback;
use App\Models\MessageThrade;
use App\Models\Chat;
use App\Models\PaymentMethods;

use Illuminate\Foundation\Auth\User as AuthUser;
use PhpParser\Builder\Class_;

class ParentController extends Controller
{
    public function parentDashboard()
    {
        return view('parent.dashboard');
    }

    public function teacherList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $teachers = User::where(function ($query) use($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->where('school_id', auth()->user()->school_id)
                        ->where('role_id', 3);
                })->paginate(10);

        } else {
            $teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('parent.user.teacher_list', compact('teachers', 'search'));
    }

    public function childList(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search != "") {

            $students = User::where(function ($query) use($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('parent_id', auth()->user()->id)
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 7);
            })->paginate(10);

        } else {
            $students = User::where('role_id', 7)->where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('parent.user.child_list', compact('students', 'search'));
    }

        public function inbox()
    {
        $userId = auth()->id();
        $schoolId = auth()->user()->school_id;

        // ✅ Mark messages as read for this user
        Message::whereRaw("FIND_IN_SET(?, to_user_id)", [$userId])
            ->where('school_id', $schoolId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        // ✅ Fetch all messages where the current user is a recipient
        $messages = Message::whereRaw("FIND_IN_SET(?, to_user_id)", [$userId])
            ->where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.compose.inbox', compact('messages'));
    }

    
    

    public function downloadAttachment($id)
    {
        $message = Message::findOrFail($id);
    
        // Ensure only the intended user can download it
        if (!in_array(auth()->id(), explode(',', $message->to_user_id)) && auth()->user()->role_id != 1) {
            abort(403, 'Unauthorized');
        }
    
        $path = $message->attachment_path;
    
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Attachment not found.');
        }
    
        $file = Storage::disk('public')->path($path);
        $originalName = basename($path); // Optional: store & use original filename separately
    
        return response()->download($file, $originalName, [
            'Content-Type' => mime_content_type($file),
        ]);
    }



    public function inlinePreview($id)
    {
        $message = Message::findOrFail($id);
    
        // Security: Only the intended recipient or admin can view
        if (!in_array(auth()->id(), explode(',', $message->to_user_id)) && auth()->user()->role_id != 1) {
            abort(403);
        }
    
        $path = $message->attachment_path;
    
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
    
        $file = Storage::disk('public')->path($path);
    
        return response()->file($file, [
            'Content-Type' => mime_content_type($file),
            'Content-Disposition' => 'inline; filename="' . basename($file) . '"',
        ]);
    }
    
    //Detail Message
    public function messageDetails($id)
    {
        $message = Message::findOrFail($id);
    
        // Optional security check (only allow student to view their own message)
        if (!in_array(auth()->id(), explode(',', $message->to_user_id)) && auth()->user()->role_id != 7) {
            abort(403);
        }
    
        return view('parent.compose.details', compact('message'));
    }
    

    public function studentIdCardGenerate($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('parent.user.id_card', ['student_details' => $student_details]);
    }



    public function FeeManagerList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $school_id = auth()->user()->school_id;
    
        if ($request->filled('eDateRange')) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to   = strtotime($date[1] . ' 23:59:59');
            $selected_status = $data['status'];
    
            $query = \App\Models\StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                ->where('parent_id', auth()->user()->id)
                ->where('school_id', $school_id)
                ->where('session_id', $active_session);
    
            // Apply status filter
            if ($selected_status != "all") {
                if ($selected_status == "partial") {
                    $query->whereColumn('paid_amount', '<', 'total_amount')
                          ->where('paid_amount', '>', 0);
                } elseif ($selected_status == "paid") {
                    $query->whereColumn('paid_amount', '=', 'total_amount');
                } elseif ($selected_status == "unpaid") {
                    $query->where(function ($q) {
                        $q->whereNull('paid_amount')->orWhere('paid_amount', '=', 0);
                    });
                } elseif ($selected_status == "pending") {
                    $query->where('status', 'pending');
                }
            }
    
            $invoices = $query->get();
        } else {
            // Default: last 30 days, all statuses
            $date_from = strtotime(date('d-M-Y', strtotime('-30 days')) . ' 00:00:00');
            $date_to = strtotime(date('d-M-Y') . ' 23:59:59');
            $selected_status = "all";
    
            $invoices = \App\Models\StudentFeeManager::whereBetween('timestamp', [$date_from, $date_to])
                ->where('parent_id', auth()->user()->id)
                ->where('school_id', $school_id)
                ->where('session_id', $active_session)
                ->get();
        }
    
        return view('parent.fee_manager.student_fee_manager', [
            'invoices' => $invoices,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'selected_status' => $selected_status
        ]);
    }


/*
    public function FeeManagerList(Request $request)
    { //parent

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $details_of_chilren = User::where('parent_id', auth()->user()->id)->get()->toArray();
        $invoices = "i";


        if (count($request->all()) > 0) {

            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $selected_status = $data['status'];

            if ($selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('parent_id', auth()->user()->id)->where('session_id', $active_session)->get();
            } else if ($selected_status == "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('parent_id', auth()->user()->id)->where('session_id', $active_session)->get();
            }


            return view('parent.fee_manager.student_fee_manager', ['invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to,  'selected_status' => $selected_status]);
        } else {

            $date_from = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
            $date_to = strtotime(date('d-M-Y') . ' 23:59:59');
            $selected_status = "";

            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();


            return view('parent.fee_manager.student_fee_manager', ['invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to,  'selected_status' => $selected_status]);
        }
    }
*/
    public function FeePayment(Request $request, $id)
    {

        $fee_details = StudentFeeManager::where('id', $id)->first()->toArray();
        $user_info = User::where('id', auth()->user()->id)->first()->toArray();
        return view('parent.payment.payment_gateway', ['fee_details' => $fee_details, 'user_info' => $user_info]);
    }

    public function feeManagerExport($date_from = "", $date_to = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');


        if ($selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('parent_id', auth()->user()->id)->where('session_id', $active_session)->get();
        } else if ($selected_status == "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('parent_id', auth()->user()->id)->where('session_id', $active_session)->get();
        }

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();



        $file = "student_fee-" . date('d-m-Y', $date_from) . '-' . date('d-m-Y', $date_to) . '-' . $selected_status . ".csv";

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

    public function studentFeeinvoice(Request $request, $id)
    {

        $invoice_details = StudentFeeManager::find($id)->toArray();
        //$student_details = (new CommonController)->get_student_details_by_id($invoice_details['student_id'])->toArray();
        $student_details_obj = (new CommonController)->get_student_details_by_id($invoice_details['student_id']);
        $student_details = json_decode(json_encode($student_details_obj), true);

            // ✅ Fetch payment method from StudentFeeManager model
        $payment_method = $invoice_details['payment_method'] ?? 'Not Available'; 
            // Fetch school details
        $school = School::where('id', $student_details['school_id'])->first();
        $school_name = $school->title ?? 'Unknown School';
        $school_logo = $school->school_logo ? asset('assets/uploads/school_logo/' . $school->school_logo) : null;
        
        // ✅ Fetch address, phone, and email from School model
        $school_address = $school->address ?? 'Address Not Available';
        $school_phone = $school->phone ?? 'Phone Not Available';
        $school_email = $school->email ?? 'Email Not Available';

        // Fetch session title
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $session = $session_id ? \App\Models\Session::find($session_id) : null;
        $session_title = $session ? $session->session_title : 'Unknown Session';

        return view('parent.fee_manager.invoice', 
        ['invoice_details' => $invoice_details, 
        'student_details' => $student_details,
        'school_name' => $school_name,
        'school_logo' => $school_logo,
        'school_address' => $school_address,
        'school_phone' => $school_phone,
        'school_email' => $school_email,
        'session_title' => $session_title,
        'payment_method' => $payment_method // ✅ Pass payment method
    ]);
}

    public function gradeList()
    {
        $grades = Grade::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('parent.grade.grade_list', compact('grades'));
    }

    public function subjectList()
    {


        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $student_data = User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->get();
        if (!empty($student_data)) {
            $student_data = $student_data->toArray();
        }

        return view('parent.subject.subject_list', compact('student_data'));
    }

    public function subjectList_by_student_name(Request $request)
    {
        $data = $request->all();
        $class_id = Enrollment::where('user_id', $data['user_id'])->first()->toArray();
        $class_name = Classes::where('id', $class_id['class_id'])->get()->toArray();
        $subjects = Subject::where('class_id', $class_id['class_id'])->get()->toArray();
        return view('parent.subject.table', ['class_name' => $class_name, 'subjects' => $subjects]);
    }

    public function offlinePayment(Request $request, $id = "")
    {
        Log::info("Parent initiated offline payment request for Invoice ID: $id by User ID: " . auth()->user()->id);
    
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'document_image' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,txt|max:2048'
        ]);
    
        try {
            $file = $request->file('document_image');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('assets/uploads/offline_payment'), $filename);
    
            StudentFeeManager::where('id', $id)->update([
                'status' => 'pending',
                'document_image'=> $filename,
                'payment_method' => 'offline',
                'timestamp' => time(),
            ]);
    
            Log::info("Offline payment document uploaded: $filename for invoice $id");
    
            return redirect()->route('parent.fee_manager.list')->with('message', 'Payment requested successfully');
        } catch (\Exception $e) {
            Log::error("Offline payment failed for Invoice ID: $id. Error: " . $e->getMessage());
            return redirect()->route('parent.fee_manager.list')->with('message', 'Payment request failed');
        }
    }

   
/*
    public function offlinePayment(Request $request, $id = "")
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

            StudentFeeManager::where('id',  $id)->update([
                'status' => 'pending',
                'document_image'=> $data['document_image'],
                'payment_method' => 'offline'
            ]);





            return redirect()->route('parent.fee_manager.list')->with('message', 'Payment requested successfully');
        }else{
            return redirect()->route('parent.fee_manager.list')->with('message', 'Payment requested fail');
        }


    }
*/

/*
    public function syllabusList()
    {


        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $student_data = User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->get();
        if (!empty($student_data)) {
            $student_data = $student_data->toArray();
        }

        return view('parent.syllabus.syllabus_list', compact('student_data'));
    }
    


    public function syllabusList_by_student_name(Request $request)
    {
        $data = $request->all();
        $class_id = Enrollment::where('user_id', $data['user_id'])->first()->toArray();
        $class_name = Classes::where('id', $class_id['class_id'])->get()->toArray();
        $syllabus = Syllabus::where('class_id', $class_id['class_id'])->get()->toArray();

        return view('parent.syllabus.table', ['class_name' => $class_name, 'syllabus' => $syllabus]);
    }
    
*/


    public function syllabusList()
    {
        \Log::info('Parent syllabusList() called.', [
            'parent_id' => auth()->user()->id,
            'school_id' => auth()->user()->school_id
        ]);
    
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
    
        $student_data = User::where('parent_id', auth()->user()->id)
            ->where('school_id', auth()->user()->school_id)
            ->get();
    
        \Log::info('Children fetched for parent.', ['children_count' => $student_data->count()]);
    
        if (!empty($student_data)) {
            $student_data = $student_data->toArray();
        }
    
        return view('parent.syllabus.syllabus_list', compact('student_data'));
    }



    public function syllabusList_by_student_name(Request $request)
    {
        \Log::info('Parent syllabusList_by_student_name() called.', ['request_data' => $request->all()]);
    
        $data = $request->all();
    
        $enrollment = Enrollment::where('user_id', $data['user_id'])->first();
    
        if (!$enrollment) {
            \Log::warning('No enrollment found for user.', ['user_id' => $data['user_id']]);
            return response()->view('parent.syllabus.table', [
                'class_name' => [],
                'syllabus' => []
            ]);
        }
    
        $class_id = $enrollment->class_id;
    
        \Log::info('Enrollment found.', [
            'user_id' => $data['user_id'],
            'class_id' => $class_id
        ]);
    
        $class_name = Classes::where('id', $class_id)->get()->toArray();
        $syllabus = \App\Models\Syllabus::where('class_id', $class_id)->get()->toArray();
    
        \Log::info('Syllabus and class name fetched.', [
            'class_name_count' => count($class_name),
            'syllabus_count' => count($syllabus)
        ]);
    
        return view('parent.syllabus.table', [
            'class_name' => $class_name,
            'syllabus' => $syllabus
        ]);
    }


    public function routine()
    {
        $child = User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->get();
        $student_data = array();

        if (!empty($child)) {
            $child = $child->toArray();
            foreach ($child as $info) {
                $each_child = (new CommonController)->get_student_academic_info($info['id']);

                $each_child = $each_child->toArray();
                array_push($student_data, $each_child);
            }
        }




        return view('parent.routine.routine', ['student_data' => $student_data]);
    }

    public function routineList(Request $request)
    {
        $data = $request->all();

        $student_id = $data['student_id'];

        $academic_info = Enrollment::where('user_id', $student_id)->first()->toArray();

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('parent.routine.routine_list', ['student_id' => $student_id, 'class_id' => $academic_info['class_id'], 'section_id' => $academic_info['section_id'], 'classes' => $classes]);
    }


    public function list_of_attendence(Request $request)
    {
        Log::info('list_of_attendence Function started');
    
        $school_id = auth()->user()->school_id;
        $parent_id = auth()->user()->id;
    
        $child = User::where('parent_id', $parent_id)
            ->where('school_id', $school_id)
            ->get();
    
        Log::info('Fetched child users:', ['child_count' => $child->count()]);
    
        $child_data = [];
        foreach ($child as $info) {
            $each_child = (new CommonController)->get_student_academic_info($info->id)->toArray();
            $child_data[] = $each_child;
        }
        Log::info('Processed child academic info.', ['child_data_count' => count($child_data)]);
    
        // Defaults
        $attendance_of_students = [];
        $student_data = [];
        $no_of_users = 0;
        $page_data = [];
    
        if ($request->has(['month', 'year', 'student_id'])) {
            $data = $request->only(['month', 'year', 'student_id']);
            Log::info('Request data received:', $data);
    
            $attendance_date = date('Y-m-d', strtotime('01 '.$data['month'].' '.$data['year']));
            $first_date = date('Y-m-01 00:00:00', strtotime($attendance_date));
            $last_date  = date('Y-m-t 23:59:59', strtotime($attendance_date));
    
            $page_data = [
                'attendance_date' => $attendance_date,
                'month' => $data['month'],
                'year' => $data['year'],
            ];
            Log::info('Page date info:', $page_data);
    
            // Get selected student info
            $student_data = (new CommonController)->get_student_academic_info($data['student_id']);
            if ($student_data) {
                Log::info('Selected student academic info:', $student_data->toArray());
    
                $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])
                    ->where([
                        'class_id' => $student_data['class_id'],
                        'section_id' => $student_data['section_id'],
                        'student_id' => $student_data['user_id'],
                        'school_id' => $school_id,
                    ])
                    ->get();
    
                Log::info('Fetched attendance records:', ['count' => $attendance_of_students->count()]);
    
                $no_of_users = DailyAttendances::where([
                        'class_id' => $student_data['class_id'],
                        'section_id' => $student_data['section_id'],
                        'student_id' => $student_data['user_id'],
                        'school_id' => $school_id
                    ])
                    ->distinct()
                    ->count('student_id');
                Log::info('Count of distinct students in attendance records:', ['no_of_users' => $no_of_users]);
            } else {
                Log::warning("Student academic info not found for ID: " . $data['student_id']);
            }
        } else {
            Log::info('No filter request, loading default view');
    
            $date = date('Y-m-d');
            $page_data = [
                'attendance_date' => $date,
                'month' => date('M'),
                'year' => date('Y'),
            ];
        }
    
        $last_record = DailyAttendances::orderBy('updated_at', 'desc')->first();
        Log::info('Rendering view: parent.attendence.list_of_attendence');
    
        return view('parent.attendence.list_of_attendence', [
            'child_data' => $child_data,
            'page_data' => $page_data,
            'attendance_of_students' => $attendance_of_students,
            'student_data' => $student_data,
            'no_of_users' => $no_of_users,
            'last_record' => $last_record
        ]);
    }






/*
    public function list_of_attendence(Request $request)
    {
        Log::info('list_of_attendence Function started');
    
        $child = User::where('parent_id', auth()->user()->id)
            ->where('school_id', auth()->user()->school_id)
            ->get();
        Log::info('Fetched child users:', ['child_count' => $child->count()]);
    
        $child_data = [];
        if (!empty($child)) {
            foreach ($child as $info) {
                $each_child = (new CommonController)->get_student_academic_info($info->id)->toArray();
                array_push($child_data, $each_child);
            }
            Log::info('Processed child academic info.', ['child_data_count' => count($child_data)]);
        }
    
        if (!empty($request->all())) {
            $data = $request->all();
            Log::info('Request data received:', $data);
    
            $date = '01 ' . $data['month'] . ' ' . $data['year'];
            $attendance_date = date('Y-m-d', strtotime($date)); // Store as date string
            $page_data = [
                'attendance_date' => $attendance_date,
                'month' => $data['month'],
                'year' => $data['year'],
            ];
            Log::info('Page date info:', $page_data);
    
            $student_data = (new CommonController)->get_student_academic_info($data['student_id']);
            Log::info('Selected student academic info:', $student_data->toArray());
    
            $first_date = date('Y-m-01 00:00:00', strtotime($attendance_date));
            $last_date  = date('Y-m-t 23:59:59', strtotime($attendance_date));
            Log::info('Date range for attendance query:', ['from' => $first_date, 'to' => $last_date]);
    
            $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])
                ->where([
                    'class_id' => $student_data['class_id'],
                    'section_id' => $student_data['section_id'],
                    'student_id' => $student_data['user_id'],
                    'school_id' => auth()->user()->school_id,
                ])
                ->get();
    
            Log::info('Fetched attendance records:', ['count' => $attendance_of_students->count()]);
    
            $no_of_users = DailyAttendances::where([
                    'class_id' => $student_data['class_id'],
                    'section_id' => $student_data['section_id'],
                    'student_id' => $student_data['user_id'],
                    'school_id' => auth()->user()->school_id
                ])
                ->distinct()
                ->count('student_id');
            Log::info('Count of distinct students in attendance records:', ['no_of_users' => $no_of_users]);
        } else {
            Log::info('No filter request, loading default view');
    
            $date = '01 ' . date('M') . ' ' . date('Y');
            $attendance_date = date('Y-m-d', strtotime($date));
            $page_data = [
                'attendance_date' => $attendance_date,
                'month' => date('M'),
                'year' => date('Y'),
            ];
            $attendance_of_students = [];
            $student_data = [];
            $no_of_users = [];
        }
        $last_record = DailyAttendances::orderBy('updated_at', 'desc')->first();
        Log::info('Rendering view: parent.attendence.list_of_attendence');
    
        return view('parent.attendence.list_of_attendence', [
            'child_data' => $child_data,
            'page_data' => $page_data,
            'attendance_of_students' => $attendance_of_students,
            'student_data' => $student_data,
            'no_of_users' => $no_of_users,
            'last_record' => $last_record // <-- Pass last record
        ]);
    }
    */

    public function dailyAttendanceFilter_csv(Request $request)
    {

        $data = $request->all();

        $store_get_data=array_keys($data);


        $data['month']= substr($store_get_data[0],0,3);
        $data['year']= substr($store_get_data[0],4,4);
        $data['role_id']=substr($store_get_data[0],9,5);

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

      
        $date = '01 ' . $data['month'] . ' ' . $data['year'];


        $first_date = strtotime($date);

        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;


        $no_of_users = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id,  'session_id' => $active_session])->distinct()->count('student_id');
        $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id, 'student_id' => auth()->user()->id, 'session_id' => $active_session])->get()->toArray();
       

        $csv_content ="Student"."/".get_phrase('Date');
        $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
        for ($i = 1; $i <= $number_of_days; $i++)
        {
            $csv_content .=','.get_phrase($i);

        }


        $file = "Attendence_report.csv";


        $student_id_count = 0;


        foreach(array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student ){
            $csv_content .= "\n";

            $user_details = (new CommonController)->get_user_by_id_from_user_table($attendance_of_student['student_id']);
            if(date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['timestamp'])) {
                
                if($student_id_count != $attendance_of_student['student_id']) {
                    
                    $csv_content .= $user_details['name'] . ',';

                    for ($i = 1; $i <= $number_of_days; $i++) {

                        $page_data['date'] = $i.' '.$page_data['month'].' '.$page_data['year'];
                        $timestamp = strtotime($page_data['date']);

                        $attendance_by_id = DailyAttendances::where([ 'student_id' => $attendance_of_student['student_id'], 'school_id' => auth()->user()->school_id, 'timestamp' => $timestamp])->first();

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

                $student_id_count = $attendance_of_student['student_id'];
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
/*
    public function marks()
    {

        $child = User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->get();
        $student_data = array();

        if (!empty($child)) {
            $child = $child->toArray();
            foreach ($child as $info) {
                $each_child = (new CommonController)->get_student_academic_info($info['id']);

                $each_child = $each_child->toArray();
                array_push($student_data, $each_child);
            }
        }

        return view('parent.marks.index', ['student_data' => $student_data]);
    }

    public function marks_list(Request $request, $value = '')
    {
        $data = $request->all();
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $user_id = $data['student_id'];
        $student_details = (new CommonController)->get_student_details_by_id($user_id);

        //$subjects = Subject::where(['class_id' => $student_details['class_id'], 'school_id' => auth()->user()->school_id])->get();
        $subjects = Subject::where(['class_id' => $student_details->class_id, 'school_id' => auth()->user()->school_id])->get();


        return view('parent.marks.table', ['exam_categories' => $exam_categories, 'student_details' => $student_details, 'subjects' => $subjects]);
    }

*/

    public function marks()
{
    \Log::info('Parent marks() method called.', ['parent_id' => auth()->user()->id]);

    $child = User::where('parent_id', auth()->user()->id)
                 ->where('school_id', auth()->user()->school_id)
                 ->get();

    \Log::info('Fetched children for parent.', ['count' => $child->count()]);

    $student_data = [];

    if (!empty($child)) {
        $child = $child->toArray();
        foreach ($child as $info) {
            \Log::info('Fetching academic info for child.', ['student_id' => $info['id']]);

            $each_child = (new CommonController)->get_student_academic_info($info['id']);
            $each_child_array = $each_child->toArray();

            \Log::info('Academic info for child fetched.', ['data' => $each_child_array]);

            array_push($student_data, $each_child_array);
        }
    }

    \Log::info('Returning student data to parent.marks.index view.', ['students' => $student_data]);

    return view('parent.marks.index', ['student_data' => $student_data]);
}


    public function marks_list(Request $request, $value = '')
{
    \Log::info('Parent marks_list() method called.', ['request_data' => $request->all()]);

    $data = $request->all();
    $school_id = auth()->user()->school_id;
    $exam_categories = ExamCategory::where('school_id', $school_id)->get();

    \Log::info('Fetched exam categories.', ['count' => $exam_categories->count()]);

    $user_id = $data['student_id'];
    $student_details = (new CommonController)->get_student_details_by_id($user_id);

    \Log::info('Fetched student details by ID.', ['student_id' => $user_id, 'class_id' => $student_details->class_id ?? null]);

    $subjects = Subject::where([
        'class_id' => $student_details->class_id,
        'school_id' => $school_id
    ])->get();

    \Log::info('Fetched subjects for class.', ['subject_count' => $subjects->count(), 'class_id' => $student_details->class_id]);

    return view('parent.marks.table', [
        'exam_categories' => $exam_categories,
        'student_details' => $student_details,
        'subjects' => $subjects
    ]);
}









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
            'gradebook' => $gradebook,
        ];
    
        \Log::info('Data sent to Blade:', $data);
    
        $pdf = Pdf::loadView('parent.marks.report_card', $data);
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


    public function noticeboardList()
    {

        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);

        $events = array();

        foreach ($notices as $notice) {
            if ($notice['end_date'] != "") {
                if ($notice['start_date'] != $notice['end_date']) {
                    $end_date = strtotime($notice['end_date']) + 24 * 60 * 60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice['end_date']));
                }
            }

            if ($notice['end_date'] == "" && $notice['start_time'] == "" && $notice['end_time'] == "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            } else if ($notice['start_time'] != "" && ($notice['end_date'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time']
                );
            } else if ($notice['end_date'] != "" && ($notice['start_time'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])),
                    'end' => $end_date
                );
            } else if ($notice['end_date'] != "" && $notice['start_time'] != "" && $notice['end_time'] != "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time'],
                    'end' => date('Y-m-d', strtotime($notice['end_date'])) . 'T' . $notice['end_time']
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

        return view('parent.noticeboard.noticeboard', ['events' => $events]);
    }

    public function editNoticeboard($id = "")
    {
        $notice = Noticeboard::find($id);
        return view('parent.noticeboard.edit', ['notice' => $notice]);
    }

    function profile(){
        return view('parent.profile.view');
    }

    function profile_update(Request $request){
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        
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
        
        return redirect(route('parent.profile'))->with('message', get_phrase('Profile info updated successfully'));
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

            return redirect(route('parent.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('parent.profile.password');
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

        return view('parent.events.events', compact('events', 'search'));
    }

    public function filter()
    {
        $child = User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->get();
        $student_data = array();

        if (!empty($child)) {
            $child = $child->toArray();
            foreach ($child as $info) {
                $each_child = (new CommonController)->get_student_academic_info($info['id']);

                $each_child = $each_child->toArray();
                array_push($student_data, $each_child);
            }
        }

        return view('parent.feedback.filter', ['student_data' => $student_data]);
    }

    public function marks_listc(Request $request, $value = '')
    {
        $data = $request->all();
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $user_id = $data['student_id'];
        $student_details = (new CommonController)->get_student_details_by_id($user_id);

        $subjects = Subject::where(['class_id' => $student_details['class_id'], 'school_id' => auth()->user()->school_id])->get();


        return view('parent.marks.table', ['exam_categories' => $exam_categories, 'student_details' => $student_details, 'subjects' => $subjects]);
    }

    public function feedback_list(Request $request, $value = '')
    {
        $data = $request->all();
        $user_id = $data['student_id'];
        //$student_details = (new CommonController)->get_student_details_by_id($user_id);
        $feedbacks = Feedback::where(['student_id' => $user_id,'school_id'=> auth()->user()->school_id])->orderBy('created_at', 'DESC')->paginate(20);

        return view('parent.feedback.feedback_list', ['feedbacks' => $feedbacks]);
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
                            <a href="' . route('parent.message.messagethrades', ['id' => $user->id]).'">
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

       
       if($counter_condition->sender_id != auth()->user()->id){
            Chat::where('message_thrade', $id)->update(['read_status' => 1]);
        }
        
        return view('parent.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
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
                return view('parent.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
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
                        <a href="' . route('parent.message.messagethrades', ['id' => $user->id]).'">
                            <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                            <span class="ms-3">' . $user->name . '</span>
                        </a>
                    </div>
                ';
            }

            return response()->json($html);
        }

        // Pass the data to the view only if msg_user_details is not null
        return view('parent.message.chat_empty');
    }
    
    
    
    
        public function fetchPresentDays(Request $request)
    {
        $childId = $request->child_id;

        $currentMonth = date('m');
        $currentYear = date('Y');

        $presentDays = DailyAttendances::where('student_id', $childId)
                        ->where('school_id', auth()->user()->school_id)
                        ->whereMonth('stu_intime', $currentMonth)
                        ->whereYear('stu_intime', $currentYear)
                        ->where('status', 1)
                        ->count();

        return response()->json([
            'presentDays' => $presentDays
        ]);
    }

    public function fetchDueAmount(Request $request)
    {
        $child_id = $request->child_id;
    
        $invoices = \App\Models\StudentFeeManager::where('student_id', $child_id)
                        ->where('school_id', auth()->user()->school_id)
                        ->get();
    
        $totalDue = 0;
    
        foreach ($invoices as $invoice) {
            $actualTotal = (!empty($invoice->discounted_price) && $invoice->discounted_price > 0)
                ? ($invoice->amount - $invoice->discounted_price)
                : $invoice->amount;
    
            $due = $actualTotal - $invoice->paid_amount;
    
            if ($due > 0) {
                $totalDue += $due;
            }
        }
    
        return response()->json([
            'success' => true,
            'total_due' => number_format($totalDue, 0) // Proper formatted amount like 1,234.00
        ]);
    }

}
