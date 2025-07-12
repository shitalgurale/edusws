<?php $__env->startSection('content'); ?>
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4><?php echo e(get_phrase('Driver')); ?></h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                            <li><a href="#"><?php echo e(get_phrase('Transport')); ?></a></li>
                            <li><a href="#"><?php echo e(get_phrase('Driver')); ?></a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn"
                            onclick="rightModal('<?php echo e(route('admin.driver.create_modal')); ?>', '<?php echo e(get_phrase('Create Driver')); ?>')"><?php echo e(get_phrase('Create Driver')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">


                
                <div
                    class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">


                    <form action="<?php echo e(route('admin.driver.search')); ?>">
                        <div class="search-input d-flex justify-content-start align-items-center">

                            
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                    <path id="Search_icon" data-name="Search icon"
                                        d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                                        fill="#797c8b" />
                                </svg>
                            </span>

                            
                            <input type="text" id="search" name="search" value="" placeholder="Search Driver"
                                value="<?php echo e($search); ?>" class="form-control" />
                        </div>
                    </form>


                    <!-- Export Button -->
                    <?php if(count($driver_info) > 0): ?>
                        <div class="position-relative">
                            <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown"
                                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                <span class="pr-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77"
                                        viewBox="0 0 10.771 12.31">
                                        <path id="arrow-right-from-bracket-solid"
                                            d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                                            transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
                                    </svg>
                                </span>
                                <?php echo e(get_phrase('Export')); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                                <li>
                                    <a class="dropdown-item" id="pdf" href="javascript:;"
                                        onclick="Export()"><?php echo e(get_phrase('PDF')); ?></a>
                                </li>
                                <li>
                                    <a class="dropdown-item" id="print" href="javascript:;"
                                        onclick="printableDiv('driver_list')"><?php echo e(get_phrase('Print')); ?></a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>


                <?php if(count($driver_info) > 0): ?>
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table eTable eTable-2">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('Email')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('User Info')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('Options')); ?></th>
                            </thead>


                            <tbody>
                                <?php $__currentLoopData = $driver_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <?php
                                    $info = json_decode($driver->user_information);
                                    $user_image = $info->photo;
                                    if (!empty($info->photo)) {
                                        $user_image = 'uploads/user-images/' . $info->photo;
                                    } else {
                                        $user_image = 'uploads/user-images/thumbnail.png';
                                    }
                                    ?>

                                    <tr>


                                        
                                        <th scope="row">
                                            <p class="row-number"><?php echo e($driver_info->firstItem() + $key); ?></p>
                                        </th>

                                        
                                        <td>
                                            <div class="dAdmin_profile d-flex align-items-center">
                                                <div class="dAdmin_profile_img">
                                                    <img class="img-fluid" width="50" height="50"
                                                        src="<?php echo e(asset('assets')); ?>/<?php echo e($user_image); ?>" />
                                                </div>
                                                <div class="dAdmin_profile_name">
                                                    <h4><?php echo e($driver['name']); ?></h4>
                                                </div>
                                            </div>
                                        </td>

                                        
                                        <td>
                                            <?php echo e($driver['email']); ?>

                                        </td>

                                        
                                        <td>
                                            <div class="dAdmin_info_name">
                                                <p>
                                                    <span><?php echo e(get_phrase('Phone')); ?>:</span> <?php echo e($info->phone); ?>

                                                </p>
                                                <p>
                                                    <span><?php echo e(get_phrase('Address')); ?>:</span> <?php echo e($info->address); ?>

                                                </p>
                                            </div>
                                        </td>

                                        
                                        <td>
                                            <div class="adminTable-action">
                                                <button type="button"
                                                    class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?php echo e(get_phrase('Actions')); ?>

                                                </button>
                                                <ul
                                                    class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="rightModal('<?php echo e(route('admin.driver.edit_modal', $driver['id'])); ?>', '<?php echo e(get_phrase('Edit Driver')); ?>')"><?php echo e(get_phrase('Edit')); ?></a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="confirmModal('<?php echo e(route('admin.driver.delete', $driver['id'])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        
                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                <?php echo e(get_phrase('Showing') . ' 1 - ' . count($driver_info) . ' ' . get_phrase('from') . ' ' . $driver_info->total() . ' ' . get_phrase('data')); ?>

                            </p>
                            <div class="admin-pagi">
                                <?php echo $driver_info->appends(request()->all())->links(); ?>

                            </div>
                        </div>
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

    
    <?php if(count($driver_info) > 0): ?>
        <!-- Table -->
        <div class="table-responsive driver_list display-none-view" id="driver_list">
            <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;"><?php echo e(get_phrase('Driver List')); ?></h4>
            <table class="table eTable eTable-2">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
                        <th scope="col"><?php echo e(get_phrase('Email')); ?></th>
                        <th scope="col"><?php echo e(get_phrase('User Info')); ?></th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $driver_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php
                        $info = json_decode($driver->user_information);
                        $user_image = $info->photo;
                        if (!empty($info->photo)) {
                            $user_image = 'uploads/user-images/' . $info->photo;
                        } else {
                            $user_image = 'uploads/user-images/thumbnail.png';
                        }
                        ?>
                        <tr>
                            <th scope="row">
                                <p class="row-number"><?php echo e($driver->id); ?></p>
                            </th>
                            <td>
                                <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                    <div class="dAdmin_profile_img">
                                        <img class="img-fluid" width="50" height="50"
                                            src="<?php echo e(asset('assets')); ?>/<?php echo e($user_image); ?>" />
                                    </div>
                                    <div class="dAdmin_profile_name">
                                        <h4><?php echo e($driver->name); ?></h4>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php echo e($driver->email); ?>

                            </td>
                            <td>
                                <div class="dAdmin_info_name min-w-250px">
                                    <p><span><?php echo e(get_phrase('Phone')); ?>:</span> <?php echo e($info->phone); ?></p>
                                    <p>
                                        <span><?php echo e(get_phrase('Address')); ?>:</span> <?php echo e($info->address); ?>

                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        "use strict";

        function Export() {

            // Choose the element that our invoice is rendered in.
            const element = document.getElementById("driver_list");

            // clone the element
            var clonedElement = element.cloneNode(true);

            // change display of cloned element
            $(clonedElement).css("display", "block");

            // Choose the clonedElement and save the PDF for our user.
            var opt = {
                margin: 1,
                filename: 'driver_list_<?php echo e(date('y-m-d')); ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(clonedElement).save();

            // remove cloned element
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

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/transport/drivers/driver_list.blade.php ENDPATH**/ ?>