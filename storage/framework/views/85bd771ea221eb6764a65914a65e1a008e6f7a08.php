
   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4><?php echo e(get_phrase('Admission')); ?></h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
              <li><a href="#"><?php echo e(get_phrase('Admissions')); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="user-profile-area d-flex flex-wrap">
  <!-- Right side -->
  <div class="user-details-info">
    <!-- Tab label -->
    <ul
     class="nav nav-tabs eNav-Tabs-custom"
     id="myTab"
     role="tablist"
     >
      <li class="nav-item" role="presentation">
      	<a href="<?php echo e(route('admin.offline_admission.single', ['type' => 'single'])); ?>">
      		<button
              class="nav-link <?php echo e($aria_expand == 'single' ? 'active':''); ?>"
              id="basicInfo-tab"
              data-bs-toggle="tab"
              data-bs-target="#basicInfo"
              type="button"
              role="tab"
              aria-controls="basicInfo"
              aria-selected="true"
            >
              Single student admission
              <span></span>
          </button>
      	</a>
      </li>
      <li class="nav-item d-none" role="presentation">
        <a href="<?php echo e(route('admin.offline_admission.single', ['type' => 'bulk'])); ?>">
      		<button
              class="nav-link <?php echo e($aria_expand == 'bulk' ? 'active':''); ?>"
              id="attendance-tab"
              data-bs-toggle="tab"
              data-bs-target="#attendance"
              type="button"
              role="tab"
              aria-controls="attendance"
              aria-selected="false"
            >
              <?php echo e(get_phrase('Bulk student admission')); ?>

              <span></span>
          </button>
      	</a>
      </li>
      <li class="nav-item" role="presentation">
      	<a href="<?php echo e(route('admin.offline_admission.single', ['type' => 'excel'])); ?>">
      		<button
              class="nav-link <?php echo e($aria_expand == 'excel' ? 'active':''); ?>"
              id="attendance-tab"
              data-bs-toggle="tab"
              data-bs-target="#attendance"
              type="button"
              role="tab"
              aria-controls="attendance"
              aria-selected="false"
            >
              <?php echo e(get_phrase('Excel upload')); ?>

              <span></span>
          </button>
      	</a>
      </li>
	  </ul>
    <!-- Tab content -->
    <div class="tab-content eNav-Tabs-content" id="myTabContent">
      <div
        class="tab-pane fade show active"
        id="basicInfo"
        role="tabpanel"
        aria-labelledby="basicInfo-tab"
      >
        <?php if($aria_expand == 'single'): ?>
					<?php echo $__env->make('admin.offline_admission.single_student_admission', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				<?php elseif($aria_expand == 'bulk'): ?>
					<?php echo $__env->make('admin.offline_admission.bulk_student_admission', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				<?php else: ?>
					<?php echo $__env->make('admin.offline_admission.excel_student_admission', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				<?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<script type="text/javascript">

  "use strict";

	function classWiseSection(classId) {
	    let url = "<?php echo e(route('admin.class_wise_sections', ['id' => ":classId"])); ?>";
	    url = url.replace(":classId", classId);
	    $.ajax({
	        url: url,
	        success: function(response){
	            $('#section_id').html(response);
	        }
	    });
	}
</script>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/admin/offline_admission/offline_admission.blade.php ENDPATH**/ ?>