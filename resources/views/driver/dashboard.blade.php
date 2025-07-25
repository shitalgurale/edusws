@extends('driver.navigation')
@section('content')

@php
    $total_trips = DB::table('trips')
        ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
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

    $total_staff = DB::table('users')
        ->whereIn('role_id', [2,3,4,5])
        ->where('school_id', auth()->user()->school_id)
        ->count();

    $upcoming_events = DB::table('frontend_events')
        ->where('school_id', auth()->user()->school_id)
        ->where('timestamp', '>', time())
        ->where('status', 1)
        ->orderBy('timestamp', 'ASC')
        ->take(5)
        ->get();
@endphp
  
<head>
  <!-- Existing meta & CSS links -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
 
<style>
.card-block {
    position: relative;
    border-radius: 15px;
    background-color: #3db7ac; /* fallback teal, override per card */
    color: #fff;
    padding: 20px 20px 50px;
    min-height: 120px;
    overflow: hidden;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
}

.card-block:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    transform: translateY(-5px);
}

.card-bg-icon {
    position: absolute;
    top: 15px;
    left: 15px;
    width: 60px;
    height: auto;
    opacity: 0.12;
}

.card-value {
    font-size: 26px;
    font-weight: 600;
    text-align: right;
}

.card-label {
    font-size: 14px;
    text-align: right;
    margin-top: 4px;
}

.card-footer-link {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 38px;
    width: 100%;
    background-color: rgba(0,0,0,0.08);
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    font-size: 12px;           /* smaller font */
    font-weight: normal;       /* not bold */
    text-transform: uppercase;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
}

.card-footer-link span {
    font-size: 12px;
    font-weight: normal;
}

.card-footer-link a {
    color: #fff;
    text-decoration: none;
    font-size: 13px;
}

.card-footer-link a:hover {
    text-decoration: underline;
}

/* Responsive card layout spacing */
.col-md-4 {
    padding-left: 10px;
    padding-right: 10px;
}

@media (max-width: 767px) {
    .card-block {
        min-height: 100px;
        padding: 18px 15px 45px;
    }

    .card-value {
        font-size: 22px;
    }

    .card-label {
        font-size: 13px;
    }

    .card-footer-link {
        font-size: 11px;
        height: 36px;
    }
}

.dashboard_attendance {
    border-radius: 15px;
    overflow: hidden;
}


.dashboard_attendance {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    /*background: linear-gradient(135deg, #3498db, #2ecc71); 
    background: #20B7DA;*/
    background: #4EC8E4;
    color: #fff;  
    border-radius: 15px;
    padding: 20px;
}


</style>
 </head>
<div class="mainSection-title mb-4">
    <h4>{{ get_phrase('Dashboard') }}</h4>
    <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Home') }}</a></li>
        <li><a href="#">{{ get_phrase('Dashboard') }}</a></li>
    </ul>
</div>
    
           <!-- Start Alerts -->
       <div class="row">
       <div class="col-12">
         <div class="eSection-dashboardItems">
           <div class="row flex-wrap">
            <div class="col-lg-12">
                <div class="dashboard_ShortListItem">
                        <h4 class="text-dark">{{ auth()->user()->name }}</h4>
                        <p>Welcome, to {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</p>
                </div>
            </div>

<div class="row align-items-stretch">
    <!-- Left: 2x2 Cards -->
    <div class="col-md-6">
        <div class="row">
            <!-- Trips -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #1abc9c;">
                    <img src="{{ asset('assets/images/trip.png') }}" class="card-bg-icon" alt="Trip Icon">
                    <div class="card-value">{{ $total_trips }}</div>
                    <div class="card-label">Trips</div>
                </div>
            </div>

            <!-- Vehicles -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #3498db;">
                    <img src="{{ asset('assets/images/vehicle.png') }}" class="card-bg-icon" alt="Vehicle Icon">
                    <div class="card-value">{{ $total_vehicles }}</div>
                    <div class="card-label">Vehicles</div>
                </div>
            </div>

            <!-- Students -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #f39c12;">
                    <img src="{{ asset('assets/images/student.png') }}" class="card-bg-icon" alt="Student Icon">
                    <div class="card-value">{{ $total_students }}</div>
                    <div class="card-label">Students</div>
                </div>
            </div>

            <!-- Staff -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #9b59b6;">
                    <img src="{{ asset('assets/images/staff.png') }}" class="card-bg-icon" alt="Staff Icon">
                    <div class="card-value">{{ $total_staff }}</div>
                    <div class="card-label">Staff</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Upcoming Events -->
    <div class="col-md-6">
        <div class="card shadow p-3 d-flex flex-column justify-content-between h-100"
     style="background: #e74c3c; color: #fff; border-radius: 20px;">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">{{ get_phrase('Upcoming Events') }}</h4>
                <a href="{{ route('driver.events.list') }}" class="text-white text-decoration-underline">{{ get_phrase('See all') }}</a>
            </div>
            <ul class="list-unstyled mt-3" style="overflow-y: auto; max-height: 250px; padding-right: 5px;">
                @forelse ($upcoming_events as $event)
                    <li class="mb-3 pb-2 border-bottom border-light">
                        <strong>{{ $event->title }}</strong><br>
                        <small>{{ date('D, M d Y', $event->timestamp) }}</small>
                    </li>
                @empty
                    <li>{{ get_phrase('No upcoming events.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- Optional Firebase JS -->
@include('includes.firebase')

@endsection
