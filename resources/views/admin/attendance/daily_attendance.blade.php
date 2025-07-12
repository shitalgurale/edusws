@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
        <div class="d-flex flex-column">
          <h4>{{ get_phrase('Monthly Attendance') }}</h4>
          <ul class="d-flex align-items-center eBreadcrumb-2">
            <li><a href="#">{{ get_phrase('Home') }}</a></li>
            <li><a href="#">{{ get_phrase('Academic') }}</a></li>
            <li><a href="#">{{ get_phrase('Monthly Attendance') }}</a></li>
          </ul>
        </div>
        <div class="export-btn-area">
          <a href="#" class="export_btn" onclick="rightModal('{{ route('admin.take_attendance.open_modal') }}', '{{ get_phrase('Mark Attendance') }}')">
            {{ get_phrase('Mark Attendance') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="eSection-wrap-2">
      <!-- Filter area -->
      <form method="GET" enctype="multipart/form-data" class="d-block ajaxForm" onsubmit="filter_attendance(event)">
        <div class="att-filter d-flex flex-wrap">
          <div class="att-filter-option">
            <select name="month" id="month" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value="">{{ get_phrase('Select a month') }}</option>
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ date('M', mktime(0, 0, 0, $m, 1)) }}"
                  {{ date('M') == date('M', mktime(0, 0, 0, $m, 1)) ? 'selected' : '' }}>
                  {{ get_phrase(date('F', mktime(0, 0, 0, $m, 1))) }}
                </option>
              @endfor
            </select>
          </div>
          <div class="att-filter-option">
            <select name="year" id="year" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value="">{{ get_phrase('Select a year') }}</option>
              @for($year = 2015; $year <= date('Y'); $year++)
                <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
              @endfor
            </select>
          </div>
          <div class="att-filter-option">
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove"
              onchange="classWiseSection(this.value)" required>
              <option value="">{{ get_phrase('Select a class') }}</option>
              @foreach($classes as $class)
                  <option value="{{ $class->id }}">{{ $class->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="att-filter-option">
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
              <option value="">{{ get_phrase('Select section') }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-block btn-secondary">
              {{ get_phrase('Filter') }}
            </button>
          </div>
          <div class="col-md-2">
            <div class="position-relative">
              <button class="eBtn-3 dropdown-toggle" type="button" id="defaultDropdown" data-bs-toggle="dropdown"
                data-bs-auto-close="true" aria-expanded="false">
                <span class="pr-10">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.31" height="10.77" viewBox="0 0 10.771 12.31">
                    <path id="arrow-right-from-bracket-solid"
                      d="M3.847,1.539H2.308a.769.769,0,0,0-.769.769V8.463a.769.769,0,0,0,.769.769H3.847a.769.769,0,0,1,0,1.539H2.308A2.308,2.308,0,0,1,0,8.463V2.308A2.308,2.308,0,0,1,2.308,0H3.847a.769.769,0,1,1,0,1.539Zm8.237,4.39L9.007,9.007A.769.769,0,0,1,7.919,7.919L9.685,6.155H4.616a.769.769,0,0,1,0-1.539H9.685L7.92,2.852A.769.769,0,0,1,9.008,1.764l3.078,3.078A.77.77,0,0,1,12.084,5.929Z"
                      transform="translate(0 12.31) rotate(-90)" fill="#00a3ff" />
                  </svg>
                </span>
                Export
              </button>
              <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                 <li>
                  <button class="dropdown-item" onclick="download_csv()">CSV</button>
                </li> 
                <li>
                  <button class="dropdown-item" onclick="exportStudentAttendancePDF()">PDF</button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </form>
      <div class="card-body attendance_content">
        <div class="empty_box text-center">
          <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
          <br>
          <span>{{ get_phrase('Search Attendance Report') }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>

<script>
"use strict";

function classWiseSection(classId) {
    let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}".replace(":classId", classId);
    fetch(url)
        .then(response => response.text())
        .then(data => document.getElementById('section_id').innerHTML = data)
        .catch(error => console.error("Error loading sections:", error));
}

function filter_attendance(event) {
    event.preventDefault();
    
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;

    if (class_id && section_id && month && year) {
        getDailyAttendance();
         // Update the underlying data for the student attendance report
         updateStudentAttendanceData(month, year, class_id, section_id);
    } else {
        toastr.error('Please select all fields!');
    }
}

function getDailyAttendance() {
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;
    
    let url = "{{ route('admin.daily_attendance.filter') }}";
    fetch(url + `?month=${month}&year=${year}&class_id=${class_id}&section_id=${section_id}`)
        .then(response => response.text())
        .then(data => {
            document.querySelector('.attendance_content').innerHTML = data;
        })
        .catch(error => {
            console.error("AJAX Error:", error);
            toastr.error('Failed to load attendance report.');
        });
}


function updateStudentAttendanceData(month, year, class_id, section_id) {
    let url = "{{ route('attendance.update_student_attendance_data') }}";
    let formData = new FormData();
    formData.append('month', month);
    formData.append('year', year);
    formData.append('class_id', class_id);
    formData.append('section_id', section_id);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Student attendance data updated successfully', data);
        // No need to update the UI as this file's data will be used when the report is rendered later.
    })
    .catch(error => {
        console.error("AJAX Error:", error);
        toastr.error('Failed to update student attendance data.');
    });
}
function exportStudentAttendancePDF() {
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;

    let url = `{{ route('attendance.export_student_pdf') }}?month=${month}&year=${year}&class_id=${class_id}&section_id=${section_id}`;
    window.location.href = url;
}

/*
function Export() {
    var element = document.getElementById('attendance_table');
    
    if (!element) {
        toastr.error('No data found! Please filter attendance first.');
        return;
    }

    html2canvas(element).then(canvas => {
        var imgData = canvas.toDataURL('image/png');
        var docDefinition = {
            pageOrientation: 'landscape',
            content: [{
                image: imgData,
                width: 750
            }]
        };
        pdfMake.createPdf(docDefinition).download("Student_Attendance_Report.pdf");
    }).catch(error => {
        console.error("Error generating PDF:", error);
        toastr.error('Failed to generate PDF.');
    });
}*/

function download_csv() {
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let class_id = document.getElementById('class_id').value;
    let section_id = document.getElementById('section_id').value;

    if (!month || !year || !class_id || !section_id) {
        toastr.error('Please select all the required filters.');
        return;
    }

    // ✅ Create and submit a form with real data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('admin.dailyAttendanceFilter_csv') }}"; // no placeholders here
    form.target = '_blank';

    // ✅ Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = "{{ csrf_token() }}";
    form.appendChild(csrfInput);

    // ✅ Append form values
    const fields = { month, year, class_id, section_id };
    for (let key in fields) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

</script>
