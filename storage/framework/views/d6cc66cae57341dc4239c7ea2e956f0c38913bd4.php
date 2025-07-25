<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm responsive_media_query" action="<?php echo e(route('teacher.attendance_take')); ?>">
    <?php echo csrf_field(); ?> 
    <div class="form-row">
        <div class="fpb-7">
            <label for="date_on_taking_attendance" class="eForm-label"><?php echo e(get_phrase('Date')); ?><span class="required">*</span></label>
            <input type="text" class="form-control eForm-control inputDate" id="date_on_taking_attendance" name="date" value="<?php echo e(date('m/d/Y')); ?>" />
        </div>

        <div class="fpb-7">
            <label for="class_id_on_taking_attendance" class="eForm-label"><?php echo e(get_phrase('Class')); ?></label>
            <select name="class_id" id="class_id_on_taking_attendance" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSectionOnTakingAttendance(this.value)" required>
                <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($class['id']); ?>"><?php echo e($class['name']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="fpb-7">
            <label for="section_id_on_taking_attendance" class="eForm-label"><?php echo e(get_phrase('Section')); ?></label>
            <select name="section_id" id="section_id_on_taking_attendance" class="form-select eForm-select eChoice-multiple-with-remove" required >
                <option value=""><?php echo e(get_phrase('Select section')); ?></option>
            </select>
        </div>

        <div class="row py-1" id = "student_content">
        </div>

        <div class='row py-1'>
            <div class="form-group col-md-12" id="showStudentDiv">
                <a class="btn btn-block btn-secondary" onclick="getStudentList()" disabled><?php echo e(get_phrase('Show student list')); ?></a>
            </div>

        </div>
        <div class="form-group display-none-view col-md-12 mt-4" id = "updateAttendanceDiv">
            <button class="btn w-100 btn-primary" type="submit"><?php echo e(get_phrase('Update attendance')); ?></button>
        </div>

    </div>
</form>

<script type="text/javascript">
  
  "use strict";

    $('document').ready(function(){

        $('#class_id_on_taking_attendance').change(function(){
            $('#showStudentDiv').show();
            $('#updateAttendanceDiv').hide();
            $('#student_content').hide();
        });
        $('#section_id_on_taking_attendance').change(function(){
            $('#showStudentDiv').show();
            $('#updateAttendanceDiv').hide();
            $('#student_content').hide();
        });
    });

    function classWiseSectionOnTakingAttendance(classId) {
        let url = "<?php echo e(route('class_wise_sections', ['id' => ":classId"])); ?>";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_on_taking_attendance').html(response);
            }
        });
    }

    function getStudentList() {
        var date = $('#date_on_taking_attendance').val();
        var class_id = $('#class_id_on_taking_attendance').val();
        var section_id = $('#section_id_on_taking_attendance').val();

        if(date != '' && class_id != '' && section_id != ''){
            $.ajax({
                // type : 'POST',
                url : '<?php echo e(route('teacher.attendance.student')); ?>',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {date : date, class_id : class_id, section_id : section_id},
                success : function(response) {
                    $('#student_content').show();
                    $('#student_content').html(response);
                    $('#showStudentDiv').hide();
                    $('#updateAttendanceDiv').show();
                }
            });
        }else{
            toastr.error("<?php echo e(get_phrase('Please select in all fields !')); ?>");
        }
    }

    $(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

    
</script>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/attendance/take_attendance.blade.php ENDPATH**/ ?>