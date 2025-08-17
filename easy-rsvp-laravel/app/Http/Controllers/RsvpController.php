<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rsvp;
use App\Models\CustomFieldResponse;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class RsvpController extends Controller
{
    public function create(Request $request, $eventId)
    {
        $hashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($hashid);
        
        if (!$event) {
            return redirect()->route('events.new')->with('alert', 'Event not found.');
        }

        // Build validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'response' => 'required|in:yes,maybe,no'
        ];

        // Add validation rules for custom fields
        foreach ($event->customFields as $field) {
            $fieldKey = "custom_field_{$field->id}";
            
            if ($field->required) {
                $rules[$fieldKey] = 'required';
            }
            
            if ($field->type === 'number') {
                $rules[$fieldKey] = ($field->required ? 'required|' : 'nullable|') . 'numeric';
            } elseif ($field->type === 'multi_select' || $field->type === 'checkbox') {
                $rules[$fieldKey] = ($field->required ? 'required|' : 'nullable|') . 'array';
                $rules[$fieldKey . '.*'] = 'string';
            } else {
                $rules[$fieldKey] = ($field->required ? 'required|' : 'nullable|') . 'string|max:1000';
            }
        }

        $request->validate($rules);

        $rsvp = $event->rsvps()->create($request->only(['name', 'response']));

        // Save custom field responses
        foreach ($event->customFields as $field) {
            $fieldKey = "custom_field_{$field->id}";
            $value = $request->input($fieldKey);
            
            if ($value !== null && $value !== '') {
                // Handle multi-value fields
                if (is_array($value)) {
                    $value = json_encode(array_values($value));
                }
                
                $rsvp->customFieldResponses()->create([
                    'custom_field_id' => $field->id,
                    'value' => $value
                ]);
            }
        }

        // Store RSVP hashid in session for tracking
        $userRsvpHashids = session($event->hashid, []);
        $userRsvpHashids[] = $rsvp->hashid;
        session([$event->hashid => $userRsvpHashids]);

        return redirect()->route('events.show', $event->toParam())
            ->with('success', 'Your RSVP has been recorded!');
    }

    public function destroy($eventId, $rsvpId)
    {
        $eventHashid = $this->hashidFromParam($eventId);
        $event = Event::findByHashid($eventHashid);
        
        if (!$event) {
            return redirect()->route('events.new')->with('alert', 'Event not found.');
        }

        $rsvpHashid = $this->hashidFromParam($rsvpId);
        $rsvpIdDecoded = Hashids::decode($rsvpHashid);
        
        if (empty($rsvpIdDecoded)) {
            return redirect()->route('events.show', $event->toParam())
                ->with('alert', 'RSVP not found.');
        }

        $rsvp = $event->rsvps()->find($rsvpIdDecoded[0]);
        
        if (!$rsvp) {
            return redirect()->route('events.show', $event->toParam())
                ->with('alert', 'RSVP not found.');
        }

        // Check if user has permission to delete this RSVP
        $userRsvpHashids = session($event->hashid, []);
        if (!in_array($rsvp->hashid, $userRsvpHashids)) {
            return redirect()->route('events.show', $event->toParam())
                ->with('alert', 'You can only delete your own RSVPs.');
        }

        $rsvp->delete();

        // Remove from session
        $userRsvpHashids = array_filter($userRsvpHashids, function($hashid) use ($rsvp) {
            return $hashid !== $rsvp->hashid;
        });
        session([$event->hashid => array_values($userRsvpHashids)]);

        return redirect()->route('events.show', $event->toParam())
            ->with('success', 'Your RSVP has been removed.');
    }

    private function hashidFromParam($parameterizedId)
    {
        return explode('-', $parameterizedId)[0];
    }
}
