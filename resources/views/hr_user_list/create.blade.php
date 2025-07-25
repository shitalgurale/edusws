<form method="POST" class="d-block ajaxForm" action="{{ route('hr.create_user_post') }}" enctype='multipart/form-data'>
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">
                {{ get_phrase('User name') }}
            </label>
            <input type="text" class="form-control eForm-control" id="name" name="name" required>

        </div>


        <div class="fpb-7">
            <label for="bioid" class="eForm-label">
                {{ get_phrase('Bio ID') }}
            </label>
            <input type="number" class="form-control eForm-control" id="bioid" name="emp_bioid" value="{{ old('emp_bioid') }}" required>
            
        </div>


        <div class="fpb-7">

            <label for="email" class="eForm-label">
                {{ get_phrase('Email') }}
            </label>
            <input type="email" class="form-control eForm-control" name="email" value="" required />

        </div>



        <div class="fpb-7">
            <label for="gender" class="eForm-label">{{ get_phrase('Gender') }}</label>
            <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">{{ get_phrase('Select a gender') }}</option>
                <option value="Male">{{ get_phrase('Male') }}</option>
                <option value="Female">{{ get_phrase('Female') }}</option>
                <option value="Others">{{ get_phrase('Others') }}</option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="blood_group" class="eForm-label">{{ get_phrase('Blood group') }}</label>
            <select name="blood_group" id="blood_group" class="form-select eForm-select eChoice-multiple-with-remove">
                <option value="">{{ get_phrase('Select a blood group') }}</option>
                <option value="a+">{{ get_phrase('A+') }}</option>
                <option value="a-">{{ get_phrase('A-') }}</option>
                <option value="b+">{{ get_phrase('B+') }}</option>
                <option value="b-">{{ get_phrase('B-') }}</option>
                <option value="ab+">{{ get_phrase('AB+') }}</option>
                <option value="ab-">{{ get_phrase('AB-') }}</option>
                <option value="o+">{{ get_phrase('O+') }}</option>
                <option value="o-">{{ get_phrase('O-') }}</option>
            </select>

        </div>

        <div class="fpb-7">
            <label for="phone" class="eForm-label">
                {{ get_phrase('Phone') }}
            </label>
            <input type="text" class="form-control eForm-control" name="phone" value="">

        </div>

        <div class="fpb-7">
            <label for="address" class="eForm-label">
                {{ get_phrase('Address') }}
            </label>
            <input type="text" class="form-control eForm-control" name="address" value="">

        </div>

        <div class="fpb-7">
            <label for="role" class="eForm-label">{{ get_phrase('User role') }}</label>
            <select name="role_id" id="role_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="password_field(this.value);" required>
                <option value="">{{ get_phrase('select a role') }}</option>

                <?php foreach ($roles as $role): ?>
                <option value="{{ $role['id'] }}">
                    {{ get_phrase($role['name']) }}
                </option>
                <?php endforeach; ?>
            </select>

        </div>

        <div class="fpb-7 hidden" id="show_pass">
            <label for="password" class="eForm-label">
                {{ get_phrase('password') }}
            </label>
            <input type="password" name="password" class="form-control eForm-control" id="password">
        </div>


        <div class="fpb-7">
            <label for="salary" class="eForm-label">
                {{ get_phrase('Joining salary') }}
            </label>
            <input type="number" class="form-control eForm-control" name="joining_salary" value="">

        </div>



        


        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">
                {{ get_phrase('Create user') }}
            </button>
        </div>
    </div>
</form>

<script>

    "use strict";

    $(document).ready(function () {
        $(".eChoice-multiple-with-remove").select2();
    });

    $(document).ready(function () {

        $('#show_pass').hide();

    });

    $(".ajaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, showAllUsers);
    });



    function password_field(role_id)
      {
        let url = "{{ route('hr.role_check', ['id' => ":role_id"]) }}";
        url = url.replace(":role_id", role_id);
        var test;
        $.ajax({
            url: url,
            success: function(response){
                if(response == 'yes')
                {
                    $('#show_pass').show();

                }
            }
        });
    }

</script>
