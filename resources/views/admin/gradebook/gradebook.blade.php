<?php 

use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\School;
use App\Models\Gradebook;
use App\Models\Exam;

$index = 0;

?>

@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Gradebooks') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Academic') }}</a></li>
              <li><a href="#">{{ get_phrase('Gradebooks') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block ajaxForm" action="{{ route('admin.gradebook') }}">
                <div class="row mt-3">
                    <div class="col-md-2"></div>
                    <div class="col-md-2">
                        <label for="class_id" class="eForm-label">{{ get_phrase('Class') }}</label>
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                            <option value="">{{ get_phrase('Select a class') }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class_id == $class->id ?  'selected':'' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="section_id" class="eForm-label">{{ get_phrase('Section') }}</label>
                        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('First select a class') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="exam_category_id" class="eForm-label">{{ get_phrase('Exam') }}</label>
                        <select name="exam_category_id" id="exam_category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('Select an exam category') }}</option>
                            @foreach($exam_categories as $exam_category)
                                <option value="{{ $exam_category->id }}" {{ $exam_category_id == $exam_category->id ?  'selected':'' }}>{{ $exam_category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pt-2">
                        <button class="eBtn eBtn btn-secondary mt-4" type="submit" id="filter_routine">{{ get_phrase('Filter') }}</button>
                    </div>
                    <div class="table-responsive gradebook_content pt-4" id="gradebook_report">
                        @if(count($filter_list) > 0)
                            <table class="table eTable" id="gradebook_table">
                                <thead>
                                    <th>#</th>
                                    <th>{{ get_phrase('Student Name') }}</th>
                                    @foreach($subjects as $subject)
                                       <th>{{ $subject->name }}</th>
                                    @endforeach
                                    <th>{{ get_phrase('Report Card') }}</th>
                                </thead>
                                <tbody>
                                    @foreach($filter_list as $student)
                                    <?php 
                                        $gradebook = Gradebook::where([
                                            'student_id' => $student->student_id,
                                            'exam_category_id' => $exam_category_id, // ✅ Use selected exam
                                            'school_id' => auth()->user()->school_id,
                                            'session_id' => get_school_settings(auth()->user()->school_id)->value('running_session'),
                                        ])->first();

                                        $marks = $gradebook ? json_decode($gradebook->marks, true) : [];
                                    ?>
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <?php 
                                        $student_details = User::find($student->student_id);
                                        $school_name = School::where('id', $student_details->school_id)->value('title');
                                        $exam_name = Exam::where('id', $gradebook->exam_category_id ?? null)->value('name');
                                        ?>
                                        <td>{{ $student_details->name }}</td>
                                        @foreach($subjects as $subject)
                                            <td>{{ $marks[$subject->id] ?? '-' }}</td>
                                        @endforeach
                                        <td>
                                            <a href="{{ route('admin.download_report_card', ['student_id' => $student->student_id, 'exam_category_id' => $exam_category_id]) }}" class="btn btn-primary btn-sm">{{ get_phrase('Report Card') }}</a>

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty_box center">
                                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                                <br>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    "use strict";
    function classWiseSection(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
            }
        });
    }
</script>
@endsection
