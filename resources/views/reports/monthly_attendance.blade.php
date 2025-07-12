   
@section('content')
<h3>Attendance Report for {{ $student->name }}</h3>
<p>Month: {{ $month }}/{{ $year }}</p>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendances as $a)
            <tr>
                <td>{{ \Carbon\Carbon::parse($a->timestamp)->format('d M Y') }}</td>
                <td>{{ $a->stu_intime ? 'Present' : 'Absent' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
