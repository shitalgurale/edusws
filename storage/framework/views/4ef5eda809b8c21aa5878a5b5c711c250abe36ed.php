
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Exam List')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Examination')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Exam List')); ?></a></li>
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
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                        <option value=""><?php echo e(get_phrase('Select a class')); ?></option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="eBtn eBtn btn-secondary" onclick="filter_class()" ><?php echo e(get_phrase('Filter')); ?></button>
                </div>
                <?php if(count($exams) > 0): ?>
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
                            <a class="dropdown-item" id="pdf" href="javascript:;" onclick="Export()"><?php echo e(get_phrase('PDF')); ?></a>
                        </li>
                        <li>
                            <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('exam_list')"><?php echo e(get_phrase('Print')); ?></a>
                        </li>
                      </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body exam_list" id="exam_list">
                <table id="basic-datatable" class="table eTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(get_phrase('Exam')); ?></th>
                            <th><?php echo e(get_phrase('Starting Time')); ?></th>
                            <th><?php echo e(get_phrase('Ending Time')); ?></th>
                            <th><?php echo e(get_phrase('Total Marks')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($exams->firstItem() + $key); ?></td>
                                <td><?php echo e($exam->name); ?></td>
                                <td><?php echo e(date('d M Y - h:i A', $exam->starting_time)); ?></td>
                                <td><?php echo e(date('d M Y - h:i A', $exam->ending_time)); ?></td>
                                <td><?php echo e($exam->total_marks); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $exams->appends(request()->all())->links(); ?>

            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    
    "use strict";
    
    function filter_class(){
        var class_id = $('#class_id').val();
        if(class_id != ""){
            showAllExams();
        }else{
            toastr.error("<?php echo e('Please select a class'); ?>");
        }
    }

    var showAllExams = function () {
        var class_id = $('#class_id').val();
        let url = "<?php echo e(route('teacher.class_wise_exam_list', ['id' => ":class_id"])); ?>";
        url = url.replace(":class_id", class_id);
        if(class_id != ""){
            $.ajax({
                url: url,
                success: function(response){
                    $('.exam_list').html(response);
                }
            });
        }
    }

    function Export() {

        // Choose the element that our invoice is rendered in.
        const element = document.getElementById("exam_list");

        // clone the element
        var clonedElement = element.cloneNode(true);

        // change display of cloned element
        $(clonedElement).css("display", "block");

        // Choose the clonedElement and save the PDF for our user.

        var opt = {
          margin:       1,
          filename:     'exam_list.pdf',
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('teacher.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/teacher/examination/offline_exam_list.blade.php ENDPATH**/ ?>