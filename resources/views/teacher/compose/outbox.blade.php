@extends('teacher.navigation')

@section('content')
<style>
    .outbox-row {
        border-bottom: 1px solid #dee2e6;
        padding: 6px 16px;
        transition: background-color 0.15s ease-in-out;
        font-size: 14px;
    }
    .outbox-row:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }
    .outbox-subject {
        font-weight: 500;
        color: #212529;
    }
    .outbox-recipients {
        color: #6c757d;
        margin-left: 6px;
        font-weight: 400;
    }
    .outbox-date {
        font-size: 13px;
        color: #6c757d;
        white-space: nowrap;
    }
</style>

<div class="container-fluid mt-4 px-0">
    <h2 class="text-xl font-semibold mb-4 d-flex align-items-center gap-2" style="color: #6c757d;">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" style="color: inherit;">
        <!-- Moved Upward Arrow (translated upward by 2 units) -->
        <g transform="translate(0, -4)">
            <path d="M13 16V9.83l2.59 2.58L17 11l-5-5-5 5 1.41 1.41L11 9.83V16h2z"/>
        </g>
        <!-- Envelope body and flap -->
        <path d="M20 8v10H4V8H2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8h-2z"/>
        <path d="M4 8l8 5 8-5H4z"/>
        </svg>
        {{ get_phrase('Outbox') }}
    </h2>

    @if ($messageGroups->isEmpty())
        <div class="alert alert-info">No messages sent yet.</div>
    @else
        @foreach ($messageGroups as $group)
            <a href="{{ route('teacher.message.details', ['id' => $group->message_id]) }}"
               class="d-flex justify-content-between align-items-center outbox-row text-decoration-none">

                <div class="text-truncate" style="max-width: 80%;">
                    <span class="outbox-subject">
                        {{ $group->receiver_label }} {{-- Show class name or count of students/parents --}}
                    </span>
                </div>

                <div class="outbox-date">
                    {{ \Carbon\Carbon::parse($group->created_at)->format('M d') }}
                </div>
            </a>
        @endforeach
    @endif
</div>
@endsection
