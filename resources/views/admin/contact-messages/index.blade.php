@extends('admin.layouts.app')

@section('title', 'Contact Messages')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Contact Messages</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Contact Messages</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-envelope me-1"></i>
                All Messages
            </div>
            <div class="card-body">
                @if (count($messages) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($messages as $message)
                                    <tr class="{{ $message->is_read ? '' : 'table-primary' }}">
                                        <td>{{ $message->id }}</td>
                                        <td>{{ $message->name }}</td>
                                        <td>
                                            <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                        </td>
                                        <td>{{ Str::limit($message->subject, 30) }}</td>
                                        <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $message->is_read ? 'bg-success' : 'bg-primary' }}">
                                                {{ $message->is_read ? 'Read' : 'Unread' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.contact-messages.show', $message->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                                @if ($message->is_read)
                                                    <form action="{{ route('admin.contact-messages.mark-unread', $message->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-envelope"></i> Mark Unread
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.contact-messages.mark-read', $message->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-envelope-open"></i> Mark Read
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('admin.contact-messages.destroy', $message->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $messages->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No contact messages found.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
