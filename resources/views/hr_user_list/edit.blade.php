<form method="POST" class="d-block ajaxForm" id="edit_form" action="" enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">
                {{ get_phrase('User name') }}
            </label>
            <input type="text" class="form-control eForm-control" id="name" name="name" value="{{ $user['name'] }}" required>
            <input type="hidden" class="form-control eForm-control" id="user_id_for_edit" name="user_id_for_edit" value="{{ $user['id'] }}" required>
        </div>


        <div class="fpb-7">
            <label for="emp_bioid" class="eForm-label">
                {{ get_phrase('Bio ID') }}
            </label>
            <input type="number" class="form-control eForm-control" id="emp_bioid" name="emp_bioid" value="{{ $user['emp_bioid']}}" required>

        </div>


        <div class="fpb-7">
            <label for="email" class="eForm-label">
                {{ get_phrase('Email') }}
            </label>
            <input type="text" class="form-control eForm-control" id="email" name="email" value="{{ $user['email'] }}" required>

        </div>

        <div class="fpb-7">
            <label for="gender" class="eForm-label">
                {{ get_phrase('Gender') }}
            </label>
            <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">
                    {{ get_phrase('Select a gender') }}
                </option>
                <option value="male" {{ $user['gender'] == 'male' ? 'selected' :'' }}>
                    {{ 'Male' }}
                </option>
                <option value="female" {{ $user['gender'] == 'female' ? 'selected' :'' }}>
                    {{ 'Female' }}
                </option>
                <option value="other" {{ $user['gender'] == 'other' ? 'selected' :'' }}>
                    {{ 'Other' }}
                </option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="blood_group" class="eForm-label">
                {{ get_phrase('Blood group') }}
            </label>
            <select name="blood_group" id="blood_group" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">
                    {{ get_phrase('Select a blood group') }}
                </option>
                <option value="a+" {{ $user['blood_group'] == 'a+' ? 'selected' :'' }}>
                    {{ 'A+' }}
                </option>
                <option value="a-" {{ $user['blood_group'] == 'a-' ? 'selected' :'' }}>
                    {{ 'A-' }}
                </option>
                <option value="b+" {{ $user['blood_group'] == 'b+' ? 'selected' :'' }}>
                    {{ 'B+' }}
                </option>
                <option value="b-" {{ $user['blood_group'] == 'b-' ? 'selected' :'' }}>
                    {{ 'B-' }}
                </option>
                <option value="ab+" {{ $user['blood_group'] == 'ab+' ? 'selected' :'' }}>
                    {{ 'AB+' }}
                </option>
                <option value="ab-" {{ $user['blood_group'] == 'ab-' ? 'selected' :'' }}>
                    {{ 'AB-' }}
                </option>
                <option value="o+" {{ $user['blood_group'] == 'o+' ? 'selected' :'' }}>
                    {{ 'O+' }}
                </option>
                <option value="o-" {{ $user['blood_group'] == 'o-' ? 'selected' :'' }}>
                    {{ 'O-' }}
                </option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="phone" class="eForm-label">
                {{ get_phrase('Phone') }}
            </label>
            <input type="text" class="form-control eForm-control" id="phone" name="phone" value="{{ $user['phone'] }}" required>

        </div>

        <div class="fpb-7">
            <label for="address" class="eForm-label">
                {{ get_phrase('Address') }}
            </label>
            <input type="text" class="form-control eForm-control" id="address" name="address" value="{{ $user['address'] }}" required>

        </div>

        <div class="fpb-7">
            <label for="role" class="eForm-label">
                {{ get_phrase('Role') }}
            </label>
            <select name="aj_role_id" id="aj_role_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">
                    {{ get_phrase('Select a role') }}
                </option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $role->id == $user['role_id'] ? 'selected':'' }}>
                        {{ ucfirst($role['name']) }}
                    </option>
                @endforeach

            </select>

        </div>

        <div class="fpb-7">
            <label for="salary" class="eForm-label">
                {{ get_phrase('Salary') }}
            </label>
            <input type="number" class="form-control eForm-control" id="joining_salary" name="joining_salary" value="{{ $user['joining_salary'] }}" required>

        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" id="edituser" type="button">
                {{ get_phrase('Update Data') }}
            </button>
        </div>
    </div>
</form>

<script>
"use strict";

$(document).ready(function () {
  $(".eChoice-multiple-with-remove").select2();
});

$("#edituser").on("click",function(e){
               e.preventDefault();
            let form=new FormData(edit_form);

            let name= $("#name").val();
            let email= $("#email").val();
            let gender= $("#gender").val();
            let bloodgroup= $("#blood_group").val();
            let phone= $("#phone").val();
            let address= $("#address").val();
            let role_id= $("#aj_role_id").val();
            let joining_salary= $("#joining_salary").val();
            let user_id_for_edit= $("#user_id_for_edit").val();
            let emp_bioid = $("#emp_bioid").val();


            form.append("name",name);
            form.append("email",email);
            form.append("gender",gender);
            form.append("bloodgroup",bloodgroup);
            form.append("phone",phone);
            form.append("address",address);
            form.append("role_id",role_id);
            form.append("joining_salary",joining_salary);
            form.append("user_id_for_edit",user_id_for_edit);
            form.append("emp_bioid", emp_bioid);
            form.append("_token","{{ csrf_token() }}");

            console.log(role_id);



            var url = '{{ route("hr.user_lists_user_edit_post", ":id") }}';
            url = url.replace(':id', user_id_for_edit );

            $.ajax({
                url:url,
                type: "POST",
                data:form,
                contentType: false,
                processData: false,
                success : function(data){
                    filter_user();
                    $("#offcanvasScrollingRightBS").removeClass("show");
                        document.querySelectorAll(".offcanvas-backdrop").forEach(el => el.remove());
                        toastr.success("updated Successfully");


                }

            });

      })






</script>
