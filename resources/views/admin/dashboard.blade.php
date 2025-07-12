@extends('admin.navigation')

@section('content')

@php
$class_wise_attandance = [];
$total_student = 0;
$todays_total_attandance = 0;

$school_id = auth()->user()->school_id;
$running_session_id = get_school_settings($school_id)->value('running_session');
$all_classes = DB::table('classes')->where('school_id', $school_id)->get();

$today_date = date('Y-m-d');

foreach ($all_classes as $class) {
    logger()->info("🔍 Checking attendance", [
        'class_id' => $class->id,
        'school_id' => $school_id,
        'session_id' => $running_session_id,
        'today_date' => $today_date,
    ]);

    $total_student += DB::table('enrollments')
        ->where('school_id', $school_id)
        ->where('class_id', $class->id)
        ->where('session_id', $running_session_id)
        ->count();

    $today_attended = DB::table('daily_attendances')
        ->where('school_id', $school_id)
        ->where('class_id', $class->id)
        ->where('session_id', $running_session_id)
        ->whereDate('timestamp', $today_date)
        ->count();

    logger()->info("✅ Attendance found", [
        'class_id' => $class->id,
        'count' => $today_attended
    ]);

    $class_attendance_entry = [
        'class_name' => trim($class->name),
        'today_attended' => $today_attended
    ];

    logger()->info("📊 Pushing class attendance", $class_attendance_entry);

    $class_wise_attandance[] = $class_attendance_entry;
    $todays_total_attandance += $today_attended;
}
@endphp

<head>
  <!-- Existing meta & CSS links -->
  <!--
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    .dashboard-count {
      font-family: 'Poppins', sans-serif;
      font-size: 26px;
      font-weight: 600;
      color: #2c3e50;
    }
    
    .dashboard-icon {
      width: 60px;
      height: 60px;
      object-fit: contain;
    }
    
    .dashboard_ShortListItem {
      transition: transform 0.3s ease, filter 0.3s ease;
      animation: floatCard 3s ease-in-out infinite;
      border-radius: 10px;
    }
    
    .dashboard_ShortListItem:hover {
      transform: scale(1.03);
      filter: brightness(1.05);
    }
   

  </style>
   -->
   
      <!-- FontAwesome for dashboard icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
 

