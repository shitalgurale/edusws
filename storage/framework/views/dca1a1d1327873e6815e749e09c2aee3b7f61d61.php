

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4><?php echo e(get_phrase('Daily Attendance')); ?></h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
            <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
            <li><a href="#"><?php echo e(get_phrase('Daily Attendance')); ?></a></li>
          </ul>
        </div>
        
         
     </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-3">

      
      <div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
   <!-- Filter area -->
      <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" onsubmit="event.preventDefault(); getStudentList();">
  <?php echo csrf_field(); ?>
  <div class="d-flex flex-wrap align-items-end gap-2">

    <div class="att-filter-option">
     
      <input type="text" class="form-select inputDate" id="date_on_taking_attendance" name="date"
             value="<?php echo e(date('m/d/Y')); ?>" autocomplete="off" />
    </div>

    <div class="att-filter-option">
  <select name="class_id" id="class_id_on_taking_attendance"
    class="form-select eForm-select eChoice-multiple-with-remove" required
    onchange="classWiseSectionOnTakingAttendance(this.value)">
    <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</div>

<div class="att-filter-option">
  <select name="section_id" id="section_id_on_taking_attendance"
    class="form-select eForm-select eChoice-multiple-with-remove" required>
    <option value=""><?php echo e(get_phrase('Select section')); ?></option>
  </select>
</div>

    <div>
      <button type="submit" class="btn btn-secondary">
        <?php echo e(get_phrase('Filter')); ?>

      </button>
    </div>

    <div class="position-relative">
      <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown" data-bs-toggle="dropdown"
              data-bs-auto-close="true" aria-expanded="false">
        <span class="pr-10">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
            <path id="arrow-right-from-bracket-solid"
              d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
              transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
          </svg>
        </span>
        Export
      </button>
      <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
        <li>
          <button class="dropdown-item" onclick="download_csv()">CSV</button>
        </li> 
        <li>
          <button class="dropdown-item" onclick="submitExportForm()">PDF</button>
        </li>
      </ul>
    </div>

  </div>
</form>


      
   <!-- Container for dynamic attendance data -->
<div class="mt-3" id="student_content" style="display: none;"></div>

<!-- Default message box -->
<div class="card-body attendance_content text-center" id="default_attendance_box">
  <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
  <br>
  <span id="attendance_message"><?php echo e(get_phrase('Search Attendance Report')); ?></span>
</div>
</div></div>
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

<script>
"use strict";

$(document).ready(function () {
    // Initialize date picker
    $('.inputDate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1901,
        maxYear: parseInt(moment().format("YYYY"), 10),
    });

    $(".eChoice-multiple-with-remove").select2();

    $('#class_id_on_taking_attendance').change(function () {
        $('#showStudentDiv').show();
        $('#student_content').hide();
    });

    $('#section_id_on_taking_attendance').change(function () {
        $('#showStudentDiv').show();
        $('#student_content').hide();
    });
});

function classWiseSectionOnTakingAttendance(classId) {
    let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ':classId'])); ?>";
    url = url.replace(":classId", classId);
    $.ajax({
        url: url,
        success: function (response) {
            $('#section_id_on_taking_attendance').html(response);
        }
    });
}

function getStudentList() {
    var date = $('#date_on_taking_attendance').val();
    var class_id = $('#class_id_on_taking_attendance').val();
    var section_id = $('#section_id_on_taking_attendance').val();

    if (date && class_id && section_id) {
        $.ajax({
            url: '<?php echo e(route('admin.attendance.student_daily_attendance')); ?>',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                date: date,
                class_id: class_id,
                section_id: section_id
            },
            success: function(response) {
                if ($.trim(response) === '') {
                    $('#student_content').hide();
                    $('#default_attendance_box').show();
                    $('#attendance_message').text("<?php echo e(get_phrase('No Attendance Found')); ?>");
                } else {
                    $('#default_attendance_box').hide();
                    $('#student_content').html(response).show();
                }
            },
            error: function() {
                $('#student_content').hide();
                $('#default_attendance_box').show();
                $('#attendance_message').text("<?php echo e(get_phrase('Error fetching data')); ?>");
            }
        });
    } else {
        toastr.error("<?php echo e(get_phrase('Please select all fields.')); ?>");
    }
}

function download_csv() {
    let date = document.getElementById('date_on_taking_attendance').value;
    let class_id = document.getElementById('class_id_on_taking_attendance').value;
    let section_id = document.getElementById('section_id_on_taking_attendance').value;

    if (!date || !class_id || !section_id) {
        toastr.error('Please select all the required filters.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "<?php echo e(route('admin.exportStudentDailyCSV')); ?>";
    form.target = '_blank';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = "<?php echo e(csrf_token()); ?>";
    form.appendChild(csrf);

    const fields = { date, class_id, section_id };
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

<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/attendance/student_daily_attendance.blade.php ENDPATH**/ ?>