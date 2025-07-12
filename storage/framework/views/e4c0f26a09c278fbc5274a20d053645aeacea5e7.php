

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('All Staff')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Human Resource')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('All Staff')); ?></a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="<?php echo e(route('hr.create_user')); ?>" class="export_btn">
                        <i class="bi bi-plus"></i> <?php echo e(get_phrase('Add Staff')); ?>

                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(count($users) > 0): ?>
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="table-responsive">
                <table class="table eTable eTable-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Email')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Address')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Options')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $info = json_decode($user->user_information); ?>
                            <tr>
                                <th scope="row">
                                    <p class="row-number"><?php echo e($index++); ?></p>
                                </th>
                                <td>
                                    <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                        <div class="dAdmin_profile_name">
                                            <h4><?php echo e(ucfirst($user->name)); ?></h4>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-250px">
                                        <p><?php echo e($user->email); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-250px">
                                    <p>   <?php echo e(get_phrase(ucfirst($user['address']))); ?> </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="adminTable-action">
                                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo e(get_phrase('Actions')); ?>

                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('hr.user_lists_user_edit', ['id' => $user->id])); ?>', '<?php echo e(get_phrase('Update user')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('delete_user(<?php echo e($user->id); ?>)', 'ajax_delete')"><?php echo e(get_phrase('Delete')); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="empty_box text-center">
    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
    <br>
    <span class=""><?php echo e(get_phrase('No data found')); ?></span>
</div>
<?php endif; ?>

<script>
"use strict";
function delete_user(id) {
    let delete_user_id = id;
    var url = '<?php echo e(route("hr.user_lists_user_delete", ":id")); ?>';
    url = url.replace(':id', delete_user_id);

    $.ajax({
        url: url,
        type: "GET",
        contentType: false,
        processData: false,
        success: function (data) {
            location.reload();
            $("#confirmSweetAlerts").modal('hide');
            toastr.success("Deleted Successfully");
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/hr_user_list/staff_list.blade.php ENDPATH**/ ?>