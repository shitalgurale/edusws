<!DOCTYPE html>
<html lang="en">
<head>
	<!-- New -->
  <title>{{ get_phrase('Parent').' | '.get_settings('system_title') }}</title>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}" />
    <!-- Datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

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
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/calender/main.css') }}"
    />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    
    
    <style>
/* Tooltip styling */
.message_ico {
    position: relative;
}

.message_ico:hover::after {
    content: 'Chat';
    position: absolute;
    top: -25px; /* adjust position */
    left: 50%;
    transform: translateX(-50%);
    background: #fff; /* white background */
    color: #000; /* black text */
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.15);
    white-space: nowrap;
    z-index: 100;
}

/* Hide tooltip when not hovering */
.message_ico::after {
    display: none;
}

.message_ico:hover::after {
    display: block;
}
</style>

     <!--Main Jquery-->
     <script src="{{ asset('assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
</head>
<body>

	<div class="sidebar">
		<div class="logo-details mt-4 mb-3">
      <div class="img_wrapper">
          <img height="40px" class="" src="{{ asset('assets/uploads/logo/'.get_settings('white_logo')) }}" alt="" />
      </div>
      <span class="logo_name">{{ get_settings('navbar_title') }}</span>
    </div>
		<div class="closeIcon">
      <span>
        <img src="{{ asset('assets/images/close.svg') }}">
      </span>
    </div>
		<ul class="nav-links">
			<!-- sidebar title -->
      <li class="nav-links-li {{ request()->is('parent/dashboard') ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="{{ route('parent.dashboard') }}">
            <div class="sidebar_icon">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
              <g>
                <path d="M117.333,234.667C52.532,234.667,0,182.135,0,117.333S52.532,0,117.333,0s117.333,52.532,117.333,117.333   C234.596,182.106,182.106,234.596,117.333,234.667z M117.333,64C87.878,64,64,87.878,64,117.333s23.878,53.333,53.333,53.333   s53.333-23.878,53.333-53.333S146.789,64,117.333,64z"/>
                <path d="M394.667,234.667c-64.801,0-117.333-52.532-117.333-117.333S329.865,0,394.667,0S512,52.532,512,117.333   C511.929,182.106,459.439,234.596,394.667,234.667z M394.667,64c-29.455,0-53.333,23.878-53.333,53.333   s23.878,53.333,53.333,53.333S448,146.789,448,117.333S424.122,64,394.667,64z"/>
                <path d="M117.333,512C52.532,512,0,459.468,0,394.667s52.532-117.333,117.333-117.333s117.333,52.532,117.333,117.333   C234.596,459.439,182.106,511.929,117.333,512z M117.333,341.333C87.878,341.333,64,365.211,64,394.667S87.878,448,117.333,448   s53.333-23.878,53.333-53.333S146.789,341.333,117.333,341.333z"/>
                <path d="M394.667,512c-64.801,0-117.333-52.532-117.333-117.333s52.532-117.333,117.333-117.333S512,329.865,512,394.667   C511.929,459.439,459.439,511.929,394.667,512z M394.667,341.333c-29.455,0-53.333,23.878-53.333,53.333S365.211,448,394.667,448   S448,424.122,448,394.667S424.122,341.333,394.667,341.333z"/>
              </g>
              </svg>

            </div>
            <span class="link_name">{{ get_phrase('Dashboard') }}</span>
          </a>
        </div>
      </li>
      <!-- Inbox -->
    <!--  <li class="nav-links-li {{ request()->is('parent/compose/inbox') ? 'showMenu' : '' }}">
        <div class="iocn-link">
          <a href="{{ route('parent.inbox') }}">
            <div class="sidebar_icon">
              <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1.2l-10 6-10-6V6zm0 3.8v8.2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9.8l-10 6-10-6z"/>
              </svg>
            </div>
            <span class="link_name">{{ get_phrase('Inbox') }}</span>
            @if(isset($unreadMessageCount) && $unreadMessageCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadMessageCount }}
            </span>
        @endif
          </a>
        </div>
      </li>-->
