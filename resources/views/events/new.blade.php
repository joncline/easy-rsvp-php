@extends('layouts.app')

@section('content')
<h1 class="text-center">
    Make it <i class="text-success">easy</i> for your guests to <i class="text-success">RSVP</i>
</h1>

<br>

<form method="POST" action="{{ route('events.create') }}">
    @csrf
    
    <div class="mb-3">
        <label for="title" class="form-label">What are you planning?</label>
        <input type="text" 
               class="form-control @error('title') is-invalid @enderror" 
               id="title" 
               name="title" 
               value="{{ old('title') }}" 
               placeholder="{{ $placeholders['title'] }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">When is this happening?</label>
        <input type="date" 
               class="form-control @error('date') is-invalid @enderror" 
               id="date" 
               name="date" 
               value="{{ old('date') }}">
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <div class="mb-3">
        <label for="body" class="form-label">More details (optional):</label>
        <input id="body" type="hidden" name="body" value="{{ old('body') }}">
        <trix-editor input="body" placeholder="{{ $placeholders['body'] }}"></trix-editor>
        @error('body')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <br>

    <!-- Custom Fields Section -->
    <div class="mb-4">
        <h4>Custom Fields <small class="text-muted">(Optional)</small></h4>
        <p class="text-muted">Add custom questions for your guests to answer when they RSVP.</p>
        
        <div id="custom-fields-container">
            <!-- Custom fields will be added here dynamically -->
        </div>
        
        <button type="button" class="btn btn-outline-secondary btn-sm" id="add-custom-field">
            + Add Custom Field
        </button>
    </div>

    <!-- Security Question Section -->
    <div class="mb-4">
        <h4>Admin URL Recovery <small class="text-muted">(Optional)</small></h4>
        <p class="text-muted">Set a security question in case you forget your admin URL. This will help you recover access to manage your event.</p>
        
        <div class="row">
            <div class="col-md-6">
                <label for="security_question" class="form-label">Security Question</label>
                <select class="form-control @error('security_question') is-invalid @enderror" 
                        id="security_question" 
                        name="security_question">
                    <option value="">Choose a security question (optional)</option>
                    <option value="What is your mother's maiden name?" {{ old('security_question') == "What is your mother's maiden name?" ? 'selected' : '' }}>What is your mother's maiden name?</option>
                    <option value="What was the name of your first pet?" {{ old('security_question') == "What was the name of your first pet?" ? 'selected' : '' }}>What was the name of your first pet?</option>
                    <option value="What city were you born in?" {{ old('security_question') == "What city were you born in?" ? 'selected' : '' }}>What city were you born in?</option>
                    <option value="What is your favorite movie?" {{ old('security_question') == "What is your favorite movie?" ? 'selected' : '' }}>What is your favorite movie?</option>
                    <option value="What was your childhood nickname?" {{ old('security_question') == "What was your childhood nickname?" ? 'selected' : '' }}>What was your childhood nickname?</option>
                    <option value="What is the name of your favorite teacher?" {{ old('security_question') == "What is the name of your favorite teacher?" ? 'selected' : '' }}>What is the name of your favorite teacher?</option>
                    <option value="custom">Custom question</option>
                </select>
                @error('security_question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="security_answer" class="form-label">Your Answer</label>
                <input type="text" 
                       class="form-control @error('security_answer') is-invalid @enderror" 
                       id="security_answer" 
                       name="security_answer" 
                       value="{{ old('security_answer') }}" 
                       placeholder="Enter your answer">
                @error('security_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="mt-2" id="custom_question_container" style="display: none;">
            <label for="custom_security_question" class="form-label">Custom Security Question</label>
            <input type="text" 
                   class="form-control" 
                   id="custom_security_question" 
                   placeholder="Enter your custom security question">
        </div>
        
        <small class="text-muted">
            <i class="fas fa-info-circle"></i> 
            If you set a security question, you can recover your admin URL by visiting 
            <a href="{{ route('admin.recovery') }}" target="_blank">/recover-admin</a> 
            and providing your event details and security answer.
        </small>
    </div>

    <br>

    <button type="submit" class="btn btn-primary me-2">Create your event, for free!</button>
</form>

<script>
let fieldIndex = 0;

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

// Handle security question custom option
document.getElementById('security_question').addEventListener('change', function() {
    const customContainer = document.getElementById('custom_question_container');
    const customInput = document.getElementById('custom_security_question');
    
    if (this.value === 'custom') {
        customContainer.style.display = 'block';
        customInput.required = true;
    } else {
        customContainer.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
});

// Convert options text to array before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    // Handle custom security question
    const securityQuestionSelect = document.getElementById('security_question');
    const customQuestionInput = document.getElementById('custom_security_question');
    
    if (securityQuestionSelect.value === 'custom' && customQuestionInput.value.trim()) {
        securityQuestionSelect.value = customQuestionInput.value.trim();
    }
    
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
