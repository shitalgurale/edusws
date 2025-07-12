
   
<?php $__env->startSection('content'); ?>

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\Section;

$user = Auth()->user();
$menu_permission = (empty($user->menu_permission) || $user->menu_permission == 'null') ? []:json_decode($user->menu_permission, true);
?>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Students')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Users')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Students')); ?></a></li>
            </ul>
          </div>
          <?php if(empty($user->menu_permission) || in_array('admin.offline_admission.single', $menu_permission)): ?> 
          <div class="export-btn-area">
            <a href="<?php echo e(route('admin.offline_admission.single', ['type' => 'single'])); ?>" class="export_btn"><?php echo e(get_phrase('Create Student')); ?></a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
</div>
<!-- Start Students area -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
          <!-- Search and filter -->
            <div
              class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
            >
              <form action="<?php echo e(route('admin.student')); ?>">
                <div
                  class="search-input d-flex justify-content-start align-items-center"
                >
                  <span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 16 16"
                    >
                      <path
                        id="Search_icon"
                        data-name="Search icon"
                        d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                        fill="#797c8b"
                      />
                    </svg>
                  </span>
                  <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?php echo e($search); ?>"
                    placeholder="Search Students"
                    class="form-control"
                  />
               <!--   <?php if($class_id != ''): ?>
                  <input type="hidden" name="class_id" id="class_id" value="<?php echo e($class_id); ?>">
                  <?php endif; ?>
                  <?php if($section_id != ''): ?>
                  <input type="hidden" name="section_id" id="section_id" value="<?php echo e($section_id); ?>">
                  <?php endif; ?>-->
                </div>
              </form>
              
              
              <div class="filter-export-area d-flex align-items-center">
                <div class="position-relative filter-option d-flex">
                    
                        <?php if($search != ''): ?>
                        <input type="hidden" name="search" id="search" value="<?php echo e($search); ?>">
                        <?php endif; ?>
                        <div>
                            
                        <!--  <label for="class_id" class="eForm-label"
                            ><?php echo e(get_phrase('Class')); ?></label
                          >-->
                          <select class="form-select" name="class_id" id="class_id" onchange="classWiseSection(this.value)" required>
    <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($class->id); ?>" <?php echo e((!empty($class_id) && $class_id == $class->id) ? 'selected' : ''); ?>>
            <?php echo e($class->name); ?>

        </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

                        </div>
                        <div class="position-relative">
                          <div class="position-relative">
  <select class="form-select" name="section_id" id="section_id" data-placeholder="Type to search...">
    <?php if(!empty($class_id)): ?>
        <option value=""><?php echo e(get_phrase('Select a section')); ?></option>
        <?php $__currentLoopData = \App\Models\Section::where('class_id', $class_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($section->id); ?>" <?php echo e((!empty($section_id) && $section_id == $section->id) ? 'selected' : ''); ?>>
                <?php echo e($section->name); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <option value=""><?php echo e(get_phrase('Select a section')); ?></option>
    <?php endif; ?>
</select>
</div>
                        </div>
                      
                      <div
                        class="filter-button d-flex justify-content-end align-items-center"
                      >
                        <a class="form-group">
                          <button class="eBtn eBtn btn-primary" type="submit"><?php echo e(get_phrase('Filter')); ?></button>
                        </a>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- Export Button -->
                <?php if(count($students) > 0): ?>
                <div class="position-relative">
                  <button
                    class="eBtn-3 dropdown-toggle"
                    type="button"
                    id="defaultDropdown"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="true"
                    aria-expanded="false"
                  >
                    <span class="pr-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12.31"
                        height="10.77"
                        viewBox="0 0 10.771 12.31"
                      >
                        <path
                          id="arrow-right-from-bracket-solid"
                          d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                          transform="translate(0 12.31) rotate(-90)"
                          fill="#00a3ff"
                        />
                      </svg>
                    </span>
                    <?php echo e(get_phrase('Export')); ?>

                  </button>
                  <ul
                    class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                  >
                    <li>
                        <a class="dropdown-item" id="pdf" href="javascript:;" onclick="Export()"><?php echo e(get_phrase('PDF')); ?></a>
                    </li>
                    <li>
                        <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('student_list')"><?php echo e(get_phrase('Print')); ?></a>
                    </li>
                  </ul>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php if(count($students) > 0): ?>
            <!-- Table -->
            <div class="table-responsive">
              <table class="table eTable eTable-2">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
                    <th scope="col"><?php echo e(get_phrase('Bio ID')); ?></th></th>
                   <!-- <th scope="col"><?php echo e(get_phrase('Email')); ?></th>-->
                    <th scope="col"><?php echo e(get_phrase('User Info')); ?></th>
                    <th scope="col"><?php echo e(get_phrase('Account Status')); ?></th>
                    <th scope="col"><?php echo e(get_phrase('Options')); ?></th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 

                        $student = DB::table('users')->where('id', $user->user_id)->first();

                        $user_image = get_user_image($user->user_id);
                        $info = json_decode($student->user_information);

                        $student_details = (new CommonController)->get_student_academic_info($student->id);
                    ?>
                      <tr>
                        <th scope="row">
                          <p class="row-number"><?php echo e($students->firstItem() + $key); ?></p>
                        </th>
                        <td>
                          <div
                            class="dAdmin_profile d-flex align-items-center min-w-200px"
                          >
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="<?php echo e($user_image); ?>"
                              />
                            </div>
                            <div class="dAdmin_profile_name dAdmin_info_name">
                              <h4><?php echo e($student->name); ?></h4>
                              <p>
                                <?php if(empty($student_details->class_name)): ?>
                                <span><?php echo e(get_phrase('Class')); ?>:</span>
                                 <?php echo e(get_phrase('Removed')); ?>

                                 <br>
                                <span><?php echo e(get_phrase('Section')); ?>:</span>
                                <?php echo e(get_phrase('Removed')); ?>

                                <?php else: ?>
                                <span><?php echo e(get_phrase('Class')); ?>:</span> <?php echo e($student_details->class_name); ?>

                                <br>
                                <span><?php echo e(get_phrase('Section')); ?>:</span> <?php echo e($student_details->section_name); ?>

                                <?php endif; ?>
                              </p>
                            </div>
                          </div>
                        </td>
                       <!-- <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><?php echo e($student->email); ?></p>
                          </div>
                        </td>-->
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><?php echo e($student_details->stu_bioid ?? '-'); ?></p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><span><?php echo e(get_phrase('Phone')); ?>:</span> <?php echo e($info->phone ?? '-'); ?></p>
                            <p>
                              <span><?php echo e(get_phrase('Address')); ?>:</span> <?php echo e($info->address ?? '-'); ?>

                            </p>
                          </div>
                        </td>

                        <td>
                          <div class="dAdmin_info_name min-w-100px">
                            <?php if(!empty($student->account_status == 'disable')): ?>
                            <span class="eBadge ebg-soft-danger"><?php echo e(get_phrase('Disabled')); ?></span>
                            <?php else: ?>
                            <span class="eBadge ebg-soft-success"><?php echo e(get_phrase('Enable')); ?></span>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <div class="adminTable-action">
                            <button
                              type="button"
                              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                              data-bs-toggle="dropdown"
                              aria-expanded="false"
                            >
                              <?php echo e(get_phrase('Actions')); ?>

                            </button>
                            <ul
                              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                            >
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('<?php echo e(route('admin.student.id_card', ['id' => $student->id])); ?>', '<?php echo e(get_phrase('Generate id card')); ?>')"><?php echo e(get_phrase('Generate Id card')); ?></a>
                              </li>

                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('<?php echo e(route('admin.student_edit_modal', ['id' => $student->id])); ?>', 'Edit Student')"><?php echo e(get_phrase('Edit')); ?></a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.student.delete', ['id' => $student->id])); ?>', 'undefined');"><?php echo e(get_phrase('Delete')); ?></a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('<?php echo e(route('admin.student.student_profile', ['id' => $student->id])); ?>','<?php echo e(get_phrase('Student Profile')); ?>')"><?php echo e(get_phrase('Profile')); ?></a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="<?php echo e(route('admin.student.documents', ['id' => $student->id])); ?>"><?php echo e(get_phrase('Documents')); ?></a>
                              </li>
                              <?php if(!empty($student->account_status == 'disable')): ?>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.account_enable', ['id' => $student->id])); ?>', 'undefined');"><?php echo e(get_phrase('Enable')); ?></a>
                              </li>
                              <?php else: ?>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('<?php echo e(route('admin.account_disable', ['id' => $student->id])); ?>', 'undefined');"><?php echo e(get_phrase('Disable')); ?></a>
                              </li>
                              <?php endif; ?>
                            </ul>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
              
              <div
                  class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
                >
                  <p class="admin-tInfo"><?php echo e(get_phrase('Showing').' 1 - '.count($students).' '.get_phrase('from').' '.$students->total().' '.get_phrase('data')); ?></p>
                  <div class="admin-pagi">
                    <?php echo $students->appends(request()->all())->links(); ?>

                  </div>
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

