<?php

namespace App\Http\Controllers;

use PDF;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use App\Models\Session;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Gradebook;
use App\Models\Grade;
use App\Models\ClassList;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\Routine;
use App\Models\Syllabus;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\Admin;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\PaymentMethods;
use App\Models\Payments;
use App\Models\MessageThrade;
use App\Models\Chat;
use Illuminate\Foundation\Auth\User as AuthUser;

class StudentController extends Controller
{
    /**
     * Show the student dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentDashboard()
    {

        if (auth()->user()->role_id == 7) {
            return view('student.dashboard');
        } else {
            redirect()->route('login')
                ->with('error', 'You are not logged in.');
        }
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

        return view('student.compose.inbox', compact('messages'));
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
        if (!in_array(auth()->id(), explode(',', $message->to_user_id)) && auth()->user()->role_id != 7) {
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

    // Detail Message
    public function messageDetails($id)
    {
        $message = Message::findOrFail($id);
    
        // Optional security check (only allow student to view their own message)
        if (!in_array(auth()->id(), explode(',', $message->to_user_id)) && auth()->user()->role_id != 7) {
            abort(403);
        }
    
        return view('student.compose.details', compact('message'));
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
                })->paginate(10);

        } else {
            $teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('student.teacher.teacher_list', compact('teachers', 'search'));
    }

    /**
     * Show the daily attendance.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    

    public function dailyAttendance(Request $request)
    {
        return $this->dailyAttendanceFilter($request, true); // Call filter with default mode
    }
    
    
    
    public function dailyAttendanceFilter(Request $request, $default = false)
    {
        Log::info('Entered dailyAttendanceFilter method');
    
        if ($default || !$request->has(['month', 'year'])) {
            $month = date('M');
            $year = date('Y');
            Log::info('No filter, default month/year', compact('month', 'year'));
        } else {
            $month = $request->input('month');
            $year = $request->input('year');
            Log::info('Filtered month/year', compact('month', 'year'));
        }
    
        $date = '01 ' . $month . ' ' . $year;
        $timestamp = strtotime($date);
    
        $first_date = date('Y-m-01 00:00:00', $timestamp);
        $last_date  = date('Y-m-t 23:59:59', $timestamp);
    
        $page_data = [
            'attendance_date' => $first_date,
            'first_date'      => $first_date,
            'last_date'       => $last_date,
            'month'           => $month,
            'year'            => $year
        ];
    
        Log::info('Parsed attendance date:', $page_data);
    
        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
    
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $sections = Section::where('class_id', $student_data->class_id)->get();

        $attendance_of_students = DailyAttendances::where([
            'student_id' => auth()->user()->id,
            'class_id'   => $student_data->class_id,
            'section_id' => $student_data->section_id,
            'school_id'  => auth()->user()->school_id
        ])->whereBetween('timestamp', [$first_date, $last_date])->get();

    
        $no_of_users = $attendance_of_students->unique('student_id')->count();
    
        return view('student.attendance.daily_attendance', [
            'student_data'            => $student_data,
            'classes'                 => $classes,
            'sections'                => $sections,
            'page_data'               => $page_data,
            'attendance_of_students'  => $attendance_of_students,
            'no_of_users'             => $no_of_users,
        ]);
    }
    /* 

    public function dailyAttendanceFilter(Request $request, $default = false)
    {
        Log::info('Entered dailyAttendanceFilter method');
    
        if ($default || !$request->has(['month', 'year'])) {
            $month = date('M');
            $year = date('Y');
            Log::info('No filter, default month/year', compact('month', 'year'));
        } else {
            $month = $request->input('month');
            $year = $request->input('year');
            Log::info('Filtered month/year', compact('month', 'year'));
        }
    
        $date = '01 ' . $month . ' ' . $year;
        $timestamp = strtotime($date);
    
        $first_date = date('Y-m-01 00:00:00', $timestamp);
        $last_date  = date('Y-m-t 23:59:59', $timestamp);
    
        $page_data = [
            'attendance_date' => $first_date,
            'first_date'      => $first_date,
            'last_date'       => $last_date,
            'month'           => $month,
            'year'            => $year
        ];
    
        Log::info('Parsed attendance date:', $page_data);
    
        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
    
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $sections = Section::where(['class_id' => $student_data['class_id']])->get();
    
        $attendance_of_students = DailyAttendances::where([
            'student_id' => auth()->user()->id,
            'class_id'   => $student_data['class_id'],
            'section_id' => $student_data['section_id'],
            'school_id'  => auth()->user()->school_id
        ])
        ->whereBetween('timestamp', [$first_date, $last_date])
        ->get();
    
        $no_of_users = $attendance_of_students->unique('student_id')->count();
    
        return view('student.attendance.daily_attendance', [
            'student_data'            => $student_data,
            'classes'                 => $classes,
            'sections'                => $sections,
            'page_data'               => $page_data,
            'attendance_of_students'  => $attendance_of_students,
            'no_of_users'             => $no_of_users,
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

    /**
     * Show the routine.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     
    /* 
    public function routine()
    {
        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
        $class_id = $student_data['class_id'];
        $section_id = $student_data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('student.routine.routine', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }
    */
    public function routine()
    {
        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
    
        // Use object access instead of array
        $class_id = $student_data->class_id;
        $section_id = $student_data->section_id;
    
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
    
        return view('student.routine.routine', [
            'class_id' => $class_id,
            'section_id' => $section_id,
            'classes' => $classes
        ]);
    }


