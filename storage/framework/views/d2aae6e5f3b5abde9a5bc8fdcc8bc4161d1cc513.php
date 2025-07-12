<?php
use App\Models\Addon\Hr_user_list;
?>




<?php $__env->startSection('content'); ?>






<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Payslip Details')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Human Resource')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Payroll')); ?></a></li>
            </ul>
          </div>

        </div>
      </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="eSection-wrap human_resource_content">
            
            <?php if(!empty($payroll)): ?>

            <table class="table eTable eTable-2">
                <thead>
                <tr>
                    <th scope="col">#</th>

                    <th scope="col"><?php echo e(get_phrase('User')); ?></th>
                    <th scope="col"><?php echo e(get_phrase('Summary')); ?></th>

                    <th scope="col"><?php echo e(get_phrase('')); ?></th>
                    <th scope="col"><?php echo e(get_phrase('Date')); ?></th>


                    <th scope="col"><?php echo e(get_phrase('Status')); ?></th>
                    <th scope="col" class="text-center"><?php echo e(get_phrase('Option')); ?></th>


                </thead>
                <tbody>
                    <?php
                    $count = 1;

                    foreach($payroll as $row): ?>
                    <tr>
                        <td>
                            <?php echo e($count++); ?>

                        </td>
                        <td>
                            <?php
                                $user =Hr_user_list::where( array('id' =>  $row['user_id'],'school_id' => $row['school_id']))->first();?>

                            <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                <div class="dAdmin_profile_name">
                                    <h4> <?php echo e(get_phrase(ucfirst($user->name))); ?></h4>

                                </div>
                            </div>
                        </td>
                        <td>
                            <?php

                                $net_salary = $user->joining_salary+$row['allowances'] - $row['deducition'];
                                ?>

                            <div class="dAdmin_info_name min-w-150px">

                                <p><span><?php echo e(get_phrase('Net Salary')); ?>: </span> </p>


                            </div>

                        </td>
                        <td>

                            <div class="dAdmin_info_name min-w-150px">

                                <p> <?php echo e($net_salary); ?></p>


                            </div>

                        </td>
                        <td>
                            <?php
                                $date = date('M-Y', (int)$row['created_at']);


                                ?>

                            <div class="dAdmin_info_name min-w-150px">
                                <p>  <?php echo e($date); ?></p>

                            </div>
                        </td>

                        <td >
                            <div class="dAdmin_info_name min-w-150px">
                                <?php if($row['status'] == 1): ?>
                                    <span class="eBadge eBadge-pill ebg-soft-success "><?php echo e(get_phrase('Paid')); ?></span>
                                <?php else: ?>
                                    <span class="eBadge eBadge-pill ebg-soft-danger "><?php echo e(get_phrase('Unpaid')); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        
                        <div class="dAdmin_info_name min-w-150px">
                            <td>
                                 <div class="adminTable-action">
                                 <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo e(get_phrase('Actions')); ?>

                                </button>
                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">

                                    <a href="#" class="dropdown-item" onclick="largeModal('<?php echo e(route('hr.payslip',['id'=>$row['id']])); ?>', '<?php echo e(get_phrase('payslip details')); ?>');">
                                        <i class="mdi mdi-eye"></i>
                                        <?php echo e(get_phrase('View payslip details')); ?>




                                    </a>
                                        <li>
                                            <a href="<?php echo e(route('hr.print_invoice',['id'=>$row['id']])); ?>" target="_blank" class="dropdown-item">
                                                <i class="mdi mdi-printer"> </i>
                                                <?php echo e(get_phrase('Print invoice')); ?>

                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </div>


                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty_box text-center">
                <img class="mb-3 " width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />

                <br>
                <span class="">
                    <?php echo e(get_phrase('No data found')); ?>

                 </span>
            </div>

           <?php endif; ?>
            
        </div>
    </div>
</div>





<?php $__env->stopSection(); ?>

<?php echo $__env->make($roleName, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/hr_payroll/users/list.blade.php ENDPATH**/ ?>