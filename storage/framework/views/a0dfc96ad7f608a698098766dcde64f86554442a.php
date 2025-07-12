<?php if(count($exams) > 0): ?>
<table id="basic-datatable" class="table eTable">
    <thead>
        <tr>
            <th>#</th>
            <th><?php echo e(get_phrase('Exam')); ?></th>
            <th><?php echo e(get_phrase('Starting Time')); ?></th>
            <th><?php echo e(get_phrase('Ending Time')); ?></th>
            <th><?php echo e(get_phrase('Total Marks')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($exams->firstItem() + $key); ?></td>
                <td><?php echo e($exam->name); ?></td>
                <td><?php echo e(date('d M Y - h:i A', $exam->starting_time)); ?></td>
                <td><?php echo e(date('d M Y - h:i A', $exam->ending_time)); ?></td>
                <td><?php echo e($exam->total_marks); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php echo $exams->appends(request()->all())->links(); ?>

<?php else: ?>
<div class="empty_box center">
    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
    <br>
    <?php echo e(get_phrase('Data not found')); ?>

</div>
<?php endif; ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/examination/exam_list.blade.php ENDPATH**/ ?>