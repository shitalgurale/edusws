        @extends('admin.navigation')

        @section('content')

        {{-- Start style for compose forms --}}
<style>
.radio-group {
    display: flex;
    gap: 40px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.radio-option {
    display: flex;
    align-items: center;
    font-size: 14px;
    font-weight: 500;
    color: #4A4E69;
    cursor: pointer;
    gap: 8px;
}

.radio-option input[type="radio"] {
    appearance: none;
    width: 16px;
    height: 16px;
    border: 2px solid #6B708A;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
}

.radio-option input[type="radio"]:checked {
    border-color: #00A3FF;
}

.radio-option input[type="radio"]:checked::before {
    content: '';
    display: block;
    width: 8px;
    height: 8px;
    background-color: #00A3FF;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
}


    .inport_btn {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 30px;
        padding: 12px 18px;
        border: 1px solid transparent;
        border-radius: 5px;
        background-color: #EFF2EF;
        font-size: 13px;
        font-weight: 500;
        color: #595d6f;
        transition: all 0.3s;
    }

    .inport_btn:hover {
        background-color: transparent;
        border-color: #DFDFE7;
        color: #6B708A;
    }

    .compose-form .form-group {
        margin-bottom: 15px;
    }

    .compose-form label {
        font-weight: 500;
        color: #595d6f;
    }

    .compose-form .form-control {
        font-size: 13px;
        font-weight: 500;
        border-radius: 5px;
        border: 1px solid #DFDFE7;
        background-color: #F9F9F9;
        color: #595d6f;
    }

    .compose-form .form-control:focus {
        border-color: #c9ccdb;
        box-shadow: none;
    }

    .btn-form {
        background-color: #00A3FF;  /* Bright blue */
        color: #fff;                /* White text */
        font-size: 14px;
        font-weight: 600;           /* Bold text */
        padding: 8px 20px;
        border-radius: 6px;
        border: none;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .btn-form:hover {
        background-color: #0090e7;  /* Slightly darker blue on hover */
        box-shadow: 0 4px 8px rgba(0, 163, 255, 0.3);
    }
    /* Notification textarea: Single-line look */
    .notification-textarea {
    height: 38px !important;  /* Match input height */
    overflow: hidden;
    resize: none;             /* Disable resize */
 
}
</style>
{{-- End style --}}




        <div class="container mt-4">
            <div class="card p-4 shadow rounded-2xl">
            <h2 class="text-xl font-semibold mb-4 d-flex align-items-center gap-2" style="color: #6c757d;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" style="color: inherit;" viewBox="0 0 24 24">
        <path d="M21 4H3c-1.1 0-1.99.9-1.99 2L1 18c0 1.1.89 2 1.99 2H21c1.1 0 1.99-.9 1.99-2L23 6c0-1.1-.89-2-1.99-2zM20 18H4V8h16v10z"/>
    </svg>  
    {{ get_phrase('Compose') }}
</h2>

<div class="radio-group">
    <label class="radio-option">
        <input type="radio" name="user_type" value="parent" onclick="selectTab('parent', this)">
        <span>{{ get_phrase('Parents') }}</span>
    </label>

    <label class="radio-option">
        <input type="radio" name="user_type" value="student" onclick="selectTab('student', this)">
        <span>{{ get_phrase('Students') }}</span>
    </label>

    <label class="radio-option">
        <input type="radio" name="user_type" value="both" onclick="selectTab('both', this)">
        <span>{{ get_phrase('Both') }}</span>
    </label>

    <label class="radio-option">
        <input type="radio" name="user_type" value="teacher" onclick="selectTab('teacher', this)">
        <span>{{ get_phrase('Teachers') }}</span>
    </label>
</div>


<!-- ✅ Parent Form -->
<form id="form-parent" class="compose-form" method="POST" action="{{ route('admin.send_mail') }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="hidden" name="recipient_type" value="parent">

    <!-- Select Class -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="class_id_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Class') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="class_id" id="class_id_parent" class="form-control eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value, 'parent')">
                <option value="">{{ get_phrase('Select Class') }}</option>
                <option value="all">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Select Section -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="section_id_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Section') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="section_id" id="section_id_parent" class="form-control eForm-select eChoice-multiple-with-remove" disabled onchange="loadStudents('parent'); loadParents('parent');">
                <option value="">{{ get_phrase('Select Section') }}</option>
            </select>
        </div>
    </div>

    <!-- Select Parents -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="parent_id_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Parents') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="parent_id" id="parent_id_parent" class="form-control eForm-select eChoice-multiple-with-remove" disabled>
                <option value="">{{ get_phrase('Select Parent') }}</option>
            </select>
        </div>
    </div>

    <!-- Subject -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="subject_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="text" class="form-control eForm-control" name="subject" id="subject_parent" required>
        </div>
    </div>

    <!-- Message -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="message_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Message') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="message" id="message_parent" class="form-control eForm-control" rows="4" required></textarea>
        </div>
    </div>

    <!-- Attachment -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="attachment_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Attachment') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="file" class="form-control eForm-control-file" name="attachment" id="attachment_parent" accept="application/pdf,image/*">
        </div>
    </div>

    <!-- Notification -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="notification_parent" class="col-sm-2 col-eForm-label">{{ get_phrase('Notification') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="notification" id="notification_parent" class="form-control notification-textarea" rows="1" readonly>
Dear Parent, Please check your inbox for an important update from the school.
            </textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row p-3">
        <div class="col-sm-10 offset-sm-2">
            <button type="button" onclick="sendFormWithNotification('form-parent', 'parents')" class="btn-form">
                {{ get_phrase('Send') }}
            </button>
        </div>
    </div>
</form>
<!-- ✅ Student Form -->
<form id="form-student" class="compose-form" method="POST" action="{{ route('admin.send_mail') }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="hidden" name="recipient_type" value="student">

    <!-- Select Class -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="class_id_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Class') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="class_id" id="class_id_student" class="form-control eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value, 'student')">
                <option value="">{{ get_phrase('Select Class') }}</option>
                <option value="all">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Select Section -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="section_id_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Section') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="section_id" id="section_id_student" class="form-control eForm-select eChoice-multiple-with-remove" disabled onchange="loadStudents('student')">
                <option value="">{{ get_phrase('Select Section') }}</option>
            </select>
        </div>
    </div>

    <!-- Select Student -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="student_id_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Students') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="student_id" id="student_id_student" class="form-control eForm-select eChoice-multiple-with-remove" disabled>
                <option value="">{{ get_phrase('Select Student') }}</option>
            </select>
        </div>
    </div>

    <!-- Subject -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="subject_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="text" class="form-control eForm-control" name="subject" id="subject_student" required>
        </div>
    </div>

    <!-- Message -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="message_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Message') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="message" id="message_student" class="form-control eForm-control" rows="4" required></textarea>
        </div>
    </div>

    <!-- Attachment -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="attachment_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Attachment') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="file" class="form-control eForm-control-file" name="attachment" id="attachment_student">
        </div>
    </div>

    <!-- Notification -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="notification_student" class="col-sm-2 col-eForm-label">{{ get_phrase('Notification') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="notification" id="notification_student" class="form-control notification-textarea" rows="1" readonly>
Dear Student, Please check your inbox for an important update from the school.
            </textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row p-3">
        <div class="col-sm-10 offset-sm-2">
            <button type="button" onclick="sendFormWithNotification('form-student', 'students')" class="btn-form">
                {{ get_phrase('Send') }}
            </button>
        </div>
    </div>
</form>


<!-- ✅ Both Form -->
<form id="form-both" class="compose-form" method="POST" action="{{ route('admin.send_mail') }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="hidden" name="recipient_type" value="both">

    <!-- Select Class -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="class_id_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Class') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="class_id" id="class_id_both" class="form-control eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value, 'both')">
                <option value="">{{ get_phrase('Select Class') }}</option>
                <option value="all">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Select Section -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="section_id_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Section') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="section_id" id="section_id_both" class="form-control eForm-select eChoice-multiple-with-remove" disabled onchange="loadStudents('both'); loadParents('both');">
                <option value="">{{ get_phrase('Select Section') }}</option>
            </select>
        </div>
    </div>

    <!-- Select Parents -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="parent_id_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Parents') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="parent_id" id="parent_id_both" class="form-control eForm-select eChoice-multiple-with-remove" disabled>
                <option value="">{{ get_phrase('Select Parent') }}</option>
            </select>
        </div>
    </div>

    <!-- Select Students -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="student_id_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Students') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="student_id" id="student_id_both" class="form-control eForm-select eChoice-multiple-with-remove" disabled>
                <option value="">{{ get_phrase('Select Student') }}</option>
            </select>
        </div>
    </div>

    <!-- Subject -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="subject_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="text" class="form-control eForm-control" name="subject" id="subject_both" required>
        </div>
    </div>

    <!-- Message -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="message_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Message') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="message" id="message_both" class="form-control eForm-control" rows="4" required></textarea>
        </div>
    </div>

    <!-- Attachment -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="attachment_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Attachment') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="file" class="form-control eForm-control-file" name="attachment" id="attachment_both">
        </div>
    </div>

    <!-- Notification -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="notification_both" class="col-sm-2 col-eForm-label">{{ get_phrase('Notification') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="notification" id="notification_both" class="form-control eForm-control notification-textarea" rows="1" readonly>
Dear Parent / Student, Please check your inbox for an important update from the school.
            </textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row p-3">
        <div class="col-sm-10 offset-sm-2">
            <button type="button" onclick="sendFormWithNotification('form-both', 'all')" class="btn-form">
                {{ get_phrase('Send') }}
            </button>
        </div>
    </div>
</form>

<!-- ✅ Teacher Form -->
<form id="form-teacher" class="compose-form" method="POST" action="{{ route('admin.send_mail') }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <input type="hidden" name="recipient_type" value="teacher">

    <!-- Select Class -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="class_id_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Class') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="class_id" id="class_id_teacher" class="form-control eForm-select eChoice-multiple-with-remove" onchange="classWiseSection(this.value, 'teacher')">
                <option value="">{{ get_phrase('Select Class') }}</option>
                <option value="all">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Select Section -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="section_id_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Section') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="section_id" id="section_id_teacher" class="form-control eForm-select eChoice-multiple-with-remove" disabled onchange="loadTeachers('teacher')">
                <option value="">{{ get_phrase('Select Section') }}</option>
            </select>
        </div>
    </div>

    <!-- Select Teachers -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="teacher_id_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Select Teachers') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <select name="teacher_id" id="teacher_id_teacher" class="form-control eForm-select eChoice-multiple-with-remove" disabled>
                <option value="">{{ get_phrase('Select Teacher') }}</option>
            </select>
        </div>
    </div>

    <!-- Subject -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="subject_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="text" class="form-control eForm-control" name="subject" id="subject_teacher" required>
        </div>
    </div>

    <!-- Message -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="message_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Message') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="message" id="message_teacher" class="form-control eForm-control" rows="4" required></textarea>
        </div>
    </div>

    <!-- Attachment -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="attachment_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Attachment') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <input type="file" class="form-control eForm-control-file" name="attachment" id="attachment_teacher">
        </div>
    </div>

    <!-- Notification -->
    <div class="form-group row fmb-14 justify-content-between align-items-center">
        <label for="notification_teacher" class="col-sm-2 col-eForm-label">{{ get_phrase('Notification') }}</label>
        <div class="col-sm-10 col-md-9 col-lg-10">
            <textarea name="notification" id="notification_teacher" class="form-control eForm-control notification-textarea" rows="1" readonly>
Dear Teacher, Please check your inbox for an important update from the school.
            </textarea>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row p-3">
        <div class="col-sm-10 offset-sm-2">
            <button type="button" onclick="sendFormWithNotification('form-teacher', 'teachers')" class="btn-form">
                {{ get_phrase('Send') }}
            </button>
        </div>
    </div>
</form>

        @endsection

        {{-- Include the script directly here to ensure it loads --}}
        <script>
            console.log("✅ Script Loaded");
            function selectTab(tab, element) {
    document.querySelectorAll('.compose-form').forEach(form => form.style.display = 'none');
    document.getElementById('form-' + tab).style.display = 'block';
}

// Hide all forms on load, no selection
window.onload = function () {
    document.querySelectorAll('.compose-form').forEach(form => form.style.display = 'none');
    document.querySelectorAll('input[name="user_type"]').forEach(radio => radio.checked = false);
};



    $(document).ready(function () {
        $(".compose-form .form-control").each(function () {
            if ($(this).is('select')) {
                $(this).select2();
            }
        });
    });


// Function to show the correct form based on the selected tab
function showForm(type) {
    console.log("🔍 showForm called with:", type);
    document.querySelectorAll('.compose-form').forEach(form => form.style.display = 'none');

    const formToShow = document.getElementById('form-' + type);
    if (formToShow) {
        formToShow.style.display = 'block';
    }
}


function classWiseSection(classId, formPrefix) {
    const sectionSelect = document.getElementById(`section_id_${formPrefix}`);
    if (!sectionSelect) return;

    sectionSelect.innerHTML = "";
    sectionSelect.disabled = false;

    // Add default option
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.text = "Select Section";
    sectionSelect.appendChild(defaultOption);

    // If "All Classes" selected, load all school sections
    if (classId === "all") {
        fetch(`/admin/getAllSectionsBySchool`)
            .then(response => response.json())
            .then(data => {
                // Add "All Sections" option
                const allOption = document.createElement("option");
                allOption.value = "all";
                allOption.text = "All Sections";
                sectionSelect.appendChild(allOption);

                data.sections.forEach(section => {
                    const opt = document.createElement("option");
                    opt.value = section.id;
                    opt.text = section.name;
                    sectionSelect.appendChild(opt);
                });

                loadStudents(formPrefix, true);
                loadParents(formPrefix, true);
            })
            .catch(error => console.error("Error loading sections:", error));
    } else {
        // Load sections specific to selected class
        fetch(`/admin/getSectionsByClass/${classId}`)
            .then(response => response.json())
            .then(data => {
                // Add "All Sections" option
                const allOption = document.createElement("option");
                allOption.value = "all";
                allOption.text = "All Sections";
                sectionSelect.appendChild(allOption);

                data.sections.forEach(section => {
                    const opt = document.createElement("option");
                    opt.value = section.id;
                    opt.text = section.name;
                    sectionSelect.appendChild(opt);
                });

                // Clear student & parent dropdowns
                const studentSelect = document.getElementById(`student_id_${formPrefix}`);
                if (studentSelect) {
                    studentSelect.innerHTML = "<option value=''>Select Student</option>";
                    studentSelect.disabled = true;
                }

                const parentSelect = document.getElementById(`parent_id_${formPrefix}`);
                if (parentSelect) {
                    parentSelect.innerHTML = "<option value=''>Select Parent</option>";
                    parentSelect.disabled = true;
                }
            })
            .catch(err => console.error("Error fetching class-wise sections:", err));
    }
}

function loadStudents(formPrefix, loadAll = false) {
    const studentSelect = document.getElementById(`student_id_${formPrefix}`);
    if (!studentSelect) {
        console.warn(`⛔ Skipping loadStudents: student_id_${formPrefix} not found`);
        return;
    }

    studentSelect.disabled = false;
    studentSelect.innerHTML = "<option value=''>Select Student</option>";

    const allOption = document.createElement("option");
    allOption.value = "all";
    allOption.text = "All Students";
    studentSelect.appendChild(allOption);

    let url;
    if (loadAll) {
        url = `/admin/getAllStudentsBySchool`;
    } else {
        const classId = document.getElementById(`class_id_${formPrefix}`).value;
        const sectionId = document.getElementById(`section_id_${formPrefix}`).value;

        url = `/admin/getStudentsByClassAndSection/${classId}/${sectionId}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.students.forEach(student => {
                const opt = document.createElement("option");
                opt.value = student.id;
                opt.text = student.name;
                studentSelect.appendChild(opt);
            });
        });
}

function loadParents(formPrefix, loadAll = false) {
    const parentSelect = document.getElementById(`parent_id_${formPrefix}`);
    if (!parentSelect) {
        console.warn(`❌ parent_id_${formPrefix} element not found`);
        return;
    }

    parentSelect.disabled = false;
    parentSelect.innerHTML = "<option value=''>Select Parent</option>";

    const allOption = document.createElement("option");
    allOption.value = "all";
    allOption.text = "All Parents";
    parentSelect.appendChild(allOption);

    let url;
    if (loadAll) {
        url = `/admin/getAllParentsBySchool`;
    } else {
        const classId = document.getElementById(`class_id_${formPrefix}`).value;
        const sectionId = document.getElementById(`section_id_${formPrefix}`).value;

        url = `/admin/getParentsByClassAndSection/${classId}/${sectionId}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.parents.forEach(parent => {
                const opt = document.createElement("option");
                opt.value = parent.id;
                opt.text = parent.name;
                parentSelect.appendChild(opt);
            });
        });
}

function loadTeachers(formPrefix, loadAll = false) {
    const teacherSelect = document.getElementById(`teacher_id_${formPrefix}`);
    if (!teacherSelect) return;

    teacherSelect.disabled = false;
    teacherSelect.innerHTML = "<option value=''>Select Teacher</option>";

    const allOption = document.createElement("option");
    allOption.value = "all";
    allOption.text = "All Teachers";
    teacherSelect.appendChild(allOption);

    let url;
    if (loadAll) {
        url = `/admin/getAllTeachersBySchool`;
    } else {
        const classId = document.getElementById(`class_id_${formPrefix}`).value;
        const sectionId = document.getElementById(`section_id_${formPrefix}`).value;
        url = `/admin/getTeachersByClassAndSection/${classId}/${sectionId}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.teachers.forEach(teacher => {
                const opt = document.createElement("option");
                opt.value = teacher.id;
                opt.text = teacher.name;
                teacherSelect.appendChild(opt);
            });
        });
}


function sendFormWithNotification(formId, targetType) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const notificationMessage = form.querySelector('textarea[name="notification"]').value;

    fetch("{{ route('admin.send_mail') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(async (response) => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            const text = await response.text();
            console.warn("⚠️ Response is not JSON:", text);
            return { success: true }; // fallback for non-JSON response
        }
    })
    .then(res => {
        console.log("✅ Message sent:", res);
        sendPushNotification(notificationMessage, targetType, form);
    })
    .catch(error => {
        console.error("❌ Error sending message:", error);
    });
}

function sendPushNotification(message, targetType, form) {
    let payload = {
        message: message,
        target: targetType,
    };

    const classId = form.querySelector('[name="class_id"]')?.value;
    const sectionId = form.querySelector('[name="section_id"]')?.value;

    if (targetType === 'class' || targetType === 'section') {
        payload.class_id = classId;
    }

    if (targetType === 'section') {
        payload.section_id = sectionId;
    }

    fetch("{{ route('admin.broadcast.notification') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            console.log("📣 Notification sent:", res.summary);
        } else {
            alert("⚠️ Failed to send notification.");
        }
    })
    .catch(err => {
        console.error("❌ Notification error:", err);
    });
}

</script>