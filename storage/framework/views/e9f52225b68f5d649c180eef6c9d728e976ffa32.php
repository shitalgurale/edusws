

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><?php echo e(get_phrase('Routines')); ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="row mt-3">

                <div class="col-md-4"></div>

                <div class="col-md-3">
                    <select name="student" id="student_id" class="form-select eForm-control" required>
                        <option value=""><?php echo e(get_phrase('Select Student')); ?></option>
                        <?php $__currentLoopData = $student_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $each_student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($each_student['user_id']); ?>"><?php echo e($each_student['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    </select>
                </div>

                <div class="col-md-3">
                    <button class="eBtn eBtn btn-secondary" onclick="filter_class_routine()" ><?php echo e(get_phrase('Filter')); ?></button>
                </div>

                <div class="card-body class_routine_content">
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                        <br>
                        <span class=""><?php echo e(get_phrase('No data found')); ?></span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script type="text/javascript">

  "use strict";


    function filter_class_routine(){
        var student_id = $('#student_id').val();

        if(student_id != "" ){
            getFilteredClassRoutine();
        }else{
            toastr.error('<?php echo e(get_phrase('Please select student')); ?>');
        }
    }

    var getFilteredClassRoutine = function() {
        var student_id = $('#student_id').val();

        if(student_id != ""){
            let url = "<?php echo e(route('parent.routine.routine_list')); ?>";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {student_id : student_id},
                success: function(response){
                    $('.class_routine_content').html(response);
                }
            });
        }
    }

</script>

<?php echo $__env->make('parent.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/routine/routine.blade.php ENDPATH**/ ?>