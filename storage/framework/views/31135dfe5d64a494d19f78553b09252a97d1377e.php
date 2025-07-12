<?php
use App\Models\Subject;
use App\Models\Classes;
?>

<?php if(isset($syllabus) && count($syllabus) > 0): ?>
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Title')); ?></th>
                <th><?php echo e(get_phrase('Syllabus')); ?></th>
                <th><?php echo e(get_phrase('Subject')); ?></th>
                <th><?php echo e(get_phrase('Class')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $syllabus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject_details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $subject = Subject::find($subject_details['subject_id']);
                    $class = Classes::find($subject_details['class_id']);
                ?>
                <tr>
                    <td><?php echo e($subject_details['title'] ?? '-'); ?></td>
                    <td>
                        <?php if(!empty($subject_details['file'])): ?>
                            <a href="<?php echo e(asset('assets/uploads/syllabus/' . $subject_details['file'])); ?>"
                               class="btn btn-primary btn-sm bi bi-download" download>
                               <?php echo e(get_phrase('Download')); ?>

                            </a>
                        <?php else: ?>
                            <span class="text-muted"><?php echo e(get_phrase('No File')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($subject->name ?? 'Unknown Subject'); ?></td>
                    <td><?php echo e($class->name ?? 'Unknown Class'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty_box center">
        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
        <br>
        <?php echo e(get_phrase('No data found')); ?>

    </div>
<?php endif; ?>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/syllabus/table.blade.php ENDPATH**/ ?>