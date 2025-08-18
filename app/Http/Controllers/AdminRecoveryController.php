<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminRecoveryController extends Controller
{
    public function show()
    {
        return view('admin.recovery');
    }

    public function recover(Request $request)
    {
        // Rate limiting: 3 attempts per hour per IP
        $key = 'admin-recovery:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'rate_limit' => "Too many recovery attempts. Please try again in " . ceil($seconds / 60) . " minutes."
            ]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'security_answer' => 'required|string|max:255'
        ]);

        // Find event by title and date
        $event = Event::where('title', $request->title)
                     ->where('date', $request->date)
                     ->whereNotNull('security_question')
                     ->whereNotNull('security_answer')
                     ->first();

        if (!$event) {
            RateLimiter::hit($key, 3600); // 1 hour
            throw ValidationException::withMessages([
                'event' => 'No event found with the provided details or no security question was set for this event.'
            ]);
        }

        // Check security answer (case-insensitive, trimmed)
        $providedAnswer = trim(strtolower($request->security_answer));
        $storedAnswer = trim(strtolower($event->security_answer));

        if ($providedAnswer !== $storedAnswer) {
            RateLimiter::hit($key, 3600); // 1 hour
            throw ValidationException::withMessages([
                'security_answer' => 'The security answer is incorrect.'
            ]);
        }

        // Success - clear rate limiting and show admin URL
        RateLimiter::clear($key);
        
        $adminUrl = route('events.admin.show', [
            'event' => $event->toParam(),
            'admin_token' => $event->admin_token
        ]);

        return view('admin.recovery-success', compact('event', 'adminUrl'));
    }
}
