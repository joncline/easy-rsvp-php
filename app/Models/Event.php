<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class Event extends Model
{
    protected $fillable = [
        'title',
        'date',
        'start_time',
        'end_time',
        'body',
        'admin_token',
        'show_rsvp_names',
        'published',
        'security_question',
        'security_answer'
    ];

    protected $casts = [
        'date' => 'date',
        'show_rsvp_names' => 'boolean',
        'published' => 'boolean',
        'security_answer' => 'encrypted'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            if (empty($event->admin_token)) {
                $event->admin_token = Str::uuid();
            }
        });
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class)->ordered();
    }

    public function getHashidAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function getRouteKeyName(): string
    {
        return 'hashid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = Hashids::decode($value);
        if (empty($id)) {
            return null;
        }
        return $this->where('id', $id[0])->first();
    }

    public function toParam(): string
    {
        return $this->hashid . '-' . Str::slug($this->title);
    }

    public static function findByHashid(string $hashid): ?self
    {
        $id = Hashids::decode($hashid);
        if (empty($id)) {
            return null;
        }
        return self::find($id[0]);
    }
}
