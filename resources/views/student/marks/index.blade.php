<?php 

use App\Models\Subject;
use App\Models\Session;
use App\Models\Gradebook;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$index = 0;

?>

@extends('student.navigation')
   
@section('content')

<style>
    .table th, .table td {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('View Marks') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Examination') }}</a></li>
                        <li><a href="#">{{ get_phrase('Marks') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export button and table container -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <!-- Export Button -->
            <div class="d-flex justify-content-end mb-3">
                <div class="dropdown">
                    <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="pr-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
                                <path id="arrow-right-from-bracket-solid" d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z" transform="translate(0 12.31) rotate(-90)" fill="#00a3ff"/>
                            </svg>
                        </span>
                        {{ get_phrase('Export') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                        <li><a class="dropdown-item" id="pdf" href="javascript:;" onclick="generatePDF()">{{ get_phrase('PDF') }}</a></li>
                        <li><a class="dropdown-item" id="print" href="javascript:;" onclick="ePrintDiv('mark_report')">{{ get_phrase('Print') }}</a></li>
                    </ul>
                </div>
            </div>

            <!-- Mark Report Card -->
            <div class="p-0" id="mark_report" style="background-color: transparent; box-shadow: none; border: none;">

                <div class="table-responsive">
                   <table class="table eTable align-middle text-center border-0">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ get_phrase('Subject name') }}</th>
            @foreach($exam_categories as $exam_category)
                <th>{{ $exam_category->name }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($subjects as $subject)
        <tr>
            <td>{{ ++$index }}</td>
            <td>{{ $subject->name }}</td>
            @foreach($exam_categories as $exam_category)
            <td>
                <?php 
                    $exam_marks = \App\Models\Gradebook::where('exam_category_id', $exam_category->id)
                        ->where('class_id', $student_details->class_id)
                        ->where('section_id', $student_details->section_id)
                        ->where('student_id', $student_details->user_id)
                        ->where('school_id', auth()->user()->school_id)
                        ->where('session_id', $active_session)
                        ->first();

                    $subject_list = isset($exam_marks->marks) ? json_decode($exam_marks->marks, true) : [];
                ?>
                @if(is_array($subject_list) && array_key_exists($subject->id, $subject_list))
                    {{ $subject_list[$subject->id] }}
                @else
                    -
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
        <tr>
            <td>{{ ++$index }}</td>
            <td>{{ get_phrase('Report Card') }}</td>
            @foreach($exam_categories as $exam_category)
                <td>
                    <a href="{{ route('student.download_report_card', ['exam_category_id' => $exam_category->id]) }}"
                       class="btn btn-primary btn-sm">
                        {{ get_phrase('Report Card') }}
                    </a>
                </td>
            @endforeach
        </tr>
    </tbody>
</table>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection

<script type="text/javascript">
"use strict";

function ePrintDiv(ePrintDivId) {
    var printContents = document.getElementById(ePrintDivId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function generatePDF() {
    const element = document.getElementById("mark_report");
    var clonedElement = element.cloneNode(true);
    $(clonedElement).css("display", "block");

    var opt = {
        margin: 1,
        filename: 'Mark report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 }
    };

    html2pdf().set(opt).from(clonedElement).save();
    clonedElement.remove();
}
</script>
