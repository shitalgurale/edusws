@extends('admin.navigation')
   
@section('content')

<?php 

use App\Http\Controllers\CommonController;
use App\Models\School;
use App\Models\Section;

$user = Auth()->user();
$menu_permission = (empty($user->menu_permission) || $user->menu_permission == 'null') ? []:json_decode($user->menu_permission, true);
?>

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Students') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Users') }}</a></li>
              <li><a href="#">{{ get_phrase('Students') }}</a></li>
            </ul>
          </div>
          @if(empty($user->menu_permission) || in_array('admin.offline_admission.single', $menu_permission)) 
          <div class="export-btn-area">
            <a href="{{ route('admin.offline_admission.single', ['type' => 'single']) }}" class="export_btn">{{ get_phrase('Create Student') }}</a>
          </div>
          @endif
        </div>
      </div>
    </div>
</div>
<!-- Start Students area -->
<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
          <!-- Search and filter -->
            <div
              class="search-filter-area d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
            >
              <form action="{{ route('admin.student') }}">
                <div
                  class="search-input d-flex justify-content-start align-items-center"
                >
                  <span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 16 16"
                    >
                      <path
                        id="Search_icon"
                        data-name="Search icon"
                        d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z"
                        fill="#797c8b"
                      />
                    </svg>
                  </span>
                  <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search Students"
                    class="form-control"
                  />
               <!--   @if($class_id != '')
                  <input type="hidden" name="class_id" id="class_id" value="{{ $class_id }}">
                  @endif
                  @if($section_id != '')
                  <input type="hidden" name="section_id" id="section_id" value="{{ $section_id }}">
                  @endif-->
                </div>
              </form>
              
              <!-- Without SESSION ONLY Class & Section
              <div class="filter-export-area d-flex align-items-center">
    <form action="{{ route('admin.student.filter') }}" method="GET" class="position-relative filter-option d-flex">

        @if($search != '')
            <input type="hidden" name="search" value="{{ $search }}">
        @endif

        <div class="position-relative me-2">
            <select class="form-select" name="class_id" id="class_id" onchange="classWiseSection(this.value)" required>
                <option value="">{{ get_phrase('Select a class') }}</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ (!empty($class_id) && $class_id == $class->id) ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="position-relative me-2">
            <select class="form-select" name="section_id" id="section_id">
                <option value="">{{ get_phrase('Select a section') }}</option>
                @if(!empty($class_id))
                    @foreach(\App\Models\Section::where('class_id', $class_id)->get() as $section)
                        <option value="{{ $section->id }}" {{ (!empty($section_id) && $section_id == $section->id) ? 'selected' : '' }}>
                            {{ $section->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="filter-button d-flex justify-content-end align-items-center">
            <button class="eBtn eBtn btn-primary" type="submit">{{ get_phrase('Filter') }}</button>
        </div>

    </form>
</div>   -->





<!--  With class, section and session-->

<div class="filter-export-area d-flex align-items-center">
    <form action="{{ route('admin.student.filter') }}" method="GET" class="position-relative filter-option d-flex">

        @if($search != '')
            <input type="hidden" name="search" value="{{ $search }}">
        @endif

        
            <!-- Filter By Dropdown -->
            <div class="position-relative me-2">
                <select class="form-select" id="filter_by" onchange="onFilterByChange(this.value)">
                    <option value="">{{ get_phrase('Filter By') }}</option>
                    <option value="religion">{{ get_phrase('Religion') }}</option>
                    <option value="gender">{{ get_phrase('Gender') }}</option>
                    <option value="caste">{{ get_phrase('Caste') }}</option>
                </select>
            </div>
            
            <!-- Dynamic Input Dropdown -->
            <div class="position-relative me-2" id="filter_value_container" style="display: none;">
                <select class="form-select" id="filter_value" name="filter_value">
                    <option value="">{{ get_phrase('Select Value') }}</option>
                    <!-- Options will be populated via JavaScript -->
                </select>
                <input type="hidden" name="filter_type" id="filter_type">
            </div>
            <!-- Class Dropdown -->
            <div class="position-relative me-2">
                <select class="form-select" name="class_id" id="class_id" onchange="classWiseSection(this.value)" required>
                    <option value="">{{ get_phrase('Class') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (!empty($class_id) && $class_id == $class->id) ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
    
            <!-- Section Dropdown -->
            <div class="position-relative me-2">
                <select class="form-select" name="section_id" id="section_id">
                    <option value="">{{ get_phrase('Section') }}</option>
                    @if(!empty($class_id))
                        @foreach(\App\Models\Section::where('class_id', $class_id)->get() as $section)
                            <option value="{{ $section->id }}" {{ (!empty($section_id) && $section_id == $section->id) ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            
        
            <!-- Session Dropdown -->
           <div class="position-relative me-2">
        <select name="session_id" id="session_id" class="form-select" required>
            <option value="">{{ get_phrase('Session') }}</option>
            @foreach ($sessions as $session)
                <option value="{{ $session->id }}" {{ (!empty($session_id) && $session_id == $session->id) ? 'selected' : '' }}>
                    {{ $session->session_title }}
                </option>
            @endforeach
        </select>
    </div>
        

        <!-- Filter Button -->
        <div class="filter-button d-flex justify-content-end align-items-center">
            <button class="eBtn eBtn btn-primary" type="submit">{{ get_phrase('Filter') }}</button>
        </div>

    </form>




                <!-- Export Button -->
                @if(count($students) > 0)
                <div class="position-relative">
                  <button
                    class="eBtn-3 dropdown-toggle"
                    type="button"
                    id="defaultDropdown"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="true"
                    aria-expanded="false"
                  >
                    <span class="pr-10">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="12.31"
                        height="10.77"
                        viewBox="0 0 10.771 12.31"
                      >
                        <path
                          id="arrow-right-from-bracket-solid"
                          d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                          transform="translate(0 12.31) rotate(-90)"
                          fill="#00a3ff"
                        />
                      </svg>
                    </span>
                    {{ get_phrase('Export') }}
                  </button>
                  <ul
                    class="dropdown-menu dropdown-menu-end eDropdown-menu-2"
                  >
                    <li>
                        <a class="dropdown-item" id="pdf" href="javascript:;" onclick="Export()">{{ get_phrase('PDF') }}</a>
                    </li>
                    <li>
                        <a class="dropdown-item" id="print" href="javascript:;" onclick="printableDiv('student_list')">{{ get_phrase('Print') }}</a>
                    </li>
                  </ul>
                </div>
                @endif
              </div>
            </div>
            @if(count($students) > 0)
            <!-- Table -->
            <div class="table-responsive">
              <table class="table eTable eTable-2">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ get_phrase('Name') }}</th>
                    <th scope="col">{{ get_phrase('Bio ID') }}</th></th>
                   <!-- <th scope="col">{{ get_phrase('Email') }}</th>-->
                    <th scope="col">{{ get_phrase('User Info') }}</th>
                    <th scope="col">{{ get_phrase('Account Status') }}</th>
                    <th scope="col">{{ get_phrase('Options') }}</th>
                </thead>
                <tbody>
                    @foreach($students as $key => $user)
                    <?php 

                        $student = DB::table('users')->where('id', $user->user_id)->first();

                        $user_image = get_user_image($user->user_id);
                        $info = json_decode($student->user_information);

                        $student_details = (new CommonController)->get_student_academic_info($student->id);
                    ?>
                      <tr>
                        <th scope="row">
                          <p class="row-number">{{ $students->firstItem() + $key }}</p>
                        </th>
                        <td>
                          <div
                            class="dAdmin_profile d-flex align-items-center min-w-200px"
                          >
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="{{ $user_image }}"
                              />
                            </div>
                            <div class="dAdmin_profile_name dAdmin_info_name">
                              <h4>{{ $student->name }}</h4>
                              <p>
                                @if(empty($student_details->class_name))
                                <span>{{ get_phrase('Class') }}:</span>
                                 {{ get_phrase('Removed') }}
                                 <br>
                                <span>{{ get_phrase('Section') }}:</span>
                                {{ get_phrase('Removed') }}
                                @else
                                <span>{{ get_phrase('Class') }}:</span> {{ $student_details->class_name }}
                                <br>
                                <span>{{ get_phrase('Section') }}:</span> {{ $student_details->section_name }}
                                @endif
                              </p>
                            </div>
                          </div>
                        </td>
                       <!-- <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p>{{ $student->email }}</p>
                          </div>
                        </td>-->
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p>{{ $student_details->stu_bioid ?? '-' }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone ?? '-' }}</p>
                            <p>
                              <span>{{ get_phrase('Address') }}:</span> {{ $info->address ?? '-' }}
                            </p>
                          </div>
                        </td>

                        <td>
                          <div class="dAdmin_info_name min-w-100px">
                            @if(!empty($student->account_status == 'disable'))
                            <span class="eBadge ebg-soft-danger">{{get_phrase('Disabled')}}</span>
                            @else
                            <span class="eBadge ebg-soft-success">{{get_phrase('Enable')}}</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <div class="adminTable-action">
                            <button
                              type="button"
                              class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                              data-bs-toggle="dropdown"
                              aria-expanded="false"
                            >
                              {{ get_phrase('Actions') }}
                            </button>
                            <ul
                              class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action"
                            >
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('admin.student.id_card', ['id' => $student->id]) }}', '{{ get_phrase('Generate id card') }}')">{{ get_phrase('Generate Id card') }}</a>
                              </li>

                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.student_edit_modal', ['id' => $student->id]) }}', 'Edit Student')">{{ get_phrase('Edit') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.student.delete', ['id' => $student->id]) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="largeModal('{{ route('admin.student.student_profile', ['id' => $student->id]) }}','{{ get_phrase('Student Profile') }}')">{{ get_phrase('Profile') }}</a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="{{ route('admin.student.documents', ['id' => $student->id]) }}">{{ get_phrase('Documents') }}</a>
                              </li>
                              @if(!empty($student->account_status == 'disable'))
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_enable', ['id' => $student->id]) }}', 'undefined');">{{ get_phrase('Enable') }}</a>
                              </li>
                              @else
                              <li>
                                <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.account_disable', ['id' => $student->id]) }}', 'undefined');">{{ get_phrase('Disable') }}</a>
                              </li>
                              @endif
                            </ul>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
              
              <div
                  class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"
                >
                  <p class="admin-tInfo">{{ get_phrase('Showing').' 1 - '.count($students).' '.get_phrase('from').' '.$students->total().' '.get_phrase('data') }}</p>
                  <div class="admin-pagi">
                    {!! $students->appends(request()->all())->links() !!}
                  </div>
                </div>
              </div>
              
            </div>
            @else
            <div class="empty_box center">
              <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
              <br>
              <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

