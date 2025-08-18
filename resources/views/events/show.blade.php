@extends('layouts.app')

@section('content')
<h1>
    {{ $event->title }}

    <small class="text-muted">
        //
        {{ $event->date->format('l, F j, Y') }}
        @if($event->start_time)
            at {{ date('g:i A', strtotime($event->start_time)) }}
            @if($event->end_time)
                - {{ date('g:i A', strtotime($event->end_time)) }}
            @endif
        @endif
    </small>

    @if(config('app.env') === 'local')
        <small><a href="{{ route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}">admin</a></small>
    @endif
</h1>

@if($event->start_time)
    <div class="mb-3">
        <a href="{{ $googleCalendarUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
            📅 Add to Google Calendar
        </a>
    </div>
@endif

<div class="trix-content">{!! $event->body !!}</div>

<br>

<h2>Who's coming?</h2>

@unless($event->show_rsvp_names)
    <small>Guest names are hidden to other guests.</small>
@endunless

<div class="rsvp-section">
    <form method="POST" action="{{ route('rsvps.create', $event->toParam()) }}" class="{{ $responded ? 'd-none' : '' }}" id="rsvp-form">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Your name:</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        @if($event->customFields->count() > 0)
            <div class="custom-fields-section mb-3">
                <h5>Additional Information</h5>
                @foreach($event->customFields as $field)
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $field->name }}
                            @if($field->required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        
                        @php
                            $fieldName = "custom_field_{$field->id}";
                            $oldValue = old($fieldName);
                        @endphp

                        @if($field->type === 'text')
                            <input type="text" name="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" value="{{ $oldValue }}">
                        
                        @elseif($field->type === 'number')
                            <input type="number" name="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" value="{{ $oldValue }}">
                        
                        @elseif($field->type === 'textarea')
                            <textarea name="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror" rows="3">{{ $oldValue }}</textarea>
                        
                        @elseif($field->type === 'select')
                            <select name="{{ $fieldName }}" class="form-control @error($fieldName) is-invalid @enderror">
                                <option value="">Choose an option...</option>
                                @foreach($field->options_list as $option)
                                    <option value="{{ $option }}" {{ $oldValue === $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        
                        @elseif($field->type === 'radio')
                            @foreach($field->options_list as $option)
                                <div class="form-check">
                                    <input type="radio" name="{{ $fieldName }}" value="{{ $option }}" class="form-check-input" id="{{ $fieldName }}_{{ $loop->index }}" {{ $oldValue === $option ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $fieldName }}_{{ $loop->index }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        
                        @elseif($field->type === 'multi_select')
                            @foreach($field->options_list as $option)
                                <div class="form-check">
                                    <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $option }}" class="form-check-input" id="{{ $fieldName }}_{{ $loop->index }}" {{ is_array($oldValue) && in_array($option, $oldValue) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $fieldName }}_{{ $loop->index }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        
                        @elseif($field->type === 'checkbox')
                            @if(count($field->options_list) === 1)
                                <div class="form-check">
                                    <input type="checkbox" name="{{ $fieldName }}" value="{{ $field->options_list[0] }}" class="form-check-input" id="{{ $fieldName }}" {{ $oldValue ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $fieldName }}">{{ $field->options_list[0] }}</label>
                                </div>
                            @else
                                @foreach($field->options_list as $option)
                                    <div class="form-check">
                                        <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $option }}" class="form-check-input" id="{{ $fieldName }}_{{ $loop->index }}" {{ is_array($oldValue) && in_array($option, $oldValue) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $fieldName }}_{{ $loop->index }}">{{ $option }}</label>
                                    </div>
                                @endforeach
                            @endif
                        @endif

                        @error($fieldName)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Your response:</label>
            <div class="btn-group" role="group">
                @foreach(\App\Models\Rsvp::RESPONSES as $response)
                    <button type="submit" name="response" value="{{ $response }}" class="btn btn-primary">
                        {{ ucfirst($response) }}
                    </button>
                @endforeach
            </div>
        </div>
    </form>

    @if($responded)
        <p id="rsvp-again"><a href="#" onclick="document.getElementById('rsvp-form').classList.remove('d-none'); this.parentElement.classList.add('d-none'); return false;">RSVP again</a></p>
    @endif
</div>

@foreach(\App\Models\Rsvp::RESPONSES as $response)
    @php
        $responseRsvps = $rsvps->where('response', $response);
    @endphp

    @if($responseRsvps->count() > 0)
        <strong>{{ ucfirst($response) }}</strong> ({{ $responseRsvps->count() }})

        <ul>
            @foreach($responseRsvps as $rsvp)
                @if($event->show_rsvp_names)
                    <li>
                        <strong>{{ $rsvp->name }}</strong>

                        @if(in_array($rsvp->hashid, session($event->hashid, [])))
                            <small>
                                [<a href="#" onclick="event.preventDefault(); document.getElementById('delete-rsvp-{{ $rsvp->id }}').submit();">x</a>]
                                <form id="delete-rsvp-{{ $rsvp->id }}" method="POST" action="{{ route('rsvps.destroy', [$event->toParam(), $rsvp->hashid]) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </small>
                        @endif

                        @if($rsvp->customFieldResponses->count() > 0)
                            <div class="mt-1 ms-3">
                                @foreach($rsvp->customFieldResponses as $response)
                                    <small class="text-muted d-block">
                                        <strong>{{ $response->customField->name }}:</strong> {{ $response->formatted_value }}
                                    </small>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @elseif(in_array($rsvp->hashid, session($event->hashid, [])))
                    <li>
                        <strong>{{ $rsvp->name }}</strong>

                        <small>
                            [<a href="#" onclick="event.preventDefault(); document.getElementById('delete-rsvp-{{ $rsvp->id }}').submit();">x</a>]
                            <form id="delete-rsvp-{{ $rsvp->id }}" method="POST" action="{{ route('rsvps.destroy', [$event->toParam(), $rsvp->hashid]) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </small>

                        @if($rsvp->customFieldResponses->count() > 0)
                            <div class="mt-1 ms-3">
                                @foreach($rsvp->customFieldResponses as $response)
                                    <small class="text-muted d-block">
                                        <strong>{{ $response->customField->name }}:</strong> {{ $response->formatted_value }}
                                    </small>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
@endforeach
@endsection
