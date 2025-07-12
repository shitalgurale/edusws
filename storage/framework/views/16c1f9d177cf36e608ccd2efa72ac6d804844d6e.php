<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(get_settings('system_title')); ?></title>

    <?php echo $__env->make('frontend.include_top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</head>

<body data-bs-spy="scroll" data-bs-target=".header-area" data-bs-offset="50" tabindex="0">

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('external_plugin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php echo $__env->make('frontend.include_buttom', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
</body>
</html><?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/frontend/index.blade.php ENDPATH**/ ?>