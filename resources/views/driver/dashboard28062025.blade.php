@extends('driver.navigation')
@section('content')
    @php
        
        // dashboard short_list_item for total trips
        $total_trips = DB::table('trips')
            ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
            ->select('trips.*', 'vehicles.driver_id')
            ->where('vehicles.driver_id', auth()->user()->id)
            ->where('trips.active', 0)
            ->count();
        
        $total_vehicles = DB::table('vehicles')
            ->where('school_id', auth()->user()->school_id)
            ->where('driver_id', auth()->user()->id)
            ->count();
        
        $total_students = DB::table('assigned_students')
            ->where('school_id', auth()->user()->school_id)
            ->where('driver_id', auth()->user()->id)
            ->count();
        
    @endphp

    <!-- Main section header and breadcrumb -->
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Dashboard') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Dashboard') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Alerts -->
    <div class="row">
        <div class="col-12">
            <div class="eSection-dashboardItems">
                <div class="row flex-wrap">
                    <div class="col-lg-12">
                        <div class="dashboard_ShortListItem">
                            <h4 class="text-dark">{{ auth()->user()->name }}</h4>
                            <p>{{ get_phrase('Welcome, to') }}
                                {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</p>
                        </div>
                    </div>
                    <!-- Dashboard Short Details -->
                    <div class="col-lg-6">
                        <div class="dashboard_ShortListItems">
                            <div class="row">

                                {{-- total trips --}}
                                <div class="col-md-6">
                                    <div class="dashboard_ShortListItem">
                                        <div class="dsHeader d-flex justify-content-between align-items-center">
                                            <h5 class="title">{{ get_phrase('Trips') }}</h5>
                                        </div>
                                        <div class="dsBody d-flex justify-content-between align-items-center">
                                            <div class="ds_item_details">
                                                <h4 class="total_no">{{ $total_trips }}</h4>
                                                <p class="total_info">{{ get_phrase('Total Trips') }}</p>
                                            </div>
                                            <div class="ds_item_icon">
                                                <img src="{{ asset('assets/images/Student_icon.png') }}" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- total vehicles --}}
                                <div class="col-md-6">
                                    <div class="dashboard_ShortListItem">
                                        <div class="dsHeader d-flex justify-content-between align-items-center">
                                            <h5 class="title">{{ get_phrase('Vehicles') }}</h5>
                                        </div>
                                        <div class="dsBody d-flex justify-content-between align-items-center">
                                            <div class="ds_item_details">
                                                <h4 class="total_no">
                                                    {{ $total_vehicles }}
                                                </h4>
                                                <p class="total_info">{{ get_phrase('Total vehicles') }}</p>
                                            </div>
                                            <div class="ds_item_icon">
                                                <img src="{{ asset('assets/images/Teacher_icon.png') }}" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dashboard_ShortListItem">
                                        <div class="dsHeader d-flex justify-content-between align-items-center">
                                            <h5 class="title">{{ get_phrase('Students') }}</h5>
                                        </div>
                                        <div class="dsBody d-flex justify-content-between align-items-center">
                                            <div class="ds_item_details">
                                                <h4 class="total_no">
                                                    {{ $total_students }}
                                                </h4>
                                                <p class="total_info">{{ get_phrase('Total students') }}</p>
                                            </div>
                                            <div class="ds_item_icon">
                                                <img src="{{ asset('assets/images/Parents_icon.png') }}" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dashboard_ShortListItem">
                                        <div class="dsHeader d-flex justify-content-between align-items-center">
                                            <h5 class="title">{{ get_phrase('Staff') }}</h5>
                                        </div>
                                        <div class="dsBody d-flex justify-content-between align-items-center">
                                            <div class="ds_item_details">
                                                @php
                                                    $admin = DB::table('users')
                                                        ->where('role_id', 2)
                                                        ->where('school_id', auth()->user()->school_id)
                                                        ->get()
                                                        ->count();
                                                @endphp
                                                @php
                                                    $teacher = DB::table('users')
                                                        ->where('role_id', 3)
                                                        ->where('school_id', auth()->user()->school_id)
                                                        ->get()
                                                        ->count();
                                                @endphp
                                                @php
                                                    $accountant = DB::table('users')
                                                        ->where('role_id', 4)
                                                        ->where('school_id', auth()->user()->school_id)
                                                        ->get()
                                                        ->count();
                                                @endphp
                                                @php
                                                    $librarian = DB::table('users')
                                                        ->where('role_id', 5)
                                                        ->where('school_id', auth()->user()->school_id)
                                                        ->get()
                                                        ->count();
                                                @endphp
                                                <h4 class="total_no">{{ $admin + $teacher + $accountant + $librarian }}
                                                </h4>
                                                <p class="total_info">{{ get_phrase('Total Staff') }}</p>
                                            </div>
                                            <div class="ds_item_icon">
                                                <img src="{{ asset('assets/images/Staff_icon.png') }}" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Imcome Report -->

                    <!-- Upcoming Events -->
                    <div class="col-md-6 ms-auto">
                        <div class="dashboard_report dashboard_upcoming_events">
                            <div class="ds_report_header d-flex justify-content-between align-items-start">
                                <div class="ds_report_left">
                                    <h4 class="title">{{ get_phrase('Upcoming Events') }}</h4>
                                </div>

                            </div>
                            <div class="ds_report_list pt-38">
                                <ul class="upcoming_events_items d-flex flex-column">

                                    @php
                                        $upcoming_events = DB::table('frontend_events')
                                            ->where('school_id', auth()->user()->school_id)
                                            ->where('timestamp', '>', time())
                                            ->where('status', 1)
                                            ->take(3)
                                            ->orderBy('id', 'DESC')
                                            ->get();
                                    @endphp
                                    @foreach ($upcoming_events as $upcoming_event)
                                        <li>
                                            <div
                                                class="upcoming_events_item d-flex justify-content-between align-items-start">
                                                <div class="events_info">
                                                    <a href="#" class="title">{{ $upcoming_event->title }}</a>
                                                    <p class="date">{{ date('D, M d Y', $upcoming_event->timestamp) }}
                                                    </p>
                                                </div>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-end">
                                    <a href="{{ route('librarian.events.list') }}"
                                        class="all_report_btn_2">{{ get_phrase('See all') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Resources -->
    <script src="{{ asset('assets/amchart/index.js') }}"></script>
    <script src="{{ asset('assets/amchart/xy.js') }}"></script>
    <script src="{{ asset('assets/amchart/animated.js') }}"></script>



    <!-- HTML -->
@include('includes.firebase')
    
@endsection