<?php if(count($students) > 0): ?>
<!-- Table -->
<div class="table-responsive student_list display-none-view" id="student_list">
  <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;"><?php echo e(get_phrase(' Students List')); ?></h4>
  <table class="table eTable eTable-2">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col"><?php echo e(get_phrase('Name')); ?></th>
        <th scope="col"><?php echo e(get_phrase('Email')); ?></th>
        <th scope="col"><?php echo e(get_phrase('User Info')); ?></th>
    </thead>
    <tbody>
      <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php 

          $student = DB::table('users')->where('id', $user->user_id)->first();

          $user_image = get_user_image($user->user_id);
          $info = json_decode($student->user_information);

          $student_details = (new CommonController)->get_student_academic_info($student->id);
      ?>
        <tr>
          <th scope="row">
            <p class="row-number"><?php echo e($loop->index + 1); ?></p>
          </th>
          <td>
            <div
              class="dAdmin_profile d-flex align-items-center min-w-200px"
            >
              <div class="dAdmin_profile_img">
                <img
                  class="img-fluid"
                  width="50"
                  height="50"
                  src="<?php echo e(asset('assets')); ?>/<?php echo e($user_image); ?>"
                />
              </div>
              <div class="dAdmin_profile_name dAdmin_info_name">
                <h4><?php echo e($student->name); ?></h4>
                <p>
                  <?php if(empty($student_details->class_name)): ?>
                  <span><?php echo e(get_phrase('Class')); ?>:</span> removed
                  <?php else: ?>
                  <span><?php echo e(get_phrase('Class')); ?>:</span> <?php echo e($student_details->class_name); ?>

                  <?php endif; ?>
                </p>
              </div>
            </div>
          </td>
          <td>
            <div class="dAdmin_info_name min-w-250px">
              <p><?php echo e($student->email); ?></p>
            </div>
          </td>
          <td>
              <div class="dAdmin_info_name min-w-250px">
                <p><span><?php echo e(get_phrase('Phone')); ?>:</span> <?php echo e($info->phone ?? '-'); ?></p>
                <p>
                  <span><?php echo e(get_phrase('Address')); ?>:</span> <?php echo e($info->address ?? '-'); ?>

                </p>
              </div>
            </td>
          
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
  </table>
  {<?php echo $students->appends(request()->all())->links(); ?>}
</div>
<?php endif; ?>


<script type="text/javascript">

  "use strict";

  function classWiseSection(classId) {
    let url = "<?php echo e(route('class_wise_sections', ['id' => ":classId"])); ?>";
    url = url.replace(":classId", classId);
    $.ajax({
        url: url,
        success: function(response){
            $('#section_id').html(response);
        }
    });
  }

  function Export() {

      // Choose the element that our invoice is rendered in.
      const element = document.getElementById("student_list");

      // clone the element
      var clonedElement = element.cloneNode(true);

      // change display of cloned element
      $(clonedElement).css("display", "block");

      // Choose the clonedElement and save the PDF for our user.
    var opt = {
      margin:       1,
      filename:     'student_list_<?php echo e(date("y-m-d")); ?>.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 }
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


<!-- End Students area -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/student/student_list.blade.php ENDPATH**/ ?>