<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_roles;




?>

<form method="POST" class="d-block ajaxForm" action="{{ route('hr.add_update_delete_leave_request', ['action' =>'add_by_admin', 'id' => 1]) }}">
    @csrf
    
    <div class="form-row">

        <div class="fpb-7">
            <label for="role" class="eForm-label">
                {{ get_phrase('Roles') }}
            </label>
            <select name="role_id" id="role_id" class="form-select eForm-control" required onchange="roleWiseUser(this.value)">
                <option value="">
                    {{ get_phrase('Select a role') }}
                </option>
                <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                <?php foreach ($roles as $role): ?>
                <option value="{{ $role['id'] }}">
                    {{ get_phrase($role['name']) }}
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fpb-7">
            <label for="name" class="eForm-label">
                {{ get_phrase('User name') }}
            </label>
            <select name="user_id" id="user_id" class="form-select eForm-control" required>
                <option value="">
                    {{ get_phrase('Select a user') }}
                </option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="start_date" class="eForm-label">
                {{ get_phrase('Start date') }}<span class="required">*</span>
            </label>
            <input type="text" class="form-control eForm-control inputDate" id="start_date" name="start_date" value="{{ date('m/d/Y') }}" />
        </div>

        <div class="fpb-7">
            <label for="end_date" class="eForm-label">
                {{ get_phrase('End date') }}<span class="required">*</span>
            </label>
            <input type="text" class="form-control eForm-control inputDate" id="end_date" name="end_date" value="{{ date('m/d/Y') }}" />
        </div>

        <div class="fpb-7">
            <label for="field-1" class="eForm-label">
                {{ get_phrase('Reason') }}
            </label>
            <textarea class="form-control eForm-control" name="reason" rows="3" required></textarea>

        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">
                {{ get_phrase('Submit') }}
            </button>
        </div>
    </div>
</form>

<script>
    "use strict";
    function roleWiseUser(role_id) {
        let url = "{{ route('hr.roleWiseUser', ['id' => ":role_id"]) }}";
        url = url.replace(":role_id", role_id);
        $.ajax({
            url: url,
            success: function(response){
                $('#user_id').html(response);


        }
        });
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
