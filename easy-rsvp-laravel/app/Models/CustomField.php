<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    const FIELD_TYPES = [
        'text' => 'Text Field',
        'number' => 'Number Field',
        'select' => 'Select/Dropdown',
        'multi_select' => 'Multi-Select',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkbox',
        'textarea' => 'Textarea'
    ];

    protected $fillable = [
        'event_id',
        'name',
        'type',
        'options',
        'required',
        'sort_order'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CustomFieldResponse::class);
    }

    public function getTypeNameAttribute(): string
    {
        return self::FIELD_TYPES[$this->type] ?? $this->type;
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'multi_select', 'radio', 'checkbox']);
    }

    public function isMultiValue(): bool
    {
        return in_array($this->type, ['multi_select', 'checkbox']);
    }

    public function getOptionsListAttribute(): array
    {
        return $this->options ?? [];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
