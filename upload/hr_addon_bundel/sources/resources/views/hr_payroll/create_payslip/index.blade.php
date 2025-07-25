<!-- start page title-->


@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <h4>
          {{ get_phrase('Create payslip') }}
        </h4>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap">
      <div class="row mt-3">
        <div class="col-md-1"></div>
        <div class="col-md-2 mb-1">
          <select name="role_id" id="role_id" class="form-select eForm-control " onchange="get_users_in_select_box(this.value);" required>
            <option value="">
              {{ get_phrase('Select a role') }}
            </option>
            <?php
            foreach($roles as $row): ?>
            <option value="{{ $row['id'] }}">
              {{ get_phrase(ucfirst($row['name'])) }}
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2 mb-1">
          <select name="user_id" class="form-select eForm-control" id="user_id" required>
            <option value="">
              {{ get_phrase('Select a role first') }}
            </option>
          </select>
        </div>
        <div class="col-md-2 mb-1">
          <select name="month" id="month" class="form-select eForm-control" required>
            <option value="">
              {{ get_phrase('Select a month') }}
            </option>
            <option value="01" {{ date('M') == 'Jan' ? 'selected' :'' }}>
              {{ get_phrase('January') }}
            </option>
            <option value="02" {{ date('M') == 'Feb' ? 'selected' :'' }}>
              {{ get_phrase('February') }}
            </option>
            <option value="03" {{ date('M') == 'Mar' ? 'selected' :'' }}>
              {{ get_phrase('March') }}
            </option>
            <option value="04" {{ date('M') == 'Apr' ? 'selected' :'' }}>
              {{ get_phrase('April') }}
            </option>
            <option value="05" {{ date('M') == 'May' ? 'selected' :'' }}>
              {{ get_phrase('May') }}
            </option>
            <option value="06" {{ date('M') == 'Jun' ? 'selected' :'' }}>
              {{ get_phrase('June') }}
            </option>
            <option value="07" {{ date('M') == 'Jul' ? 'selected' :'' }}>
              {{ get_phrase('July') }}
            </option>
            <option value="08" {{ date('M') == 'Aug' ? 'selected' :'' }}>
              {{ get_phrase('August') }}
            </option>
            <option value="09" {{ date('M') == 'Sep' ? 'selected' :'' }}>
              {{ get_phrase('September') }}
            </option>
            <option value="10" {{ date('M') == 'Oct' ? 'selected' :'' }}>
              {{ get_phrase('October') }}
            </option>
            <option value="11" {{ date('M') == 'Nov' ? 'selected' :'' }}>
              {{ get_phrase('November') }}
            </option>
            <option value="12" {{ date('M') == 'Dec' ? 'selected' :'' }}>
              {{ get_phrase('December') }}
            </option>
          </select>
        </div>
        <div class="col-md-2 mb-1">
          <select name="year" id="year" class="form-select eForm-control" required>
            <option value="">
              {{ get_phrase('Select a year') }}
            </option>
            <?php for($year = 2015; $year <= date('Y'); $year++){ ?>
            <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' :'' }}>
              {{ $year }}
            </option>
            <?php } ?>

          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-block btn-secondary" onclick="payroll_create()">
            {{ get_phrase('Submit') }}
          </button>
        </div>
      </div>
      <div class="card-body payroll_content" id="payroll_content_id">

        <div class="empty_box center">
          <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
          <br>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
"use strict";
  function payroll_create(){
    var role_id = $('#role_id').val();
    var user_id = $('#user_id').val();
    var month = $('#month').val();
    var year = $('#year').val();
    if(month != "" && year != ""){
      showPayrollForm();
    }else{
      toastr.error('{{ get_phrase('Please select in all fields !') }}');
    }
  }

  function get_users_in_select_box(role_id)
  {


    if(role_id != '')
    {
        var role_id = $('#role_id').val();
        var url="{{ route('hr.get_user_by_role') }}";
        $.ajax({
            url: url,
            data:{role_id:role_id},
        success : function(response)
        {

          jQuery('#user_id').html(response);
        }
      });
    }
    else
      jQuery('#user_id').html('<option value="">{{ get_phrase("Select a role first") }}</option>');
  }

  var showPayrollForm = function () {
    var role_id = $('#role_id').val();
    var user_id = $('#user_id').val();
    var month = $('#month').val();
    var year = $('#year').val();
    console.log(role_id, user_id, month, year);
    if(role_id != "" && user_id != "" && month != "" && year != ""){
        var url="{{ route('hr.payroll_add_view') }}";
        console.log("here");

      $.ajax({
        type: 'GET',
        url: url,
        data: {role_id: role_id, user_id: user_id, month : month, year : year},
        success: function(response){
          $('.payroll_content').html(response);
     
        }
      });
    }
  }

</script>

@endsection
