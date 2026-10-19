@php
    $key = $field->field_key;
    $type = $field->field_type;
    $val = old($key, isset($customer) 
        ? app(\App\Services\CustomerFieldService::class)->getFieldValue($customer, $field, false) 
        : ($key === 'customer_id' ? ($latestId ?? $field->default_value) : $field->default_value));

    // Determine grid column size matching the UI layout
    $colClass = 'col-md-3 col-sm-6';
    if ($key === 'address' || $key === 'contact_person' || $key === 'email_address') {
        $colClass = 'col-md-6 col-sm-12';
    } elseif ($key === 'opening_balance' || $key === 'balance_range' || $key === 'reminder_day') {
        $colClass = 'col-md-4 col-sm-6';
    } elseif ($key === 'notes' || $type === 'textarea') {
        $colClass = 'col-12';
    }

    // Determine Prefix Icon
    $iconClass = 'fas fa-pen';
    switch ($key) {
        case 'customer_id':
        case 'ntn_number':
        case 'strn_number':
            $iconClass = 'fas fa-hashtag';
            break;
        case 'customer_type':
            $iconClass = 'fas fa-list-ul';
            break;
        case 'customer_name':
            $iconClass = 'far fa-building';
            break;
        case 'customer_name_ur':
            $iconClass = 'fas fa-language';
            break;
        case 'mobile':
            $iconClass = 'fas fa-phone-alt';
            break;
        case 'zone':
            $iconClass = 'fas fa-map-marker-alt';
            break;
        case 'address':
            $iconClass = 'fas fa-home';
            break;
        case 'opening_balance':
        case 'balance_range':
            $iconClass = 'fas fa-layer-group';
            break;
        case 'reminder_day':
            $iconClass = 'far fa-calendar-alt';
            break;
        case 'cnic':
            $iconClass = 'far fa-id-card';
            break;
        case 'filer_type':
            $iconClass = 'far fa-file-alt';
            break;
        case 'contact_person':
            $iconClass = 'far fa-user';
            break;
        case 'email_address':
            $iconClass = 'far fa-envelope';
            break;
        case 'notes':
            $iconClass = 'far fa-clipboard';
            break;
        default:
            if ($type === 'phone') $iconClass = 'fas fa-phone-alt';
            elseif ($type === 'email') $iconClass = 'far fa-envelope';
            elseif ($type === 'date') $iconClass = 'far fa-calendar-alt';
            elseif ($type === 'number') $iconClass = 'fas fa-hashtag';
            elseif ($type === 'dropdown') $iconClass = 'fas fa-list-ul';
            elseif ($type === 'url') $iconClass = 'fas fa-link';
            elseif ($type === 'textarea') $iconClass = 'far fa-comment-alt';
            else $iconClass = 'fas fa-font';
            break;
    }
@endphp

<div class="{{ $colClass }} mb-3">
    <!-- Field Label -->
    <label class="form-label fw-bold text-dark mb-0 d-block" for="field_{{ $key }}" style="font-size: 0.88rem;">
        {{ $field->label }}
        @if($field->is_required)
            <span class="text-danger fw-bold ms-1">*</span>
        @endif
    </label>

    <!-- Sub-label / Help text -->
    <div class="text-muted" style="font-size: 0.74rem; margin-top: 2px; margin-bottom: 6px; line-height: 1.25; min-height: 16px;">
        {{ $field->help_text ?: '' }}
    </div>

    <!-- Input Group with Icon Addon -->
    @if($key === 'address')
        {{-- Single line Address input to match Row 2 in screenshot --}}
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.95rem; min-width: 44px; justify-content: center;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <input type="text" name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder ?: 'Shop #, Street Area, City' }}"
                style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.9rem; padding: 8px 12px; height: 42px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background:#f8fafc;' : '' }}>
        </div>

    @elseif($type === 'dropdown')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.95rem; min-width: 44px; justify-content: center;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <select name="{{ $key }}" id="field_{{ $key }}" 
                class="form-control form-select @error($key) is-invalid @enderror" 
                style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.9rem; padding: 8px 12px; height: 42px;"
                {{ $field->is_required ? 'required' : '' }} 
                {{ $field->is_readonly ? 'disabled' : '' }}>
                <option value="">-- Select {{ $field->label }} --</option>
                
                @if($field->system_column === 'customer_type' && isset($dropdownLookups['customer_types']))
                    @foreach($dropdownLookups['customer_types'] as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$val === (string)$optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                @elseif($field->system_column === 'zone' && isset($dropdownLookups['zones']))
                    @foreach($dropdownLookups['zones'] as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$val === (string)$optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                @elseif($field->system_column === 'sales_officer_id' && isset($dropdownLookups['sales_officers']))
                    @foreach($dropdownLookups['sales_officers'] as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$val === (string)$optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                @else
                    @foreach($field->getParsedOptions() as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$val === (string)$optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        @if($field->is_readonly)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif

    @elseif($type === 'textarea')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.95rem; min-width: 44px; justify-content: center; align-items: flex-start; padding-top: 10px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <textarea name="{{ $key }}" id="field_{{ $key }}" rows="2"
                class="form-control @error($key) is-invalid @enderror" 
                placeholder="{{ $field->placeholder }}"
                style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.9rem; padding: 8px 12px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background-color:#f8fafc;' : '' }}>{{ $val }}</textarea>
        </div>

    @elseif($key === 'opening_balance' || $key === 'balance_range')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.92rem; min-width: 44px; justify-content: center;">
                <i class="{{ $iconClass }} text-secondary me-2"></i>
                <span class="fw-bold text-dark" style="font-size: 0.82rem;">Rs.</span>
            </span>
            <input type="number" step="any" name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val ?? 0 }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder ?: '0' }}"
                style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.9rem; padding: 8px 12px; height: 42px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background-color:#f8fafc;' : '' }}>
        </div>

    @elseif($type === 'checkbox')
        <div class="input-group mt-2">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; min-width: 44px; justify-content: center;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <div class="form-control d-flex align-items-center" style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; height: 42px;">
                <input type="hidden" name="{{ $key }}" value="0">
                <input class="form-check-input me-2 mt-0" type="checkbox" name="{{ $key }}" id="field_{{ $key }}" value="1"
                    {{ (bool)$val ? 'checked' : '' }}
                    {{ $field->is_readonly ? 'disabled' : '' }}>
                <label class="form-check-label text-dark small fw-semibold" for="field_{{ $key }}">
                    {{ $field->placeholder ?: 'Check to enable' }}
                </label>
            </div>
        </div>

    @else
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #d1d5db; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.95rem; min-width: 44px; justify-content: center;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <input type="{{ $type === 'phone' ? 'tel' : ($type === 'email' ? 'email' : ($type === 'date' ? 'date' : ($type === 'number' ? 'number' : ($type === 'url' ? 'url' : 'text')))) }}" 
                name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder }}"
                style="border-color: #d1d5db; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.9rem; padding: 8px 12px; height: 42px; {{ $field->is_readonly ? 'background:#f1f5f9; cursor:not-allowed;' : 'background:#fff;' }} {{ $key === 'customer_name_ur' ? 'direction:rtl; text-align:right; font-family:\'Noto Nastaliq Urdu\', serif;' : '' }}"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly' : '' }}>
        </div>
    @endif

    @error($key)
        <div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>
    @enderror
</div>