<!-- Inbox -->
<li class="nav-links-li {{ request()->is('parent/compose/inbox') ? 'showMenu' : '' }}">
  <a href="{{ route('parent.inbox') }}" class="d-flex align-items-center justify-content-start">
    <div class="sidebar_icon d-flex align-items-center position-relative">
      <div class="position-relative">
        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="currentColor" viewBox="0 0 24 24">
          <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1.2l-10 6-10-6V6zm0 3.8v8.2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9.8l-10 6-10-6z"/>
        </svg>
        @if(isset($unreadMessageCount) && $unreadMessageCount > 0)
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
            {{ $unreadMessageCount }}
          </span>
        @endif
      </div>
      <span class="link_name ms-2">{{ get_phrase('Inbox') }}</span>
    </div>
  </a>
</li>




			<!-- Sidebar menu -->

			<li class="nav-links-li {{ request()->is('parent/teacherlist*')||request()->is('parent/childlist*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a href="#">
						<div class="sidebar_icon">
							<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,24a1.5,1.5,0,0,1-1.489-1.335,3.031,3.031,0,0,0-6.018,0,1.5,1.5,0,0,1-2.982-.33,6.031,6.031,0,0,1,11.982,0,1.5,1.5,0,0,1-1.326,1.656A1.557,1.557,0,0,1,16.5,24Zm6.167-9.009a1.5,1.5,0,0,0,1.326-1.656A5.815,5.815,0,0,0,18.5,8a1.5,1.5,0,0,0,0,3,2.835,2.835,0,0,1,2.509,2.665A1.5,1.5,0,0,0,22.5,15,1.557,1.557,0,0,0,22.665,14.991ZM2.991,13.665A2.835,2.835,0,0,1,5.5,11a1.5,1.5,0,0,0,0-3A5.815,5.815,0,0,0,.009,13.335a1.5,1.5,0,0,0,1.326,1.656A1.557,1.557,0,0,0,1.5,15,1.5,1.5,0,0,0,2.991,13.665ZM12.077,16a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,12.077,16Zm6-9a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,18.077,7Zm-12,0a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,6.077,7Z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Users') }}</span>
					</a>
					<span class="arrow">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="4.743"
              height="7.773"
              viewBox="0 0 4.743 7.773"
            >
              <path
                id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                fill="#fff"
                opacity="1"
              />
            </svg>
          </span>
				</div>
				<ul class="sub-menu">
					<li><a class="{{ (request()->is('parent/teacherlist*')) ? 'active' : '' }}" href="{{ route('parent.teacherlist') }}"><span>{{ get_phrase('Teacher') }}</span></a></li>
          <li><a class="{{ (request()->is('parent/childlist*')) ? 'active' : '' }}" href="{{ route('parent.childlist') }}"><span>{{ get_phrase('Child') }}</span></a></li>
				</ul>
			</li>

			<li class="nav-links-li {{ request()->is('parent/attendence/list*') || request()->is('parent/routine*') || request()->is('parent/child/syllabus*')|| request()->is('parent/child/subjects*') || request()->is('student/syllabus*') ? 'showMenu':'' }}">
				<div class="iocn-link">
					<a href="#">
						<div class="sidebar_icon">
							<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M7.5,4.5c.151-5.935,8.85-5.934,9,0-.151,5.935-8.85,5.934-9,0ZM24,15.5v1.793c0,2.659-1.899,4.935-4.516,5.411l-5.763,1.139c-1.142,.207-2.285,.21-3.421,.004l-5.807-1.147c-2.595-.472-4.494-2.748-4.494-5.407v-1.793c-.083-3.331,3.222-6.087,6.483-5.411l3.36,.702c.824,.15,1.564,.527,2.16,1.062,.601-.537,1.351-.916,2.191-1.069l3.282-.688c1.653-.301,3.293,.134,4.548,1.181,1.256,1.048,1.976,2.587,1.976,4.223Zm-13.5-.289c0-.726-.518-1.346-1.231-1.476l-3.36-.702c-.707-.126-1.439,.075-2.01,.548-.571,.477-.898,1.176-.898,1.919v1.793c0,1.209,.863,2.243,2.053,2.46l5.447,1.076v-5.618Zm10.5,.289c0-.744-.327-1.443-.897-1.919-.57-.476-1.318-.674-2.05-.54l-3.282,.687c-.753,.137-1.271,.758-1.271,1.483v5.618l5.425-1.072c1.212-.221,2.075-1.255,2.075-2.464v-1.793Z"/></svg>
						</div>
						<span class="link_name">{{ get_phrase('Academic') }}</span>
					</a>
					<span class="arrow">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="4.743"
              height="7.773"
              viewBox="0 0 4.743 7.773"
            >
              <path
                id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                fill="#fff"
                opacity="1"
              />
            </svg>
          </span>
				</div>
				<ul class="sub-menu">
					<li><a class="{{ (request()->is('parent/attendence/list*')) ? 'active' : '' }}" href="{{ route('parent.list_of_attendence') }}"><span>{{ get_phrase('Daily Attendance') }}</span></a></li>
					<li><a class="{{ (request()->is('parent/routine*')) ? 'active' : '' }}" href="{{ route('parent.routine') }}"><span>{{ get_phrase('Class Routine') }}</span></a></li>
          <li><a class="{{ (request()->is('parent/child/subjects*')) ? 'active' : '' }}" href="{{ route('parent.subject_list') }}"><span>{{ get_phrase('Subjects') }}</span></a></li>
          <li><a class="{{ (request()->is('parent/child/syllabus*')) ? 'active' : '' }}" href="{{ route('parent.syllabus_list') }}"><span>{{ get_phrase('Syllabus') }}</span></a></li>
        </ul>
			</li>

      @if (addon_status('transport') == 1)
                <li class="nav-links-li {{ request()->is('parent/trips/list*') ? 'showMenu' : '' }}">
                    <div class="iocn-link">
                        <a href="{{ route('parent.trips.list') }}">

                            {{-- trip icon --}}
                            <div class="sidebar_icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="48"
                                    height="48">
                                    <path
                                        d="M408 120c0 54.6-73.1 151.9-105.2 192c-7.7 9.6-22 9.6-29.6 0C241.1 271.9 168 174.6 168 120C168 53.7 221.7 0 288 0s120 53.7 120 120zm8 80.4c3.5-6.9 6.7-13.8 9.6-20.6c.5-1.2 1-2.5 1.5-3.7l116-46.4C558.9 123.4 576 135 576 152V422.8c0 9.8-6 18.6-15.1 22.3L416 503V200.4zM137.6 138.3c2.4 14.1 7.2 28.3 12.8 41.5c2.9 6.8 6.1 13.7 9.6 20.6V451.8L32.9 502.7C17.1 509 0 497.4 0 480.4V209.6c0-9.8 6-18.6 15.1-22.3l122.6-49zM327.8 332c13.9-17.4 35.7-45.7 56.2-77V504.3L192 449.4V255c20.5 31.3 42.3 59.6 56.2 77c20.5 25.6 59.1 25.6 79.6 0zM288 152a40 40 0 1 0 0-80 40 40 0 1 0 0 80z" />
                                </svg>
                            </div>
                            <span class="link_name">{{ get_phrase('Trips') }}</span>
                        </a>
                    </div>
                </li>
            @endif

			<li class="nav-links-li {{ request()->is('parent/marks') || request()->is('parent/grade') ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="#">
            <div class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M18,17.5A1.5,1.5,0,0,1,16.5,19h-1a1.5,1.5,0,0,1,0-3h1A1.5,1.5,0,0,1,18,17.5ZM13.092,14H10.908A1.5,1.5,0,0,1,8,13.5V10a4,4,0,0,1,8,0v3.5a1.5,1.5,0,0,1-2.908.5ZM11,10v1h2V10a1,1,0,0,0-2,0Zm-.569,5.947-.925.941a1.5,1.5,0,0,0-2.139,2.095s.163.187.189.211a2.757,2.757,0,0,0,3.9-.007l1.116-1.134a1.5,1.5,0,1,0-2.138-2.106ZM22,7.157V18.5A5.507,5.507,0,0,1,16.5,24h-9A5.507,5.507,0,0,1,2,18.5V5.5A5.507,5.507,0,0,1,7.5,0h7.343a5.464,5.464,0,0,1,3.889,1.611l1.657,1.657A5.464,5.464,0,0,1,22,7.157ZM18.985,7H17a2,2,0,0,1-2-2V3.015C14.947,3.012,7.5,3,7.5,3A2.5,2.5,0,0,0,5,5.5v13A2.5,2.5,0,0,0,7.5,21h9A2.5,2.5,0,0,0,19,18.5S18.988,7.053,18.985,7Z"/></svg>
            </div>
            <span class="link_name">
                {{ get_phrase('Examination') }}
            </span>
          </a>
          <span class="arrow">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="4.743"
              height="7.773"
              viewBox="0 0 4.743 7.773"
            >
              <path
                id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                fill="#fff"
                opacity="1"
              />
            </svg>
          </span>
        </div>
        <ul class="sub-menu">
            <li>
                <a class="{{ (request()->is('parent/marks')) ? 'active' : '' }}" href="{{ route('parent.marks') }}"><span>{{ get_phrase('Marks') }}</span></a>
            </li>
            <li>
                <a class="{{ (request()->is('parent/grade')) ? 'active' : '' }}" href="{{ route('parent.grade_list') }}"><span>{{ get_phrase('Grades') }}</span></a>
            </li>
        </ul>
      </li>


      <li class="nav-links-li {{ request()->is('parent/fee_manager*')  ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="#">
            <div class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48" height="48"><path d="M16.5,10c-1.972-.034-1.971-2.967,0-3h1c1.972,.034,1.971,2.967,0,3h-1Zm-3.5,4.413c0-1.476-.885-2.783-2.255-3.331l-2.376-.95c-.591-.216-.411-1.15,.218-1.132h1.181c.181,0,.343,.094,.434,.251,.415,.717,1.334,.962,2.05,.547,.717-.415,.962-1.333,.548-2.049-.511-.883-1.381-1.492-2.363-1.684-.399-1.442-2.588-1.375-2.896,.091-3.161,.875-3.414,5.6-.285,6.762l2.376,.95c.591,.216,.411,1.15-.218,1.132h-1.181c-.181,0-.343-.094-.434-.25-.415-.717-1.334-.961-2.05-.547-.717,.415-.962,1.333-.548,2.049,.511,.883,1.381,1.491,2.363,1.683,.399,1.442,2.588,1.375,2.896-.091,1.469-.449,2.54-1.817,2.54-3.431ZM18.5,1H5.5C2.468,1,0,3.467,0,6.5v11c0,3.033,2.468,5.5,5.5,5.5h3c1.972-.034,1.971-2.967,0-3h-3c-1.379,0-2.5-1.122-2.5-2.5V6.5c0-1.378,1.121-2.5,2.5-2.5h13c1.379,0,2.5,1.122,2.5,2.5v2c.034,1.972,2.967,1.971,3,0v-2c0-3.033-2.468-5.5-5.5-5.5Zm-5.205,18.481c-.813,.813-1.269,1.915-1.269,3.064,.044,.422-.21,1.464,.5,1.455,1.446,.094,2.986-.171,4.019-1.269l6.715-6.715c2.194-2.202-.9-5.469-3.157-3.343l-6.808,6.808Z"/></svg>
            </div>
            <span class="link_name">
                {{ get_phrase('Accounting') }}
            </span>
          </a>
          <span class="arrow">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="4.743"
              height="7.773"
              viewBox="0 0 4.743 7.773"
            >
              <path
                id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                fill="#fff"
                opacity="1"
              />
            </svg>
          </span>
        </div>
        <ul class="sub-menu">
            <li>
              <a class="{{ (request()->is('parent/fee_manager*')) ? 'active' : '' }}" href="{{ route('parent.fee_manager.list') }}">
                <span>{{ get_phrase('Fee Manager') }}</span>
              </a>
            </li>
        </ul>
      </li>

      <li class="nav-links-li {{ request()->is('parent/noticeboard*') || request()->is('parent/events/list*') || request()->is('parent/feedback/filter*') ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="#">
            <div class="sidebar_icon">
                <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="48" height="48"><path d="M18.5,3h-.642A4,4,0,0,0,14,0H10A4,4,0,0,0,6.142,3H5.5A5.506,5.506,0,0,0,0,8.5v10A5.506,5.506,0,0,0,5.5,24h13A5.507,5.507,0,0,0,24,18.5V8.5A5.507,5.507,0,0,0,18.5,3ZM5.5,6h13A2.5,2.5,0,0,1,21,8.5V11H3V8.5A2.5,2.5,0,0,1,5.5,6Zm13,15H5.5A2.5,2.5,0,0,1,3,18.5V14h7a2,2,0,0,0,2,2h0a2,2,0,0,0,2-2h7v4.5A2.5,2.5,0,0,1,18.5,21Z"/></svg>
            </div>
            <span class="link_name">
                {{ get_phrase('Back Office') }}
            </span>
          </a>
          <span class="arrow">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="4.743"
              height="7.773"
              viewBox="0 0 4.743 7.773"
            >
              <path
                id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                fill="#fff"
                opacity="1"
              />
            </svg>
          </span>
        </div>
        <ul class="sub-menu">
          <li>
            <a class="{{ (request()->is('parent/noticeboard*')) ? 'active' : '' }}" href="{{ route('parent.noticeboard.list') }}">
              <span>{{ get_phrase('Noticeboard') }}</span>
            </a>
          </li>
          <li>
              <a class="{{ (request()->is('parent/events/list*')) ? 'active' : '' }}" href="{{ route('parent.events.list') }}">
                <span>{{ get_phrase('Events') }}</span>
              </a>
          </li>
          <li>
            <a class="{{ (request()->is('parent/feedback/filter*')) ? 'active' : '' }}" href="{{ route('parent.feedback.filter') }}"><span>{{ get_phrase('Feedback') }}
            </span></a>
        </li>
        </ul>
      </li>

      <li class="nav-links-li {{ request()->is('parent/profile*') ? 'showMenu':'' }}">
        <div class="iocn-link">
          <a href="{{ route('parent.profile') }}">
            <div class="sidebar_icon">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48" height="48">
                <g>
                  <path d="M244.317,299.051c-90.917,8.218-160.183,85.041-158.976,176.32V480c0,17.673,14.327,32,32,32l0,0c17.673,0,32-14.327,32-32   v-5.909c-0.962-56.045,40.398-103.838,96-110.933c58.693-5.82,110.992,37.042,116.812,95.735c0.344,3.47,0.518,6.954,0.521,10.441   V480c0,17.673,14.327,32,32,32l0,0c17.673,0,32-14.327,32-32v-10.667c-0.104-94.363-76.685-170.774-171.047-170.67   C251.854,298.668,248.082,298.797,244.317,299.051z"/>
                  <path d="M256.008,256c70.692,0,128-57.308,128-128S326.7,0,256.008,0s-128,57.308-128,128   C128.078,198.663,185.345,255.929,256.008,256z M256.008,64c35.346,0,64,28.654,64,64s-28.654,64-64,64s-64-28.654-64-64   S220.662,64,256.008,64z"/>
                </g>
              </svg>
            </div>
            <span class="link_name">{{ get_phrase('Profile') }}</span>
          </a>
        </div>
      </li>
		</ul>
	</div>

	<section class="home-section">
      <div class="home-content">
        <div class="home-header">
          <div class="row w-100 justify-content-between align-items-center">
            <div class="col-auto">
              <div class="sidebar_menu_icon">
                <div class="menuList">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="15"
                    height="12"
                    viewBox="0 0 15 12"
                  >
                    <path
                      id="Union_5"
                      data-name="Union 5"
                      d="M-2188.5,52.5v-2h15v2Zm0-5v-2h15v2Zm0-5v-2h15v2Z"
                      transform="translate(2188.5 -40.5)"
                      fill="#6e6f78"
                    />
                  </svg>
                </div>
              </div>
            </div>
            
            
               
            
