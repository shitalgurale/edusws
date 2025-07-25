

<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('View Marks')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Examination')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Marks')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="row mt-3">

                <div class="col-md-4"></div>

                <div class="col-md-3">
                    <select name="student" id="student_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value=""><?php echo e(get_phrase('Select student')); ?></option>
                        <?php $__currentLoopData = $student_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $each_student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($each_student['user_id']); ?>"><?php echo e($each_student['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    </select>
                </div>

                <div class="col-md-2">
                    <button class="eBtn eBtn btn-secondary" onclick="filter_class_routine()" ><?php echo e(get_phrase('Filter')); ?></button>
                </div>

                <div class="col-md-3">
                    <div class="export position-relative">
                      <button class="eBtn-3 dropdown-toggle float-end mb-4" type="button" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <span class="pr-10">
                          <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
                            <path id="arrow-right-from-bracket-solid" d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z" transform="translate(0 12.31) rotate(-90)" fill="#00a3ff"></path>
                          </svg>
                        </span>
                        <?php echo e(get_phrase('Export')); ?>

                      </button>
                      <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                        <li>
                            <a class="dropdown-item" id="pdf" href="javascript:;" onclick="generatePDF()"><?php echo e(get_phrase('PDF')); ?></a>
                        </li>
                        <li>
                            <a class="dropdown-item" id="print" href="javascript:;" onclick="ePrintDiv('mark_report')"><?php echo e(get_phrase('Print')); ?></a>
                        </li>
                      </ul>
                    </div>
                </div>

                <div class="card-body marks_content">
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                        <br>
                        <?php echo e(get_phrase('No data found')); ?>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<script type="text/javascript">

  "use strict";


    function filter_class_routine(){
        var student_id = $('#student_id').val();

        if(student_id != "" ){
            getFilteredClassRoutine();
        }else{
            toastr.error('<?php echo e(get_phrase('Please select student')); ?>');
        }
    }

    var getFilteredClassRoutine = function() {
        var student_id = $('#student_id').val();

        if(student_id != ""){
            let url = "<?php echo e(route('parent.marks_list')); ?>";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {student_id : student_id},
                success: function(response){
                    $('.marks_content').html(response);
                }
            });
        }
    }

    function ePrintDiv(ePrintDivId) {
        var printContents = document.getElementById(ePrintDivId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }

    function generatePDF() {

        // Choose the element that our invoice is rendered in.
        const element = document.getElementById("mark_report");

        // clone the element
        var clonedElement = element.cloneNode(true);

        // change display of cloned element 
        $(clonedElement).css("display", "block");

        // Choose the clonedElement and save the PDF for our user.

        var opt = {
          margin:       1,
          filename:     'Mark report.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2 }
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(clonedElement).save();

        // remove cloned element
        clonedElement.remove();
    }

</script>

<?php echo $__env->make('parent.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/parent/marks/index.blade.php ENDPATH**/ ?>