<?php
use App\Models\Classes;
?>

<?php if(!empty($subjects) && count($subjects) > 0): ?>
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th><?php echo e(get_phrase('Subjects')); ?></th>
            <th><?php echo e(get_phrase('Class')); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php $subjectCount = count($subjects); ?>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($subject['name']); ?><?php if($key != $subjectCount-1): ?>, <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td>
                <?php
                    $className = '-';
                    $firstSubject = $subjects[0] ?? null;
                    if ($firstSubject && isset($firstSubject['class_id'])) {
                        $class = Classes::find($firstSubject['class_id']);
                        if ($class) {
                            $className = $class->name;
                        }
                    }
                ?>
                <?php echo e($className); ?>

            </td>
        </tr>
    </tbody>
</table>
<?php else: ?>
<div class="empty_box center">
    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
    <br>
    <span><?php echo e(get_phrase('No data found')); ?></span>
</div>
<?php endif; ?>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/subject/table.blade.php ENDPATH**/ ?>