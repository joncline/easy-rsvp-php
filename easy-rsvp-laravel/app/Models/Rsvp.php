<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vinkla\Hashids\Facades\Hashids;

class Rsvp extends Model
{
    const RESPONSES = [
        'yes',
        'maybe',
        'no'
    ];

    protected $fillable = [
        'event_id',
        'name',
        'response'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getHashidAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function getSessionKey(): string
    {
        return "event:{$this->event_id}:rsvp:{$this->id}";
    }

    public function scopePersisted($query)
    {
        return $query->whereNotNull('id');
    }
}
