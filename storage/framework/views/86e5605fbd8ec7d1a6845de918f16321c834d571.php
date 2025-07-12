<?php 

use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\School;
use App\Models\Gradebook;
use App\Models\Exam;

$index = 0;

?>


   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Gradebooks')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Gradebooks')); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <form method="GET" class="d-block ajaxForm" action="<?php echo e(route('admin.gradebook')); ?>">
                <div class="row mt-3">
                    <div class="col-md-2"></div>
                    <div class="col-md-2">
                        <label for="class_id" class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                            <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e($class_id == $class->id ?  'selected':''); ?>><?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="section_id" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
                        <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value=""><?php echo e(get_phrase('First select a class')); ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="exam_category_id" class="eForm-label"><?php echo e(get_phrase('Exam')); ?></label>
                        <select name="exam_category_id" id="exam_category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value=""><?php echo e(get_phrase('Select an exam category')); ?></option>
                            <?php $__currentLoopData = $exam_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($exam_category->id); ?>" <?php echo e($exam_category_id == $exam_category->id ?  'selected':''); ?>><?php echo e($exam_category->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2 pt-2">
                        <button class="eBtn eBtn btn-secondary mt-4" type="submit" id="filter_routine"><?php echo e(get_phrase('Filter')); ?></button>
                    </div>
                    <div class="table-responsive gradebook_content pt-4" id="gradebook_report">
                        <?php if(count($filter_list) > 0): ?>
                            <table class="table eTable" id="gradebook_table">
                                <thead>
                                    <th>#</th>
                                    <th><?php echo e(get_phrase('Student Name')); ?></th>
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                       <th><?php echo e($subject->name); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th><?php echo e(get_phrase('Report Card')); ?></th>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $filter_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                        $gradebook = Gradebook::where([
                                            'student_id' => $student->student_id,
                                            'exam_category_id' => $exam_category_id, // ✅ Use selected exam
                                            'school_id' => auth()->user()->school_id,
                                            'session_id' => get_school_settings(auth()->user()->school_id)->value('running_session'),
                                        ])->first();

                                        $marks = $gradebook ? json_decode($gradebook->marks, true) : [];
                                    ?>
                                    <tr>
                                        <td><?php echo e(++$index); ?></td>
                                        <?php 
                                        $student_details = User::find($student->student_id);
                                        $school_name = School::where('id', $student_details->school_id)->value('title');
                                        $exam_name = Exam::where('id', $gradebook->exam_category_id ?? null)->value('name');
                                        ?>
                                        <td><?php echo e($student_details->name); ?></td>
                                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($marks[$subject->id] ?? '-'); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td>
                                            <a href="<?php echo e(route('admin.download_report_card', ['student_id' => $student->student_id, 'exam_category_id' => $exam_category_id])); ?>" class="btn btn-primary btn-sm"><?php echo e(get_phrase('Report Card')); ?></a>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty_box center">
                                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                                <br>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    "use strict";
    function classWiseSection(classId) {
        let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/gradebook/gradebook.blade.php ENDPATH**/ ?>