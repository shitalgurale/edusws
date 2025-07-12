<!DOCTYPE html>
<html lang="en">
<head>
	<!-- New -->
  <title>{{ get_phrase('Student').' | '.get_settings('system_title') }}</title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- all the css files -->
    <link rel="shortcut icon" href="{{ asset('assets/uploads/logo/'.get_settings('favicon')) }}" />
    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}"
    />

    <!--Custom css-->
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/css/swiper-bundle.min.css') }}"
    />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" />
    <!-- Datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <!-- fontawasome css -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}" />
    <!-- Player Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css') }}" />

    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}"
    />

    <!--Toaster css-->
    <link 
      rel="stylesheet" 
      type="text/css" 
      href="{{ asset('assets/css/toastr.min.css') }}"
    />

    <!-- Calender css -->
    <link  rel="stylesheet" type="text/css" href="{{ asset('assets/calender/main.css') }}"/>
    <link  rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom2.css') }}"/>

</head>
<body>
