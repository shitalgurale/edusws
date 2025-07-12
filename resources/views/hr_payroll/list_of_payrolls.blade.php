<?php
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\CommonController;
use App\Models\Addon\HrDailyAttendence;



?>
@extends('admin.navigation')

@section('content')


<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Payslip list') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Human Resource') }}</a></li>
              <li><a href="#">{{ get_phrase('Payroll') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">

            <a href="{{ route('hr.create_payslip') }}" class="export_btn" >
                {{ get_phrase('Create payslip') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>


<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <div class="row mt-3">
                <div class="col-md-3"></div>
                <div class="col-md-2 mb-1">
                    <select name="month" id="month" class="form-select eForm-control" required>


                        <option value="{{ $filtered_month }}" {{ 'selected'  }}>
                            {{ date("F", mktime(0, 0, 0,$filtered_month, 10)) }}
                        </option>
                        <option value="01">
                            {{ get_phrase('january') }}
                        </option>
                        <option value="02">
                            {{ get_phrase('february') }}
                        </option>
                        <option value="03">
                            {{ get_phrase('march') }}
                        </option>
                        <option value="04">
                            {{ get_phrase('april') }}
                        </option>
                        <option value="05">
                            {{ get_phrase('may') }}
                        </option>
                        <option value="06">
                            {{ get_phrase('june') }}
                        </option>
                        <option value="07">
                            {{ get_phrase('july') }}
                        </option>
                        <option value="08">
                            {{ get_phrase('august') }}
                        </option>
                        <option value="09">
                            {{ get_phrase('september') }}
                        </option>
                        <option value="10">
                            {{ get_phrase('october') }}
                        </option>
                        <option value="11">
                            {{ get_phrase('november') }}
                        </option>
                        <option value="12">
                            {{ get_phrase('december') }}
                        </option>

                    </select>
                </div>
                <div class="col-md-2 mb-1">
                    <select name="year" id="year" class="form-select eForm-control" required>


                        <option value="{{ $filtered_year }}">
                            {{ $filtered_year }}
                        </option>
                        <?php for($yr = 2015; $yr <= date('Y'); $yr++){ ?>
                        <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' :'' }}>
                            {{ $yr }}
                        </option>
                        <?php } ?>

                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-block btn-secondary" onclick="filter_payroll()">
                        {{ get_phrase('Filter') }}
                    </button>
                </div>
            </div>
            <div class="card-body payroll_content">
                <div class="empty_box text-center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/custom/js/191jquery.min.js') }}"></script>
<script>
    "use strict";
    function  filter_payroll(){
  var month =  document.getElementById("month").value;
  var year =  document.getElementById("year").value;
  if(month != "" && year != ""){
    showAllPayroll()
  }else{
    toastr.error('{{ get_phrase('please_select_in_all_fields !') }}');
  }
}

var showAllPayroll = function () {
  var month =  document.getElementById("month").value;
  var year =  document.getElementById("year").value;






  var url="{{ route('hr.payrolls_details') }}";
  console.log(month, year);
  if(month != "" && year != ""){
    $.ajax({
      url: url,
      data: {month : month, year : year},
      success: function(response){
        $('.payroll_content').html(response);

      }
    });
  }
}

$(document).ready(function () {
    filter_payroll();
    });




</script>
@endsection
