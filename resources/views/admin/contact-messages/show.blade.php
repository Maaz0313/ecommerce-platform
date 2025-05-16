@extends('admin.layouts.app')

@section('title', 'View Contact Message')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Contact Message</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.contact-messages.index') }}">Contact Messages</a></li>
            <li class="breadcrumb-item active">View Message</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-envelope me-1"></i>
                    Message Details
                </div>
                <div>
                    <span class="badge {{ $message->is_read ? 'bg-success' : 'bg-primary' }}">
                        {{ $message->is_read ? 'Read' : 'Unread' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>From</h5>
                        <p>{{ $message->name }} &lt;<a href="mailto:{{ $message->email }}">{{ $message->email }}</a>&gt;</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5>Date</h5>
                        <p>{{ $message->created_at->format('F d, Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Subject</h5>
                    <p class="lead">{{ $message->subject }}</p>
                </div>

                <div class="mb-4">
                    <h5>Message</h5>
                    <div class="card">
                        <div class="card-body bg-light">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Messages
                    </a>
                    
                    <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Reply
                    </a>
                    
                    @if ($message->is_read)
                        <form action="{{ route('admin.contact-messages.mark-unread', $message->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-envelope"></i> Mark as Unread
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.contact-messages.mark-read', $message->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-envelope-open"></i> Mark as Read
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this message?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
