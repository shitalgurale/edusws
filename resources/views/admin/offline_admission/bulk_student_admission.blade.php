<form method="POST" class="col-md-12 ajaxForm" action="{{ route('admin.offline_admission.bulk_create') }}" id="student_admission_form" enctype="multipart/form-data">
    @csrf
    <div class="row justify-content-md-center">
        <div class="col-xl-4">
            <select name="class_id" id="class_id" class="form-select" onchange="classWiseSection(this.value)" required>
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($data['classes'] as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-4" id="section_content">
            <select name="section_id" id="section_id" class="form-select" required>
                <option value="">{{ get_phrase('Select section') }}</option>
            </select>
        </div>
    </div>

    <div id="first-row">
        <div class="row student-row">
            <div class="col-xl-11">
                <div class="row">
                    <input type="text" name="name[]" class="form-control col-xl-2" placeholder="Name" required>
                    <input type="email" name="email[]" class="form-control col-xl-2" placeholder="Email" required>
                    <input type="password" name="password[]" class="form-control col-xl-2" placeholder="Password" required>
                    <input type="text" name="phone[]" class="form-control col-xl-2" placeholder="Phone" required>
                    <input type="text" name="stu_bioid[]" class="form-control col-xl-2" placeholder="Bio ID" required>
                    <input type="text" name="father_name[]" class="form-control col-xl-2" placeholder="Father's Name" required>
                    <input type="text" name="mother_name[]" class="form-control col-xl-2" placeholder="Mother's Name" required>
                    <input type="text" name="admission_date[]" class="form-control col-xl-2" placeholder="Admission Date" value="{{ date('m/d/Y') }}">
                    <input type="text" name="date_of_birth[]" class="form-control col-xl-2" placeholder="Date of Birth" value="{{ date('m/d/Y') }}">
                    <select name="gender[]" class="form-control col-xl-2">
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" name="nationality[]" class="form-control col-xl-2" placeholder="Nationality" required>
                    <select name="caste[]" class="form-control col-xl-2">
                        <option value="">Caste</option>
                        <option value="OC">OC</option>
                        <option value="BC">BC</option>
                        <option value="SC">SC</option>
                        <option value="ST">ST</option>
                    </select>
                    <select name="blood_group[]" class="form-control col-xl-2">
                        <option value="">Blood Group</option>
                        <option value="a+">A+</option>
                        <option value="a-">A-</option>
                        <option value="b+">B+</option>
                        <option value="b-">B-</option>
                        <option value="ab+">AB+</option>
                        <option value="ab-">AB-</option>
                        <option value="o+">O+</option>
                        <option value="o-">O-</option>
                    </select>
                    <input type="text" name="address[]" class="form-control col-xl-3" placeholder="Address" required>
                </div>
            </div>
            <div class="col-xl-1">
                <button type="button" class="btn btn-success" onclick="appendRow()"> <i class="bi bi-plus"></i> </button>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-secondary">{{ get_phrase('Add students') }}</button>
    </div>
</form>

<div class="display-none-view" id="blank-row">
    <div class="row student-row pt-3">
        <div class="col-xl-11">
            <div class="row">
                <input type="text" name="name[]" class="form-control col-xl-2" placeholder="Name">
                <input type="email" name="email[]" class="form-control col-xl-2" placeholder="Email">
                <input type="password" name="password[]" class="form-control col-xl-2" placeholder="Password">
                <input type="text" name="phone[]" class="form-control col-xl-2" placeholder="Phone">
                <input type="text" name="stu_bioid[]" class="form-control col-xl-2" placeholder="Bio ID">
                <input type="text" name="father_name[]" class="form-control col-xl-2" placeholder="Father's Name">
                <input type="text" name="mother_name[]" class="form-control col-xl-2" placeholder="Mother's Name">
                <input type="text" name="admission_date[]" class="form-control col-xl-2" placeholder="Admission Date" value="{{ date('m/d/Y') }}">
                <input type="text" name="date_of_birth[]" class="form-control col-xl-2" placeholder="Date of Birth" value="{{ date('m/d/Y') }}">
                <select name="gender[]" class="form-control col-xl-2">
                    <option value="">Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                </select>
                <input type="text" name="nationality[]" class="form-control col-xl-2" placeholder="Nationality">
                <select name="caste[]" class="form-control col-xl-2">
                    <option value="">Caste</option>
                    <option value="OC">OC</option>
                    <option value="BC">BC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                </select>
                <select name="blood_group[]" class="form-control col-xl-2">
                    <option value="">Blood Group</option>
                    <option value="a+">A+</option>
                    <option value="a-">A-</option>
                    <option value="b+">B+</option>
                    <option value="b-">B-</option>
                    <option value="ab+">AB+</option>
                    <option value="ab-">AB-</option>
                    <option value="o+">O+</option>
                    <option value="o-">O-</option>
                </select>
                <input type="text" name="address[]" class="form-control col-xl-3" placeholder="Address">
            </div>
        </div>
        <div class="col-xl-1">
            <button type="button" class="btn btn-icon btn-danger" onclick="removeRow(this)"> <i class="bi bi-x"></i> </button>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    var blank_field = $('#blank-row').html();

    function appendRow() {
        $('#first-row').append(blank_field);
    }

    function removeRow(elem) {
        $(elem).closest('.student-row').remove();
    }

    var form;
    $(".ajaxForm").submit(function(e) {
        form = $(this);
        ajaxSubmit(e, form, refreshForm);
    });

    var refreshForm = function () {
        form.trigger("reset");
    }

    $(document).on('input', 'input[name="name[]"]', function () {
        let row = $(this).closest('.row');
        let name = $(this).val().toLowerCase().replace(/[^a-z0-9]/g, '');
        let classId = $('#class_id').val();

        if (name && classId) {
            row.find('input[name="email[]"]').val(`${name}_${classId}@student.xyz`);
            row.find('input[name="password[]"]').val(name);
        }
    });
</script>
