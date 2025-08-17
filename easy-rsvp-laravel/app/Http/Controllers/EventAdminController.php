<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventAdminController extends Controller
{
    public function show($eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        $rsvps = $event->rsvps()->persisted()->orderBy('created_at', 'asc')->get();

        return view('events.admin.show', compact('event', 'rsvps'));
    }

    public function edit($eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        return view('events.admin.edit', compact('event'));
    }

    public function update(Request $request, $eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'nullable|string',
            'show_rsvp_names' => 'boolean'
        ]);

        $event->update($request->only(['title', 'date', 'body', 'show_rsvp_names']));

        return redirect()->route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $adminToken])
            ->with('success', 'Event updated successfully!');
    }

    public function destroy($eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        $event->delete();

        return redirect()->route('events.new')->with('success', 'Event deleted successfully.');
    }

    public function togglePublish($eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        $event->update(['published' => !$event->published]);

        $status = $event->published ? 'published' : 'unpublished';
        return redirect()->route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $adminToken])
            ->with('success', "Event {$status} successfully!");
    }

    private function hashidFromParam($parameterizedId)
    {
        return explode('-', $parameterizedId)[0];
    }
}
