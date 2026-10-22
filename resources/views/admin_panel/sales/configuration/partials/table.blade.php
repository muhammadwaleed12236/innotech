<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 250px;">Screen Element &amp; Key</th>
                <th style="width: 150px;">Section</th>
                <th>Description</th>
                <th class="text-center" style="width: 110px;">Default / Rate</th>
                <th class="text-center" style="width: 90px;" title="Master Enable/Disable toggle">Active</th>
                <th class="text-center" style="width: 90px;" title="Show on Create Sale Screen (/sale/create)">Create</th>
                <th class="text-center" style="width: 90px;" title="Show on Edit Sale Screen">Edit</th>
                <th class="text-center" style="width: 90px;" title="Show on A4/Thermal Prints">Print</th>
                <th class="text-center" style="width: 80px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tableFields as $f)
                @php
                    $isTax = $f->section === 'Taxes & Legal Details';
                    $isCol = $f->section === 'Item Table Columns';
                    $isSummary = $f->section === 'Order Summary & Totals';
                    $badgeClass = $isTax ? 'badge-tax' : ($isCol ? 'badge-col' : ($isSummary ? 'badge-summary' : 'badge-field'));
                @endphp
                <tr id="row-field-{{ $f->id }}">
                    <!-- Element & Key -->
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 13.5px;">{{ $f->label }}</span>
                                <code class="small text-muted" style="font-size: 11px;">{{ $f->field_key }}</code>
                            </div>
                        </div>
                    </td>

                    <!-- Section -->
                    <td>
                        <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 11px;">
                            {{ $f->section }}
                        </span>
                    </td>

                    <!-- Description -->
                    <td>
                        <small class="text-muted d-block" style="font-size: 12px; line-height: 1.35;">
                            {{ $f->description ?? '—' }}
                        </small>
                    </td>

                    <!-- Default / Rate -->
                    <td class="text-center">
                        @if($f->field_type === 'tax' || is_numeric($f->default_value))
                            <span class="badge bg-light text-dark border fw-bold px-2 py-1 font-monospace" style="font-size: 12px;">
                                {{ $f->default_value ? $f->default_value . '%' : '0%' }}
                            </span>
                        @elseif($f->default_value)
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">
                                {{ $f->default_value }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    <!-- Master Active Switch -->
                    <td class="text-center">
                        <label class="switch-sm" title="Turn on/off across system">
                            <input type="checkbox" class="ajax-field-toggle" data-id="{{ $f->id }}" data-attr="is_enabled" value="1" {{ $f->is_enabled ? 'checked' : '' }}>
                            <span class="slider-sm"></span>
                        </label>
                    </td>

                    <!-- Create Screen Switch -->
                    <td class="text-center">
                        <label class="switch-sm" title="Show/hide on /sale/create">
                            <input type="checkbox" class="ajax-field-toggle" data-id="{{ $f->id }}" data-attr="visible_in_create" value="1" {{ $f->visible_in_create ? 'checked' : '' }}>
                            <span class="slider-sm"></span>
                        </label>
                    </td>

                    <!-- Edit Screen Switch -->
                    <td class="text-center">
                        <label class="switch-sm" title="Show/hide on edit sale screen">
                            <input type="checkbox" class="ajax-field-toggle" data-id="{{ $f->id }}" data-attr="visible_in_edit" value="1" {{ $f->visible_in_edit ? 'checked' : '' }}>
                            <span class="slider-sm"></span>
                        </label>
                    </td>

                    <!-- Print Switch -->
                    <td class="text-center">
                        <label class="switch-sm" title="Show/hide on A4 and Thermal prints">
                            <input type="checkbox" class="ajax-field-toggle" data-id="{{ $f->id }}" data-attr="visible_in_print" value="1" {{ $f->visible_in_print ? 'checked' : '' }}>
                            <span class="slider-sm"></span>
                        </label>
                    </td>

                    <!-- Action -->
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary p-1 px-2 btn-edit-field" 
                                data-field="{{ json_encode($f) }}" title="Edit field properties">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fs-3 mb-2 d-block opacity-50"></i>
                        No configuration fields found for this category.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
