@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">✅ Admin URL Recovered Successfully!</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <strong>Great news!</strong> We found your event and verified your security answer.
                </div>

                <h5>Event Details:</h5>
                <ul class="list-unstyled">
                    <li><strong>Event:</strong> {{ $event->title }}</li>
                    <li><strong>Date:</strong> {{ $event->date->format('F j, Y') }}</li>
                    <li><strong>Security Question:</strong> {{ $event->security_question }}</li>
                </ul>

                <hr>

                <h5>Your Admin URL:</h5>
                <div class="input-group mb-3">
                    <input type="text" 
                           class="form-control" 
                           id="admin-url" 
                           value="{{ $adminUrl }}" 
                           readonly>
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            id="copy-admin-url"
                            onclick="copyToClipboard('admin-url')">
                        📋 Copy
                    </button>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <a href="{{ $adminUrl }}" class="btn btn-primary btn-lg">
                        🚀 Go to Admin Panel
                    </a>
                </div>

                <div class="alert alert-warning">
                    <strong>Important:</strong> 
                    <ul class="mb-0">
                        <li>Save this URL in a secure location (bookmark it, save in password manager, etc.)</li>
                        <li>This URL gives full admin access to your event</li>
                        <li>Don't share this URL publicly - only share the public RSVP URL with guests</li>
                    </ul>
                </div>

                <hr>

                <h6>Public RSVP URL (share this with guests):</h6>
                <div class="input-group mb-3">
                    <input type="text" 
                           class="form-control" 
                           id="public-url" 
                           value="{{ route('events.show', $event->toParam()) }}" 
                           readonly>
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            id="copy-public-url"
                            onclick="copyToClipboard('public-url')">
                        📋 Copy
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('events.new') }}" class="btn btn-outline-secondary">
                        Create Another Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Update button text temporarily
        const button = document.getElementById('copy-' + elementId.replace('-url', '') + '-url');
        const originalText = button.innerHTML;
        button.innerHTML = '✅ Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy URL. Please select and copy manually.');
    }
}
</script>
@endsection