<div class="col-auto d-xl-block d-none">
  <div class="d-flex align-items-center gap-2 px-2 py-2">
    @php
      $school_data = App\Models\School::where('id', auth()->user()->school_id)->first();
    @endphp

    <!-- Logo -->
    <div class="school-logo">
      @if(!empty($school_data->school_logo))
        <img src="{{ asset('assets/uploads/school_logo/' . $school_data->school_logo) }}"
             alt="School Logo"
             width="60" height="60"
             class="rounded-circle shadow"
             style="object-fit: cover; border: 2px solid #dee2e6;">
      @else
        <img src="{{ asset('assets/images/id_logo.png') }}"
             alt="Default Logo"
             width="60" height="60"
             class="rounded-circle shadow"
             style="object-fit: cover; border: 2px solid #dee2e6;">
      @endif
    </div>

    <!-- Name & ID -->
    <div class="school-meta">
      <h2 style="
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #247bbd;
        font-family: 'Rubik', 'Segoe UI', sans-serif;

        letter-spacing: 0.3px;
      ">
        {{ $school_data->title }} : {{ str_pad($school_data->id, 5, '0', STR_PAD_LEFT) }}
      </h2>
     
     
    </div>
  </div>
</div>



            
            
            <!--   *****School Info******

            <div class="col-auto d-xl-block d-none">
              <div class="header_notification d-flex align-items-center">
                <div class="notification_icon">
                  @php
                $school_data = App\Models\School::where('id', auth()->user()->school_id)->first();
            @endphp
           
              @if(!empty($school_data->school_logo))
                <img class="" src="{{ asset('assets/uploads/school_logo/'.DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo') ) }}" width="30px" height="30px" style="border-radius: 50%; ">
              @else
                <img class="" src="{{ asset('assets') }}/images/id_logo.png" width="30px" height="30px">
              @endif
                  
                </div>
                <p>
                  {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}
                </p>
              </div>
            </div>
            
            
            -->
            
            
            
          <!--  <div class="col-auto d-flex ">
              <div class="message">
                @php
                $last_message = DB::table('message_thrades')
                        ->where(function ($query) {
                            $query->where('reciver_id', auth()->user()->id)
                                  ->orWhere('sender_id', auth()->user()->id);
                        })
                        ->orderBy('id', 'desc')
                        ->first(); 
                        
                        $countUnreadThreads = DB::table('chats')
                            ->where('read_status', 0)
                            ->where('reciver_id', auth()->user()->id)
                            ->distinct('message_thrade')
                            ->count('message_thrade');

                        
                  @endphp
                  @if(!empty($last_message))
                    <a href="{{route('parent.message.all_message', ['id' => $last_message->id])}}" class="message_ico">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z" clip-rule="evenodd"/>
                        </svg>
                      @if($countUnreadThreads != 0)
                        <div class="countUnread">
                            <span class="countUnreadThreads">{{$countUnreadThreads}}</span>
                        </div>
                        @endif
                    </a>
                  @else
                    <a href="{{route('parent.message.chat_empty')}}" class="message_ico">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M4 3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h1v2a1 1 0 0 0 1.707.707L9.414 13H15a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M8.023 17.215c.033-.03.066-.062.098-.094L10.243 15H15a3 3 0 0 0 3-3V8h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1v2a1 1 0 0 1-1.707.707L14.586 18H9a1 1 0 0 1-.977-.785Z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                  @endif
              </div>
              @php
                $all_languages = get_all_language();
                $usersinfo = DB::table('users')->where('id', auth()->user()->id)->first();

                $userlanguage = $usersinfo->language;
                
              @endphp 
              <div class="adminTable-action" style="margin-right: 20px; margin-top: 14px;">
                <button
                  type="button"
                  class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  style="width: 91px; height: 29px; padding: 0;"
                >
                   <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" class="ep0rzf NMm5M" style="width: 17px"><path d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"></path></svg>
                   
                    @if(!empty($userlanguage))
                   <span style="font-size: 10px;">{{ucwords($userlanguage)}}</span>
                   @else
                   <span style="font-size: 10px;">{{ucwords(get_settings('language'))}}</span>
                   @endif
                </button>
                
                <ul style="min-width: 0;" class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                  <form method="post" id="languageForm" action="{{ route('parent.language') }}">
                    @csrf
                    @foreach ($all_languages as $all_language)
                        <li>
                            <a class="dropdown-item language-item" href="javascript:;" data-language-name="{{ $all_language->name }}">{{ ucwords($all_language->name) }}</a>
                        </li>
                    @endforeach
                    <input type="hidden" name="language" id="selectedLanguageName">
                </form>
                </ul>
              </div>  -->
              <div class="header-menu">
                <ul>
                  <li class="user-profile">
                    <div class="btn-group">
                      <button
                        class="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="defaultDropdown"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                      >
                        <div class="">
                          <img src="{{ get_user_image(auth()->user()->id) }}" height="42px" />
                        </div>
                        <div class="px-2 text-start">
                          <span class="user-name">{{ auth()->user()->name }}</span>
                          <span class="user-title">{{ get_phrase('Parent') }}</span>
                        </div>
                      </button>
                      <ul
                        class="dropdown-menu dropdown-menu-end eDropdown-menu"
                        aria-labelledby="defaultDropdown"
                      >
                        <li class="user-profile user-profile-inner">
                          <button
                            class="btn w-100 d-flex align-items-center"
                            type="button"
                          >
                            <div class="">
                              <img
                                class="radious-5px"
                                src="{{ get_user_image(auth()->user()->id) }}"
                                height="42px"
                              />
                            </div>
                            <div class="px-2 text-start">
                              <span class="user-name">{{ auth()->user()->name }}</span>
                              <span class="user-title">{{ get_phrase('Parent') }}</span>
                            </div>
                          </button>
                        </li>

                        <li>
                          <a class="dropdown-item" href="{{route('parent.profile')}}">
                            <span>
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="13.275"
                                height="14.944"
                                viewBox="0 0 13.275 14.944"
                              >
                                <g
                                  id="user_icon"
                                  data-name="user icon"
                                  transform="translate(-1368.531 -147.15)"
                                >
                                  <g
                                    id="Ellipse_1"
                                    data-name="Ellipse 1"
                                    transform="translate(1370.609 147.15)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  >
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="4.576"
                                      ry="4.435"
                                      stroke="none"
                                    />
                                    <ellipse
                                      cx="4.576"
                                      cy="4.435"
                                      rx="3.576"
                                      ry="3.435"
                                      fill="none"
                                    />
                                  </g>
                                  <path
                                    id="Path_41"
                                    data-name="Path 41"
                                    d="M1485.186,311.087a5.818,5.818,0,0,1,5.856-4.283,5.534,5.534,0,0,1,5.466,4.283"
                                    transform="translate(-115.686 -149.241)"
                                    fill="none"
                                    stroke="#181c32"
                                    stroke-width="2"
                                  />
                                </g>
                              </svg>
                            </span>
                            {{ get_phrase('My Account') }}
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{route('parent.password', ['edit'])}}">
                            <span>
                              <svg id="Layer_1" width="13.275" height="14.944" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m6.5 16a1.5 1.5 0 1 1 -1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm3 7.861a7.939 7.939 0 0 0 6.065-5.261 7.8 7.8 0 0 0 .32-3.85l.681-.689a1.5 1.5 0 0 0 .434-1.061v-2h.5a2.5 2.5 0 0 0 2.5-2.5v-.5h1.251a2.512 2.512 0 0 0 2.307-1.52 5.323 5.323 0 0 0 .416-2.635 4.317 4.317 0 0 0 -4.345-3.845 5.467 5.467 0 0 0 -3.891 1.612l-6.5 6.5a7.776 7.776 0 0 0 -3.84.326 8 8 0 0 0 2.627 15.562 8.131 8.131 0 0 0 1.475-.139zm-.185-12.661a1.5 1.5 0 0 0 1.463-.385l7.081-7.08a2.487 2.487 0 0 1 1.77-.735 1.342 1.342 0 0 1 1.36 1.149 2.2 2.2 0 0 1 -.08.851h-1.409a2.5 2.5 0 0 0 -2.5 2.5v.5h-.5a2.5 2.5 0 0 0 -2.5 2.5v1.884l-.822.831a1.5 1.5 0 0 0 -.378 1.459 4.923 4.923 0 0 1 -.074 2.955 5 5 0 1 1 -6.36-6.352 4.9 4.9 0 0 1 1.592-.268 5.053 5.053 0 0 1 1.357.191z"/></svg>
                            </span>
                            {{ get_phrase('Change Password') }}
                          </a>
                        </li>
                        <!-- Logout Button -->
                        <li>
                            <a class="btn eLogut_btn" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <span>
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="14.046"
                                    height="12.29"
                                    viewBox="0 0 14.046 12.29"
                                  >
                                    <path
                                      id="Logout"
                                      d="M4.389,42.535H2.634a.878.878,0,0,1-.878-.878V34.634a.878.878,0,0,1,.878-.878H4.389a.878.878,0,0,0,0-1.756H2.634A2.634,2.634,0,0,0,0,34.634v7.023A2.634,2.634,0,0,0,2.634,44.29H4.389a.878.878,0,1,0,0-1.756Zm9.4-5.009-3.512-3.512a.878.878,0,0,0-1.241,1.241l2.015,2.012H5.267a.878.878,0,0,0,0,1.756H11.05L9.037,41.036a.878.878,0,1,0,1.241,1.241l3.512-3.512A.879.879,0,0,0,13.788,37.525Z"
                                      transform="translate(0 -32)"
                                      fill="#fff"
                                    />
                                  </svg>
                                </span>
                                {{ get_phrase('Log out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="main_content">
            @yield('content')
            <!-- Start Footer -->
            <div class="copyright-text">
              <?php $active_session = DB::table('sessions')->where('id',  get_settings('running_session'))->value('session_title'); ?>
                <p>{{ $active_session }} &copy; <span><a class="text-info" target="_blank" href="{{ get_settings('footer_link') }}">{{ get_settings('footer_text') }}</a></span></p>
            </div>
            <!-- End Footer -->
        </div>
      </div>
      @include('modal')
    </section>

    @include('external_plugin')
    @include('jquery-form')

    <!--Bootstrap bundle with popper-->
    <script src="{{ asset('assets/vendors/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <!-- Datepicker js -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <!--Custom Script-->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Calender js -->
    <script src="{{ asset('assets/calender/main.js') }}"></script>
    <script src="{{ asset('assets/calender/locales-all.js') }}"></script>

    <!--Toaster Script-->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <!--pdf Script-->
    <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>

    <!--html2canvas Script-->
    <script src="{{ asset('assets/js/html2canvas.min.js') }}"></script>

    <script>

            // JavaScript to handle language selection
            document.addEventListener('DOMContentLoaded', function() {
        let languageLinks = document.querySelectorAll('.language-item');
        
        languageLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let languageName = this.getAttribute('data-language-name');
                document.getElementById('selectedLanguageName').value = languageName;
                document.getElementById('languageForm').submit();
            });
        });
    });
    
    
    
 // For Message thread Icon
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });


    "use strict";
		@if(Session::has('message'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.success("{{ session('message') }}");
		@endif

		@if(Session::has('error'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.error("{{ session('error') }}");
		@endif

		@if(Session::has('info'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.info("{{ session('info') }}");
		@endif

		@if(Session::has('warning'))
		toastr.options =
		{
			"closeButton" : true,
			"progressBar" : true
		}
				toastr.warning("{{ session('warning') }}");
		@endif
	</script>

	<script>

    "use strict";

    jQuery(document).ready(function(){
      $('input[name="datetimes"]').daterangepicker({
          timePicker: true,
          startDate: moment().startOf('day').subtract(30, 'day'),
          endDate: moment().startOf('day'),
          locale: {
         format: 'M/DD/YYYY '
        }

      });
    });

    </script>

</body>
</html>
