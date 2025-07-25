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
        <div class="row">
            <div class="col-xl-10">
                <div class="row">
                    <input type="text" name="name[]" class="form-control col-xl-2" placeholder="Name" required>
                    <input type="email" name="email[]" class="form-control col-xl-2" placeholder="Email" required>
                    <input type="password" name="password[]" class="form-control col-xl-2" placeholder="Password" required>
                    <input type="text" name="phone[]" class="form-control col-xl-2" placeholder="Phone" required>
                    <input type="text" name="stu_bioid[]" class="form-control col-xl-2" placeholder="Student Bio ID" required>
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






<div class="display-none-view" id = "blank-row ">
    <div class="row student-row pt-3">
        <div class="col-xl-11 col-lg-11 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <div class="row justify-content-md-center">
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="text" name="name[]" class="form-control eForm-control"  value="">
                </div>
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="email" name="email[]" class="form-control eForm-control"  value="">
                </div>
                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <input type="password" name="password[]" class="form-control eForm-control"  value="" placeholder="Password">
                </div>

                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <select name="gender[]" class="form-control eForm-control">
                        <option value="">{{ get_phrase('Select gender') }}</option>
                        <option value="Male">{{ get_phrase('Male') }}</option>
                        <option value="Female">{{ get_phrase('Female') }}</option>
                        <option value="Others">{{ get_phrase('Others') }}</option>
                    </select>
                </div>

                <div class="form-group col-xl-2 col-lg-2 col-md-12 col-sm-12 mb-1 mb-lg-0">
                    <select name="parent_id[]" class="form-control eForm-control" required>
                        <option value="">{{ get_phrase('Select a parent') }}</option>
                        @foreach($data['parents'] as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xl-1 col-lg-1 col-md-12 col-sm-12 mb-3 mb-lg-0">
            <div class="row justify-content-md-center">
                <div class="form-group col">
                    <button type="button" class="btn btn-icon btn-danger" onclick="removeRow(this)"> <i class="bi bi-x"></i> </button>
                </div>
            </div>
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