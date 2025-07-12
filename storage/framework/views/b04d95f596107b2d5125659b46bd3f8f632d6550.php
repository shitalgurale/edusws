<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="<?php echo e(route('admin.vehicle.create')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-row">

            
            <div class="fpb-7">
                <input type="hidden" class="form-control eForm-control" id="school_id" name="school_id"
                    value="<?php echo e($school_id); ?>" required>
            </div>

            
            <div class="fpb-7">
                <label for="vehicle_number" class="eForm-label"><?php echo e(get_phrase('Vehicle Number')); ?></label>
                <input type="text" class="form-control eForm-control" id="vehicle_number" name="vehicle_number"
                    required>
            </div>

            
            <div class="fpb-7">
                <label for="vehicle_model" class="eForm-label"><?php echo e(get_phrase('Vehicle Model')); ?></label>
                <input type="text" class="form-control eForm-control" id="vehicle_model" name="vehicle_model"
                    required>
            </div>

            
            <div class="fpb-7">
                <label for="chassis_number" class="eForm-label"><?php echo e(get_phrase('Chassis Number')); ?></label>
                <input type="text" class="form-control eForm-control" id="chassis_number" name="chassis_number"
                    required>
            </div>

            
            <div class="fpb-7">
                <label for="seat" class="eForm-label"><?php echo e(get_phrase('Seat Capacity')); ?></label>
                <input type="number" class="form-control eForm-control" id="seat" name="seat" min="1"
                    required>
            </div>

            
            <div class="fpb-7">
                <label for="assign_driver" class="eForm-label"><?php echo e(get_phrase('Assign driver')); ?></label>
                <select name="assign_driver" id="assign_driver"
                    class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value=""><?php echo e(get_phrase('Select a driver')); ?></option>
                    <?php $__currentLoopData = $driver_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>


            
            <div class="fpb-7">
                <label for="route" class="eForm-label"><?php echo e(get_phrase('Route')); ?></label>
                <textarea class="form-control eForm-control" id="route" name="route" rows="5" placeholder="Define route"
                    required></textarea>
            </div>

        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit"><?php echo e(get_phrase('Create')); ?></button>
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
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/transport/vehicle/add_vehicle.blade.php ENDPATH**/ ?>