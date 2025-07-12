<?php
use App\Http\Controllers\CommonController;
?>

<div>
    <table id="basic-datatable" class="table eTable">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Invoice No')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Invoice Title')); ?></th>
                <th><?php echo e(get_phrase('Amount')); ?></th>
                <th><?php echo e(get_phrase('Total Amount')); ?></th>
                <th><?php echo e(get_phrase('Paid Amount')); ?></th>
                <th><?php echo e(get_phrase('Due Amount')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
                <th><?php echo e(get_phrase('Option')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td><?php echo e(sprintf('%08d', $invoice['id'])); ?></td>
                    <td>
                        <?php echo e($student_details->name); ?> <br>
                        <small><strong><?php echo e(get_phrase('Class')); ?> :</strong> <?php echo e($student_details->class_name); ?></small> <br>
                        <small><strong><?php echo e(get_phrase('Section')); ?> :</strong> <?php echo e($student_details->section_name); ?></small>
                    </td>
                    <td><?php echo e($invoice['title']); ?></td>
                    <td>
                        <?php echo e(school_currency($invoice['amount'])); ?>

                        <?php if(!empty($invoice['discounted_price'])): ?><br>
                        <small><strong><?php echo e(get_phrase('Discount:')); ?></strong> <?php echo e(school_currency($invoice['discounted_price'])); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['total_amount'])); ?> <br>
                        <small><strong><?php echo e(get_phrase('Created at')); ?>:</strong> <?php echo e(date('d-M-Y', $invoice['timestamp'])); ?></small>
                    </td>
                    <td>
                        <?php echo e(school_currency($invoice['paid_amount'])); ?> <br>
                        <small>
                            <strong><?php echo e(get_phrase('Payment date')); ?>:</strong>
                            <?php $updated_time = strtotime($invoice['updated_at']); ?>
                            <?php if($updated_time != ''): ?>
                                <?php echo e(date('d-M-Y', $updated_time)); ?>

                            <?php else: ?>
                                <?php echo e(get_phrase('Not found')); ?>

                            <?php endif; ?>
                        </small>
                    </td>
                    <td><?php echo e(school_currency($invoice['due_amount'])); ?></td>
                    <td>
                        <?php $status = strtolower($invoice['status']); ?>
                        <?php if($status == 'unpaid'): ?>
                            <span class="eBadge ebg-soft-danger"><?php echo e(ucfirst($status)); ?></span>
                        <?php elseif($status == 'pending'): ?>
                            <span class="eBadge ebg-soft-warning"><?php echo e(ucfirst($status)); ?></span>
                        <?php else: ?>
                            <span class="eBadge ebg-soft-success"><?php echo e(ucfirst($status)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="adminTable-action">
                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo e(get_phrase('Actions')); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('parent.studentFeeinvoice', ['id' => $invoice['id']])); ?>" target="_blank">
                                        <?php echo e(get_phrase('Print invoice')); ?>

                                    </a>
                                </li>
                                <?php
                                    $actual_total = !empty($invoice['discounted_price']) && $invoice['discounted_price'] > 0
                                        ? $invoice['amount'] - $invoice['discounted_price']
                                        : $invoice['amount'];
                                ?>
                                <?php if($status == 'unpaid' || $invoice['paid_amount'] < $actual_total): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('parent.FeePayment', ['id' => $invoice['id']])); ?>">
                                            <?php echo e(get_phrase('Pay')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<div class="display-none-view" id="student_fee_report">
    <table id="student_fee_report" class="table eTable">
        <thead>
            <tr>
                <th><?php echo e(get_phrase('Invoice No')); ?></th>
                <th><?php echo e(get_phrase('Student')); ?></th>
                <th><?php echo e(get_phrase('Class & Section')); ?></th>
                <th><?php echo e(get_phrase('Invoice Title')); ?></th>
                <th><?php echo e(get_phrase('Amount')); ?></th>
                <th><?php echo e(get_phrase('Discount Amount')); ?></th>
                <th><?php echo e(get_phrase('Total Amount')); ?></th>
                <th><?php echo e(get_phrase('Created at')); ?></th>
                <th><?php echo e(get_phrase('Paid Amount')); ?></th>
                <th><?php echo e(get_phrase('Due Amount')); ?></th>
                <th><?php echo e(get_phrase('Status')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']); ?>
                <tr>
                    <td><?php echo e(sprintf('%08d', $invoice['id'])); ?></td>
                    <td><?php echo e($student_details->name); ?></td>
                    <td>
                        <small><?php echo e($student_details->class_name); ?></small><br>
                        <small><?php echo e($student_details->section_name); ?></small>
                    </td>
                    <td><?php echo e($invoice['title']); ?></td>
                    <td>
                        <?php echo e(school_currency($invoice['amount'])); ?>

                        <?php if(!empty($invoice['discounted_price'])): ?><br>
                        <small><strong><?php echo e(get_phrase('Discount:')); ?></strong> <?php echo e(school_currency($invoice['discounted_price'])); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(school_currency($invoice['discounted_price'] ?? 0)); ?></td>
                    <td><?php echo e(school_currency($invoice['total_amount'])); ?></td>
                    <td><small><?php echo e(date('d-M-Y', $invoice['timestamp'])); ?></small></td>
                    <td><?php echo e(school_currency($invoice['paid_amount'])); ?></td>
                    <td><?php echo e(school_currency($invoice['due_amount'])); ?></td>
                    <td>
                        <?php $status = strtolower($invoice['status']); ?>
                        <?php if($status == 'unpaid'): ?>
                            <span class="bg bg-danger"><?php echo e(ucfirst($status)); ?></span>
                        <?php else: ?>
                            <span class="bg bg-success"><?php echo e(ucfirst($status)); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/fee_manager/list.blade.php ENDPATH**/ ?>