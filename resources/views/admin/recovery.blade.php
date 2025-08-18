@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">🔒 Recover Admin URL</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Forgot your admin URL? If you set a security question when creating your event, 
                    you can recover access by providing the event details and your security answer.
                </p>

                @if ($errors->has('rate_limit'))
                    <div class="alert alert-danger">
                        <strong>Rate Limited:</strong> {{ $errors->first('rate_limit') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.recovery.submit') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Enter the exact event title"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('event')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Event Date</label>
                        <input type="date" 
                               class="form-control @error('date') is-invalid @enderror" 
                               id="date" 
                               name="date" 
                               value="{{ old('date') }}"
                               required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="security_answer" class="form-label">Security Answer</label>
                        <input type="text" 
                               class="form-control @error('security_answer') is-invalid @enderror" 
                               id="security_answer" 
                               name="security_answer" 
                               value="{{ old('security_answer') }}" 
                               placeholder="Enter your security answer"
                               required>
                        @error('security_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Enter the answer to the security question you set when creating the event.
                        </small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            🔍 Recover Admin URL
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <small class="text-muted">
                        <strong>Security Note:</strong> You have 3 recovery attempts per hour. 
                        All recovery attempts are logged for security purposes.
                    </small>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('events.new') }}" class="btn btn-outline-secondary btn-sm">
                        ← Back to Create Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