{{-- TEMPORARY DEBUG OUTPUT --}}


	<!-- Mani section header and breadcrumb -->
	<div class="mainSection-title">
	<div class="row">
	  <div class="col-12">
	    <div
	      class="d-flex justify-content-between align-items-center flex-wrap gr-15"
	    >
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
	      <!-- Dashboard Short Details -->
	      <div class="col-lg-12">
	        <div class="dashboard_ShortListItems">
	         <div class="row gx-2 gy-2">
	              
	              
    <!-- Students -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #1abc9c;">
        <!-- Background Icon (relevant to students) -->
        <img src="{{ asset('assets/images/student.png') }}" class="card-bg-icon" alt="Student Icon">

        <!-- Right-aligned Value & Label -->
        <div class="card-value">
            {{ DB::table('users')->where('role_id', 7)->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Students</div>

        <!-- Bottom Link Footer -->
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.student') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Teachers -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #3498db;">
        <img src="{{ asset('assets/images/teacher.png') }}" class="card-bg-icon" alt="Teacher Icon">
        <div class="card-value">
            {{ DB::table('users')->where('role_id', 3)->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Teachers</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.teacher') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Parents -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #e74c3c;">
        <img src="{{ asset('assets/images/parents.png') }}" class="card-bg-icon" alt="Parent Icon">
        <div class="card-value">
            {{ DB::table('users')->where('role_id', 6)->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Parents</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.parent') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>


    <!-- Staff -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #9b59b6;">
        <img src="{{ asset('assets/images/staff.png') }}" class="card-bg-icon" alt="Staff Icon">
        <div class="card-value">
            {{ DB::table('users')->whereIn('role_id', [2,3,4,5])->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Staff</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('hr.showAllStaff') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>


<!-- Fee Receipts -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <!--<div class="card-block" style="background-color: #f1c40f; color: #000;">-->
    <div class="card-block" style="background-color: #f1c40f;">
        <img src="{{ asset('assets/images/fee.png') }}" class="card-bg-icon" alt="Fee Icon">
        <div class="card-value">
            {{ DB::table('student_fee_managers')->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Fee Receipts</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.fee_manager.list') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>


<!-- Drivers -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #7f8c8d;">
        <img src="{{ asset('assets/images/driver.png') }}" class="card-bg-icon" alt="Driver Icon">
        <div class="card-value">
            {{ DB::table('users')->where('role_id', 8)->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Drivers</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.driver.list') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>


<!-- Vehicles -->
<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #2c3e50;">
        <img src="{{ asset('assets/images/vehicle.png') }}" class="card-bg-icon" alt="Vehicle Icon">
        <div class="card-value">
            {{ DB::table('vehicles')->where('school_id', auth()->user()->school_id)->count() }}
        </div>
        <div class="card-label">Vehicles</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.vehicle.list') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Subscription -->

@php
    $subscription = \App\Models\Subscription::where('school_id', auth()->user()->school_id)->latest()->first();
@endphp

<div class="col-md-3 col-sm-6 mb-3 px-2">
    <div class="card-block" style="background-color: #27ae60;">
        <img src="{{ asset('assets/images/subscription.png') }}" class="card-bg-icon" alt="Subscription Icon">
        <!--<div class="card-value">
            Plan{{ auth()->user()->subscription_plan_id ?? 'N/A' }}
        </div>
           <div class="card-value">
            {{ \Carbon\Carbon::parse(auth()->user()->subscription_valid_till)->format('d M Y') }}
        </div>-->
        
@if($subscription && $subscription->status == 1)
    <div class="card-value">
        {{ date('d M, Y', $subscription->expire_date) }}
    </div>
@else
    <div class="card-value">
        N/A
    </div>
@endif
        <div class="card-label">Expiry Date</div>
        <div class="card-footer-link">
            <span>View More</span>
            <a href="{{ route('admin.subscription') }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

             </div>
	        </div>
	      </div>
	
            
            
	      
	      
	      
	      
	      
	      
	      <!-- Attendance -->
	      <div class="col-12 mt-4">
	        <div class="dashboard_report dashboard_attendance">
	          <div class="ds_report_header d-flex justify-content-between align-items-start">
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Todays Attendance') }}</h4>
	              <div
	                class="ds_report_count d-flex align-items-center"
	              >
	                <span class="total_no">{{ $todays_total_attandance }}</span>
	                <div class="ds_attend_percent">
	                  <div class="icon">
	                    <svg
	                      xmlns="http://www.w3.org/2000/svg"
	                      width="16.507"
	                      height="10.25"
	                      viewBox="0 0 16.507 10.25"
	                    >
	                      <g
	                        id="Group_2395"
	                        data-name="Group 2395"
	                        transform="translate(-343.381 -436.505)"
	                      >
	                        <path
	                          id="Path_1631"
	                          data-name="Path 1631"
	                          d="M0,4.347l4.83-3.26L6.279,5.072,12.076,0"
	                          transform="matrix(0.998, -0.07, 0.07, 0.998, 344.122, 440.793)"
	                          fill="none"
	                          stroke="#fff"
	                          stroke-linecap="round"
	                          stroke-width="1"
	                        />
	                        <g
	                          id="Polygon_2"
	                          data-name="Polygon 2"
	                          transform="matrix(0.643, 0.766, -0.766, 0.643, 356.596, 436.505)"
	                          fill="#fff"
	                        >
	                          <path
	                            d="M 4.187728404998779 3.341484308242798 L 0.9342562556266785 3.341484308242798 L 2.560992240905762 0.9013835787773132 L 4.187728404998779 3.341484308242798 Z"
	                            stroke="none"
	                          />
	                          <path
	                            d="M 2.560992240905762 1.802777767181396 L 1.868520259857178 2.841484546661377 L 3.253464221954346 2.841484546661377 L 2.560992240905762 1.802777767181396 M 2.560992240905762 4.529953002929688e-06 L 5.121982097625732 3.841484308242798 L 2.384185791015625e-06 3.841484308242798 L 2.560992240905762 4.529953002929688e-06 Z"
	                            stroke="none"
	                            fill="#fff"
	                          />
	                        </g>
	                      </g>
	                    </svg>
	                  </div>
	                  @if($total_student > 0)
	                  	<span>{{ (100 / $total_student) * $todays_total_attandance }}%</span>
	                  @else
	                  <span>0%</span>
	                  @endif
	                </div>
	              </div>
	            </div>
	            <a href="{{ route('admin.daily_attendance') }}" class="all_report_btn">{{ get_phrase('Go to Attendance') }}</a>
	          </div>
	          <div class="ds_report_list">
				<div id="chartdiv" class="chartdiv"></div>
			  </div>
	        </div>
	      </div>




@php
use Illuminate\Support\Facades\DB;

$school_id = auth()->user()->school_id;
$session_id = get_school_settings($school_id)->value('running_session');
$session = DB::table('sessions')->where('id', $session_id)->first();
$session_title = $session->session_title ?? 'Unknown Session';

// Build monthly income array for the session year
$monthly_income_data = [];
for ($m = 1; $m <= 12; $m++) {
    $start = strtotime(date("Y-$m-01"));
    $end = strtotime(date("Y-m-t", $start));

    $monthly_income = DB::table('student_fee_managers')
        ->where('school_id', $school_id)
        ->where('status', 'paid')
        ->where('session_id', $session_id)
        ->whereBetween('timestamp', [$start, $end])
        ->sum('total_amount');

    $monthly_income_data[] = $monthly_income;
}

$month_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
@endphp

@php
// ✅ Yearly total income
$total_income = DB::table('student_fee_managers')
    ->where('school_id', $school_id)
    ->where('status', 'paid')
    ->where('session_id', $session_id)
    ->sum('total_amount');
@endphp

<!-- Chart Container -->
<!--<div class="col-md-6 mt-4">
    <div class="card shadow p-3" style="background: #1abc9c; color: #fff;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">{{ get_phrase('Income Report') }}</h4>
            <span style="font-weight: bold;">YEAR {{ $session_title }}: {{ currency($total_income) }}</span>
        </div>
        
        <canvas id="incomeChart" height="180"></canvas>
    </div>
</div>-->

<!-- Chart Container -->
<div class="col-md-6 mt-4">
    <div class="card shadow p-3 d-flex flex-column justify-content-between" style="background: #1abc9c; color: #fff; height: 350px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">{{ get_phrase('Income Report') }}</h4>
            <span style="font-weight: bold;">YEAR {{ $session_title }}: {{ currency($total_income) }}</span>
        </div>
        <canvas id="incomeChart" height="270"></canvas>
    </div>
</div>



    
    @php
    
      // Upcoming Events
    $upcoming_events = DB::table('frontend_events')
        ->where('school_id', auth()->user()->school_id)
        ->where('timestamp', '>', time())
        ->where('status', 1)
        ->orderBy('timestamp', 'ASC')
        ->take(5)
        ->get();
    @endphp
    

    <!-- Upcoming Events List -->
   <!-- <div class="col-md-6 mt-4">
        <div class="card shadow p-3" style="background: #e74c3c; color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Upcoming Events</h4>
                <a href="{{ route('admin.events.list') }}" class="text-white text-decoration-underline">See all</a>
            </div>
            <ul class="list-unstyled mt-3">
                @forelse ($upcoming_events as $event)
                    <li class="mb-3 pb-2 border-bottom border-light">
                        <strong>{{ $event->title }}</strong><br>
                        <small>{{ date('D, M d Y', $event->timestamp) }}</small>
                    </li>
                @empty
                    <li>No upcoming events.</li>
                @endforelse
            </ul>
        </div>
    </div>-->
    
    <!-- Upcoming Events List -->
<div class="col-md-6 mt-4">
    <div class="card shadow p-3 d-flex flex-column justify-content-between" style="background: #e74c3c; color: #fff; height: 350px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Upcoming Events</h4>
            <a href="{{ route('admin.events.list') }}" class="text-white text-decoration-underline">See all</a>
        </div>
        
        <!-- Scrollable event list -->
        <ul class="list-unstyled mt-3" style="overflow-y: auto; max-height: 250px; padding-right: 5px;">
            @forelse ($upcoming_events as $event)
                <li class="mb-3 pb-2 border-bottom border-light">
                    <strong>{{ $event->title }}</strong><br>
                    <small>{{ date('D, M d Y', $event->timestamp) }}</small>
                </li>
            @empty
                <li>No upcoming events.</li>
            @endforelse
        </ul>
    </div>
</div>


</div>




	      <!-- Imcome Report -->
<!--
		  @php
		  	$total_income = 0;
		  	$first_day_of_this_month = strtotime(date('1 M Y', time()));
		  	$last_day_of_this_month = strtotime(date('Y-m-t', time()));
			$monthly_incomes = DB::table('student_fee_managers')->where('school_id', auth()->user()->school_id)->where('status', 'paid')->where('timestamp', '>=', $first_day_of_this_month)->where('timestamp', '<=', $last_day_of_this_month)->get();
			foreach($monthly_incomes as $monthly_income):
				$total_income += $monthly_income->total_amount;
			endforeach;
		  @endphp

	      <div class="col-6 mt-4">
	        <div class="dashboard_report dashboard_income_report">
	          <div
	            class="ds_report_header d-flex justify-content-between align-items-start"
	          >
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Income Report') }}</h4>
	              <div
	                class="ds_report_count d-flex align-items-center"
	              >
	                <span class="total_no">{{ currency($total_income) }}</span>
	              </div>
	            </div>
	            <div class="verticalMenu">
	              <button
	                type="button"
	                class="eBtn dropdown-toggle"
	                data-bs-toggle="dropdown"
	                aria-expanded="false"
	              >
	                <svg
	                  xmlns="http://www.w3.org/2000/svg"
	                  width="5"
	                  height="20.999"
	                  viewBox="0 0 5 20.999"
	                >
	                  <path
	                    id="Union_6"
	                    data-name="Union 6"
	                    d="M-4856,309.5a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,309.5Zm0-8a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,301.5Zm0-8a2.5,2.5,0,0,1,2.5-2.5,2.5,2.5,0,0,1,2.5,2.5,2.5,2.5,0,0,1-2.5,2.5A2.5,2.5,0,0,1-4856,293.5Z"
	                    transform="translate(4856 -291)"
	                    fill="#cffbe3"
	                  />
	                </svg>
	              </button>
	              <ul
	                class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
	              >
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Year') }}</a></li>
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Month') }}</a></li>
	                <li><a class="dropdown-item" href="#">{{ get_phrase('Week') }}</a></li>
	              </ul>
	            </div>
	          </div>
	          <div class="ds_report_list"></div>
	        </div>
	      </div>    
	      
	      
	  -->    
	      <!-- Upcoming Events -->
	      
	      <!--
	      <div class="col-6 mt-4">
	        <div class="dashboard_report dashboard_upcoming_events">
	          <div
	            class="ds_report_header d-flex justify-content-between align-items-start"
	          >
	            <div class="ds_report_left">
	              <h4 class="title">{{ get_phrase('Upcoming Events') }}</h4>
	            </div>
	            
	          </div>
	          <div class="ds_report_list pt-38">
	            <ul class="upcoming_events_items d-flex flex-column">

					@php $upcoming_events = DB::table('frontend_events')->where('school_id', auth()->user()->school_id)->where('timestamp', '>', time())->where('status', 1)->take(3)->orderBy('id', 'DESC')->get(); @endphp
					@foreach($upcoming_events as $upcoming_event)
					<li>
						<div
						class="upcoming_events_item d-flex justify-content-between align-items-start"
						>
						<div class="events_info">
							<a href="#" class="title">{{ $upcoming_event->title }}</a>
							<p class="date">{{ date('D, M d Y', $upcoming_event->timestamp) }}</p>
						</div>
						
						</div>
					</li>
					@endforeach
	            </ul>
	            <div class="text-end">
	              <a href="{{route('admin.events.list')}}" class="all_report_btn_2">{{ get_phrase('See all') }}</a>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	</div>
-->

<!-- Resources -->
 
<script src="{{asset('assets/amchart/index.js')}}"></script>
<script src="{{asset('assets/amchart/xy.js')}}"></script>
<script src="{{asset('assets/amchart/animated.js')}}"></script>



<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Chart Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('incomeChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($month_labels) !!},
            datasets: [{
                label: 'Monthly Income',
                data: {!! json_encode($monthly_income_data) !!},
                backgroundColor: '#ffffff',
                borderColor: '#ffffff',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#fff',
                        callback: function(value) {
                            return value + ' ₹';
                        }
                    },
                    grid: { color: 'rgba(255,255,255,0.2)' }
                },
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            }
        }
    });
});
</script>

<!-- Chart code -->
 
<script>
"use strict";

am5.ready(function() {
  // Create root element
  var root = am5.Root.new("chartdiv");

  // Set theme
  root.setThemes([ am5themes_Animated.new(root) ]);

  // Create chart
  var chart = root.container.children.push(am5xy.XYChart.new(root, {
    panX: true,
    panY: true,
    wheelX: "panX",
    wheelY: "zoomX",
    pinchZoomX: true
  }));

  // Add cursor
  var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
  cursor.lineY.set("visible", false);

  // X Axis
  var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
  xRenderer.labels.template.setAll({
    rotation: -90,
    centerY: am5.p50,
    centerX: am5.p100,
    paddingRight: 15
  });

  var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
    maxDeviation: 0.3,
    categoryField: "class_name",
    renderer: xRenderer,
    tooltip: am5.Tooltip.new(root, {})
  }));

  // Y Axis
  var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
    maxDeviation: 0.3,
    renderer: am5xy.AxisRendererY.new(root, {})
  }));

  // Series
  var series = chart.series.push(am5xy.ColumnSeries.new(root, {
    name: "Today’s Attendance",
    xAxis: xAxis,
    yAxis: yAxis,
    valueYField: "today_attended",
    sequencedInterpolation: true,
    categoryXField: "class_name",
    tooltip: am5.Tooltip.new(root, {
      labelText: "{valueY}"
    })
  }));

  series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
  series.columns.template.adapters.add("fill", function(fill, target) {
    return chart.get("colors").getIndex(series.columns.indexOf(target));
  });
  series.columns.template.adapters.add("stroke", function(stroke, target) {
    return chart.get("colors").getIndex(series.columns.indexOf(target));
  });

  // ✅ Set data
  const chartData = @json($class_wise_attandance);
  console.log("✅ Chart Data: ", chartData);
  xAxis.data.setAll(chartData);
  series.data.setAll(chartData);

  // Animate
  series.appear(1000);
  chart.appear(1000, 100);
});





</script>

<!-- HTML -->





@include('includes.firebase')

<!--Boradcast Notification-->
<!--

<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Send Notification</h5>
      </div>
      <div class="card-body">
        <form id="broadcastForm">
          <div class="mb-3">
            <label for="broadcastMessage" class="form-label">Enter Text</label>
            <input type="text" id="broadcastMessage" class="form-control" maxlength="255" placeholder="Enter your text for Notification..." required>
          </div>

          <div class="mb-3">
            <label for="target" class="form-label">Send To</label>
            <select id="target" class="form-select" required>
              <option value="all">All Users</option>
              <option value="parents">All Parents</option>
              <option value="students">All Students</option>
              <option value="teachers">All Teachers</option>
              <option value="class">All Parents of a Class</option>
              <option value="section">All Parents of a Class of a Section</option>
            </select>
          </div>

          <div class="row" id="classSectionOptions" style="display: none;">
            <div class="col-md-6 mb-3">
              <label for="class_id" class="form-label">Class ID</label>
              <input type="number" class="form-control" id="class_id" name="class_id">
            </div>
            <div class="col-md-6 mb-3" id="sectionField" style="display: none;">
              <label for="section_id" class="form-label">Section ID</label>
              <input type="number" class="form-control" id="section_id" name="section_id">
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Send Notification</button>
        </form>
        <div id="broadcastStatus" class="mt-2 text-success" style="display: none;"></div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('target').addEventListener('change', function () {
  const selected = this.value;
  const classSectionOptions = document.getElementById('classSectionOptions');
  const sectionField = document.getElementById('sectionField');

  if (selected === 'class') {
    classSectionOptions.style.display = 'flex';
    sectionField.style.display = 'none';
  } else if (selected === 'section') {
    classSectionOptions.style.display = 'flex';
    sectionField.style.display = 'block';
  } else {
    classSectionOptions.style.display = 'none';
  }
});

document.getElementById('broadcastForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const message = document.getElementById('broadcastMessage').value;
  const target = document.getElementById('target').value;
  const class_id = document.getElementById('class_id').value;
  const section_id = document.getElementById('section_id').value;

  const payload = { message, target };

  if (target === 'class') payload.class_id = class_id;
  if (target === 'section') {
    payload.class_id = class_id;
    payload.section_id = section_id;
  }

  fetch("{{ route('admin.broadcast.notification') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      document.getElementById('broadcastStatus').style.display = 'block';
      document.getElementById('broadcastStatus').innerText = res.summary || '✅ Broadcast sent.';
      document.getElementById('broadcastForm').reset();
      document.getElementById('classSectionOptions').style.display = 'none';
      document.getElementById('sectionField').style.display = 'none';
    } else {
      alert('Something went wrong!');
    }
  })
  .catch(err => {
    alert('Error sending notification.');
    console.error(err);
  });
});
</script>
-->

@endsection