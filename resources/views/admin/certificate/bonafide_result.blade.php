@extends('admin.navigation')
@section('content')
<div class="container">
    <h4>Bonafide Certificate Generated Successfully</h4>
    <a href="{{ route('admin.certificate.viewBonafide', $student_id) }}" target="_blank" class="btn btn-info">View Certificate</a>
    <a href="{{ route('admin.certificate.downloadBonafide', $student_id) }}" class="btn btn-success">Download PDF</a>
</div>
@endsection
