

<?php $__env->startSection('content'); ?>

<?php use App\Models\School; ?>

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Admins')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Users')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Admin')); ?></a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="javascript:;" class="export_btn" onclick="rightModal('<?php echo e(route('admin.open_modal')); ?>', 'Create Admin')"><?php echo e(get_phrase('Create Admin')); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Admin area -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <?php if(count($admins) > 0): ?>
            <!-- Table -->
            <div class="table-responsive">
                <table class="table eTable eTable-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Email')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('User Info')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Account Status')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Options')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                        $info = json_decode($admin->user_information);
                        
                        // Check if the image exists, if not, use the default image
                        $user_image = $info->photo;
            if(!empty($info->photo)){
                $user_image = 'uploads/user-images/'.$info->photo;
            }else{
                $user_image = 'uploads/user-images/thumbnail.png';
            }
                    ?>
                        <tr>
                            <th scope="row">
                                <p class="row-number"><?php echo e($admins->firstItem() + $key); ?></p>
                            </th>
                            <td>
                          <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="<?php echo e(asset('assets')); ?>/<?php echo e($user_image); ?>"
                              />
                            </div>
                            <div class="dAdmin_profile_name">
                              <h4><?php echo e($admin->name); ?></h4>
                            </div>
                          </div>
                        </td>                            <td>
                                <div class="dAdmin_info_name min-w-250px">
                                    <p><?php echo e($admin->email); ?></p>
                                </div>
                            </td>
                            <td>
                                <div class="dAdmin_info_name min-w-250px">
                                    <p><span><?php echo e(get_phrase('Phone')); ?>:</span> <?php echo e($info->phone); ?></p>
                                    <p><span><?php echo e(get_phrase('Address')); ?>:</span> <?php echo e($info->address); ?></p>
                                </div>
                            </td>
                            <td>
                                <div class="dAdmin_info_name min-w-100px">
                                    <?php if(!empty($admin->account_status == 'disable')): ?>
                                    <span class="eBadge ebg-soft-danger"><?php echo e(get_phrase('Disabled')); ?></span>
                                    <?php else: ?>
                                    <span class="eBadge ebg-soft-success"><?php echo e(get_phrase('Enable')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="adminTable-action">
                                    <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown">
                                        <?php echo e(get_phrase('Actions')); ?>

                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.open_edit_modal', ['id' => $admin->id])); ?>', '<?php echo e(get_phrase('Edit Admin')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.admin.delete', ['id' => $admin->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
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

<script type="text/javascript">
    "use strict";
    
    function Export() {
        const element = document.getElementById("admin_list");
        var clonedElement = element.cloneNode(true);
        $(clonedElement).css("display", "block");

        var opt = {
            margin: 1,
            filename: 'admin_list_<?php echo e(date("y-m-d")); ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 }
        };

        html2pdf().set(opt).from(clonedElement).save();
        clonedElement.remove();
    }

    function printableDiv(printableAreaDivId) {
        var printContents = document.getElementById(printableAreaDivId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/admin/admin_list.blade.php ENDPATH**/ ?>