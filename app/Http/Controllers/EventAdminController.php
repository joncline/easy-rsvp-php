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

        $rsvps = $event->rsvps()->persisted()->with('customFieldResponses.customField')->orderBy('created_at', 'asc')->get();

        return view('events.admin.show', compact('event', 'rsvps'));
    }

    public function edit($eventId, $adminToken)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event || $event->admin_token !== $adminToken) {
            return redirect()->route('events.new')->with('alert', 'Invalid admin access.');
        }

        $event->load('customFields');

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
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'body' => 'nullable|string',
            'show_rsvp_names' => 'boolean',
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

        $event->update($request->only(['title', 'date', 'start_time', 'end_time', 'body', 'show_rsvp_names']));

        // Handle custom fields updates
        if ($request->has('custom_fields')) {
            // Get existing custom field IDs
            $existingFieldIds = $event->customFields->pluck('id')->toArray();
            $updatedFieldIds = [];

            foreach ($request->custom_fields as $index => $fieldData) {
                if (!empty($fieldData['name'])) {
                    if (isset($fieldData['id']) && in_array($fieldData['id'], $existingFieldIds)) {
                        // Update existing custom field
                        $customField = $event->customFields()->find($fieldData['id']);
                        if ($customField) {
                            $customField->update([
                                'name' => $fieldData['name'],
                                'type' => $fieldData['type'],
                                'required' => $fieldData['required'] ?? false,
                                'options' => $fieldData['options'] ?? null,
                                'sort_order' => $index
                            ]);
                            $updatedFieldIds[] = $customField->id;
                        }
                    } else {
                        // Create new custom field
                        $customField = $event->customFields()->create([
                            'name' => $fieldData['name'],
                            'type' => $fieldData['type'],
                            'required' => $fieldData['required'] ?? false,
                            'options' => $fieldData['options'] ?? null,
                            'sort_order' => $index
                        ]);
                        $updatedFieldIds[] = $customField->id;
                    }
                }
            }

            // Delete custom fields that were removed
            $fieldsToDelete = array_diff($existingFieldIds, $updatedFieldIds);
            if (!empty($fieldsToDelete)) {
                $event->customFields()->whereIn('id', $fieldsToDelete)->delete();
            }
        } else {
            // If no custom fields in request, delete all existing ones
            $event->customFields()->delete();
        }

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
