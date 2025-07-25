<?php
use App\Models\User;
use App\Models\Gradebook;

?>
                <style>
 td {
    width: 150px;
    border: 1px solid #797c8b;
    padding: 10px;
}
</style>

<div class="mark_report_content mark_report" id="mark_history">
    <!--<h4 style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">
        {{ get_phrase('Exam Mark') }}
    </h4>-->
    <div style="text-align: center; margin-top: 20px; margin-bottom: 20px;">
    <h2 style="margin: 0;">{{ get_phrase('Exam Marks Report') }}</h2>
    <p style="margin: 5px 0;">{{ get_phrase('Class') }}: {{ $marks_data['class_name'] ?? 'N/A' }}</p>
    <p style="margin: 5px 0;">{{ get_phrase('Section') }}: {{ $marks_data['section_name'] ?? 'N/A' }}</p>
    <p style="margin: 5px 0;">{{ get_phrase('Subject') }}: {{ $marks_data['subject_name'] ?? 'N/A' }}</p>
    <p style="margin: 5px 0;">{{ get_phrase('Session') }}: {{ $marks_data['session_title'] ?? 'N/A' }}</p>
</div>

    <table class="table eTable eTable-2 table-bordered">
        <thead>
            <tr>
                <th scope="col">{{ get_phrase('Student name') }}</th>
                <th scope="col">{{ get_phrase('Mark') }}</th>
                <th scope="col">{{ get_phrase('Grade point') }}</th>
                <th scope="col">{{ get_phrase('Comment') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enroll_students as $enroll_student)
                <?php
                

                $student_details = User::find($enroll_student->user_id);

                $filtered_data = Gradebook::where('exam_category_id', $marks_data['exam_category_id'])
                    ->where('class_id', $marks_data['class_id'])
                    ->where('section_id', $marks_data['section_id'])
                    ->where('session_id', $marks_data['session_id'])
                    ->where('student_id', $enroll_student->user_id);

                $user_marks = 0;
                $comment = '';

                if ($filtered_data->value('marks')) {
                    $subject_mark = json_decode($filtered_data->value('marks'), true);
                    if (!empty($subject_mark[$marks_data['subject_id']])) {
                        $user_marks = $subject_mark[$marks_data['subject_id']];
                    }
                }

                if ($filtered_data->value('comment')) {
                    $comment = $filtered_data->value('comment');
                }
                ?>
                <tr>
                    <td>{{ $student_details->name ?? '' }}</td>
                    <td>
                        <span id="mark-{{ $enroll_student->user_id }}">{{ $user_marks }}</span>
                    </td>
                    <td>
                        <span id="grade-for-mark-{{ $enroll_student->user_id }}">{{ get_grade($user_marks) }}</span>
                    </td>
                    <td>
                        <span id="comment-{{ $enroll_student->user_id }}">{{ $comment }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
