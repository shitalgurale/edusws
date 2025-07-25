{{-- Start button style  --}}
<style>
    .inport_btn {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 30px;
        padding: 12px 18px;
        border: 1px solid transparent;
        border-radius: 5px;
        background-color: #EFF2EF;
        font-size: 13px;
        font-weight: 500;
        color: #595d6f;
        transition: all 0.3s;
    }

    .inport_btn:hover {
        background-color: transparent;
        border-color: #DFDFE7;
        color: #6B708A;
    }

    .bottom-border {
        border-bottom: 2px solid #c9ccdb;
        border-top: 2px solid #c9ccdb;
        /* 2px solid black border at the bottom */
    }
</style>

{{-- End button style  --}}

<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('admin.offline_admission.create') }}">
        @csrf
        
        <!--  Shital  -->
         <div class="row fmb-14 justify-content-between align-items-center">
            <label for="birthdatepicker" class="col-sm-2 col-eForm-label">{{ get_phrase('Admission Date') }}<span
                    class="required"></span></label>
            <div class="col-md-10">
                <input type="text" class="form-control eForm-control" id="admission_date" name="admission_date" value="{{ date('m/d/Y') }}" />

            </div>
        </div>
        <!--  Shital  -->
        
        
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="name" class="col-sm-2 col-eForm-label">{{ get_phrase('Full Name') }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="text" class="form-control eForm-control" id="name" name="name" required>
            </div>
        </div>
        
        
        
        <!--  Shital  -->
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="name" class="col-sm-2 col-eForm-label">{{ get_phrase("Father's Name") }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="text" class="form-control eForm-control" id="father_name" name="father_name" required>
            </div>
        </div>
        <!--  Shital  -->
        
        
        <!--  Shital  -->
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="name" class="col-sm-2 col-eForm-label">{{ get_phrase("Mother's Name") }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="text" class="form-control eForm-control" id="mother_name" name="mother_name" required>
            </div>
        </div>
        <!--  Shital  -->

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="class_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Class') }}</label>
            <div class="col-md-10">
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    required onchange="classWiseSection(this.value)">
                    <option value="">{{ get_phrase('Select a class') }}</option>
                    @foreach ($data['classes'] as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="section_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Section') }}</label>
            <div class="col-md-10" id = "section_content">
                <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('Select section') }}</option>
                </select>
            </div>
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="email" class="col-sm-2 col-eForm-label">{{ get_phrase('Email') }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="email" class="form-control eForm-control" id="email" name="email" required>
            </div>
        </div>
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="password" class="col-sm-2 col-eForm-label">{{ get_phrase('Password') }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="password" class="form-control eForm-control" id="password" name="password" required>
            </div>
        </div>
        
         <!--Shital-->
         <div class="row fmb-14 justify-content-between align-items-center">
            <label for="bioid" class="col-sm-2 col-eForm-label">{{ get_phrase('Bio ID') }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="bioid" class="form-control eForm-control" id="bioid" name="bioid" required>
        </div>
        </div>
        <!--shital-->

        
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="birthdatepicker" class="col-sm-2 col-eForm-label">{{ get_phrase('Date of Birth') }}<span
                    class="required"></span></label>
            <div class="col-md-10">
                <input type="text" class="form-control eForm-control" id="eInputDate" name="eDefaultDateRange"
                    value="{{ date('m/d/Y') }}" />
            </div>
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="gender" class="col-sm-2 col-eForm-label">{{ get_phrase('Gender') }}</label>
            <div class="col-md-10">
                <select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('Select gender') }}</option>
                    <option value="Male">{{ get_phrase('Male') }}</option>
                    <option value="Female">{{ get_phrase('Female') }}</option>
                    <option value="Others">{{ get_phrase('Others') }}</option>
                </select>
            </div>
        </div>
        
         <!--Shital-->
         <div class="row fmb-14 justify-content-between align-items-center">
            <label for="nationality" class="col-sm-2 col-eForm-label">{{ get_phrase('Nationality') }}</label>
            <div class="col-sm-10 col-md-9 col-lg-10">
                <input type="text" class="form-control eForm-control" id="nationality" name="nationality" required>
        </div>
        </div>
        <!--shital-->
        
         <div class="row fmb-14 justify-content-between align-items-center">
            <label for="religion" class="col-sm-2 col-eForm-label">{{ get_phrase('Religion') }}</label>
            <div class="col-md-10">
                <select name="religion" id="religion" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('Select a Religion') }}</option>
                    <option value="Hindu">{{ get_phrase('Hindu') }}</option>
                    <option value="Islam">{{ get_phrase('Islam') }}</option>
                    <option value="Sikh">{{ get_phrase('Sikh') }}</option>
                    <option value="Buddh">{{ get_phrase('Buddh') }}</option>
                    <option value="Christian">{{ get_phrase('Christian') }}</option>
                    <option value="Jain">{{ get_phrase('Jain') }}</option>
                    <option value="Other">{{ get_phrase('Other') }}</option>
                </select>
            </div>
        </div>
     
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="cast" class="col-sm-2 col-eForm-label">{{ get_phrase('Cast') }}</label>
            <div class="col-md-10">
                <select name="cast" id="cast" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('Select a Caste') }}</option>
                    <option value="OC">{{ get_phrase('OC (Open/General Category)') }}</option>
                    <option value="BC">{{ get_phrase('BC (Backward Class)') }}</option>
                    <option value="SC">{{ get_phrase('SC (Scheduled Caste)') }}</option>
                    <option value="ST">{{ get_phrase('ST (Scheduled Tribe)') }}</option>
                    <option value="Other">{{ get_phrase('Other') }}</option>
                </select>
            </div>
        </div>
       
        

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="blood_group" class="col-sm-2 col-eForm-label">{{ get_phrase('Blood group') }}</label>
            <div class="col-md-10">
                <select name="blood_group" id="blood_group"
                    class="form-select eForm-select eChoice-multiple-with-remove" required>
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
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="address" class="col-sm-2 col-eForm-label">{{ get_phrase('Address') }}</label>
            <div class="col-md-10">
                <input type="text" class="form-control eForm-control" id="address" rows="4" name = "address" required>
            </div>
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="phone" class="col-sm-2 col-eForm-label">{{ get_phrase('Phone') }}</label>
            <div class="col-md-10">
                <input type="text" id="phone" name="phone" class="form-control eForm-control" required>
            </div>
        </div>

        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="photo" class="col-sm-2 col-eForm-label">{{ get_phrase('Student profile image') }}</label>
            <div class="col-md-10">
                <input class="form-control eForm-control-file" type="file" id="photo" name="photo"
                    accept="image/*">
            </div>
        </div>

        <hr style="background-color:#6B708A;">
        <div class="row fmb-14 justify-content-between align-items-center">
            <label for="student_info"
                class="col-sm-2 col-eForm-label">{{ get_phrase('Additional information') }}</label>
            <div class="col-md-10">
                <div class="">
                    <div class="fmb-14" id="inputContainer">
                        <input type="text" name="student_info[]" class="form-control eForm-control text-secondary"
                            placeholder="{{ get_phrase('Enter student information') }}">
                    </div>
                    <div class="col-sm-3 p-0 d-flex gap-2">
                        <button type="button" onclick="appendInput()" data-bs-placement="right"
                            data-bs-toggle="tooltip" title="Add more" class="inport_btn"><i
                                class="bi bi-plus"></i></button>
                        <button type="button" onclick="removeInput()" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Remove" class="inport_btn">
                            <i class="bi bi-dash"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <hr style="background-color:#6B708A;">

        <div class="row p-3">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn-form">{{ get_phrase('Add Student') }}</button>
            </div>
        </div>
    </form>
