@extends('admin.navigation')
@section('content')
<div class="container">
    <h4>Select Student for Certificate</h4>

    {{-- 🔷 Class, Section, and Student Dropdowns --}}
    <div class="row">
        <div class="col-md-4">
            <label>Class</label>
            <select name="class_id" id="class_id" class="form-control" required onchange="fetchSections()">
                <option value="">Select Class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>Section</label>
            <select name="section_id" id="section_id" class="form-control" required onchange="fetchStudents()">
                <option value="">Select Section</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>Student</label>
            <select name="student_id" id="student_id" class="form-control" required>
                <option value="">Select Student</option>
            </select>
        </div>
    </div>

    {{-- ✅ Bonafide Certificate Form --}}
    <form method="POST" action="{{ route('admin.certificate.generateBonafideUI') }}">
        @csrf
        <input type="hidden" name="student_id" id="bonafide_student_id">
        <button class="btn btn-primary mt-3" type="submit" onclick="prepareBonafideForm()">Generate Bonafide Certificate</button>
    </form>

    {{-- ✅ Transfer Certificate Form --}}
    <form method="POST" action="{{ route('admin.certificate.tc') }}">
        @csrf
        <input type="hidden" name="student_id" id="tc_student_id">
        <button class="btn btn-warning mt-2" type="submit" onclick="prepareTCForm()">Generate Transfer Certificate</button>
    </form>
</div>

{{-- ✅ JavaScript --}}
<script>
    function fetchSections() {
        const classId = document.getElementById('class_id').value;
        if (!classId) return;

        fetch(`/admin/certificate/getSectionsByClass/${classId}`)
            .then(res => res.json())
            .then(data => {
                const sectionSelect = document.getElementById('section_id');
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                data.sections.forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
                });
                document.getElementById('student_id').innerHTML = '<option value="">Select Student</option>';
            });
    }

    function fetchStudents() {
        const classId = document.getElementById('class_id').value;
        const sectionId = document.getElementById('section_id').value;
        if (!classId || !sectionId) return;

        fetch(`/admin/certificate/getStudentsByClassAndSection/${classId}/${sectionId}`)
            .then(res => res.json())
            .then(data => {
                const studentSelect = document.getElementById('student_id');
                studentSelect.innerHTML = '<option value="">Select Student</option>';
                data.students.forEach(student => {
                    studentSelect.innerHTML += `<option value="${student.id}">${student.name}</option>`;
                });
            });
    }

    function prepareBonafideForm() {
        document.getElementById('bonafide_student_id').value = document.getElementById('student_id').value;
    }

    function prepareTCForm() {
        document.getElementById('tc_student_id').value = document.getElementById('student_id').value;
    }
</script>
@endsection
