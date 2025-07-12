<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('assign.individual.create') }}">
        @csrf

        <div class="form-row">
            {{-- select vehicle --}}
            <div class="fpb-7">
                <label for="vehicle_id" class="eForm-label">{{ get_phrase('Select vehicle') }}</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select a vehicle') }}</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>

            {{-- select class --}}
            <div class="fpb-7">
                <label for="class_id" class="eForm-label">{{ get_phrase('Select class') }}</label>
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    onchange="loadSections(this.value)" required>
                    <option value="">{{ get_phrase('Select a class') }}</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- select section --}}
            <div class="fpb-7">
                <label for="section_id" class="eForm-label">{{ get_phrase('Select section') }}</label>
                <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    onchange="loadStudents()" required>
                    <option value="">{{ get_phrase('Select section') }}</option>
                </select>
            </div>

            {{-- select student --}}
            <div class="fpb-7">
                <label for="student_id" class="eForm-label">{{ get_phrase('Select student') }}</label>
                <select name="student_id" id="student_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select student') }}</option>
                </select>
            </div>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Assign') }}</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        $(".eChoice-multiple-with-remove").select2();
    });

    function loadSections(classId) {
        if (!classId) return;

        let url = "{{ route('admin.class_wise_sections', ['id' => ':id']) }}".replace(":id", classId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#section_id').html(data);
                $('#student_id').html('<option value="">{{ get_phrase("Select student") }}</option>');
            }
        });
    }

    function loadStudents() {
        let classId = $('#class_id').val();
        let sectionId = $('#section_id').val();

        if (!classId || !sectionId) return;

        let url = "{{ url('/student/by-class-section') }}/" + classId + "/" + sectionId;

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                let options = '<option value="">{{ get_phrase("Select student") }}</option>';
                response.students.forEach(function (student) {
                    options += `<option value="${student.id}">${student.name}</option>`;
                });
                $('#student_id').html(options);
            }
        });
    }
</script>
