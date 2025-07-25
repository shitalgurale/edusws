
@extends('student.navigation')

@section('content')
<div class="container mt-4">
    <a href="{{ route('student.inbox') }}" class="btn btn-sm btn-secondary mb-3">← Back to Inbox</a>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="fw-bold">{{ $message->subject }}</h4>
            <p class="text-muted mb-2">From: {{ user_name($message->from_user_id) }}</p>
            <p>{{ $message->body }}</p>

            @if ($message->attachment_path)
                <div class="mt-3">
                    <a href="{{ route('student.messages.preview', $message->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    <a href="{{ route('student.messages.download', $message->id) }}" class="btn btn-outline-success btn-sm">Export</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
