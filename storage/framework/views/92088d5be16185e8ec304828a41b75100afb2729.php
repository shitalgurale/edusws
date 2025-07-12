<?php

use App\Models\Subject;
use App\Models\Session;
use App\Models\Gradebook;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$index = 0;

?>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="view_mark" id="mark_report">
                <table class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(get_phrase('Subject Name')); ?></th>
                            <?php $__currentLoopData = $exam_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th><?php echo e($exam_category->name); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(++$index); ?></td>
                                <td><?php echo e($subject->name); ?></td>
                                <?php $__currentLoopData = $exam_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td>
                                        <?php
                                            $exam_marks = \App\Models\Gradebook::where('exam_category_id', $exam_category->id)
                                                ->where('class_id', $student_details->class_id)
                                                ->where('section_id', $student_details->section_id)
                                                ->where('student_id', $student_details->user_id)
                                                ->where('school_id', auth()->user()->school_id)
                                                ->where('session_id', $active_session)
                                                ->first();

                                            $subject_list = $exam_marks && $exam_marks->marks
                                                ? json_decode($exam_marks->marks, true)
                                                : [];
                                        ?>
                                        <?php echo e($subject_list[$subject->id] ?? '-'); ?>

                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <tr>
                            <td><?php echo e(++$index); ?></td>
                            <td><?php echo e(get_phrase('Report Card')); ?></td>
                            <?php $__currentLoopData = $exam_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td>
                                    <a href="<?php echo e(route('parent.download_report_card', ['student_id' => $student_details->user_id, 'exam_category_id' => $exam_category->id])); ?>"
                                       class="btn btn-primary btn-sm">
                                        <?php echo e(get_phrase('Report Card')); ?>

                                    </a>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/marks/table.blade.php ENDPATH**/ ?>