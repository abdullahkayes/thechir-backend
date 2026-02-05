@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Message Details</h1>
            <a href="{{ route('admin.messages.index') }}" class="btn btn-primary">
                <i data-feather="arrow-left"></i> Back to Messages
            </a>
        </div>

        <!-- Message Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Message from {{ $message->name }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Name:</strong> {{ $message->name }}
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong> {{ $message->email }}
                    </div>
                </div>
                
                @if($message->phone)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong> {{ $message->phone }}
                    </div>
                </div>
                @endif

                @if($message->subject)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong>Subject:</strong> {{ $message->subject }}
                    </div>
                </div>
                @endif

                @if($message->address)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong>Address:</strong> {{ $message->address }}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong>Date:</strong> {{ $message->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong>Message:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Status:</strong>
                        @if($message->read_at)
                            <span class="badge badge-success">Read</span>
                        @else
                            <span class="badge badge-primary">Unread</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between">
            <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">
                    <i data-feather="trash-2"></i> Delete Message
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Initialize Feather icons
    feather.replace()
</script>
@endsection
