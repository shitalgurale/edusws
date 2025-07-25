<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('assign.individual.create') }}">
        @csrf

        <div class="form-row">
            {{-- select vehicle --}}
            <div class="fpb-7">
                <label for="vehicle_id" class="eForm-label">{{ get_phrase('Selecct vehicle') }}</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('Select a vehicle') }}</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>

            {{-- select class --}}
            <div class="fpb-7">
                <label for="class_id" class="eForm-label">{{ get_phrase('Selecct class') }}</label>
                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    onchange="studentByClass(this.value)" required>
                    <option value="">{{ get_phrase('Select a class') }}</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- select student --}}
            <div class="fpb-7">
                <label for="student_id" class="eForm-label">{{ get_phrase('Select student') }}</label>
                <select name="student_id" id="student_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value="">{{ get_phrase('First select class') }}</option>
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
    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });

    function studentByClass(classId) {
        let url = "{{ route('student.by.class', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response) {
                $('#student_id').html(response);
            }
        });
    }

    $(function() {
        $('.inputDate').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format("YYYY"), 10),
            },
            function(start, end, label) {
                var years = moment().diff(start, "years");
            }
        );
    });
</script>
