@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Event</h1>
    <a href="{{ route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}" class="btn btn-secondary">
        ← Back to Event
    </a>
</div>

<form method="POST" action="{{ route('events.admin.update', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="title" class="form-label">Event Title</label>
        <input type="text" 
               class="form-control @error('title') is-invalid @enderror" 
               id="title" 
               name="title" 
               value="{{ old('title', $event->title) }}" 
               required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">Event Date</label>
        <input type="date" 
               class="form-control @error('date') is-invalid @enderror" 
               id="date" 
               name="date" 
               value="{{ old('date', $event->date ? $event->date->format('Y-m-d') : '') }}" 
               required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="start_time" class="form-label">Start Time (optional)</label>
            <input type="time" 
                   class="form-control @error('start_time') is-invalid @enderror" 
                   id="start_time" 
                   name="start_time" 
                   value="{{ old('start_time', $event->start_time ? $event->start_time->format('H:i') : '') }}">
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="end_time" class="form-label">End Time (optional)</label>
            <input type="time" 
                   class="form-control @error('end_time') is-invalid @enderror" 
                   id="end_time" 
                   name="end_time" 
                   value="{{ old('end_time', $event->end_time ? $event->end_time->format('H:i') : '') }}">
            @error('end_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">Event Description</label>
        <input id="body" type="hidden" name="body" value="{{ old('body', $event->body) }}">
        <trix-editor input="body"></trix-editor>
        @error('body')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input" 
                   id="show_rsvp_names" 
                   name="show_rsvp_names" 
                   value="1"
                   {{ old('show_rsvp_names', $event->show_rsvp_names) ? 'checked' : '' }}>
            <label class="form-check-label" for="show_rsvp_names">
                Show RSVP names publicly
            </label>
        </div>
    </div>

    <!-- Custom Fields Section -->
    <div class="mb-4">
        <h4>Custom Fields</h4>
        <p class="text-muted">Manage custom questions for your guests to answer when they RSVP.</p>
        
        <div id="custom-fields-container">
            @foreach($event->customFields as $index => $customField)
                <div class="custom-field-item border rounded p-3 mb-3" data-index="{{ $index }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Field Name</label>
                            <input type="text" 
                                   name="custom_fields[{{ $index }}][name]" 
                                   class="form-control" 
                                   value="{{ $customField->name }}"
                                   placeholder="e.g., Food preference">
                            <input type="hidden" name="custom_fields[{{ $index }}][id]" value="{{ $customField->id }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field Type</label>
                            <select name="custom_fields[{{ $index }}][type]" class="form-control field-type-select">
                                <option value="text" {{ $customField->type === 'text' ? 'selected' : '' }}>Text Field</option>
                                <option value="number" {{ $customField->type === 'number' ? 'selected' : '' }}>Number Field</option>
                                <option value="textarea" {{ $customField->type === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                <option value="select" {{ $customField->type === 'select' ? 'selected' : '' }}>Select/Dropdown</option>
                                <option value="multi_select" {{ $customField->type === 'multi_select' ? 'selected' : '' }}>Multi-Select</option>
                                <option value="radio" {{ $customField->type === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                <option value="checkbox" {{ $customField->type === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="custom_fields[{{ $index }}][required]" 
                                       value="1" 
                                       class="form-check-input" 
                                       id="required_{{ $index }}"
                                       {{ $customField->required ? 'checked' : '' }}>
                                <label class="form-check-label" for="required_{{ $index }}">Required field</label>
                            </div>
                        </div>
                    </div>
                    <div class="options-container mt-2" style="display: {{ $customField->hasOptions() ? 'block' : 'none' }};">
                        <label class="form-label">Options (one per line)</label>
                        <textarea name="custom_fields[{{ $index }}][options_text]" 
                                  class="form-control options-textarea" 
                                  rows="3" 
                                  placeholder="Option 1&#10;Option 2&#10;Option 3">{{ $customField->options ? implode("\n", $customField->options) : '' }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
        
        <button type="button" class="btn btn-outline-secondary btn-sm" id="add-custom-field">
            + Add Custom Field
        </button>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="{{ route('events.admin.show', ['event' => $event->toParam(), 'admin_token' => $event->admin_token]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
let fieldIndex = {{ $event->customFields->count() }};

document.getElementById('add-custom-field').addEventListener('click', function() {
    const container = document.getElementById('custom-fields-container');
    const fieldHtml = `
        <div class="custom-field-item border rounded p-3 mb-3" data-index="${fieldIndex}">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Field Name</label>
                    <input type="text" name="custom_fields[${fieldIndex}][name]" class="form-control" placeholder="e.g., Food preference">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field Type</label>
                    <select name="custom_fields[${fieldIndex}][type]" class="form-control field-type-select">
                        <option value="text">Text Field</option>
                        <option value="number">Number Field</option>
                        <option value="textarea">Textarea</option>
                        <option value="select">Select/Dropdown</option>
                        <option value="multi_select">Multi-Select</option>
                        <option value="radio">Radio Buttons</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="custom_fields[${fieldIndex}][required]" value="1" class="form-check-input" id="required_${fieldIndex}">
                        <label class="form-check-label" for="required_${fieldIndex}">Required field</label>
                    </div>
                </div>
            </div>
            <div class="options-container mt-2" style="display: none;">
                <label class="form-label">Options (one per line)</label>
                <textarea name="custom_fields[${fieldIndex}][options_text]" class="form-control options-textarea" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fieldHtml);
    fieldIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-field')) {
        e.target.closest('.custom-field-item').remove();
    }
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('field-type-select')) {
        const optionsContainer = e.target.closest('.custom-field-item').querySelector('.options-container');
        const fieldType = e.target.value;
        
        if (['select', 'multi_select', 'radio', 'checkbox'].includes(fieldType)) {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    }
});

// Convert options text to array before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const optionsTextareas = document.querySelectorAll('.options-textarea');
    optionsTextareas.forEach(function(textarea) {
        const fieldItem = textarea.closest('.custom-field-item');
        const index = fieldItem.dataset.index;
        const optionsText = textarea.value.trim();
        
        if (optionsText) {
            const options = optionsText.split('\n').map(opt => opt.trim()).filter(opt => opt);
            
            // Remove existing option inputs
            fieldItem.querySelectorAll('input[name*="[options]"]').forEach(input => input.remove());
            
            // Add option inputs
            options.forEach(function(option, optIndex) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `custom_fields[${index}][options][${optIndex}]`;
                hiddenInput.value = option;
                fieldItem.appendChild(hiddenInput);
            });
        }
    });
});
</script>
@endsection
