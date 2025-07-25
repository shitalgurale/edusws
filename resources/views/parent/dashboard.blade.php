@extends('parent.navigation')
@section('content')

@php
    $class_wise_attandance = [];
    $total_student = 0;
    $todays_total_attandance = 0;
    $all_classes = DB::table('classes')->where('school_id', auth()->user()->school_id)->get();
    $currently_session_id = DB::table('sessions')->where('status', 1)->value('id');

    foreach($all_classes as $class){
        $total_student += DB::table('enrollments')->where('session_id', $currently_session_id)->where('class_id', $class->id)->where('school_id', auth()->user()->school_id)->count();

        $start_date = strtotime(date('d M Y'));
        $end_date = $start_date + 86400;
        $today_attanded = DB::table('daily_attendances')->where('class_id', $class->id)->where('timestamp', '>=', $start_date)->where('timestamp', '<', $end_date)->get();
        array_push($class_wise_attandance, ["class_name" => $class->name, "today_attended" => $today_attanded->count()]);
        $todays_total_attandance += $today_attanded->count();
    }
@endphp
   
<head>
    
    
  <!-- Existing meta & CSS links -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  



 
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
    .card-footer-link {
    margin-top: 10px;
    padding: 8px 12px;
    background-color: rgba(0, 0, 0, 0.1);
    border-radius: 0 0 8px 8px;
    color: #fff;
    font-weight: 500;
}
.card-footer-link a {
    color: #fff;
    font-size: 16px;
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
 <div class="d-flex flex-column"><h4>{{ get_phrase('Dashboard') }}</h4>
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
           
     <div class="row">
    <!-- Left 4 Cards (Inbox, Children, Fee, Attendance) -->
    <div class="col-md-6">
        <div class="row">

            <!-- Inbox -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #3498db;">
                    <img src="{{ asset('assets/images/inbox.png') }}" class="card-bg-icon" alt="Inbox Icon">
                    <div class="card-value">{{ $unreadMessageCount ?? 0 }}</div>
                    <div class="card-label">Unread Messages</div>
                    <div class="card-footer-link">
                        <span>View More</span>
                        <a href="{{ route('parent.inbox') }}">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Children -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #1abc9c; position: relative;">
                    <img src="{{ asset('assets/images/children.png') }}" class="card-bg-icon" alt="Child Icon">
                    <div class="card-value">
                        {{ \App\Models\User::where('parent_id', auth()->user()->id)->where('school_id', auth()->user()->school_id)->count() }}
                    </div>
                    <div class="card-label">Children</div>
                    <div class="card-footer-link d-flex justify-content-between align-items-center">
                        <span>View More</span>
                        <a href="{{ route('parent.childlist') }}" style="color: #fff;">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fee Manager -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #e67e22; position: relative;">
                    <img src="{{ asset('assets/images/fee.png') }}" class="card-bg-icon" alt="Fee Icon">

                    @php
                        $children = \App\Models\User::where('parent_id', auth()->user()->id)
                                    ->where('school_id', auth()->user()->school_id)
                                    ->get();
                    @endphp

                    <div class="card-value" id="dueAmount">0</div>
                    <div class="card-label">Fee Manager</div>

                    @if($children->count() > 1)
                        <div class="mt-2">
                            <select id="feeChildDropdown" class="form-select">
                                <option value="">Select Child</option>
                                @foreach($children as $child)
                                    <option value="{{ $child->id }}">{{ $child->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($children->count() === 1)
                        <input type="hidden" id="singleChildId" value="{{ $children->first()->id }}">
                    @endif

                    <div class="card-footer-link d-flex justify-content-between align-items-center mt-2">
                        <span>View More</span>
                        <a href="{{ route('parent.fee_manager.list') }}" style="color: #fff;">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance -->
            <div class="col-sm-6 mb-3 px-2">
                <div class="card-block h-100" style="background-color: #9b59b6; position: relative;">
                    <img src="{{ asset('assets/images/attendance.png') }}" class="card-bg-icon" alt="Attendance Icon">

                    @php
                        $defaultChildId = $children->first()->id ?? null;
                        $currentMonth = date('m');
                        $currentYear = date('Y');

                        $presentDays = 0;
                        if ($defaultChildId) {
                            $presentDays = \App\Models\DailyAttendances::where('student_id', $defaultChildId)
                                ->where('school_id', auth()->user()->school_id)
                                ->whereMonth('stu_intime', $currentMonth)
                                ->whereYear('stu_intime', $currentYear)
                                ->where('status', 1)
                                ->count();
                        }
                    @endphp

                    <div class="card-value" id="presentDays">{{ $presentDays }}</div>
                    <div class="card-label">Today Present</div>

                    @if($children->count() > 1)
                        <div class="mt-2">
                            <select id="attendanceChildDropdown" class="form-select">
                                 <option value="">Select Child</option>
                                @foreach($children as $child)
                                    <option value="{{ $child->id }}">{{ $child->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="card-footer-link d-flex justify-content-between align-items-center mt-2">
                        <span>View More</span>
                        <a href="{{ route('parent.list_of_attendence') }}" style="color: #fff;">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Right Column: Upcoming Events -->
    <div class="col-md-6">
        <div class="card shadow p-3 d-flex flex-column justify-content-between h-100" style="background: #e74c3c; color: #fff; border-radius: 15px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">{{ get_phrase('Upcoming Events') }}</h4>
                <a href="{{ route('parent.events.list') }}" class="text-white text-decoration-underline">{{ get_phrase('See all') }}</a>
            </div>

            @php
                $upcoming_events = DB::table('frontend_events')
                    ->where('school_id', auth()->user()->school_id)
                    ->where('timestamp', '>', time())
                    ->where('status', 1)
                    ->orderBy('timestamp', 'ASC')
                    ->take(5)
                    ->get();
            @endphp

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


<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('feeChildDropdown');
    const dueAmountElem = document.getElementById('dueAmount');
    const singleChildId = document.getElementById('singleChildId')?.value;

    function fetchDue(childId) {
        fetch(`/parent/fee/due-amount/${childId}`)
            .then(res => res.json())
            .then(data => {
                dueAmountElem.innerText = data.due_amount ?? '0';
            });
    }

    if (dropdown) {
        dropdown.addEventListener('change', function () {
            const selectedId = this.value;
            if (selectedId) fetchDue(selectedId);
            else dueAmountElem.innerText = '0';
        });
    } else if (singleChildId) {
        fetchDue(singleChildId);
    }
});




document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('attendanceChildDropdown');
    const presentDaysElem = document.getElementById('presentDays');

    if (dropdown) {
        dropdown.addEventListener('change', function () {
            const selectedId = this.value;
            if (selectedId) {
                fetch(`/parent/attendance/present-days/${selectedId}`)
                    .then(res => res.json())
                    .then(data => {
                        presentDaysElem.innerText = data.present_days ?? '0';
                    });
            }
        });
    }
});


</script>
   <!-- HTML -->
   
@include('includes.firebase')
   
@endsection