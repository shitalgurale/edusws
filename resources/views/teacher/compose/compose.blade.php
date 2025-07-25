@extends('teacher.navigation')

@section('content')

<style>
    .form-select {
        background-color: #f7fafc;
        border: 1px solid #d1d5db;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 1rem;
        line-height: 1.5;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-select:focus {
        border-color: #4d9bcd;
        box-shadow: 0 0 0 0.2rem rgba(77, 155, 205, 0.25);
    }

    /* Custom Button Styles */
    .btn-group .btn {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
        border-radius: 0.375rem;
        transition: background-color 0.2s ease;
    }

    /* Hover Effects for Dropdown */
    .form-select:hover {
        border-color: #4d9bcd;
    }

    /* Card and Form Styling */
    .card {
        background-color: #ffffff;
        border-radius: 0.375rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Form Label Styling */
    .form-group label {
        font-size: 1rem;
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
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

.btn-form {
    background-color: #00A3FF;  /* Bright blue */
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 6px;
    border: none;
    transition: background-color 0.3s, box-shadow 0.3s;
}

.btn-form:hover {
    background-color: #0090e7;
    box-shadow: 0 4px 8px rgba(0, 163, 255, 0.3);
}

.notification-textarea {
    height: 38px !important;
    overflow: hidden;
    resize: none;
}

</style>
<div class="container mt-4">
    <div class="card p-4 shadow rounded-2xl">
    <h2 class="text-xl font-semibold mb-4 d-flex align-items-center gap-2" style="color: #6c757d;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" style="color: inherit;" viewBox="0 0 24 24">
        <path d="M21 4H3c-1.1 0-1.99.9-1.99 2L1 18c0 1.1.89 2 1.99 2H21c1.1 0 1.99-.9 1.99-2L23 6c0-1.1-.89-2-1.99-2zM20 18H4V8h16v10z"/>
    </svg>  
    {{ get_phrase('Compose') }}
</h2>
<!--Tab Buttons-->
<div class="radio-group">
    <label class="radio-option">
        <input type="radio" name="user_type" value="parent" onclick="showForm('parent')">
        <span>{{ get_phrase('Parents') }}</span>
    </label>

    <label class="radio-option">
        <input type="radio" name="user_type" value="student" onclick="showForm('student')">
        <span>{{ get_phrase('Students') }}</span>
    </label>

    <label class="radio-option">
        <input type="radio" name="user_type" value="both" onclick="showForm('both')">
        <span>{{ get_phrase('Both') }}</span>
    </label>
</div>


        <!-- ✅ Parent Form -->
        <form id="form-parent" class="compose-form" method="POST" action="{{ route('teacher.send_mail') }}" enctype="multipart/form-data" style="display: none;">
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
            <input type="file" class="form-control eForm-control-file" name="attachment" id="attachment_parent">
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
<form id="form-student" class="compose-form" method="POST" action="{{ route('teacher.send_mail') }}" enctype="multipart/form-data" style="display: none;">
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
<form id="form-both" class="compose-form" method="POST" action="{{ route('teacher.send_mail') }}" enctype="multipart/form-data" style="display: none;">
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
            <textarea name="notification" id="notification_both" class="form-control notification-textarea" rows="1" readonly>
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





@endsection

{{-- Include the script directly here to ensure it loads --}}
<script>
    console.log("✅ Script Loaded");

    window.onload = function () {
    document.querySelectorAll('.compose-form').forEach(form => form.style.display = 'none');
    document.querySelectorAll('input[name="user_type"]').forEach(radio => radio.checked = false);
};


$(document).ready(function () {
    $(".eChoice-multiple-with-remove").select2();
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
fetch(`/teacher/getAllSectionsBySchool`)
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
fetch(`/teacher/getSectionsByClass/${classId}`)
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
        url = `/teacher/getAllParentsBySchool`;
        console.log(`📡 Fetching all parents from: ${url}`);
    } else {
        const classId = document.getElementById(`class_id_${formPrefix}`).value;
        const sectionId = document.getElementById(`section_id_${formPrefix}`).value;
        url = `/teacher/getParentsByClassAndSection/${classId}/${sectionId}`;
        console.log(`📡 Fetching parents by class/section from: ${url}`);
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log("✅ Parents loaded:", data.parents);
            data.parents.forEach(parent => {
                const opt = document.createElement("option");
                opt.value = parent.id;
                opt.text = parent.name;
                parentSelect.appendChild(opt);
            });
        })
        .catch(error => console.error("❌ Error loading parents:", error));
}
function loadStudents(formPrefix, loadAll = false) {
    const studentSelect = document.getElementById(`student_id_${formPrefix}`);
    if (!studentSelect) {
        console.warn(`⛔ student_id_${formPrefix} not found`);
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
        url = `/teacher/getAllStudentsBySchool`;
        console.log(`📡 Fetching all students from: ${url}`);
    } else {
        const classId = document.getElementById(`class_id_${formPrefix}`).value;
        const sectionId = document.getElementById(`section_id_${formPrefix}`).value;
        url = `/teacher/getStudentsByClassAndSection/${classId}/${sectionId}`;
        console.log(`📡 Fetching students by class/section from: ${url}`);
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log("✅ Students loaded:", data.students);
            data.students.forEach(student => {
                const opt = document.createElement("option");
                opt.value = student.id;
                opt.text = student.name;
                studentSelect.appendChild(opt);
            });
        })
        .catch(error => console.error("❌ Error loading students:", error));
}

function sendFormWithNotification(formId, targetType) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const notificationMessage = form.querySelector('textarea[name="notification"]').value;

    // ✅ Submit the actual form (message)
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to send message');
        return res.json().catch(() => res); // allow non-JSON response
    })
    .then(res => {
        console.log("✅ Message sent");
        // ✅ After message, send notification
        sendPushNotification(notificationMessage, targetType, formData);
    })
    .catch(err => {
        console.error("❌ Error sending message:", err);
    });
}

function sendPushNotification(message, targetType, formData) {
    const payload = {
        message: message,
        target: (targetType === 'both') ? 'all' : targetType,
    };

    if (targetType !== 'teachers' && targetType !== 'all') {
        payload.class_id = formData.get('class_id');
        payload.section_id = formData.get('section_id');
    }

    fetch("{{ route('teacher.broadcast.notification') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const contentType = res.headers.get("content-type");
        const text = await res.text();
        console.warn("⚠️ Response is not JSON:", text);

        if (!res.ok) throw new Error("Server error: " + res.status);

        if (contentType && contentType.includes("application/json")) {
            return JSON.parse(text);
        } else {
            throw new Error("Invalid JSON response");
        }
    })
    .then(res => {
        if (res.success) {
            console.log("📣 Push Notification Sent:", res.summary);
        } else {
            alert("⚠️ Failed to send notification.");
        }
    })
    .catch(err => {
        console.error("❌ Error sending notification:", err);
    });
}

</script>