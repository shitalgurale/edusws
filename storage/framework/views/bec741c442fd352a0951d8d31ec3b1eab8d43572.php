
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Assigned Permission For Teacher')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Users')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Teacher Permission')); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- Start Teacher Permission area -->
<div class="row">
    <div class="col-10 offset-md-1">
        <div class="eSection-wrap-2">
        	<form method="GET" class="d-block ajaxForm">
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
                        <button class="eBtn eBtn btn-secondary" type="button" id = "filter_routine" onclick="filter()"><?php echo e(get_phrase('Filter')); ?></button>
                    </div>

                    <div class="card-body permission_content">
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Teacher Permission area -->

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

    function filter(){
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var value = class_id + '-' + section_id;

        let url = "<?php echo e(route('admin.teacher.permission_list', ['filter' => ":value"])); ?>";
        url = url.replace(":value", value);

        if(class_id != "" && section_id!= ""){
            $.ajax({
                url: url,
                success: function(response){
                    $('.permission_content').html(response);
                }
            });
        }else{
            toastr.error('<?php echo e(get_phrase('Please select a class and section')); ?>');
        }
    }

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/permission/index.blade.php ENDPATH**/ ?>