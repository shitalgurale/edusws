@extends('admin.navigation')
@section('content')
<div class="container">
    <h4>Transfer Certificate Generated Successfully</h4>
    <a href="{{ route('admin.certificate.viewTC', $student_id) }}" target="_blank" class="btn btn-info">View Certificate</a>
    <a href="{{ route('admin.certificate.downloadTC', $student_id) }}" class="btn btn-success">Download PDF</a>
</div>
@endsection