    /**
     * Show the subject list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     /*
    public function subjectList()
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
        $subjects = Subject::where('class_id', $student_data['class_id'])
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $active_session)
            ->paginate(10);

        return view('student.subject.subject_list', compact('subjects'));
    }
    */
    
    public function subjectList()
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
    
        $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
    
        // ✅ Use object property access
        $subjects = Subject::where('class_id', $student_data->class_id)
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $active_session)
            ->paginate(10);
    
        return view('student.subject.subject_list', compact('subjects'));
    }


    /**
     * Show the syllabus.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     /*
    public function syllabus()
    {
        if (auth()->user()->role_id != "" && auth()->user()->role_id == 7) {
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
            $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);

            $syllabuses = Syllabus::where(['class_id' => $student_data['class_id'], 'section_id' => $student_data['section_id'], 'session_id' => $active_session, 'school_id' => auth()->user()->school_id])->paginate(10);

            return view('student.syllabus.syllabus', compact('syllabuses'));
        } else {
            return redirect('login')->with('error', "Please login first.");
        }
    }
    */
    
    public function syllabus()
    {
        if (auth()->user()->role_id != "" && auth()->user()->role_id == 7) {
    
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
    
            $student_data = (new CommonController)->get_student_details_by_id(auth()->user()->id);
    
            $syllabuses = Syllabus::where([
                'class_id'   => $student_data->class_id,
                'section_id' => $student_data->section_id,
                'session_id' => $active_session,
                'school_id'  => auth()->user()->school_id
            ])->paginate(10);
    
            return view('student.syllabus.syllabus', compact('syllabuses'));
    
        } else {
            return redirect('login')->with('error', "Please login first.");
        }
    }


    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     /*
    public function marks($value = '')
    {
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $user_id = auth()->user()->id;
        $student_details = (new CommonController)->get_student_details_by_id($user_id);

        $subjects = Subject::where(['class_id' => $student_details['class_id'], 'school_id' => auth()->user()->school_id])->get();
    

        return view('student.marks.index', ['exam_categories' => $exam_categories, 'student_details' => $student_details, 'subjects' => $subjects]);
    }
    */

    public function gradeList()
    {
        $grades = Grade::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('student.grade.grade_list', compact('grades'));
    }
    
    
    public function marks($value = '')
    {
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $user_id = auth()->user()->id;
        $student_details = (new CommonController)->get_student_details_by_id($user_id); // as object
    
        $subjects = Subject::where([
            'class_id' => $student_details->class_id,
            'school_id' => auth()->user()->school_id
        ])->get();
    
        return view('student.marks.index', [
            'exam_categories' => $exam_categories,
            'student_details' => $student_details,
            'subjects' => $subjects
        ]);
    }
    
    
    
    
     /**
     * Generate Report Card
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function generateReportCard($exam_category_id)
    {
        $student_id = auth()->user()->id;
        $student = User::findOrFail($student_id);
        $school = School::find($student->school_id);
    
        // Get running session
        $session_id = get_school_settings($student->school_id)->value('running_session');
        $session = Session::find($session_id);
        $session_title = $session ? $session->session_title : 'Unknown Session';
    
        // Gradebook entry
        $gradebook = Gradebook::where([
            'student_id' => $student_id,
            'school_id' => $student->school_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id
        ])->first();
    
        if (!$gradebook) {
            return back()->with('error', 'No marks found.');
        }
    
        $marks = json_decode($gradebook->marks, true) ?? [];
        $total_marks_obtained = array_sum($marks);
    
        // Subject total marks
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
    
            $subject_mark = $exam ? $exam->total_marks : 100;
            $subject_total_marks[$subject_id] = $subject_mark;
            $max_total_marks += $subject_mark;
        }
    
        $percentage = $max_total_marks > 0 ? ($total_marks_obtained / $max_total_marks) * 100 : 0;
    
        $exam_category = ExamCategory::find($exam_category_id);
        $exam_name = $exam_category ? $exam_category->name : 'Unknown Exam';
    
        $class = Classes::find($gradebook->class_id);
        $section = Section::find($gradebook->section_id);
    
        $school_logo = null;
        if ($school && $school->school_logo) {
            $logo_path = public_path('assets/uploads/school_logo/' . $school->school_logo);
            if (file_exists($logo_path)) {
                $school_logo = str_replace('\\', '/', $logo_path);
            }
        }
    
        $grade = $this->calculateGrade($percentage);
    
        $data = [
            'school_name' => $school->title ?? 'Unknown School',
            'school_logo' => $school_logo,
            'session_title' => $session_title,
            'student_name' => $student->name,
            'class_name' => $class ? $class->name : '',
            'section_name' => $section ? $section->name : '',
            'exam_name' => $exam_name,
            'marks' => $marks,
            'subject_total_marks' => $subject_total_marks,
            'total_marks_obtained' => $total_marks_obtained,
            'total_marks' => $max_total_marks,
            'percentage' => round($percentage, 2),
            'grade' => $grade,
            'current_date' => date('d-m-Y'),
            'gradebook' => $gradebook,
        ];
    
        $pdf = Pdf::loadView('student.marks.report_card', $data);
        return $pdf->download('report_card_' . str_replace(' ', '_', $student->name) . '.pdf');
    }
    
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        elseif ($percentage >= 80) return 'A';
        elseif ($percentage >= 70) return 'B';
        elseif ($percentage >= 60) return 'C';
        elseif ($percentage >= 50) return 'D';
        elseif ($percentage >= 40) return 'E';
        else return 'F';
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

        return view('student.book.list', compact('books', 'search'));
    }

    public function bookIssueList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {

            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                ->where('issue_date', '<=', $date_to)
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $active_session)
                ->get();

            return view('student.book.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        } else {

            $date_from = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
            $date_to = strtotime(date('d-M-Y') . ' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                ->where('issue_date', '<=', $date_to)
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $active_session)
                ->get();

            return view('student.book.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    /**
     * Show the noticeboard list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

        return view('student.noticeboard.noticeboard', ['events' => $events]);
    }

    public function editNoticeboard($id = "")
    {
        $notice = Noticeboard::find($id);
        return view('student.noticeboard.edit', ['notice' => $notice]);
    }

    /**
     * Show the live class.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function FeeManagerList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $student_class_information = Enrollment::where('user_id', auth()->user()->id)->first()->toArray();

        if (count($request->all()) > 0) {


            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $selected_status = $data['status'];

            if ($selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('student_id', auth()->user()->id)->where('session_id', $active_session)->get();
            } else if ($selected_status == "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('student_id', auth()->user()->id)->where('session_id', $active_session)->get();
            }


            return view('student.fee_manager.student_fee_manager', ['invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to,  'selected_status' => $selected_status]);
        } else {

            $date_from = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
            $date_to = strtotime(date('d-M-Y') . ' 23:59:59');
            $selected_status = "";

            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('student_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();

            return view('student.fee_manager.student_fee_manager', ['invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to,  'selected_status' => $selected_status]);
        }
    }

    public function feeManagerExport($date_from = "", $date_to = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');


        if ($selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('student_id', auth()->user()->id)->where('session_id', $active_session)->get();
        } else if ($selected_status == "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('student_id', auth()->user()->id)->where('session_id', $active_session)->get();
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

    public function FeePayment(Request $request, $id)
    {

        $fee_details = StudentFeeManager::where('id', $id)->first()->toArray();
        $user_info = User::where('id', $fee_details['student_id'])->first()->toArray();
        return view('student.payment.payment_gateway', ['fee_details' => $fee_details, 'user_info' => $user_info]);
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


   

    public function offlinePaymentStudent(Request $request, $id = "")
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
                'document_image' => $data['document_image'],
                'payment_method' => 'offline'
            ]);





            return redirect()->route('student.fee_manager.list')->with('message', 'offline payment requested successfully');
        } else {
            return redirect()->route('student.fee_manager.list')->with('message', 'offline payment requested fail');
        }
    }

    function profile(){
        return view('student.profile.view');
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
        
        return redirect(route('student.profile'))->with('message', get_phrase('Profile info updated successfully'));
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

            return redirect(route('student.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('student.profile.password');
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

        return view('student.events.events', compact('events', 'search'));
    }

    function complain(){
        return view('student.complain.complain');
    }
    function complainUser(Request $request){
        $data = $request->all();

        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['receiver'] = $data['receiver'];
        return view('student.complain.complainUser', ['page_data' => $page_data]);
   }

    function receivers(Request $request){
        $data = $request->all();

        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['receiver'] = $data['receiver'];
        return view('student.complain.complain', ['page_data' => $page_data]);
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
                            <a href="' . route('student.message.messagethrades', ['id' => $user->id]).'">
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
        
        return view('student.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
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
                return view('student.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
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
                        <a href="' . route('student.message.messagethrades', ['id' => $user->id]).'">
                            <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                            <span class="ms-3">' . $user->name . '</span>
                        </a>
                    </div>
                ';
            }

            return response()->json($html);
        }

        // Pass the data to the view only if msg_user_details is not null
        return view('student.message.chat_empty');
    }

   
}
