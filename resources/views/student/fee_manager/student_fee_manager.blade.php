<?php 

if($date_from == "") {
    $date_from = strtotime(date('d-M-Y', strtotime(' -30 day')));
} else {
    $date_from = strtotime(date('d-M-Y', $date_from));
}

if($date_to == "") {
    $date_to = strtotime(date('d-M-Y'));
} else {
    $date_to = strtotime(date('d-M-Y', $date_to));
}

?>

@extends('student.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Fee Manager') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Fee Manager') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('student.fee_manager.list') }}">
            	<div class="row">
                    <div class="row justify-content-md-center">
                    	@if($date_from != "" && $date_to !="")
                            <div class="col-xl-3 mb-3">
                                <input type="text" class="form-control eForm-control" name="eDateRange"
                                    value="{{ date('m/d/Y', $date_from).' - '.date('m/d/Y', $date_to) }}" />
                            </div>
                        @else
		                    <div class="col-xl-3 mb-3">
                                <input type="text" class="form-control eForm-control" name="eDateRange"
                                    value="{{ date('m/d/Y', strtotime(' -30 day')).' - '.date('m/d/Y') }}" />
                            </div>
		                @endif

				        <div class="col-xl-2 mb-3">
                            <div class="form-group">
                                <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove">
                                    <option value="all" {{ $selected_status == "all" ? 'selected' : '' }}>{{ get_phrase('All status') }}</option>
                                    <option value="paid" {{ $selected_status == "paid" ? 'selected' : '' }}>{{ get_phrase('Paid') }}</option>
                                    <option value="partial" {{ $selected_status == "partial" ? 'selected' : '' }}>{{ get_phrase('Partial Paid') }}</option>
                                    <option value="unpaid" {{ $selected_status == "unpaid" ? 'selected' : '' }}>{{ get_phrase('	Never Paid') }}</option>
                                    <option value="pending" {{ $selected_status == "pending" ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                                </select>
                            </div>
                        </div>

				        
				        <div class="col-xl-2 mb-3">
				            <button type="submit" class="btn btn-icon btn-secondary form-control">{{ get_phrase('Filter') }}</button>
				        </div>
            		
            			@if(count($invoices) > 0)
				        <div class="col-md-2 ">
		                    <div class="position-relative">
		                      <button class="eBtn-3 dropdown-toggle float-end" type="button" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
		                        <span class="pr-10">
		                          <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
		                            <path id="arrow-right-from-bracket-solid" d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z" transform="translate(0 12.31) rotate(-90)" fill="#00a3ff"></path>
		                          </svg>
		                        </span>
		                        {{ get_phrase('Export') }}
		                      </button>
		                      <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
		                        <li>
		                            <a class="dropdown-item" id="pdf" href="javascript:;" onclick="generatePDF()">{{ get_phrase('PDF') }}</a>
		                        </li>
		                        <li>
		                            <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('student_fee_report')">{{ get_phrase('Print') }}</a>
		                        </li>
		                      </ul>
		                    </div>
		                </div>
	                    @endif
	                </div>
            	</div>
            </form>
            <div class="invoice_content" id="student_fee_manager">
            	@if(count($invoices) > 0)
	            	@include('student.fee_manager.list')
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
</div>

<script type="text/javascript">

  "use strict";

	function generatePDF() {

    	// Choose the element that our invoice is rendered in.
	    const element = document.getElementById("student_fee_report");

	    // clone the element
	    var clonedElement = element.cloneNode(true);

	    // change display of cloned element 
	    $(clonedElement).css("display", "block");

	    // Choose the clonedElement and save the PDF for our user.

		var opt = {
		  margin:       1,
		  filename:     'student_fee-{{ date('d-M-Y', $date_from).'-'.date('d-M-Y', $date_to) }}.pdf',
		  image:        { type: 'jpeg', quality: 0.98 },
		  html2canvas:  { scale: 2 }
		};

		// New Promise-based usage:
		html2pdf().set(opt).from(clonedElement).save();

	    // remove cloned element
	    clonedElement.remove();
	}

	function printableDiv(printableAreaDivId) {
        var printContents = document.getElementById(printableAreaDivId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>

@endsection
