

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Syllabus')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Syllabus')); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap pb-2">

            <div class="row mt-3">
                <div class="col-md-4"></div>
                <div class="col-md-3 mb-1">
                  <select name="user_id" id="user_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                    <option value=""><?php echo e(get_phrase('Select a student')); ?></option>
                    <?php $__currentLoopData = $student_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($details['id']); ?>"><?php echo e($details['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>


                <div class="col-md-2">
                    <button class="eBtn eBtn btn-secondary" onclick="filter()" ><?php echo e(get_phrase('Filter')); ?></button>
                </div>
            </div>
            <div class="card-body payroll_content">

            </div>
            <div class="empty_box center" id="hide_me">
                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                <br>
                <?php echo e(get_phrase('No data found')); ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    "use strict";

    function filter() {
        var user_id = $('#user_id').val();

        if(user_id != "" ){
          $.ajax({
            url: "<?php echo e(route('parent.syllabusList_by_student_name')); ?>",
            data: {user_id : user_id},
            success: function(response){
              $('.payroll_content').html(response);
              document.getElementById('hide_me').style.visibility = "hidden";

            }
          });
        }

    }

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('parent.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/syllabus/syllabus_list.blade.php ENDPATH**/ ?>