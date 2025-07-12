<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_user_list;
use App\Models\Addon\Hr_roles;


?>



<?php $__env->startSection('content'); ?>

<style>
    .eTable-2 > :not(caption) > * > * {
  border-bottom: 1px dashed #dedede;
  padding: 20px 8px !important;
}
</style>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Leave Lists')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Human Resource')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Leave List')); ?></a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <?php if(auth()->user()->role_id == 2): ?>

                <a href="javascript:;" class="export_btn float-end m-1"onclick="rightModal('<?php echo e(route('hr.show_leave_request_modal_for_admin')); ?>','<?php echo e(get_phrase('Crete new leave')); ?>')"><i class="bi bi-plus"></i> <?php echo e(get_phrase('Create New Leave')); ?></a>

            <?php elseif(auth()->user()->role_id == 3): ?>

                <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('<?php echo e(route('hr.show_leave_request_modal')); ?>','<?php echo e(get_phrase('Add Leave')); ?>')"><i class="bi bi-plus"></i> <?php echo e(get_phrase('Add New Leave')); ?></a>

            <?php elseif(auth()->user()->role_id == 4): ?>
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('<?php echo e(route('hr.show_leave_request_modal')); ?>','<?php echo e(get_phrase('Add Leave')); ?>')"><i class="bi bi-plus"></i> <?php echo e(get_phrase('Add New Leave')); ?></a>

            <?php elseif(auth()->user()->role_id == 5): ?>
            <a href="javascript:;" class="export_btn float-end m-1" onclick="rightModal('<?php echo e(route('hr.show_leave_request_modal')); ?>','<?php echo e(get_phrase('Add Leave')); ?>')"><i class="bi bi-plus"></i> <?php echo e(get_phrase('Add New Leave')); ?></a>

            <?php endif; ?>


          </div>
        </div>
      </div>
    </div>
</div>



