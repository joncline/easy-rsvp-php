<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFieldResponse extends Model
{
    protected $fillable = [
        'rsvp_id',
        'custom_field_id',
        'value'
    ];

    public function rsvp(): BelongsTo
    {
        return $this->belongsTo(Rsvp::class);
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function getFormattedValueAttribute(): string
    {
        if (empty($this->value)) {
            return '';
        }

        // Handle multi-value fields (stored as JSON arrays)
        if ($this->customField && $this->customField->isMultiValue()) {
            $values = is_array($this->value) ? $this->value : json_decode($this->value, true);
            return is_array($values) ? implode(', ', $values) : $this->value;
        }

        return $this->value;
    }

    public function getValueAsArrayAttribute(): array
    {
        if (empty($this->value)) {
            return [];
        }

        if (is_array($this->value)) {
            return $this->value;
        }

        $decoded = json_decode($this->value, true);
        return is_array($decoded) ? $decoded : [$this->value];
    }
}
