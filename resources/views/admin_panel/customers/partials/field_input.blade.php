@php
    $key = $field->field_key;
    $type = $field->field_type;
    $val = old($key, isset($customer) 
        ? app(\App\Services\CustomerFieldService::class)->getFieldValue($customer, $field, false) 
        : ($key === 'customer_id' ? ($latestId ?? $field->default_value) : $field->default_value));

    // Standardized Column Sizing for perfect 3-column desktop alignment
    $colClass = 'col-lg-4 col-md-6 col-12';
    if ($key === 'address' || $key === 'notes' || $type === 'textarea') {
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
        case 'party_type':
            $iconClass = 'fas fa-users-cog';
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
            $iconClass = 'fas fa-map-marked-alt';
            break;
        case 'province':
            $iconClass = 'fas fa-map';
            break;
        case 'city':
            $iconClass = 'fas fa-city';
            break;
        case 'area':
            $iconClass = 'fas fa-map-pin';
            break;
        case 'address':
            $iconClass = 'fas fa-home';
            break;
        case 'opening_balance':
        case 'balance_range':
            $iconClass = 'fas fa-wallet';
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
            $iconClass = 'far fa-comment-alt';
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

<div class="{{ $colClass }}">
    <!-- Clean, Single-Line Field Label with Tooltip Info Icon (Eliminates Congestion) -->
    <div class="d-flex align-items-center justify-content-between mb-1" style="min-height: 20px;">
        <label class="form-label fw-bold text-dark mb-0 text-truncate" for="field_{{ $key }}" style="font-size: 0.82rem; letter-spacing: -0.01em;">
            {{ $field->label }}
            @if($field->is_required)
                <span class="text-danger ms-1 fw-bold">*</span>
            @else
                <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Optional)</span>
            @endif
        </label>
        @if(!empty($field->help_text))
            <span class="text-muted ms-1" title="{{ $field->help_text }}" style="cursor: help; font-size: 0.75rem;">
                <i class="fas fa-info-circle text-secondary opacity-75"></i>
            </span>
        @endif
    </div>

    <!-- Input Group with Icon Addon (Uniform 38px Height & Pixel-Perfect Alignment) -->
    @if($key === 'address')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <input type="text" name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder ?: 'Enter street address, shop/plot number...' }}"
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background:#f8fafc;' : '' }}>
        </div>

    @elseif($type === 'dropdown')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <select name="{{ $key }}" id="field_{{ $key }}" 
                class="form-control form-select @error($key) is-invalid @enderror" 
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
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
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; align-items: flex-start; padding-top: 8px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <textarea name="{{ $key }}" id="field_{{ $key }}" rows="2"
                class="form-control @error($key) is-invalid @enderror" 
                placeholder="{{ $field->placeholder ?: 'Add any specific terms, instructions, or internal remarks...' }}"
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 8px 12px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background-color:#f8fafc;' : '' }}>{{ $val }}</textarea>
        </div>

    @elseif($key === 'opening_balance' || $key === 'balance_range')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.85rem; width: 40px; justify-content: center; height: 38px;">
                <span class="fw-bold text-secondary" style="font-size: 0.78rem;">Rs</span>
            </span>
            <input type="number" step="any" name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val ?? 0 }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder ?: '0.00' }}"
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly style=background-color:#f8fafc;' : '' }}>
        </div>

    @elseif($type === 'checkbox')
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; width: 40px; justify-content: center; height: 38px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <div class="form-control d-flex align-items-center" style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; height: 38px;">
                <input type="hidden" name="{{ $key }}" value="0">
                <input class="form-check-input me-2 mt-0" type="checkbox" name="{{ $key }}" id="field_{{ $key }}" value="1"
                    {{ (bool)$val ? 'checked' : '' }}
                    {{ $field->is_readonly ? 'disabled' : '' }}>
                <label class="form-check-label text-dark small fw-semibold" for="field_{{ $key }}">
                    {{ $field->placeholder ?: 'Check to enable' }}
                </label>
            </div>
        </div>

    @elseif($key === 'province')
        @php
            $locSvc = app(\App\Services\LocationService::class);
            $provincesList = $locSvc->getProvinces();
        @endphp
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="fas fa-map"></i>
            </span>
            <select name="province" id="field_province" 
                class="form-control form-select @error('province') is-invalid @enderror" 
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
                {{ $field->is_required ? 'required' : '' }}>
                <option value="">-- Select Province --</option>
                @foreach($provincesList as $p)
                    <option value="{{ $p }}" {{ (string)$val === (string)$p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
                @if(!empty($val) && !in_array($val, $provincesList))
                    <option value="{{ $val }}" selected>{{ $val }}</option>
                @endif
            </select>
        </div>

    @elseif($key === 'city')
        @php
            $locSvc = app(\App\Services\LocationService::class);
            $citiesList = $locSvc->getCities();
        @endphp
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="fas fa-city"></i>
            </span>
            <select name="city" id="field_city" 
                class="form-control form-select @error('city') is-invalid @enderror" 
                style="border-color: #cbd5e1; border-left: none; border-radius: 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
                {{ $field->is_required ? 'required' : '' }}>
                <option value="">-- Select City --</option>
                @foreach($citiesList as $c)
                    <option value="{{ $c }}" {{ (string)$val === (string)$c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
                @if(!empty($val) && !in_array($val, $citiesList))
                    <option value="{{ $val }}" selected>{{ $val }}</option>
                @endif
            </select>
            <button type="button" class="btn btn-outline-primary btn-quick-add-city px-2.5" title="Add New City" style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-plus"></i>
            </button>
        </div>

    @elseif($key === 'area')
        @php
            $locSvc = app(\App\Services\LocationService::class);
            $areasList = $locSvc->getAreas();
        @endphp
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="fas fa-map-pin"></i>
            </span>
            <select name="area" id="field_area" 
                class="form-control form-select @error('area') is-invalid @enderror" 
                style="border-color: #cbd5e1; border-left: none; border-radius: 0; font-size: 0.88rem; padding: 6px 12px; height: 38px;"
                {{ $field->is_required ? 'required' : '' }}>
                <option value="">-- Select Area --</option>
                @foreach($areasList as $a)
                    <option value="{{ $a }}" {{ (string)$val === (string)$a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
                @if(!empty($val) && !in_array($val, $areasList))
                    <option value="{{ $val }}" selected>{{ $val }}</option>
                @endif
            </select>
            <button type="button" class="btn btn-outline-primary btn-quick-add-area px-2.5" title="Add New Area" style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-plus"></i>
            </button>
        </div>

    @else
        <div class="input-group">
            <span class="input-group-text bg-light text-muted" style="border-color: #cbd5e1; border-right: none; border-radius: 8px 0 0 8px; font-size: 0.88rem; width: 40px; justify-content: center; height: 38px;">
                <i class="{{ $iconClass }}"></i>
            </span>
            <input type="{{ $type === 'phone' ? 'tel' : ($type === 'email' ? 'email' : ($type === 'date' ? 'date' : ($type === 'number' ? 'number' : ($type === 'url' ? 'url' : 'text')))) }}" 
                name="{{ $key }}" id="field_{{ $key }}"
                value="{{ $val }}"
                class="form-control @error($key) is-invalid @enderror"
                placeholder="{{ $field->placeholder ?: '' }}"
                style="border-color: #cbd5e1; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.88rem; padding: 6px 12px; height: 38px; {{ $field->is_readonly ? 'background:#f8fafc; cursor:not-allowed;' : 'background:#fff;' }} {{ $key === 'customer_name_ur' ? 'direction:rtl; text-align:right; font-family:\'Noto Nastaliq Urdu\', serif;' : '' }}"
                {{ $field->is_required ? 'required' : '' }}
                {{ $field->is_readonly ? 'readonly' : '' }}>
        </div>
    @endif

    @error($key)
        <div class="text-danger small mt-1" style="font-size: 0.72rem;">{{ $message }}</div>
    @enderror
</div>
