<!DOCTYPE html>
<html lang="en">

<head>
    <!-- New -->
    <title>{{ get_phrase('Driver') . ' | ' . get_settings('system_title') }}</title>
    <!-- all the meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- all the css files -->
    <link rel="shortcut icon" href="{{ asset('assets/uploads/logo/' . get_settings('favicon')) }}" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/vendors/bootstrap-5.1.3/css/bootstrap.min.css') }}" />

    <!--Custom css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" />
    <!-- Datepicker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}" />
    <!-- Select2 css -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/vendors/bootstrap-icons-1.8.1/bootstrap-icons.css') }}" />

    <!--Toaster css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr.min.css') }}" />

    <!-- Calender css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/calender/main.css') }}" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />



</head>

<body>

    <div class="sidebar">
        <div class="logo-details mt-4 mb-3">
            <div class="img_wrapper">
                <img height="40px" class=""
                    src="{{ asset('assets/uploads/logo/' . get_settings('light_logo')) }}" alt="" />
            </div>
        </div>
        <div class="closeIcon">
            <span>{{ get_phrase('Close') }}</span>
        </div>


        <!-- sidebar title -->
        <ul class="nav-links">

            {{-- driver dashboard --}}
            <li class="nav-links-li {{ request()->is('driver/dashboard') ? 'showMenu' : '' }}">
                <div class="iocn-link">
                    <a href="{{ route('driver.dashboard') }}">

                        {{-- dashboard icon --}}
                        <div class="sidebar_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512"
                                style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48"
                                height="48">
                                <g>
                                    <path
                                        d="M117.333,234.667C52.532,234.667,0,182.135,0,117.333S52.532,0,117.333,0s117.333,52.532,117.333,117.333   C234.596,182.106,182.106,234.596,117.333,234.667z M117.333,64C87.878,64,64,87.878,64,117.333s23.878,53.333,53.333,53.333   s53.333-23.878,53.333-53.333S146.789,64,117.333,64z" />
                                    <path
                                        d="M394.667,234.667c-64.801,0-117.333-52.532-117.333-117.333S329.865,0,394.667,0S512,52.532,512,117.333   C511.929,182.106,459.439,234.596,394.667,234.667z M394.667,64c-29.455,0-53.333,23.878-53.333,53.333   s23.878,53.333,53.333,53.333S448,146.789,448,117.333S424.122,64,394.667,64z" />
                                    <path
                                        d="M117.333,512C52.532,512,0,459.468,0,394.667s52.532-117.333,117.333-117.333s117.333,52.532,117.333,117.333   C234.596,459.439,182.106,511.929,117.333,512z M117.333,341.333C87.878,341.333,64,365.211,64,394.667S87.878,448,117.333,448   s53.333-23.878,53.333-53.333S146.789,341.333,117.333,341.333z" />
                                    <path
                                        d="M394.667,512c-64.801,0-117.333-52.532-117.333-117.333s52.532-117.333,117.333-117.333S512,329.865,512,394.667   C511.929,459.439,459.439,511.929,394.667,512z M394.667,341.333c-29.455,0-53.333,23.878-53.333,53.333S365.211,448,394.667,448   S448,424.122,448,394.667S424.122,341.333,394.667,341.333z" />
                                </g>
                            </svg>

                        </div>
                        <span class="link_name">{{ get_phrase('Dashboard') }}</span>
                    </a>
                </div>
            </li>


            {{-- back office --}}
            <li
                class="nav-links-li {{ request()->is('driver/noticeboard*') || request()->is('driver/events/list*') ? 'showMenu' : '' }}">
                <div class="iocn-link">
                    <a href="#">

                        {{-- back office icon --}}
                        <div class="sidebar_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="48"
                                height="48">
                                <path
                                    d="M18.5,3h-.642A4,4,0,0,0,14,0H10A4,4,0,0,0,6.142,3H5.5A5.506,5.506,0,0,0,0,8.5v10A5.506,5.506,0,0,0,5.5,24h13A5.507,5.507,0,0,0,24,18.5V8.5A5.507,5.507,0,0,0,18.5,3ZM5.5,6h13A2.5,2.5,0,0,1,21,8.5V11H3V8.5A2.5,2.5,0,0,1,5.5,6Zm13,15H5.5A2.5,2.5,0,0,1,3,18.5V14h7a2,2,0,0,0,2,2h0a2,2,0,0,0,2-2h7v4.5A2.5,2.5,0,0,1,18.5,21Z" />
                            </svg>
                        </div>

                        <span class="link_name">
                            {{ get_phrase('Bcak Office') }}
                        </span>
                    </a>

                    {{-- arrow icon --}}
                    <span class="arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="4.743" height="7.773" viewBox="0 0 4.743 7.773">
                            <path id="navigate_before_FILL0_wght600_GRAD0_opsz24"
                                d="M1.466.247,4.5,3.277a.793.793,0,0,1,.189.288.92.92,0,0,1,0,.643A.793.793,0,0,1,4.5,4.5l-3.03,3.03a.828.828,0,0,1-.609.247.828.828,0,0,1-.609-.247.875.875,0,0,1,0-1.219L2.668,3.886.247,1.466A.828.828,0,0,1,0,.856.828.828,0,0,1,.247.247.828.828,0,0,1,.856,0,.828.828,0,0,1,1.466.247Z"
                                fill="#fff" opacity="1" />
                        </svg>
                    </span>
                </div>
                <ul class="sub-menu">
                    <li>
                        <a class="{{ request()->is('driver/noticeboard*') ? 'active' : '' }}"
                            href="{{ route('driver.noticeboard') }}">
                            <span>{{ get_phrase('Noticeboard') }}</span>
                        </a>
                    </li>

                    <li>
                        <a class="{{ request()->is('driver/events/list*') ? 'active' : '' }}"
                            href="{{ route('driver.events.list') }}">
                            <span>{{ get_phrase('Events') }}</span>
                        </a>
                    </li>

                </ul>
            </li>


            {{-- driver trips --}}
            <li class="nav-links-li {{ request()->is('driver/trips/list*') ? 'showMenu' : '' }}">
                <div class="iocn-link">
                    <a href="{{ route('driver.trips.list') }}">

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

            {{-- assigned student --}}
            <li class="nav-links-li {{ request()->is('driver/assigned/student/list*') ? 'showMenu' : '' }}">
                <div class="iocn-link">
                    <a href="{{ route('assigned.student.list') }}">

                        {{-- trip icon --}}
                        <div class="sidebar_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 512" width="48"
                                height="48">
                                <path
                                    d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm88 0c0-8.8 7.2-16 16-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16z" />
                            </svg>
                        </div>
                        <span class="link_name">{{ get_phrase('Assigned student') }}</span>
                    </a>
                </div>
            </li>


            {{-- driver profile --}}
            <li class="nav-links-li {{ request()->is('driver/profile*') ? 'showMenu' : '' }}">
                <div class="iocn-link">
                    <a href="{{ route('driver.profile') }}">
                        {{-- profile icon --}}
                        <div class="sidebar_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512"
                                style="enable-background:new 0 0 512 512;" xml:space="preserve" width="48"
                                height="48">
                                <g>
                                    <path
                                        d="M244.317,299.051c-90.917,8.218-160.183,85.041-158.976,176.32V480c0,17.673,14.327,32,32,32l0,0c17.673,0,32-14.327,32-32   v-5.909c-0.962-56.045,40.398-103.838,96-110.933c58.693-5.82,110.992,37.042,116.812,95.735c0.344,3.47,0.518,6.954,0.521,10.441   V480c0,17.673,14.327,32,32,32l0,0c17.673,0,32-14.327,32-32v-10.667c-0.104-94.363-76.685-170.774-171.047-170.67   C251.854,298.668,248.082,298.797,244.317,299.051z" />
                                    <path
                                        d="M256.008,256c70.692,0,128-57.308,128-128S326.7,0,256.008,0s-128,57.308-128,128   C128.078,198.663,185.345,255.929,256.008,256z M256.008,64c35.346,0,64,28.654,64,64s-28.654,64-64,64s-64-28.654-64-64   S220.662,64,256.008,64z" />
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="12"
                                    viewBox="0 0 15 12">
                                    <path id="Union_5" data-name="Union 5"
                                        d="M-2188.5,52.5v-2h15v2Zm0-5v-2h15v2Zm0-5v-2h15v2Z"
                                        transform="translate(2188.5 -40.5)" fill="#6e6f78" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto d-xl-block d-none">
                        <div class="header_notification d-flex align-items-center">
                            <div class="notification_icon">
                                <svg style="width: 14px; fill: #00a3ff;" xmlns="http://www.w3.org/2000/svg"
                                    id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="48px"
                                    height="48px">
                                    <path
                                        d="M19,6H14.321A3.95,3.95,0,0,0,13,5.388V5l3.53-1.652a1,1,0,0,0,0-1.7l-3.2-1.5A1.637,1.637,0,0,0,11,1.636V5.388A3.937,3.937,0,0,0,9.68,6H5a5.006,5.006,0,0,0-5,5V21a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V11A5.006,5.006,0,0,0,19,6Zm2.816,4H19.657a3.017,3.017,0,0,1-2.121-.879L16.414,8H19A3,3,0,0,1,21.816,10ZM5,8H7.586L6.464,9.121A3.017,3.017,0,0,1,4.343,10H2.184A3,3,0,0,1,5,8Zm8,14H11V19a1,1,0,0,1,2,0Zm8,0H15V19a3,3,0,0,0-6,0v3H3a1,1,0,0,1-1-1V12H4.343a4.968,4.968,0,0,0,3.535-1.465l2.708-2.707a2,2,0,0,1,2.828,0l2.708,2.707A4.968,4.968,0,0,0,19.657,12H22v9A1,1,0,0,1,21,22ZM7,15a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,15Zm0,4a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H6A1,1,0,0,1,7,19Zm13-4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,15Zm0,4a1,1,0,0,1-1,1H18a1,1,0,0,1,0-2h1A1,1,0,0,1,20,19Zm-6-7a2,2,0,1,1-2-2A2,2,0,0,1,14,12Z">
                                    </path>
                                </svg>
                            </div>
                            <p>
                                {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}
                            </p>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="header-menu">
                            <ul>
                                <li class="user-profile">
                                    <div class="btn-group">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true"
                                            aria-expanded="false">
                                            <div class="">
                                                <img src="{{ get_user_image(auth()->user()->id) }}" height="42px" />
                                            </div>
                                            <div class="px-2 text-start">
                                                <span class="user-name">{{ auth()->user()->name }}</span>
                                                <span class="user-title">{{ get_phrase('Driver') }}</span>
                                            </div>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu"
                                            aria-labelledby="defaultDropdown">
                                            <li class="user-profile user-profile-inner">
                                                <button class="btn w-100 d-flex align-items-center" type="button">
                                                    <div class="">
                                                        <img class="radious-5px"
                                                            src="{{ get_user_image(auth()->user()->id) }}"
                                                            height="42px" />
                                                    </div>
                                                    <div class="px-2 text-start">
                                                        <span class="user-name">{{ auth()->user()->name }}</span>
                                                        <span class="user-title">{{ get_phrase('Driver') }}</span>
                                                    </div>
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('driver.profile') }}">
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13.275"
                                                            height="14.944" viewBox="0 0 13.275 14.944">
                                                            <g id="user_icon" data-name="user icon"
                                                                transform="translate(-1368.531 -147.15)">
                                                                <g id="Ellipse_1" data-name="Ellipse 1"
                                                                    transform="translate(1370.609 147.15)"
                                                                    fill="none" stroke="#181c32" stroke-width="2">
                                                                    <ellipse cx="4.576" cy="4.435"
                                                                        rx="4.576" ry="4.435"
                                                                        stroke="none" />
                                                                    <ellipse cx="4.576" cy="4.435"
                                                                        rx="3.576" ry="3.435"
                                                                        fill="none" />
                                                                </g>
                                                                <path id="Path_41" data-name="Path 41"
                                                                    d="M1485.186,311.087a5.818,5.818,0,0,1,5.856-4.283,5.534,5.534,0,0,1,5.466,4.283"
                                                                    transform="translate(-115.686 -149.241)"
                                                                    fill="none" stroke="#181c32"
                                                                    stroke-width="2" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    {{ get_phrase('My Account') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('driver.password', ['edit']) }}">
                                                    <span>
                                                        <svg id="Layer_1" width="13.275" height="14.944"
                                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                                            data-name="Layer 1">
                                                            <path
                                                                d="m6.5 16a1.5 1.5 0 1 1 -1.5 1.5 1.5 1.5 0 0 1 1.5-1.5zm3 7.861a7.939 7.939 0 0 0 6.065-5.261 7.8 7.8 0 0 0 .32-3.85l.681-.689a1.5 1.5 0 0 0 .434-1.061v-2h.5a2.5 2.5 0 0 0 2.5-2.5v-.5h1.251a2.512 2.512 0 0 0 2.307-1.52 5.323 5.323 0 0 0 .416-2.635 4.317 4.317 0 0 0 -4.345-3.845 5.467 5.467 0 0 0 -3.891 1.612l-6.5 6.5a7.776 7.776 0 0 0 -3.84.326 8 8 0 0 0 2.627 15.562 8.131 8.131 0 0 0 1.475-.139zm-.185-12.661a1.5 1.5 0 0 0 1.463-.385l7.081-7.08a2.487 2.487 0 0 1 1.77-.735 1.342 1.342 0 0 1 1.36 1.149 2.2 2.2 0 0 1 -.08.851h-1.409a2.5 2.5 0 0 0 -2.5 2.5v.5h-.5a2.5 2.5 0 0 0 -2.5 2.5v1.884l-.822.831a1.5 1.5 0 0 0 -.378 1.459 4.923 4.923 0 0 1 -.074 2.955 5 5 0 1 1 -6.36-6.352 4.9 4.9 0 0 1 1.592-.268 5.053 5.053 0 0 1 1.357.191z" />
                                                        </svg>
                                                    </span>
                                                    {{ get_phrase('Change Password') }}
                                                </a>
                                            </li>
                                            <!-- Logout Button -->
                                            <li>
                                                <a class="btn eLogut_btn" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14.046"
                                                            height="12.29" viewBox="0 0 14.046 12.29">
                                                            <path id="Logout"
                                                                d="M4.389,42.535H2.634a.878.878,0,0,1-.878-.878V34.634a.878.878,0,0,1,.878-.878H4.389a.878.878,0,0,0,0-1.756H2.634A2.634,2.634,0,0,0,0,34.634v7.023A2.634,2.634,0,0,0,2.634,44.29H4.389a.878.878,0,1,0,0-1.756Zm9.4-5.009-3.512-3.512a.878.878,0,0,0-1.241,1.241l2.015,2.012H5.267a.878.878,0,0,0,0,1.756H11.05L9.037,41.036a.878.878,0,1,0,1.241,1.241l3.512-3.512A.879.879,0,0,0,13.788,37.525Z"
                                                                transform="translate(0 -32)" fill="#fff" />
                                                        </svg>
                                                    </span>
                                                    {{ get_phrase('Log out') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
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
                    <?php $active_session = DB::table('sessions')
                        ->where('id', get_settings('running_session'))
                        ->value('session_title'); ?>
                    <p>{{ $active_session }} &copy; <span><a class="text-info" target="_blank"
                                href="{{ get_settings('footer_link') }}">{{ get_settings('footer_text') }}</a></span>
                    </p>
                </div>
                <!-- End Footer -->
            </div>
        </div>
        @include('modal')
    </section>

    @include('external_plugin')


    <!--Main Jquery-->
    <script src="{{ asset('assets/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
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


    <!--Toaster Script-->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>


    <script>
        "use strict";

        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>

    <script>
        "use strict";

        jQuery(document).ready(function() {
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
