<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Addon\Hr_roles;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Addon\HrController;
use App\Models\Addon\HrDailyAttendence;
?>



<?php $__env->startSection('content'); ?>

<style>
   .custom_cs{
    padding: 0.375rem 5.75rem;

   }
   .att-custom_div {

     background-color: white !important;

}
.bdr{

    height: 21px !important;

}


</style>


<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
                <?php if(auth()->user()->role_id == 2): ?>
                <h4>
                    <?php echo e(get_phrase('Daily Attendance')); ?>

                </h4>
                <?php else: ?>
                <h4>
                    <?php echo e(get_phrase('Attendance Report')); ?>

                </h4>
                <?php endif; ?>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Human Resource')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Attendence')); ?></a></li>
            </ul>
          </div>

        </div>
      </div>
    </div>
</div>





<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="row mt-3 d-print-none">
 <?php echo csrf_field(); ?>
                 <div class="col-md-2 mb-1">
      <input type="text" class="form-select inputDate" id="date_on_taking_attendance" name="date"
             value="<?php echo e(date('m/d/Y')); ?>" autocomplete="off" />
    </div>
                    
                    
                    
    

                <?php
                if (auth()->user()->role_id == 2):
                ?>

                <div class="col-md-2 mb-1">
                    <select name="role_id" id="role_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value="">
                            <?php echo e(get_phrase('Select a role')); ?>

                        </option>
                        <option value="All">
                            <?php echo e(get_phrase('All Roles')); ?>

                        </option>
                        <?php $roles =  Hr_roles::where('school_id', auth()->user()->school_id)->get()->toArray();?>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo e($role['id']); ?>">
                            <?php echo e(ucfirst($role['name'])); ?>

                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" id="role_id" name="role_id" value="<?php echo e($user_role); ?>">

                <?php
                endif;
                ?>


                <div class="col-md-2">
                    <button class="btn btn-block btn-secondary"  onclick="filter_attendance()">

                        <?php echo e(get_phrase('Filter')); ?>

                    </button>
                </div>

                <div class="col-md-2">
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
                          Export
                        </button>
                        <ul
                          class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                          <li>
                            <button class="dropdown-item" href="#" onclick="download_csv()" >CSV</button>
                          </li>
                          <li>
                            <button class="dropdown-item" href="#" onclick="submitEmployeeExportPDF()" >PDF</button>
                          </li>
                        </ul>
                      </div>
                </div>



            </div>
            <div class="card-body attendance_content">

                <div class="empty_box text-center">
                    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                    <br>
                    <span class="">
                        <?php echo e(get_phrase('Search Attendance Report')); ?>

                    </span>
                </div>
            </div>

            <?php if($no_user == 0): ?>
                <div class="empty_box text-center">
                    <img class="mb-3 " width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />

                    <br>
                    <span class="">
                        <?php echo e(get_phrase('You are not registered yet')); ?> 
                     </span>
                </div>
    <?php endif; ?>






        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('head'); ?>
<!-- Required CSS for date picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Required JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>

<script>

"use strict";

function filter_attendance() {
    var date = $('#date_on_taking_attendance').val();
    var role_id = $('#role_id').val();

    if (date !== "" && role_id !== "") {
        $.ajax({
            url: '<?php echo e(route('admin.attendance.hr_daily_attendance.filter')); ?>',
            type: "GET",
            data: { date: date, role_id: role_id },
            success: function (response) {
                $('.attendance_content').html(response);
            },
            error: function () {
                toastr.error('Failed to load daily attendance.');
            }
        });
    } else {
        toastr.error('Please select date and role.');
    }
}

$(document).ready(function () {
    $('.inputDate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        locale: {
            format: 'MM/DD/YYYY'
        }
    });
});


function submitEmployeeExportPDF() {
    var date = $('#date_on_taking_attendance').val();
    var role_id = $('#role_id').val();

    if (date !== "" && role_id !== "") {
        var exportUrl = '<?php echo e(route("admin.attendance.export_daily_pdf")); ?>';
        var urlWithParams = exportUrl + '?date=' + encodeURIComponent(date) + '&role_id=' + encodeURIComponent(role_id);
        window.open(urlWithParams, '_blank');
    } else {
        toastr.error('Please select both date and role to export.');
    }
}


function download_csv() {
    let date = document.getElementById('date_on_taking_attendance').value;
    let role_id = document.getElementById('role_id').value;

    if (!date || !role_id) {
        toastr.error('Please select all the required filters.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "<?php echo e(route('admin.exportHrDailyCSV')); ?>";
    form.target = '_blank';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "<?php echo e(csrf_token()); ?>";
    form.appendChild(csrf);

    const fields = { date, role_id };
    for (let key in fields) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}


</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make($roleName, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/hr_daily_attendance.blade.php ENDPATH**/ ?>