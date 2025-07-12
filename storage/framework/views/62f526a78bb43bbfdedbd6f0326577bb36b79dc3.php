<?php $__env->startSection('content'); ?>
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4><?php echo e(get_phrase('Assign Student')); ?></h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                            <li><a href="#"><?php echo e(get_phrase('Transport')); ?></a></li>
                            <li><a href="#"><?php echo e(get_phrase('Assign Student')); ?></a></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-3">
                        
                        <div class="export-btn-area">
                            <a href="javascript:;" class="export_btn"
                                onclick="rightModal('<?php echo e(route('admin.assign.individual')); ?>', '<?php echo e(get_phrase('Assign student')); ?>')"><?php echo e(get_phrase('Individual')); ?></a>
                        </div>

                        
                        <div class="export-btn-area">
                            <a href="javascript:;" class="export_btn"
                                onclick="rightModal('<?php echo e(route('admin.assign.by_class')); ?>', '<?php echo e(get_phrase('Assign by class')); ?>')"><?php echo e(get_phrase('By Class')); ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">

                <div class="row">
                    <div class="col-12 d-flex justify-content-center gap-5">

                        
                        <div class="filter">
                            <form method="GET" class="d-flex align-items-center gap-2"
                                action="<?php echo e(route('admin.assign.student.list')); ?>">
                                <div class="min-w-150px">
                                    <select name="category" id="category"
                                        class="form-select eForm-select eChoice-multiple-with-remove"
                                        onchange="filterCategory(this.value)" required>

                                        <?php if($category == 0): ?>
                                            <option value=""><?php echo e(get_phrase('Category')); ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                                        <?php endif; ?>
                                        <option value="vehicle">Vehicle</option>
                                        <option value="driver">Driver</option>
                                        <option value="class">Class</option>
                                    </select>
                                </div>

                                <div class="min-w-250px">
                                    <select name="type_id" id="type_id"
                                        class="form-select eForm-select eChoice-multiple-with-remove" required>
                                        <?php if($category == 0): ?>
                                            <option value="<?php echo e($filter); ?>"><?php echo e(get_phrase('First select category')); ?>

                                            <?php else: ?>
                                            <option value="<?php echo e($name); ?>"><?php echo e($name); ?></option>
                                        <?php endif; ?>
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <button class="btn-form btn-secondary"
                                        type="submit"><?php echo e(get_phrase('Filter')); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if(count($assigned_list) > 0): ?>
                    <div class="table-responsive assign_student" id="assign_student">
                        <table class="table eTable eTable-2">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col"><?php echo e(get_phrase('Vehicle info')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('Driver Name')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('Student Name')); ?></th>
                                    <th scope="col"><?php echo e(get_phrase('Class')); ?></th>
                                    <th scope="col" class="text-center"><?php echo e(get_phrase('Action')); ?></th>
                                </tr>
                            </thead>


                            <tbody>
                                <?php $__currentLoopData = $assigned_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        
                                        <td><?php echo e($assigned_list->firstItem() + $key); ?></td>

                                        
                                        <td>
                                            <?php
                                                $vehicle = DB::table('vehicles')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->vehicle_id)
                                                    ->first();
                                            ?>
                                            <span><?php echo e($vehicle->vehicle_number); ?></span>,
                                            <span><?php echo e($vehicle->vehicle_model); ?></span>
                                        </td>

                                        
                                        <td>
                                            <?php
                                                $driver = DB::table('users')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->driver_id)
                                                    ->first();
                                            ?>
                                            <?php if(empty($driver)): ?>
                                            <span>Driver Removed</span>
                                            <?php else: ?>
                                            <span><?php echo e($driver->name); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        
                                        <td>
                                            <?php
                                                $student = DB::table('users')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->user_id)
                                                    ->first();
                                            ?>
                                            <?php if(empty($student)): ?>
                                            <span>Student Removed</span>
                                            <?php else: ?>
                                            <span><?php echo e($student->name); ?></span>
                                            <?php endif; ?>
                                            
                                        </td>

                                        
                                        <td>
                                            <?php
                                                $class = DB::table('classes')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->class_id)
                                                    ->first();
                                            ?>
                                            <span><?php echo e($class->name); ?></span>
                                        </td>

                                        
                                        <td>
                                            <a class="btn btn-secondary text-12px" href="javascript:;"
                                                onclick="confirmModal('<?php echo e(route('assign.student.remove', $list->id)); ?>', 'undefined');"><?php echo e(get_phrase('Remove')); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        
                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                <?php echo e(get_phrase('Showing') . ' 1 - ' . count($assigned_list) . ' ' . get_phrase('from') . ' ' . $assigned_list->total() . ' ' . get_phrase('data')); ?>

                            </p>
                            <div class="admin-pagi">
                                <?php echo $assigned_list->appends(request()->all())->links(); ?>

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


    <script type="text/javascript">
        "use strict";
        $(document).ready(function() {
            $(".eChoice-multiple-with-remove").select2();
        });

        function filterCategory(classId) {
            let url = "<?php echo e(route('filter.category', ['type' => ':classId'])); ?>";
            url = url.replace(":classId", classId);
            $.ajax({
                url: url,
                success: function(response) {
                    $('#type_id').html(response);
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/transport/assign/assign_student.blade.php ENDPATH**/ ?>