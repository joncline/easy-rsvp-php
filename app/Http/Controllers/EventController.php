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

        // Generate Google Calendar URL if event has time
        $googleCalendarUrl = null;
        if ($event->start_time) {
            $googleCalendarUrl = $this->generateGoogleCalendarUrl($event);
        }

        return view('events.show', compact('event', 'rsvp', 'rsvps', 'responded', 'googleCalendarUrl'));
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
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'body' => 'nullable|string',
            'security_question' => 'nullable|string|max:255',
            'security_answer' => 'nullable|string|max:255',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.name' => 'required|string|max:255',
            'custom_fields.*.type' => 'required|in:text,number,select,multi_select,radio,checkbox,textarea',
            'custom_fields.*.required' => 'boolean',
            'custom_fields.*.options' => 'nullable|array',
            'custom_fields.*.options.*' => 'string|max:255'
        ]);

        // Custom validation for end_time after start_time
        if ($request->filled('start_time') && $request->filled('end_time')) {
            if ($request->end_time <= $request->start_time) {
                return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
            }
        }

        // Validate that if security_question is provided, security_answer must also be provided
        if ($request->filled('security_question') && !$request->filled('security_answer')) {
            return back()->withErrors(['security_answer' => 'Security answer is required when security question is provided.'])->withInput();
        }

        if ($request->filled('security_answer') && !$request->filled('security_question')) {
            return back()->withErrors(['security_question' => 'Security question is required when security answer is provided.'])->withInput();
        }

        $event = Event::create($request->only(['title', 'date', 'start_time', 'end_time', 'body', 'security_question', 'security_answer']));

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

    private function generateGoogleCalendarUrl(Event $event)
    {
        $title = urlencode($event->title);
        $details = urlencode(strip_tags($event->body ?? ''));
        
        // Create start and end datetime strings
        $startDateTime = $event->date->format('Y-m-d') . 'T' . $event->start_time . ':00';
        $endDateTime = $event->date->format('Y-m-d') . 'T' . ($event->end_time ?? $event->start_time) . ':00';
        
        // Convert to UTC format for Google Calendar
        $startUtc = \Carbon\Carbon::parse($startDateTime)->utc()->format('Ymd\THis\Z');
        $endUtc = \Carbon\Carbon::parse($endDateTime)->utc()->format('Ymd\THis\Z');
        
        $dates = $startUtc . '/' . $endUtc;
        
        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$dates}&details={$details}";
    }
}