</div>

{{-- Start javasript --}}
<script type="text/javascript">
    "use strict";

    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });

    function togglepackageWiseOptions(interval) {
        if (interval === "life_time") {

            $("#days").hide();
            $('#limited').prop('disabled', true);
        } else {

            $("#days").show();
            $('#limited').prop('disabled', false);
        }
    }

    $(document).ready(function() {
        $("#unlimitedst").click(function() {
            $(".limitedst").hide();
        });
        $("#limited").click(function() {
            $(".limitedst").show();
            $("#studentLimit").attr('name', 'studentLimit');

        });
        $("#life_time").click(function() {
            $("#interval").hide();
        });
    });




    function appendInput() {
        var container = document.getElementById('inputContainer');
        var newInput = document.createElement('input');
        newInput.setAttribute('type', 'text');
        newInput.setAttribute('placeholder', '{{ get_phrase('Enter student information') }}');
        newInput.setAttribute('class', 'form-control eForm-control mt-2');
        newInput.setAttribute('name', 'student_info[]');
        container.appendChild(newInput);
    }

    function removeInput() {
        var container = document.getElementById('inputContainer');
        var inputs = container.getElementsByTagName('input');
        if (inputs.length > 1) {
            container.removeChild(inputs[inputs.length - 1]);
        }
    }


    $(document).ready(function () {
    function slugifyName(name) {
        return name.toLowerCase().replace(/[^a-z0-9]/g, ''); // removes spaces and special chars
    }

    function generateEmailAndPassword() {
        let name = $('#name').val();
        let classId = $('#class_id').val();
        if (name && classId) {
            let cleanedName = slugifyName(name);
            let email = cleanedName + '_' + classId + '@student.xyz';
            let password = cleanedName;

            $('#email').val(email);
            $('#password').val(password);
        }
    }

    // Trigger when name or class is changed
    $('#name, #class_id').on('input change', function () {
        generateEmailAndPassword();
    });
});

</script>
