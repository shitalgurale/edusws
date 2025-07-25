@extends('accountant.navigation')
@section('content')   
   
   @php
       $class_wise_attandance = array();
       $total_student = 0;
       $todays_total_attandance = 0;
       $all_classes = DB::table('classes')->where('school_id', auth()->user()->school_id)->get();
       $currently_session_id = DB::table('sessions')->where('status', 1)->value('id');
   
       foreach($all_classes as $class){
           $total_student += DB::table('enrollments')->where('session_id', $currently_session_id)->where('class_id', $class->id)->where('school_id', auth()->user()->school_id)->get()->count();
   
           $start_date = strtotime(date('d M Y'));
           $end_date = $start_date + 86400;
           $today_attanded = DB::table('daily_attendances')->where('class_id', $class->id)->where('timestamp', '>=', $start_date)->where('timestamp', '<', $end_date)->get();
           array_push($class_wise_attandance, array("class_name" => $class->name, "today_attended" => $today_attanded->count()));
           $todays_total_attandance += $today_attanded->count();
       }
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
            <div class="col-lg-12">
                <div class="dashboard_ShortListItem">
                        <h4 class="text-dark">{{ auth()->user()->name }}</h4>
                        <p>Welcome, to {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</p>
                </div>
            </div>
            <div class="row align-items-stretch">
  <!-- Left: 2x2 Cards (Students, Teachers, Parents, Staff) -->
  <div class="col-md-6">
    <div class="row">
      <!-- Card 1 -->
      <div class="col-sm-6 mb-3 px-2">
        <div class="card-block h-100" style="background-color: #1abc9c;">
          <img src="{{ asset('assets/images/student.png') }}" class="card-bg-icon" alt="Student Icon">
          <div class="card-value">
            {{ DB::table('users')->where('role_id', 7)->where('school_id', auth()->user()->school_id)->count() }}
          </div>
          <div class="card-label">Students</div>
          <div class="card-footer-link">
           <!-- <span>View More</span>
            <a href="{{ route('admin.student') }}"><i class="fas fa-arrow-right"></i></a>-->
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-sm-6 mb-3 px-2">
        <div class="card-block h-100" style="background-color: #3498db;">
          <img src="{{ asset('assets/images/teacher.png') }}" class="card-bg-icon" alt="Teacher Icon">
          <div class="card-value">
            {{ DB::table('users')->where('role_id', 3)->where('school_id', auth()->user()->school_id)->count() }}
          </div>
          <div class="card-label">Teachers</div>
          <div class="card-footer-link">
            <!--<span>View More</span>
            <a href="{{ route('admin.teacher') }}"><i class="fas fa-arrow-right"></i></a>-->
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-sm-6 mb-3 px-2">
        <div class="card-block h-100" style="background-color: #e74c3c;">
          <img src="{{ asset('assets/images/parents.png') }}" class="card-bg-icon" alt="Parent Icon">
          <div class="card-value">
            {{ DB::table('users')->where('role_id', 6)->where('school_id', auth()->user()->school_id)->count() }}
          </div>
          <div class="card-label">Parents</div>
          <div class="card-footer-link">
       <!--     <span>View More</span>
            <a href="{{ route('admin.parent') }}"><i class="fas fa-arrow-right"></i></a>-->
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col-sm-6 mb-3 px-2">
        <div class="card-block h-100" style="background-color: #9b59b6;">
          <img src="{{ asset('assets/images/staff.png') }}" class="card-bg-icon" alt="Staff Icon">
          <div class="card-value">
            {{ DB::table('users')->whereIn('role_id', [2,3,4,5])->where('school_id', auth()->user()->school_id)->count() }}
          </div>
          <div class="card-label">Staff</div>
          <div class="card-footer-link">
          <!--  <span>View More</span>
            <a href="{{ route('hr.showAllStaff') }}"><i class="fas fa-arrow-right"></i></a>-->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Upcoming Events -->
  @php
    $upcoming_events = DB::table('frontend_events')
        ->where('school_id', auth()->user()->school_id)
        ->where('timestamp', '>', time())
        ->where('status', 1)
        ->orderBy('timestamp', 'ASC')
        ->take(5)
        ->get();
  @endphp

  <div class="col-md-6">
    <div class="card shadow p-3 d-flex flex-column justify-content-between h-100" style="background: #e74c3c; color: #fff;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">{{ get_phrase('Upcoming Events') }}</h4>
        <a href="{{ route('teacher.events.list') }}" class="text-white text-decoration-underline">{{ get_phrase('See all') }}</a>
      </div>

      <!-- Scrollable event list -->
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



   <!-- Resources -->
   <script src="{{asset('assets/amchart/index.js')}}"></script>
   <script src="{{asset('assets/amchart/xy.js')}}"></script>
   <script src="{{asset('assets/amchart/animated.js')}}"></script>
   
   <!-- Chart code -->
   <script>
   "use strict";
   
   am5.ready(function() {
   
   // Create root element
   var root = am5.Root.new("chartdiv");
   
   
   // Set themes
   root.setThemes([
     am5themes_Animated.new(root)
   ]);
   
   
   // Create chart
   var chart = root.container.children.push(am5xy.XYChart.new(root, {
     panX: true,
     panY: true,
     wheelX: "panX",
     wheelY: "zoomX",
     pinchZoomX:true
   }));
   
   // Add cursor
   var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
   cursor.lineY.set("visible", false);
   
   
   // Create axes
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
   
   var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
     maxDeviation: 0.3,
     renderer: am5xy.AxisRendererY.new(root, {})
   }));
   
   
   // Create series
   var series = chart.series.push(am5xy.ColumnSeries.new(root, {
     name: "Series 1",
     xAxis: xAxis,
     yAxis: yAxis,
     valueYField: "today_attended",
     sequencedInterpolation: true,
     categoryXField: "class_name",
     tooltip: am5.Tooltip.new(root, {
       labelText:"{valueY}"
     })
   }));
   
   series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
   series.columns.template.adapters.add("fill", function(fill, target) {
     return chart.get("colors").getIndex(series.columns.indexOf(target));
   });
   
   series.columns.template.adapters.add("stroke", function(stroke, target) {
     return chart.get("colors").getIndex(series.columns.indexOf(target));
   });
   
   
   // Set data
   var data = <?php echo json_encode($class_wise_attandance); ?>;
   
   xAxis.data.setAll(data);
   series.data.setAll(data);
   
   
   // Make stuff animate on load
   // https://www.amcharts.com/docs/v5/concepts/animations/
   series.appear(1000);
   chart.appear(1000, 100);
   
   }); // end am5.ready()
   </script>
   
   <!-- HTML -->

@include('includes.firebase')
   
@endsection
