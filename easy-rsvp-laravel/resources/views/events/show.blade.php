@extends('layouts.app')

@section('content')
<h1>
    {{ $event->title }}

    <small class="text-muted">
        //
        {{ $event->date->format('l, F j, Y') }}
    </small>

    @if(config('app.env') === 'local')
        <small><a href="{{ route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}">admin</a></small>
    @endif
</h1>

<div class="trix-content">{!! $event->body !!}</div>

<br>

<h2>Who's coming?</h2>

@unless($event->show_rsvp_names)
    <small>Guest names are hidden to other guests.</small>
@endunless

<p>
    <form method="POST" action="{{ route('rsvps.create', $event->toParam()) }}" class="d-inline-flex align-items-center gap-2 {{ $responded ? 'd-none' : '' }}" id="rsvp-form">
        @csrf
        <label for="name" class="form-label mb-0">Your name:</label>
        <input type="text" name="name" id="name" class="form-control" style="width: auto;">

        @foreach(\App\Models\Rsvp::RESPONSES as $response)
            <button type="submit" name="response" value="{{ $response }}" class="btn btn-primary">
                {{ ucfirst($response) }}
            </button>
        @endforeach
    </form>

    @if($responded)
        <span id="rsvp-again"><a href="#" onclick="document.getElementById('rsvp-form').classList.remove('d-none'); this.parentElement.classList.add('d-none'); return false;">RSVP again</a></span>
    @endif
</p>

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
                        {{ $rsvp->name }}

                        @if(in_array($rsvp->hashid, session($event->hashid, [])))
                            <small>
                                [<a href="#" onclick="event.preventDefault(); document.getElementById('delete-rsvp-{{ $rsvp->id }}').submit();">x</a>]
                                <form id="delete-rsvp-{{ $rsvp->id }}" method="POST" action="{{ route('rsvps.destroy', [$event->toParam(), $rsvp->hashid]) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </small>
                        @endif
                    </li>
                @elseif(in_array($rsvp->hashid, session($event->hashid, [])))
                    <li>
                        {{ $rsvp->name }}

                        <small>
                            [<a href="#" onclick="event.preventDefault(); document.getElementById('delete-rsvp-{{ $rsvp->id }}').submit();">x</a>]
                            <form id="delete-rsvp-{{ $rsvp->id }}" method="POST" action="{{ route('rsvps.destroy', [$event->toParam(), $rsvp->hashid]) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </small>
                    </li>
                @endif
            @endforeach
        </ul>
    @endif
@endforeach
@endsection
