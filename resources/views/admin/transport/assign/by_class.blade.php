<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('assign.by_class.create') }}">
        @csrf

        <div class="form-row">
            {{-- select vehicle --}}
            <div class="fpb-7">
                <label for="vehicle_id" class="eForm-label">{{ get_phrase('Select vehicle') }}</label>
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
                <label for="class_id" class="eForm-label">{{ get_phrase('Select Class') }}</label>
                <select name="class_id" id="class_id_by_vehicle" class="form-select eForm-select eChoice-multiple-with-remove"
                    required onchange="classWiseSectionByVehicle(this.value)">
                    <option value="">{{ get_phrase('Select a class') }}</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- select section --}}
            <div class="fpb-7">
                <label for="section_id" class="eForm-label">{{ get_phrase('Select Section') }}</label>
                <select name="section_id" id="section_id_by_vehicle" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select section') }}</option>
                </select>
            </div>
        </div>

        {{-- assign button --}}
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

    function classWiseSectionByVehicle(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response) {
                $('#section_id_by_vehicle').html(response);
            }
        });
    }

    
    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });


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
