<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo e(get_phrase('Login').' | '.get_settings('system_title')); ?></title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2196f3">

  <link rel="shortcut icon" href="<?php echo e(asset('assets/uploads/logo/'.get_settings('favicon'))); ?>" />
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/css/toastr.min.css')); ?>"/>
  
    <!-- Install Button Style -->
    <style>
    #install-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 12px 18px;
      border-radius: 10px;
      cursor: pointer;
      z-index: 9999;
      box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    </style>  
</head>

<body>
<div class="container-fluid h-100">
    <?php echo $__env->yieldContent('content'); ?>
</div>

<?php echo $__env->make('external_plugin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- JS Libraries -->
<script src="<?php echo e(asset('assets/vendors/jquery/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/script.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>

<!-- Toastr Notifications -->
<script>
"use strict";
<?php if(Session::has('message')): ?>
toastr.success("<?php echo e(session('message')); ?>", "", { closeButton: true, progressBar: true });
<?php endif; ?>
<?php if(Session::has('error')): ?>
toastr.error("<?php echo e(session('error')); ?>", "", { closeButton: true, progressBar: true });
<?php endif; ?>
<?php if(Session::has('info')): ?>
toastr.info("<?php echo e(session('info')); ?>", "", { closeButton: true, progressBar: true });
<?php endif; ?>
<?php if(Session::has('warning')): ?>
toastr.warning("<?php echo e(session('warning')); ?>", "", { closeButton: true, progressBar: true });
<?php endif; ?>
</script>

<!-- PWA Install Button -->
<button id="install-btn" style="display: none;">Install EduSWS</button>

<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  const installBtn = document.getElementById('install-btn');
  if (installBtn) {
    installBtn.style.display = 'block';
    installBtn.addEventListener('click', () => {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then((choice) => {
        console.log('Install outcome:', choice.outcome);
        installBtn.style.display = 'none';
        deferredPrompt = null;
      });
    });
  }
});

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/serviceworker.js')
    .then(reg => console.log('Service worker registered.', reg))
    .catch(err => console.warn('Service worker registration failed.', err));
}

if (window.matchMedia('(display-mode: standalone)').matches) {
  const installBtn = document.getElementById('install-btn');
  if (installBtn) installBtn.style.display = 'none';
}
</script>

<script>
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('✅ beforeinstallprompt event fired');
});
</script>

</body>
</html>
<?php /**PATH /home/siliconcpanel/public_html/edusws.appstime.in/resources/views/layouts/signin_page.blade.php ENDPATH**/ ?>