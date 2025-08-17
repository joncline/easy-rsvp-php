<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class EventController extends Controller
{
    public function show($id)
    {
        // Extract hashid from parameterized ID
        $hashid = $this->hashidFromParam($id);
        $event = Event::findByHashid($hashid);
        
        if (!$event || !$event->published) {
            return redirect()->route('events.new')->with('alert', 'This event is no longer viewable.');
        }

        $rsvp = new Rsvp(['event_id' => $event->id]);
        $rsvps = $event->rsvps()->with('customFieldResponses.customField')->persisted()->orderBy('created_at', 'asc')->get();

        $userRsvpHashids = session($event->hashid, []);
        $responded = $rsvps->contains(function ($rsvp) use ($userRsvpHashids) {
            return in_array($rsvp->hashid, $userRsvpHashids);
        });

        return view('events.show', compact('event', 'rsvp', 'rsvps', 'responded'));
    }

    public function new()
    {
        $event = new Event();
        $placeholders = [
            'title' => 'BBQ party in our backyard 🏡🍔🍻',
            'body' => "Hey everyone, summer is finally here so let's celebrate with some grilled food and cold beers! Our address: 1000 Hart Street in Brooklyn."
        ];

        return view('events.new', compact('event', 'placeholders'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'nullable|string',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.name' => 'required|string|max:255',
            'custom_fields.*.type' => 'required|in:text,number,select,multi_select,radio,checkbox,textarea',
            'custom_fields.*.required' => 'boolean',
            'custom_fields.*.options' => 'nullable|array',
            'custom_fields.*.options.*' => 'string|max:255'
        ]);

        $event = Event::create($request->only(['title', 'date', 'body']));

        // Create custom fields if provided
        if ($request->has('custom_fields')) {
            foreach ($request->custom_fields as $index => $fieldData) {
                if (!empty($fieldData['name'])) {
                    $event->customFields()->create([
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'required' => $fieldData['required'] ?? false,
                        'options' => $fieldData['options'] ?? null,
                        'sort_order' => $index
                    ]);
                }
            }
        }

        return redirect()->route('events.admin.show', [
            'event' => $event->toParam(),
            'admin_token' => $event->admin_token
        ]);
    }

    private function hashidFromParam($parameterizedId)
    {
        return explode('-', $parameterizedId)[0];
    }
}
