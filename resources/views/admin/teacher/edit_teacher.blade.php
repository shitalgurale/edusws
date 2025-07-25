<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.teacher.update', ['id' => $user->id]) }}">
         @csrf 
        <div class="form-row">
            <div class="fpb-7">
                <label for="name" class="eForm-label">{{ get_phrase('Name') }}</label>
                <input type="text" class="form-control eForm-control" value="{{ $user->name }}" id="name" name = "name" required>
            </div>

            <div class="fpb-7">
                <label for="email" class="eForm-label">{{ get_phrase('Email') }}</label>
                <input type="email" class="form-control  eForm-control" value="{{ $user->email }}" id="email" name = "email" required>
            </div>
            <?php 
            $info = json_decode($user->user_information);
            ?>

            <div class="fpb-7">
                <label for="emp_bioid" class="eForm-label">{{ get_phrase('Bio ID') }}</label>
                <input type="text" class="form-control eForm-control" value="{{ isset($hrUser) ? $hrUser->emp_bioid : '' }}" id="emp_bioid" name="emp_bioid" required>
            </div> 

            <div class="fpb-7">
                <label for="department_id" class="eForm-label">{{ get_phrase("Department") }}</label>
                <select name="department_id" id="department_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase("Select a department") }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ $department['id'] == $user->department_id ?  'selected':'' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="fpb-7">
                <label for="designation" class="eForm-label">{{ get_phrase('Designation') }}</label>
                <input type="text" class="form-control eForm-control" value="{{ $user->designation }}" id="designation" name = "designation" required>
            </div>

            <div class="fpb-7">
                <label for="birthday" class="eForm-label">{{ get_phrase('Birthday') }}<span class="required"></span></label>
                <input type="text" class="form-control eForm-control inputDate" id="birthday" name="birthday" value="{{ date('m/d/Y', $info->birthday) }}" />
                </div>
            </div>

            <div class="fpb-7">
                <label for="gender" class="eForm-label">{{ get_phrase('Gender') }}</label>
                <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove"  required>
                    <option value="">{{ get_phrase('Select gender') }}</option>
                    <option value="Male" {{ $info->gender == 'Male' ?  'selected':'' }} >{{ get_phrase('Male') }}</option>
                    <option value="Female" {{ $info->gender == 'Female' ?  'selected':'' }}>{{ get_phrase('Female') }}</option>
                    <option value="Others" {{ $info->gender == 'Others' ?  'selected':'' }}>{{ get_phrase('Others') }}</option>
                </select>
            </div>
            <div class="fpb-7">
                <label for="phone" class="eForm-label">{{ get_phrase('Phone number') }}</label>
                <input type="text" class="form-control  eForm-control" value="{{ $info->phone }}" id="phone" name = "phone" required>
            </div>
            <div class="fpb-7">
                <label for="blood_group" class="eForm-label">{{ get_phrase('Blood group') }}</label>
                <select name="blood_group" id="blood_group" class="form-select eForm-control">
                    <option value="">{{ get_phrase('Select a blood group') }}</option>
                    <option value="a+" {{ $info->blood_group == 'a+' ?  'selected':'' }} >{{ get_phrase('A+') }}</option>
                    <option value="a-" {{ $info->blood_group == 'a-' ?  'selected':'' }} >{{ get_phrase('A-') }}</option>
                    <option value="b+" {{ $info->blood_group == 'b+' ?  'selected':'' }} >{{ get_phrase('B+') }}</option>
                    <option value="b-" {{ $info->blood_group == 'b-' ?  'selected':'' }} >{{ get_phrase('B-') }}</option>
                    <option value="ab+" {{ $info->blood_group == 'ab+' ?  'selected':'' }} >{{ get_phrase('AB+') }}</option>
                    <option value="ab-" {{ $info->blood_group == 'ab-' ?  'selected':'' }} >{{ get_phrase('AB-') }}</option>
                    <option value="o+" {{ $info->blood_group == 'o+' ?  'selected':'' }} >{{ get_phrase('O+') }}</option>
                    <option value="o-" {{ $info->blood_group == 'o-' ?  'selected':'' }} >{{ get_phrase('O-') }}</option>
                </select>
            </div>

            <div class="fpb-7">
                <label for="address" class="eForm-label">{{ get_phrase('Address') }}</label>
                <textarea class="form-control eForm-control" id="address" name = "address" rows="5" required>{{ $info->address }}</textarea>
            </div>
            <div class="fpb-7">
    <label for="photo" class="eForm-label">{{ get_phrase('Photo') }}</label>
    
    {{-- Upload input --}}
    <input class="form-control eForm-control-file" id="photo" name="photo" accept="image/*" type="file" onchange="previewPhoto(this)">
    
</div>

          <!--  <div class="fpb-7">
              <label for="formFile" class="eForm-label"
                >{{ get_phrase('Photo') }}</label
              >
              <input
                class="form-control eForm-control-file"
                id="photo" name="photo" accept="image/*"
                type="file"
              />
            </div>-->

            <div class="fpb-7">
                <label for="joining_salary" class="eForm-label">{{ get_phrase('Joining Salary') }}</label>
                <input type="number" class="form-control eForm-control" id="joining_salary" value="{{ isset($hrUser) ? $hrUser->joining_salary : '' }}" name="joining_salary" required>
            </div>

            <div class="form-group mt-2 col-md-12">
                <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";
    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

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