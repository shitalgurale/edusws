@extends('student.navigation')

@section('content')
<style>
    .inbox-row {
        border-bottom: 1px solid #dee2e6;
        padding: 6px 16px;
        transition: background-color 0.15s ease-in-out;
        font-size: 14px;
    }
    .inbox-row:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }
    .inbox-subject {
        font-weight: 500;
        color: #212529;
    }
    .inbox-from {
        color: #6c757d;
        margin-left: 6px;
        font-weight: 400;
    }
    .inbox-date {
        font-size: 13px;
        color: #6c757d;
        white-space: nowrap;
    }
</style>

<div class="container-fluid mt-4 px-0">
    <h2 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 22px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="text-dark" viewBox="0 0 24 24">
            <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1.2l-10 6-10-6V6zm0 3.8v8.2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9.8l-10 6-10-6z"/>
        </svg>
        Inbox
    </h2>

    @if ($messages->isEmpty())
        <div class="alert alert-info">No messages yet.</div>
    @else
        @foreach ($messages as $message)
            <a href="{{ route(auth()->user()->role_id == 7 ? 'student.message.details' : 'student.message.details', $message->id) }}"
               class="d-flex justify-content-between align-items-center inbox-row text-decoration-none">
                
                <div class="text-truncate" style="max-width: 80%;">
                    <span class="inbox-subject">{{ $message->subject }}</span>
                    <span class="inbox-from">— From: {{ user_name($message->from_user_id) }}</span>
                </div>

                <div class="inbox-date">
                    {{ \Carbon\Carbon::parse($message->created_at)->format('M d') }}
                </div>
            </a>
        @endforeach
    @endif
</div>
@endsection
