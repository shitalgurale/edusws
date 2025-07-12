

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div
        class="d-flex justify-content-between align-items-center flex-wrap gr-15"
      >
        <div class="d-flex flex-column">
          <h4><?php echo e(get_phrase('Daily Attendance')); ?></h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
            <li><a href="#"><?php echo e(get_phrase('Academic')); ?></a></li>
            <li><a href="#"><?php echo e(get_phrase('Daily Attendance')); ?></a></li>
          </ul>
        </div>
        <div class="export-btn-area">
          <a href="#" class="export_btn" onclick="rightModal('<?php echo e(route('teacher.take_attendance.open_modal')); ?>', '<?php echo e(get_phrase('Take Attendance')); ?>')"><?php echo e(get_phrase('Take Attendance')); ?></a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <!-- Filter area -->
      <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" action="<?php echo e(route('teacher.daily_attendance.filter')); ?>">
        <div class="att-filter d-flex flex-wrap">
          <div class="att-filter-option">
            <select name="month" id="month" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value=""><?php echo e(get_phrase('Select a month')); ?></option>
              <option value="Jan"<?php echo e(date('M') == 'Jan' ?  'selected':''); ?>><?php echo e(get_phrase('January')); ?></option>
              <option value="Feb"<?php echo e(date('M') == 'Feb' ?  'selected':''); ?>><?php echo e(get_phrase('February')); ?></option>
              <option value="Mar"<?php echo e(date('M') == 'Mar' ?  'selected':''); ?>><?php echo e(get_phrase('March')); ?></option>
              <option value="Apr"<?php echo e(date('M') == 'Apr' ?  'selected':''); ?>><?php echo e(get_phrase('April')); ?></option>
              <option value="May"<?php echo e(date('M') == 'May' ?  'selected':''); ?>><?php echo e(get_phrase('May')); ?></option>
              <option value="Jun"<?php echo e(date('M') == 'Jun' ?  'selected':''); ?>><?php echo e(get_phrase('June')); ?></option>
              <option value="Jul"<?php echo e(date('M') == 'Jul' ?  'selected':''); ?>><?php echo e(get_phrase('July')); ?></option>
              <option value="Aug"<?php echo e(date('M') == 'Aug' ?  'selected':''); ?>><?php echo e(get_phrase('August')); ?></option>
              <option value="Sep"<?php echo e(date('M') == 'Sep' ?  'selected':''); ?>><?php echo e(get_phrase('September')); ?></option>
              <option value="Oct"<?php echo e(date('M') == 'Oct' ?  'selected':''); ?>><?php echo e(get_phrase('October')); ?></option>
              <option value="Nov"<?php echo e(date('M') == 'Nov' ?  'selected':''); ?>><?php echo e(get_phrase('November')); ?></option>
              <option value="Dec"<?php echo e(date('M') == 'Dec' ?  'selected':''); ?>><?php echo e(get_phrase('December')); ?></option>
            </select>
          </div>
          <div class="att-filter-option">
            <select name="year" id="year" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value=""><?php echo e(get_phrase('Select a year')); ?></option>
              <?php for($year = 2015; $year <= date('Y'); $year++){ ?>
                <option value="<?php echo e($year); ?>"<?php echo e(date('Y') == $year ?  'selected':''); ?>><?php echo e($year); ?></option>
              <?php } ?>

            </select>
          </div>
          <div class="att-filter-option">
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value)" required>
              <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
              <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($class['id']); ?>"><?php echo e($class['name']); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="att-filter-option">
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value=""><?php echo e(get_phrase('Select section')); ?></option>
            </select>
          </div>
          <div class="att-filter-btn">
            <button class="eBtn eBtn btn-secondary" type="submit" ><?php echo e(get_phrase('Filter')); ?></button>
          </div>
        </div>
      </form>
      <div class="card-body attendance_content">
        <div class="empty_box center">
          <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
          <br>
          <span class=""><?php echo e(get_phrase('No data found')); ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

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

function filter_attendance(){
  var month = $('#month').val();
  var year = $('#year').val();
  var class_id = $('#class_id').val();
  var section_id = $('#section_id').val();
  if(class_id != "" && section_id != "" && month != "" && year != ""){
    getDailtyAttendance();
  }else{
    toastr.error('<?php echo e(get_phrase('Please select in all fields !')); ?>');
  }
}

var getDailtyAttendance = function () {
  var month = $('#month').val();
  var year = $('#year').val();
  var class_id = $('#class_id').val();
  var section_id = $('#section_id').val();
  let url = "<?php echo e(route('teacher.daily_attendance.filter')); ?>";
  if(class_id != "" && section_id != "" && month != "" && year != ""){
    $.ajax({
		url: url,
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		data: {month : month, year : year, class_id : class_id, section_id : section_id},
    success: function(response){
        $('.attendance_content').html(response);
      }
    });
  }
}


</script>
<?php echo $__env->make('teacher.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/attendance/daily_attendance.blade.php ENDPATH**/ ?>