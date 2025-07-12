@extends('teacher.navigation')

@section('content')
<div class="container mt-4">
    <a href="{{ route('teacher.outbox') }}" class="btn btn-sm btn-secondary mb-3">← Back to Outbox</a>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            {{-- 📨 Subject --}}
            <h4 class="fw-bold mb-2">{{ $message->subject }}</h4>

            {{-- 👤 Recipients --}}
            @php
                use App\Models\User;

                $recipientText = 'Unknown';

                if ($message->to_user_id === 'all_parents') {
                    $recipientText = 'All Parents';
                } elseif ($message->to_user_id === 'all_students') {
                    $recipientText = 'All Students';
                } elseif ($message->to_user_id === 'all_classes') {
                    $recipientText = 'All Classes';
                } elseif (!empty($message->to_user_id)) {
                    $ids = explode(',', $message->to_user_id);
                    $names = User::whereIn('id', $ids)->pluck('name')->toArray();
                    $recipientText = implode(', ', $names);
                }
            @endphp

            <p class="text-muted mb-3">
                <strong>To:</strong> {{ $recipientText }}
            </p>

            {{-- 📝 Message Body --}}
            <p>{{ $message->body }}</p>

            {{-- 📎 Attachment --}}
            @if ($message->attachment_path)
                <div class="mt-3">
                    <a href="{{ route('teacher.messages.preview', $message->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    <a href="{{ route('teacher.messages.download', $message->id) }}" class="btn btn-outline-success btn-sm">Export</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
