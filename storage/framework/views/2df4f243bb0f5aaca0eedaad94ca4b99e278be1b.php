<?php use App\Models\Classes; ?>




<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Subjects')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Subjects')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-8 col-sm-12 col-12 offset-md-2">
        <div class="eSection-wrap">
            <form method="GET" class="d-block ajaxForm" action="<?php echo e(route('teacher.subject_list')); ?>">
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>" <?php echo e($class_id == $class->id ?  'selected':''); ?>><?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 d-flex justify-content-end">
                        <button class="eBtn eBtn btn-secondary" type="submit" id = "filter_routine"><?php echo e(get_phrase('Filter')); ?></button>
                    </div>
                </div>
            </form>

            <?php if(count($subjects) > 0): ?>
            <table class="table eTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo e(get_phrase('Name')); ?></th>
                        <th><?php echo e(get_phrase('Class')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $class = Classes::get()->where('id', $subject->class_id)->first(); ?>
                         <tr>
                            <td><?php echo e($subjects->firstItem() + $key); ?></td>
                            <td><?php echo e($subject->name); ?></td>
                            <td><?php echo e($class->name); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php echo $subjects->appends(request()->all())->links(); ?>

            <?php else: ?>
            <div class="empty_box center">
                <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                <br>
                <span class=""><?php echo e(get_phrase('No data found')); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('teacher.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/subject/subject_list.blade.php ENDPATH**/ ?>