<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">

            <form class="w-100" action="<?php echo e(route('hr.list_of_leaves',['from' => 'sdd','to'=>'sdad','type'=>'dasd'])); ?>" method="get">


                <div class="row justify-content-md-center">

                    <div class="col-md-2">
                    </div>
                    <?php if(auth()->user()->role_id == 2): ?>
                    <div class="col-md-2">
                        <div class="form-group">


                            <select name="role_id" id="role_id" class="form-select eForm-control" aria-label="Default select example" onchange="fetch_type(this.value)">
                                <option value="">
                                    <?php echo e(get_phrase('Select a role')); ?>

                                </option>
                                <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?php echo e($role['id']); ?>" <?php echo e($hr_searched_role_id == $role['id'] ? 'selected':''); ?>>
                                    <?php echo e(get_phrase(ucfirst($role['name']))); ?>

                                </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <input type="text" id="datetimes" class="form-select eForm-control" aria-label="Default select example" name="datetimes" />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-block btn-secondary" id="submit-button" onclick="update_date_range();">
                            <?php echo e(get_phrase('Filter')); ?>

                        </button>
                    </div>

                </div>

                <br>



            </form>



            <ul class="nav nav-tabs eNav-Tabs-custom"id="myTab"role="tablist" >

                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="pending-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#pendingtable"
                    type="button"
                    role="tab"
                    aria-controls="pendingtable"
                    aria-selected="false"
                  >
                  <?php echo e(get_phrase('Pending ')); ?><p class="badge bg-warning ">
                    <?php echo e(count($list_of_pending_leaves)); ?>

                </p>
                    <span></span>
                  </button>
                </li>


                <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="approve-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#approvetable"
                      type="button"
                      role="tab"
                      aria-controls="approvetable"
                      aria-selected="false"
                    >
                    <?php echo e(get_phrase('Approve ')); ?><p class="badge bg-success ">
                      <?php echo e(count($list_of_approve_leaves)); ?>

                  </p>
                      <span></span>
                    </button>
                  </li>


                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="decline-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#declinetable"
                      type="button"
                      role="tab"
                      aria-controls="declinetable"
                      aria-selected="false"
                    >
                    <?php echo e(get_phrase('Decline ')); ?><p class="badge bg-danger ">
                      <?php echo e(count($list_of_decline_leaves)); ?>

                  </p>
                      <span></span>
                    </button>
                  </li>


              </ul>


            <div class="tab-content pb-2" id="nav-tabContent">
                <div class="tab-pane fade show active" id="pendingtable" role="tabpanel" aria-labelledby="pending-tab">

                    <div class="eForm-layouts">
                       <?php if(count($list_of_pending_leaves) > 0 ): ?>
                       <?php $list_of_pending_leaves=$list_of_pending_leaves->toArray();  ?>

                      <table class="table eTable eTable-2">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <?php if(auth()->user()->role_id == 2): ?>
                            <th scope="col"><?php echo e(get_phrase('Employee')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Role')); ?></th>
                            <?php endif; ?>
                            <th scope="col"><?php echo e(get_phrase('Start date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('End date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Reason')); ?></th>
                            
                            <th scope="col" class="text-center"><?php echo e(get_phrase('Option')); ?></th>

                        </thead>
                          <tbody>
                              <?php foreach($list_of_pending_leaves as $key => $leave): ?>
                              <tr>
                                  <td>
                                      <?php echo e($key+1); ?>

                                  </td>

                                  <?php if(auth()->user()->role_id == 2): ?>

                                  <td>
                                      <?php  $name=Hr_user_list::find($leave['user_id']); ?>

                                    <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                    <div class="dAdmin_profile_name">
                                        <h4> <?php echo e(get_phrase(ucfirst($name->name??""))); ?></h4>

                                    </div>
                                  </div>


                                 </td>
                                  <td>
                                      <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                               if(!empty($r))
                                               {
                                                $r=$r->toArray();
                                               }
                                                ?>

                                            <div class="dAdmin_info_name min-w-150px">
                                                <p><?php echo e($r['name']??""); ?></p>

                                            </div>
                                  </td>

                                  <?php endif; ?>

                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p>  <?php echo e(date('d/m/Y', $leave['start_date'])); ?></p>

                                    </div>


                                  </td>
                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> <?php echo e(date('d/m/Y', $leave['end_date'])); ?></p>

                                    </div>

                                  </td>
                                  <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> <?php echo e(substr($leave['reason'], 0, 50) . '...'); ?></p>

                                    </div>


                                  </td>
                              

                                 

                                    <div class="dAdmin_info_name min-w-150px">

                                         <td>
                                      <?php if(auth()->user()->role_id==2): ?>

                                      <div class="adminTable-action">

                                         
                                          <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                              <?php echo e(get_phrase('Actions')); ?>

                                          </button>

                                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                              <!-- item-->

                                              <?php if($leave['status']==0 || $leave['status']==2): ?>

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve'])); ?>', 'undefined');"> <?php echo e(get_phrase('Approve')); ?></a>
                                              </li>

                                              <?php endif; ?>

                                              <?php if($leave['status']==1 || $leave['status']==0): ?>

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline'])); ?>', 'undefined');"> <?php echo e(get_phrase('Decline')); ?></a>
                                              </li>


                                              <?php endif; ?>

                                              <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete'])); ?>', 'undefined');"> <?php echo e(get_phrase('Delete')); ?></a>
                                              </li>


                                            </ul>
                                      </div>

                                      <?php else: ?>
                                      <div class="adminTable-action">
                                          <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false" <?php if($leave['status']==1 || $leave['status']==2): ?> disabled <?php endif; ?>>
                                            <?php echo e(get_phrase('Actions')); ?>

                                          </button>

                                          <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                              <!-- item-->

                                              <li>
                                                <a class="dropdown-item" href="javascript:;"
                                                 onclick="rightModal('<?php echo e(route('hr.show_leave_update_request_modal', ['id' => $leave['id']])); ?>', '<?php echo e(get_phrase('Edit Leave')); ?>')">
                                                  <i class="mdi mdi-cancel"></i>
                                                  <?php echo e(get_phrase('Edit')); ?></a>
                                              </li>

                                              <li>
                                                <a class="dropdown-item" href="javascript:;"
                                                onclick="confirmModal('<?php echo e(route('hr.delete_leave_request', ['id'=>$leave['id']])); ?>', 'undefined');">
                                                  <?php echo e(get_phrase('Delete')); ?></a>
                                              </li>



                                          </div>
                                        </ul>

                                      <?php endif; ?>
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




                <div class="tab-pane fade show " id="approvetable" role="tabpanel" aria-labelledby="approve-tab">

                    <div class="eForm-layouts">
                      <?php if(count($list_of_approve_leaves) > 0 ): ?>
                       <?php   $list_of_approve_leaves=$list_of_approve_leaves->toArray();?>


                      <table class="table eTable eTable-2">
                      <thead>
                        <tr>
                            <th scope="col">#</th>
                            <?php if(auth()->user()->role_id == 2): ?>
                            <th scope="col"><?php echo e(get_phrase('Employee')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Role')); ?></th>
                            <?php endif; ?>
                            <th scope="col"><?php echo e(get_phrase('Start date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('End date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Reason')); ?></th>
                            <?php if(auth()->user()->role_id == 2): ?>
                            <th scope="col" class="text-center" ><?php echo e(get_phrase('Option')); ?></th>
                            <?php endif; ?>

                            </thead>
                            <tbody>
                                <?php foreach($list_of_approve_leaves as $key => $leave): ?>
                                <tr>
                                    <td>
                                        <?php echo e($key+1); ?>

                                    </td>

                                    <?php if(auth()->user()->role_id == 2): ?>

                                    <td>
                                        <?php $name=Hr_user_list::find($leave['user_id']);?>
                                            <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                                <div class="dAdmin_profile_name">
                                                    <h4> <?php echo e(get_phrase(ucfirst($name->name??""))); ?></h4>

                                                </div>
                                            </div>
                                    </td>
                                    <td>
                                        <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                                if(!empty($r))
                                                {
                                                $r=$r->toArray();
                                                }
                                                ?>

                                            <div class="dAdmin_info_name min-w-150px">
                                                <p><?php echo e($r['name']??""); ?></p>

                                            </div>
                                    </td>

                                    <?php endif; ?>

                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p>  <?php echo e(date('d/m/Y', $leave['start_date'])); ?></p>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p> <?php echo e(date('d/m/Y', $leave['end_date'])); ?></p>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-150px">
                                            <p> <?php echo e(substr($leave['reason'], 0, 50) . '...'); ?></p>

                                        </div>
                                    </td>

                                    
                                    <div class="dAdmin_info_name min-w-150px">
                                        <td>
                                        <?php if(auth()->user()->role_id==2): ?>

                                        <div class="adminTable-action">
                                          
                                            <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                                <?php echo e(get_phrase('Actions')); ?>

                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                <!-- item-->

                                                <?php if($leave['status']==0 || $leave['status']==2): ?>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve'])); ?>', 'undefined');"> <?php echo e(get_phrase('Approve')); ?></a>
                                                </li>
                                                <?php endif; ?>

                                                <?php if($leave['status']==1 || $leave['status']==0): ?>

                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline'])); ?>', 'undefined');"> <?php echo e(get_phrase('Decline')); ?></a>
                                                </li>
                                                <?php endif; ?>

                                                <li>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete'])); ?>', 'undefined');"> <?php echo e(get_phrase('Delete')); ?></a>
                                                </li>



                                            </ul>
                                        </div>


                                        <?php endif; ?>
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
                               <?php echo e(get_phrase(' No data found')); ?>

                             </span>
                        </div>

                       <?php endif; ?>
                     </div>


                </div>

                <div class="tab-pane fade show " id="declinetable" role="tabpanel" aria-labelledby="decline-tab">

                    <div class="eForm-layouts">

                        <?php if(count($list_of_decline_leaves) > 0 ): ?>
                        <?php $list_of_decline_leaves=$list_of_decline_leaves->toArray(); ?>


                    <table class="table eTable eTable-2">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <?php if(auth()->user()->role_id == 2): ?>
                            <th scope="col"><?php echo e(get_phrase('Employee')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Role')); ?></th>
                            <?php endif; ?>
                            <th scope="col"><?php echo e(get_phrase('Start date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('End date')); ?></th>
                            <th scope="col"><?php echo e(get_phrase('Reason')); ?></th>
                         
                            <?php if(auth()->user()->role_id == 2): ?>
                            <th scope="col" class="text-center"><?php echo e(get_phrase('Option')); ?></th>
                            <?php endif; ?>

                            </thead>
                         <tbody>
                            <?php foreach($list_of_decline_leaves as $key => $leave):?>
                            <tr>
                                <td>
                                    <?php echo e($key+1); ?>

                                </td>

                                <?php if(auth()->user()->role_id == 2): ?>

                                <td>
                                    <?php $name=Hr_user_list::find($leave['user_id']);
                                       ?>

                                    <div class="dAdmin_profile d-flex align-items-center min-w-150px">
                                        <div class="dAdmin_profile_name">
                                            <h4> <?php echo e(get_phrase(ucfirst($name->name??""))); ?></h4>

                                        </div>
                                      </div>

                                </td>
                                <td>
                                    <?php   $r=Hr_roles::where('id',$name['role_id']??"0")->first();
                                             if(!empty($r))
                                             {
                                              $r=$r->toArray();
                                             }
                                              ?>

                                          <div class="dAdmin_info_name min-w-150px">
                                              <p><?php echo e($r['name']??""); ?></p>

                                          </div>
                                </td>

                                <?php endif; ?>

                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p>  <?php echo e(date('d/m/Y', $leave['start_date'])); ?></p>

                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> <?php echo e(date('d/m/Y', $leave['end_date'])); ?></p>

                                    </div>
                                </td>
                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                        <p> <?php echo e(substr($leave['reason'], 0, 50) . '...'); ?></p>

                                    </div>
                                </td>
                           

                                <td>
                                    <div class="dAdmin_info_name min-w-150px">
                                    <?php if(auth()->user()->role_id==2): ?>

                                    <div class="dropdown text-start">
                                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2 " data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo e(get_phrase('Actions')); ?>

                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <!-- item-->

                                            <?php if($leave['status']==0 || $leave['status']==2): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'approve'])); ?>', 'undefined');"> <?php echo e(get_phrase('Approve')); ?></a>
                                              </li>
                                            <?php endif; ?>

                                            <?php if($leave['status']==1 || $leave['status']==0): ?>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'decline'])); ?>', 'undefined');"> <?php echo e(get_phrase('Decline')); ?></a>
                                              </li>
                                            <?php endif; ?>

                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('hr.actions_on_employee_leave', ['id'=>$leave['id'],'action'=>'delete'])); ?>', 'undefined');"> <?php echo e(get_phrase('Delete')); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                    <?php endif; ?>

                                </td>
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

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make($roleName, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/hr_leave/list.blade.php ENDPATH**/ ?>