@extends('parent.navigation')
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
                        <p>{{ get_phrase('Welcome, to') }} {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</p>
                </div>
            </div>
             <!-- Dashboard Short Details -->
             <div class="col-lg-6">
               <div class="dashboard_ShortListItems">
                 <div class="row">
                 <div class="col-md-6">
    <div class="dashboard_ShortListItem">
        <div class="dsHeader d-flex justify-content-between align-items-center">
            <h5 class="title">{{ get_phrase('Inbox') }}</h5>

            <!-- Link to Inbox -->
            <a href="{{ route('parent.inbox') }}"><!-- class="ds_link">-->
            <img
	                      src="{{ asset('assets/images/forward.png') }}"
	                      alt=""
	                    />
                <!--<svg xmlns="http://www.w3.org/2000/svg" width="10.146" height="4.764" viewBox="0 0 10.146 4.764">
                    <path
                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
                        transform="translate(-3.573 -5.978)"
                        fill="#0d6efd" />
                </svg> -->
            </a>
        </div>

        <div class="dsBody d-flex justify-content-between align-items-center">
            <div class="ds_item_details">
                <h4 class="total_no">
                    {{ $unreadMessageCount ?? 0 }} <!-- ✅ Show unread message count -->
                </h4>
                <p class="total_info">{{ get_phrase('Unread Messages') }}</p> <!-- ✅ Change label -->
            </div>
            <div class="ds_item_icon">
               <img src="{{ asset('assets/images/Inbox_icon.png') }}" alt=""/>
                 <!--<img src="{{ asset('assets/images/Inbox_icon.png') }}" alt=""/>-->
            </div>
        </div>
    </div>
</div>


<div class="col-md-6">
    <div class="dashboard_ShortListItem">
        <div class="dsHeader d-flex justify-content-between align-items-center">
            <h5 class="title">{{ get_phrase('Child') }}</h5>
            <a href="{{ route('parent.childlist') }}"> <!-- class="ds_link">-->
            <img
	                      src="{{ asset('assets/images/forward.png') }}"
	                      alt=""
	                    />
                <!--<svg xmlns="http://www.w3.org/2000/svg" width="10.146" height="4.764" viewBox="0 0 10.146 4.764">
                    <path
                        id="Read_more_icon"
                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
                        transform="translate(-3.573 -5.978)"
                        fill="#36B37E"
                    />
                </svg> -->
            </a>
        </div>

        <div class="dsBody d-flex justify-content-between align-items-center">
            <div class="ds_item_details">
                <h4 class="total_no">
                    {{ \App\Models\User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->count() }}
                </h4>
                <p class="total_info">{{ get_phrase('Total Child') }}</p>
            </div>
            <div class="ds_item_icon">
                <img src="{{ asset('assets/images/Student_icon1.png') }}" alt=""/>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="dashboard_ShortListItem">
        <div class="dsHeader d-flex justify-content-between align-items-center">
            <h5 class="title">{{ get_phrase('Fee Manager') }}</h5>
            <a href="{{ route('parent.fee_manager.list') }}" >    <!-- class="ds_link"> -->
            <img
	                      src="{{ asset('assets/images/forward.png') }}"
	                      alt=""
	                    />
                <!--<svg xmlns="http://www.w3.org/2000/svg" width="10.146" height="4.764" viewBox="0 0 10.146 4.764">
                    <path
                        id="Read_more_icon"
                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
                        transform="translate(-3.573 -5.978)"
                        fill="#FFC107"
                    />
                </svg>-->
            </a>
        </div>

        <div class="dsBody d-flex justify-content-between align-items-center">
            <div class="ds_item_details">
                @php
                    $children = \App\Models\User::where('parent_id', auth()->user()->id)
                                ->where('school_id', auth()->user()->school_id)
                                 ->get();
                @endphp

                <h4 class="total_no" id="dueAmount">0</h4>
                <p class="total_info">{{ get_phrase('Due Amount') }}</p>

                @if($children->count() > 1)
                    <div class="mt-2">
                        <select id="feeChildDropdown" class="form-select">
                            <option value="">Select Child</option>
                            @foreach($children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" id="singleChildId" value="{{ $children->first()->id }}">
                @endif
            </div>

            <div class="ds_item_icon">
                <img src="{{ asset('assets/images/Fee_manager_icon.png') }}" alt=""/>
            </div>
        </div>
    </div>
</div>


<div class="col-md-6">
    <div class="dashboard_ShortListItem">
        <div class="dsHeader d-flex justify-content-between align-items-center">
            <h5 class="title">{{ get_phrase('Attendance') }}</h5>
            <a href="{{ route('parent.list_of_attendence') }}"> <!--class="ds_link">-->
            <img
	                      src="{{ asset('assets/images/forward.png') }}"
	                      alt=""
	                    />
                <!--<svg xmlns="http://www.w3.org/2000/svg" width="10.146" height="4.764" viewBox="0 0 10.146 4.764">
                    <path
                        id="Read_more_icon"
                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
                        transform="translate(-3.573 -5.978)"
                        fill="#F24976"
                    />
                </svg>-->
            </a>
        </div>

        <div class="dsBody d-flex justify-content-between align-items-center">
            <div class="ds_item_details">
                @php
                    // Fetch all children for the logged-in parent
                    $children = \App\Models\User::where('parent_id', auth()->user()->id)
                                ->where('school_id', auth()->user()->school_id)
                                ->get();

                    $defaultChildId = $children->first()->id ?? null;

                    $currentMonth = date('m');
                    $currentYear = date('Y');

                    $presentDays = 0;
                    if ($defaultChildId) {
                        $presentDays = \App\Models\DailyAttendances::where('student_id', $defaultChildId)
                            ->where('school_id', auth()->user()->school_id)
                            ->whereMonth('stu_intime', $currentMonth)
                            ->whereYear('stu_intime', $currentYear)
                            ->where('status', 1) // Only Present
                            ->count();
                    }
                @endphp

                <h4 class="total_no">{{ $presentDays }}</h4>
                <p class="total_info">{{ get_phrase (' Present Days') }}</p>

                @if($children->count() > 1)
                    <div class="mt-2">
                        <select id="attendanceChildDropdown" class="form-select">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="ds_item_icon">
                <img src="{{ asset('assets/images/Attendance_icon.png') }}" alt=""/>
            </div>
        </div>
    </div>
    <script>
document.getElementById('attendanceChildDropdown')?.addEventListener('change', function() {
    var childId = this.value;

    fetch('{{ route('parent.fetch_present_days') }}?child_id=' + childId)
        .then(response => response.json())
        .then(data => {
            var attendanceCard = this.closest('.dashboard_ShortListItem');
            attendanceCard.querySelector('.total_no').innerText = data.presentDays ?? 0;
        })
        .catch(error => console.error('Error fetching present days:', error));
});
</script>
                  </div>
                  </div>
                  </div>
                  </div>

            
<!--
                    <div class="col-md-6">
                     <div class="dashboard_ShortListItem">
                       <div
                         class="dsHeader d-flex justify-content-between align-items-center"
                       >
                         <h5 class="title">{{ get_phrase('Trips') }}</h5>
                         <a href="{{ route('parent.trips.list') }}" class="ds_link">
                <svg xmlns="http://www.w3.org/2000/svg" width="10.146" height="4.764" viewBox="0 0 10.146 4.764">
                    <path
                        id="Read_more_icon"
                        d="M11.337,5.978l-.84.84.941.947H3.573V8.955h7.865L10.5,9.9l.84.846L13.719,8.36Z"
                        transform="translate(-3.573 -5.978)"
                        fill="#F24976"
                    />
                </svg>
            </a>
                       </div>
                       <div
                         class="dsBody d-flex justify-content-between align-items-center"
                       >
                        <div class="ds_item_details">
                          @php $admin = DB::table('users')->where('role_id', 2)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
                          @php $teacher = DB::table('users')->where('role_id', 3)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
                          @php $accountant = DB::table('users')->where('role_id', 4)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
                          @php $librarian = DB::table('users')->where('role_id', 5)->where('school_id', auth()->user()->school_id)->get()->count() @endphp
                           <h4 class="total_no">{{ $admin + $teacher + $accountant + $librarian }}</h4>
                           <p class="total_info">Total Staff</p>
                        </div>
                        <div class="ds_item_icon">
                           <img
                             src="{{ asset('assets/images/Staff_icon.png') }}"
                             alt=""
                           />
                        </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>

                  -->
             <!-- Imcome Report -->
   
             <!-- Upcoming Events -->

         <div class="col-md-6 ms-auto">
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
                     <a href="{{route('parent.events.list')}}" class="all_report_btn_2">{{ get_phrase('See all') }}</a>
                   </div>
                 </div>
               </div>
             </div>
             
             
           </div>
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
   
   
   
   
   
   
// Script for fee Manager
document.addEventListener('DOMContentLoaded', function () {
    const feeChildDropdown = document.getElementById('feeChildDropdown');
    const singleChildIdInput = document.getElementById('singleChildId');

    function fetchDueAmount(childId) {
        fetch("{{ route('parent.fetch_due_amount') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ child_id: childId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('dueAmount').innerText = data.total_due;
            } else {
                document.getElementById('dueAmount').innerText = '0.00';
            }
        })
        .catch(error => {
            console.error('Error fetching due amount:', error);
            document.getElementById('dueAmount').innerText = '0.00';
        });
    }

    if (feeChildDropdown) {
        // Multiple children: Fetch on selection
        feeChildDropdown.addEventListener('change', function () {
            const childId = this.value;
            if (childId) {
                fetchDueAmount(childId);
            } else {
                document.getElementById('dueAmount').innerText = '0.00';
            }
        });
    } else if (singleChildIdInput) {
        // Only one child: Auto fetch on page load
        const childId = singleChildIdInput.value;
        fetchDueAmount(childId);
    }
});



   </script>
   
   <!-- HTML -->
   
@include('includes.firebase')
   
@endsection