@if(count($students) > 0)
<!-- Table -->
<div class="table-responsive student_list display-none-view" id="student_list">
  <h4 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase(' Students List') }}</h4>
@if(!empty($session_id))
  @php
    $selected_session = $sessions->firstWhere('id', $session_id);
  @endphp
  <h5 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase('Session') }}: {{ $selected_session->session_title ?? '-' }}</h5>
@elseif(!empty($current_session))
  <h5 class="" style="font-size: 16px; font-weight: 600; line-height: 26px; color: #181c32; margin-left:45%; margin-bottom:15px; margin-top:17px;">{{ get_phrase('Session') }}: {{ $current_session->session_title }}</h5>
@endif
  <table class="table eTable eTable-2">
    <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ get_phrase('Name') }}</th>
                    <th scope="col">{{ get_phrase('Student ID') }}</th>
                    <th scope="col">{{ get_phrase('Bio ID') }}</th>
                   <!-- <th scope="col">{{ get_phrase('Email') }}</th>-->
                    
                    <th scope="col">{{ get_phrase('User Info') }}</th>
                    <th scope="col">{{ get_phrase('Account Status') }}</th>

                 
                </thead>
                <tbody>
                    @foreach($students as $key => $user)
                    <?php 

                        $student = DB::table('users')->where('id', $user->user_id)->first();

                        $user_image = get_user_image($user->user_id);
                        $info = json_decode($student->user_information);

                        $student_details = (new CommonController)->get_student_academic_info($student->id);
                    ?>
                      <tr>
                        <th scope="row">
                          <p class="row-number">{{ $students->firstItem() + $key }}</p>
                        </th>
                        <td>
                          <div
                            class="dAdmin_profile d-flex"
                          >
                            <div class="dAdmin_profile_img">
                              <img
                                class="img-fluid"
                                width="50"
                                height="50"
                                src="{{ $user_image }}"
                              />
                            </div>
                            <div class="dAdmin_profile_name dAdmin_info_name">
                              <h4>{{ $student->name }}</h4>
                              <p>
                                @if(empty($student_details->class_name))
                                <span>{{ get_phrase('Class') }}:</span>
                                 {{ get_phrase('Removed') }}
                                 <br>
                                <span>{{ get_phrase('Section') }}:</span>
                                {{ get_phrase('Removed') }}
                                @else
                                <span>{{ get_phrase('Class') }}:</span> {{ $student_details->class_name }}
                                <br>
                                <span>{{ get_phrase('Section') }}:</span> {{ $student_details->section_name }}
                                @endif
                              </p>
                            </div>
                          </div>
                        </td>
                       <!-- <td>
                          <div class="dAdmin_info_name min-w-250px">
                            <p>{{ $student->email }}</p>
                          </div>
                        </td>-->
                        
                        <td>
                          <div class="dAdmin_info_name">
                            <p>{{ $student_details->user_id ?? '-' }}</p>
                          </div>
                        </td>
                        <td>
                          <div class="dAdmin_info_name">
                            <p>{{ $student_details->stu_bioid ?? '-' }}</p>
                          </div>
                        </td>
                        
                        <td>
                          <div class="dAdmin_info_name">
                            <p><span>{{ get_phrase('Phone') }}:</span> {{ $info->phone ?? '-' }}</p>
                            <p>
                              <span>{{ get_phrase('Address') }}:</span> {{ $info->address ?? '-' }}
                            </p>
                          </div>
                        </td>
                        <td>
                          {{ isset($student->account_status) && $student->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Enable') }}
                        </td>

                      </tr>
                    @endforeach
                </tbody>
  </table>
  <!--{{!! $students->appends(request()->all())->links() !!}}-->
  {!! $students->appends(request()->all())->links() !!}
</div>
@endif


<script type="text/javascript">

  "use strict";

  function classWiseSection(classId) {
    let url = "{{ route('class_wise_sections', ['id' => ":classId"]) }}";
    url = url.replace(":classId", classId);
    $.ajax({
        url: url,
        success: function(response){
            $('#section_id').html(response);
        }
    });
  }
  
  
function Export() {
    const element = document.getElementById("student_list");
    element.classList.remove("display-none-view");

    setTimeout(() => {
        const opt = {
            margin: 0.5,
            filename: 'student_list_{{ date("Y-m-d") }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            element.classList.add("display-none-view");
        });
    }, 300);
}

  
  
/*
  function Export() {

      // Choose the element that our invoice is rendered in.
      const element = document.getElementById("student_list");

      // clone the element
      var clonedElement = element.cloneNode(true);

      // change display of cloned element
      $(clonedElement).css("display", "block");

      // Choose the clonedElement and save the PDF for our user.
    var opt = {
      margin:       1,
      filename:     'student_list_{{ date("y-m-d") }}.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 }
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(clonedElement).save();

      // remove cloned element
      clonedElement.remove();
  }
*/
  function printableDiv(printableAreaDivId) {
    var printContents = document.getElementById(printableAreaDivId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
  }
  
  
  
  
  function onFilterByChange(type) {
    const filterDropdown = document.getElementById('filter_value');
    const filterContainer = document.getElementById('filter_value_container');
    const filterTypeInput = document.getElementById('filter_type');

    filterDropdown.innerHTML = '<option value="">{{ get_phrase("Select Value") }}</option>';
    filterTypeInput.value = type;

    if (!type) {
        filterContainer.style.display = 'none';
        return;
    }

    let options = [];

    if (type === 'religion') {
        options = ['Hindu', 'Islam', 'Sikh', 'Buddh','Christian','Jain','Other'];
    } else if (type === 'gender') {
        options = ['Male', 'Female', 'Others'];
    } else if (type === 'caste') {
        options = ['OC', 'BC', 'SC', 'ST', 'Other'];
    }

    options.forEach(function(opt) {
        const option = document.createElement("option");
        option.value = opt;
        option.text = opt;
        filterDropdown.appendChild(option);
    });

    filterContainer.style.display = 'block';
}

function submitFilterBy() {
    document.querySelector('.filter-option').submit();
}

</script>


<!-- End Students area -->
@endsection