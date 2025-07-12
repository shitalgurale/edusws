<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="<?php echo e(route('assign.by_class.create')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-row">
            
            <div class="fpb-7">
                <label for="vehicle_id" class="eForm-label"><?php echo e(get_phrase('Select vehicle')); ?></label>
                <select name="vehicle_id" id="vehicle_id" class="form-select eForm-select eChoice-multiple-with-remove"
                    required>
                    <option value=""><?php echo e(get_phrase('Select a vehicle')); ?></option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vehicle->id); ?>"><?php echo e($vehicle->vehicle_number); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="fpb-7">
                <label for="class_id" class="eForm-label"><?php echo e(get_phrase('Select Class')); ?></label>
                <select name="class_id" id="class_id_by_vehicle" class="form-select eForm-select eChoice-multiple-with-remove"
                    required onchange="classWiseSectionByVehicle(this.value)">
                    <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="fpb-7">
                <label for="section_id" class="eForm-label"><?php echo e(get_phrase('Select Section')); ?></label>
                <select name="section_id" id="section_id_by_vehicle" class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value=""><?php echo e(get_phrase('Select section')); ?></option>
                </select>
            </div>
        </div>

        
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit"><?php echo e(get_phrase('Assign')); ?></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    "use strict";
    
    

    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });

    function classWiseSectionByVehicle(classId) {
        let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ':classId'])); ?>";
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
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/transport/assign/by_class.blade.php ENDPATH**/ ?>