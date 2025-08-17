@extends('layouts.app')

@section('content')
<h1 class="text-center">
    Make it <i class="text-success">easy</i> for your guests to <i class="text-success">RSVP</i>
</h1>

<br>

<form method="POST" action="{{ route('events.create') }}">
    @csrf
    
    <div class="mb-3">
        <label for="title" class="form-label">What are you planning?</label>
        <input type="text" 
               class="form-control @error('title') is-invalid @enderror" 
               id="title" 
               name="title" 
               value="{{ old('title') }}" 
               placeholder="{{ $placeholders['title'] }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">When is this happening?</label>
        <input type="date" 
               class="form-control @error('date') is-invalid @enderror" 
               id="date" 
               name="date" 
               value="{{ old('date') }}">
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <div class="mb-3">
        <label for="body" class="form-label">More details (optional):</label>
        <input id="body" type="hidden" name="body" value="{{ old('body') }}">
        <trix-editor input="body" placeholder="{{ $placeholders['body'] }}"></trix-editor>
        @error('body')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <button type="submit" class="btn btn-primary me-2">Create your event, for free!</button>
</form>
@endsection
