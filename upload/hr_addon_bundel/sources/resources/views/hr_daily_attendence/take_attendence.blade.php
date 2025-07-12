<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_roles;
?>

<Style>

    .form_stryle{
        min-width: 300px; max-width: 400px;
    }
    .row_style{
        margin-left: 2px;
    }
    .style_colour{
        color: #fff;
    }
    .disp_n{
        display: none;
    }
</Style>

<form method="POST" class="d-block ajaxForm responsive_media_query form_stryle" action="{{ route('hr.hr_take_attendance') }}" >

    @csrf
    

    <div class="form-group row">
        <div class="fpb-7">
            <label for="date_on_taking_attendance" class="eForm-label">
                {{ get_phrase('Date') }}
            </label>
            <input type="text" class="form-control eForm-control inputDate" id="date_on_taking_attendance" name="date" value="{{ date('m/d/Y') }}" required>
        </div>

        <div class="fpb-7">
            <label for="role_id_on_taking_attendance" class="eForm-label">
                {{ get_phrase('Role') }}
            </label>
            <select name="role_id" id="role_id_on_taking_attendance" class="form-select eForm-select eChoice-multiple-with-remove" onchange="roleWiseTakingAttendance(this.value)" required>
                <option value="">
                    {{ get_phrase('Select a role') }}
                </option>
                <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                <?php foreach ($roles as $role): ?>
                <option value="{{ $role['id'] }}">
                    {{ get_phrase(ucfirst($role['name'])) }}
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row row_style mt-2" id="user_content" >
    </div>
    <br>

    <div class='row'>
        <div class="form-group col-md-12" id="showUserDiv">
            <a class="btn btn-block btn-secondary style_colour" onclick="getUserList()"  disabled>
                {{ get_phrase('Show user list') }}
            </a>
        </div>
    </div>
    <div class="form-group col-md-12 mt-2 disp_n" id="updateAttendanceDiv" >
        <button class="btn w-100 btn-primary" type="submit">
            {{ get_phrase('Update attendance') }}
        </button>
    </div>
</form>


<script type="text/javascript">
    // "use strict";

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

    $(".ajaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, getHrDailtyAttendance);
    });

    $('document').ready(function(){


        $('#date_on_taking_attendance').change(function(){
            $('#showUserDiv').show();
            $('#updateAttendanceDiv').hide();
            $('#user_content').hide();
        });
        $('#role_id_on_taking_attendance').change(function(){
            $('#showUserDiv').show();
            $('#updateAttendanceDiv').hide();
            $('#user_content').hide();
        });
    });

    function roleWiseTakingAttendance(role_id) {
        var url = "{{ route('hr.roleWiseUser', ['id' => ":role_id"]) }}";
        url = url.replace(":role_id", role_id);
        $.ajax({
            url: url,
            success: function(response){
                $('').html(response);


        }
        });
    }

       function getUserList() {
        var date = $('#date_on_taking_attendance').val();
        var role_id = $('#role_id_on_taking_attendance').val();

        if(date != '' && role_id != ''){
            $.ajax({
                url : '{{ route('hr.roleWiseUserlist') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {date : date, role_id : role_id},
                success : function(response) {
                    $('#user_content').show();
                    $('#user_content').html(response);
                    $('#showUserDiv').hide();
                    $('#updateAttendanceDiv').show();
                }
            });
        }else{
            toastr.error('{{ get_phrase("please_select_in_all_fields !") }}');
        }
    }

    $(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });


</script>
