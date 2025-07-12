
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Routines')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Routines')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <form method="GET" class="d-block ajaxForm" action="<?php echo e(route('teacher.routine.routine_list')); ?>">
                <div class="row mt-3">

                    <div class="col-md-2"></div>

                    <div class="col-md-3">
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                            <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
                            <option value=""><?php echo e(get_phrase('First select a class')); ?></option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button class="eBtn eBtn btn-secondary" type="submit" id = "filter_routine"><?php echo e(get_phrase('Filter')); ?></button>
                    </div>

                    <div class="card-body class_routine_content">
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script type="text/javascript">

  "use strict";


    function classWiseSection(classId) {
        let url = "<?php echo e(route('class_wise_sections', ['id' => ":classId"])); ?>";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
            }
        });
    }

</script>
<?php echo $__env->make('teacher.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/routine/routine.blade.php ENDPATH**/ ?>