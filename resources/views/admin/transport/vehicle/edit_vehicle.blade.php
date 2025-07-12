<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('admin.vehicle.update', $vehicle_info->id) }}">
        @csrf
        <div class="form-row">

            {{-- school id --}}
            <div class="fpb-7">
                <input type="hidden" class="form-control eForm-control" id="school_id" name="school_id"
                    value="{{ $vehicle_info->school_id }}" required>
            </div>

            {{-- vehicle number --}}
            <div class="fpb-7">
                <label for="vehicle_number" class="eForm-label">{{ get_phrase('Vehicle Number') }}</label>
                <input type="text" class="form-control eForm-control" id="vehicle_number" name="vehicle_number"
                    value="{{ $vehicle_info->vehicle_number }}" required>
            </div>

            {{-- vehicle model --}}
            <div class="fpb-7">
                <label for="vehicle_model" class="eForm-label">{{ get_phrase('Vehicle Model') }}</label>
                <input type="text" class="form-control eForm-control" id="vehicle_model" name="vehicle_model"
                    value="{{ $vehicle_info->vehicle_model }}" required>
            </div>

            {{-- chassis number --}}
            <div class="fpb-7">
                <label for="chassis_number" class="eForm-label">{{ get_phrase('Chassis Number') }}</label>
                <input type="text" class="form-control eForm-control" id="chassis_number" name="chassis_number"
                    value="{{ $vehicle_info->chassis_number }}" required>
            </div>

            {{-- seat capacity --}}
            <div class="fpb-7">
                <label for="seat" class="eForm-label">{{ get_phrase('Seat Capacity') }}</label>
                <input type="number" class="form-control eForm-control" id="seat" name="seat"
                    value="{{ $vehicle_info->seat }}"required>
            </div>

            {{-- assign a vehicle to driver --}}
            <div class="fpb-7">
                <label for="assign_driver" class="eForm-label">{{ get_phrase('Assign Driver') }}</label>
                <select name="assign_driver" id="assign_driver"
                    class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="">{{ get_phrase('Select a driver') }}</option>
                    @foreach ($driver_info as $driver)
                        <option value="{{ $driver->id }}" selected>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- define vehicle route --}}
            <div class="fpb-7">
                <label for="route" class="eForm-label">{{ get_phrase('Route') }}</label>
                <input class="form-control eForm-control" id="route" name="route" rows="5"
                    value="{{ $vehicle_info->route }}" required></input>
            </div>

        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";
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
