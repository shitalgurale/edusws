@extends('student.navigation')

@section('content')

<style>
    .grade_content {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }
</style>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Grades') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Examination') }}</a></li>
                        <li><a href="#">{{ get_phrase('Grades') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap pb-2">
            @if(count($grades) > 0)
            <div class="d-flex justify-content-end mb-3">
                <div class="dropdown">
                    <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="pr-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
                                <path d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z" transform="translate(0 12.31) rotate(-90)" fill="#00a3ff"/>
                            </svg>
                        </span>
                        {{ get_phrase('Export') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                        <li><a class="dropdown-item" id="pdf" href="javascript:;" onclick="generatePDF()">{{ get_phrase('PDF') }}</a></li>
                        <li><a class="dropdown-item" id="print" href="javascript:;" onclick="ePrintDiv('grade_report')">{{ get_phrase('Print') }}</a></li>
                    </ul>
                </div>
            </div>
            @endif

            @if(count($grades) > 0)
            <div class="grade_content" id="grade_report">
                <div class="table-responsive">
                    <table class="table eTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ get_phrase('Grade') }}</th>
                                <th>{{ get_phrase('Grade Point') }}</th>
                                <th>{{ get_phrase('Mark From') }}</th>
                                <th>{{ get_phrase('Mark Upto') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $key => $grade)
                            <tr>
                                <td>{{ $grades->firstItem() + $key }}</td>
                                <td>{{ $grade->name }}</td>
                                <td>{{ $grade->grade_point }}</td>
                                <td>{{ $grade->mark_from }}</td>
                                <td>{{ $grade->mark_upto }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {!! $grades->appends(request()->all())->links() !!}
                </div>
            </div>
            @else
            <div class="empty_box center">
                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                <br>
                <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
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
    const element = document.getElementById("grade_report");
    var clonedElement = element.cloneNode(true);
    $(clonedElement).css("display", "block");

    var opt = {
        margin: 1,
        filename: 'grade-sheet.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 }
    };

    html2pdf().set(opt).from(clonedElement).save();
    clonedElement.remove();
}
</script>