<!DOCTYPE html>
<html lang="en">

<head>
  <title>{{ get_phrase('Login').' | '.get_settings('system_title') }}</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2196f3">

  <link rel="shortcut icon" href="{{ asset('assets/uploads/logo/'.get_settings('favicon')) }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}"/>
  
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
    @yield('content')
</div>

@include('external_plugin')

<!-- JS Libraries -->
<script src="{{ asset('assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

<!-- Toastr Notifications -->
<script>
"use strict";
@if(Session::has('message'))
toastr.success("{{ session('message') }}", "", { closeButton: true, progressBar: true });
@endif
@if(Session::has('error'))
toastr.error("{{ session('error') }}", "", { closeButton: true, progressBar: true });
@endif
@if(Session::has('info'))
toastr.info("{{ session('info') }}", "", { closeButton: true, progressBar: true });
@endif
@if(Session::has('warning'))
toastr.warning("{{ session('warning') }}", "", { closeButton: true, progressBar: true });
@endif
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
