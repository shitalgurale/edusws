<!-- Jquery Js -->
<script src="<?php echo e(asset('frontend/assets/js/jquery-3.6.1.min.js')); ?>"></script>
<!-- Bootstarp Js -->
<script src="<?php echo e(asset('frontend/assets/js/bootstrap.bundle.min.js')); ?>"></script>
<!-- Main Js -->
<script src="<?php echo e(asset('frontend/assets/js/custom.js')); ?>"></script>

<!--Toaster Script-->
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>

<script>

"use strict";

    <?php if(Session::has('message')): ?>
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.success("<?php echo e(session('message')); ?>");
    <?php endif; ?>

    <?php if(Session::has('error')): ?>
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.error("<?php echo e(session('error')); ?>");
    <?php endif; ?>

    <?php if(Session::has('info')): ?>
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.info("<?php echo e(session('info')); ?>");
    <?php endif; ?>

    <?php if(Session::has('warning')): ?>
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.warning("<?php echo e(session('warning')); ?>");
    <?php endif; ?>
</script><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/frontend/include_buttom.blade.php ENDPATH**/ ?>