@extends('layouts.app')

@section('content')
<h1>
    Admin: {{ $event->title }}
    <small class="text-muted">
        //
        {{ $event->date->format('l, F j, Y') }}
    </small>
</h1>

<div class="alert alert-info">
    <strong>Event URL:</strong> 
    <a href="{{ route('events.show', $event->toParam()) }}" target="_blank">
        {{ url(route('events.show', $event->toParam())) }}
    </a>
    <button class="btn btn-sm btn-outline-secondary clipboard ms-2" data-clipboard-text="{{ url(route('events.show', $event->toParam())) }}">
        Copy
    </button>
</div>

<div class="mb-3">
    <a href="{{ route('events.admin.edit', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}" class="btn btn-primary">
        Edit Event
    </a>
    
    <form method="POST" action="{{ route('events.admin.toggle_publish', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-{{ $event->published ? 'warning' : 'success' }}">
            {{ $event->published ? 'Unpublish' : 'Publish' }} Event
        </button>
    </form>
    
    <form method="POST" action="{{ route('events.admin.destroy', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Event</button>
    </form>
</div>

<div class="trix-content">{!! $event->body !!}</div>

<br>

<h2>RSVPs ({{ $rsvps->count() }})</h2>

@if($rsvps->count() > 0)
    @foreach(\App\Models\Rsvp::RESPONSES as $response)
        @php
            $responseRsvps = $rsvps->where('response', $response);
        @endphp

        @if($responseRsvps->count() > 0)
            <h4>{{ ucfirst($response) }} ({{ $responseRsvps->count() }})</h4>
            <ul>
                @foreach($responseRsvps as $rsvp)
                    <li>{{ $rsvp->name }} - <small class="text-muted">{{ $rsvp->created_at->format('M j, Y g:i A') }}</small></li>
                @endforeach
            </ul>
        @endif
    @endforeach
@else
    <p class="text-muted">No RSVPs yet.</p>
@endif
@endsection
