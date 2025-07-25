

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
          <div class="export-btn-area">
            <a href="javascript:;" class="export_btn" onclick="rightModal('<?php echo e(route('teacher.show_syllabus_modal')); ?>', '<?php echo e(get_phrase('Create Syllabus')); ?>')"><i class="bi bi-plus"></i><?php echo e(get_phrase('Add syllabus')); ?></a>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-8 offset-md-2">
        <div class="eSection-wrap">
            <div class="row mb-3">
                <div class="syllabus_body">
                    <div class="row mb-3">
                        <div class="col-md-2 mb-1"></div>
                        <div class="col-md-3 mb-1">
                            <select name="class" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value)" required>
                                <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                                <?php

                                foreach ($permitted_classes as $class): ?>
                                    <option value="<?php echo e($class['id']); ?>"><?php echo e($class['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <select name="section" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                                <option value=""><?php echo e(get_phrase('Select section')); ?></option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="eBtn eBtn btn-secondary" onclick="filter_syllabus()" ><?php echo e(get_phrase('Filter')); ?></button>
                        </div>
                    </div>
                    <div class="syllabus_content">
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";

    function classWiseSection(classId) {


        $.ajax({

            url: '<?php echo e(route('teacher.class_wise_section_for_syllabus')); ?>',
            data: {classId : classId},
            success: function(response){
                $('#section_id').html(response);
            }
        });


    }

    function filter_syllabus(){
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        if(class_id != "" && section_id!= ""){
            showAllSyllabuses();
        }else{
            toastr.error('<?php echo e(get_phrase('Please select a class and section')); ?>');
        }
    }

    var showAllSyllabuses = function () {


        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        if(class_id != "" && section_id!= "")
        {
            $.ajax({

            url: '<?php echo e(route('teacher.syllabus_details')); ?>',
            data: {class_id : class_id,section_id:section_id},
                success: function(response)
                {
                    $('.syllabus_content').html(response);
                }
                });
            }
        }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/syllabus/index.blade.php ENDPATH**/ ?>