<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerCustomFieldValue;
use App\Models\CustomerFieldDefinition;
use App\Models\CustomerType;
use App\Models\SalesOfficer;
use App\Models\Zone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerFieldService
{
    const CACHE_KEY = 'customer_field_definitions_all';
    const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all field definitions (cached)
     */
    public function getAllDefinitions(bool $refresh = false)
    {
        if ($refresh) {
            $this->clearCache();
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            // If empty, auto-seed default fields on first run
            if (CustomerFieldDefinition::where('field_key', 'customer_name')->doesntExist() || CustomerFieldDefinition::count() < 10) {
                $this->seedDefaultDefinitions();
            }

            return CustomerFieldDefinition::sorted()->get();
        });
    }

    /**
     * Clear definitions cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get enabled fields for a specific context and user
     * Contexts: 'list', 'view', 'create', 'edit', 'search', 'export'
     */
    public function getFieldsForContext(string $context, $user = null)
    {
        $all = $this->getAllDefinitions();

        return $all->filter(function (CustomerFieldDefinition $field) use ($context, $user) {
            if (!$field->is_enabled) {
                return false;
            }

            // Check context visibility
            $visibilityProperty = "visible_in_{$context}";
            if (isset($field->{$visibilityProperty}) && !$field->{$visibilityProperty}) {
                return false;
            }

            // Check permissions
            if (in_array($context, ['create', 'edit'])) {
                return $field->canEdit($user);
            }

            return $field->canView($user);
        })->values();
    }

    /**
     * Group fields by section for Create/Edit forms
     */
    public function getGroupedFieldsForForm(string $context, $user = null): array
    {
        $fields = $this->getFieldsForContext($context, $user);
        $grouped = [];

        foreach ($fields as $field) {
            $section = !empty($field->section) ? $field->section : 'General Information';
            if (!isset($grouped[$section])) {
                $grouped[$section] = [];
            }
            $grouped[$section][] = $field;
        }

        return $grouped;
    }

    /**
     * Build dynamic validation rules
     */
    public function buildValidationRules(string $context = 'create', $customerId = null, $user = null): array
    {
        $fields = $this->getFieldsForContext($context, $user);
        $rules = [];

        foreach ($fields as $field) {
            $key = $field->field_key;
            $fieldRules = [];

            // Required or Nullable
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Unique rule
            if ($field->is_unique) {
                if ($field->is_system && $field->system_column) {
                    $uniqueRule = "unique:customers,{$field->system_column}";
                    if ($customerId) {
                        $uniqueRule .= ",{$customerId}";
                    }
                    $fieldRules[] = $uniqueRule;
                } else {
                    // Custom field uniqueness validation
                    $fieldRules[] = function ($attribute, $value, $fail) use ($field, $customerId) {
                        if ($value === null || $value === '') {
                            return;
                        }
                        $query = CustomerCustomFieldValue::where('field_definition_id', $field->id)
                            ->where('value', $value);
                        if ($customerId) {
                            $query->where('customer_id', '!=', $customerId);
                        }
                        if ($query->exists()) {
                            $fail("The {$field->label} has already been taken.");
                        }
                    };
                }
            }

            // Type rules
            switch ($field->field_type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'dropdown':
                    // If options are configured, we can validate in:options
                    break;
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Save dynamic fields for a customer (both native and custom fields)
     */
    public function saveCustomerFields(Customer $customer, array $input, bool $isEdit = false, $user = null): void
    {
        $context = $isEdit ? 'edit' : 'create';
        $activeFields = $this->getFieldsForContext($context, $user);

        $systemAttributes = [];
        $customValues = [];

        foreach ($activeFields as $field) {
            $key = $field->field_key;

            // If field is readonly in edit mode, do not overwrite
            if ($isEdit && $field->is_readonly) {
                continue;
            }

            // Check if key is present in request input
            $hasInput = array_key_exists($key, $input);
            $val = $hasInput ? $input[$key] : null;

            if ($field->is_system && $field->system_column) {
                $col = $field->system_column;
                if ($hasInput) {
                    $systemAttributes[$col] = $val;
                }
            } else {
                // Custom field
                if ($hasInput) {
                    $customValues[$field->id] = [
                        'field_key' => $field->field_key,
                        'value'     => is_array($val) ? json_encode($val) : $val,
                    ];
                }
            }
        }

        // Also capture province, city, area directly if provided
        foreach (['province', 'city', 'area'] as $locKey) {
            if (array_key_exists($locKey, $input)) {
                $systemAttributes[$locKey] = !empty($input[$locKey]) ? trim($input[$locKey]) : null;
            }
        }

        // Auto-assign customer_id if not present during creation
        if (!$isEdit && empty($customer->customer_id) && empty($systemAttributes['customer_id'])) {
            $maxId = (int) Customer::max('id') + 1;
            $systemAttributes['customer_id'] = 'CUST-' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
        }

        // Ensure party_type has default
        if (empty($customer->party_type) && empty($systemAttributes['party_type'])) {
            $systemAttributes['party_type'] = 'customer';
        }

        // Ensure opening_balance is not null for new records
        if (!$isEdit && (!isset($systemAttributes['opening_balance']) || $systemAttributes['opening_balance'] === null || $systemAttributes['opening_balance'] === '')) {
            $systemAttributes['opening_balance'] = 0;
        }

        // Update system attributes on customer model
        if (!empty($systemAttributes)) {
            $customer->fill($systemAttributes);
            $customer->save();
        }

        // Save custom field values
        foreach ($customValues as $fieldDefId => $data) {
            CustomerCustomFieldValue::updateOrCreate(
                [
                    'customer_id'         => $customer->id,
                    'field_definition_id' => $fieldDefId,
                ],
                [
                    'field_key' => $data['field_key'],
                    'value'     => $data['value'],
                ]
            );
        }
    }

    /**
     * Get raw or formatted value of a field for a customer
     */
    public function getFieldValue(Customer $customer, CustomerFieldDefinition $field, bool $formatted = false)
    {
        $val = null;

        if ($field->is_system && $field->system_column) {
            $col = $field->system_column;
            $val = $customer->{$col};
        } else {
            $val = $customer->getCustomValue($field->field_key);
        }

        if ($val === null || $val === '') {
            return $formatted ? '-' : null;
        }

        if (!$formatted) {
            return $val;
        }

        // Format for display
        if ($field->system_column === 'zone') {
            $zone = Zone::find($val);
            return $zone ? $zone->zone : $val;
        }

        if ($field->system_column === 'sales_officer_id') {
            $officer = SalesOfficer::find($val);
            return $officer ? $officer->name : $val;
        }

        if ($field->field_type === 'number') {
            if ($field->system_column === 'balance_range') {
                return (float)$val == 0 ? 'Unlimited' : number_format((float)$val, 2);
            }
            return number_format((float)$val, 2);
        }

        if ($field->field_type === 'date' && !empty($val)) {
            try {
                return \Carbon\Carbon::parse($val)->format('d M Y');
            } catch (\Exception $e) {
                return $val;
            }
        }

        return (string)$val;
    }

    /**
     * Pre-load dropdown data needed by forms (zones, types, sales officers)
     */
    public function getDropdownLookupData(): array
    {
        return [
            'customer_types' => CustomerType::orderBy('name')->pluck('name', 'name')->toArray(),
            'zones'          => Zone::orderBy('zone')->pluck('zone', 'id')->toArray(),
            'sales_officers' => SalesOfficer::orderBy('name')->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Seed default customer fields matching existing ERP schema
     */
    public function seedDefaultDefinitions(): void
    {
        $defaults = [
            [
                'field_key'         => 'party_type',
                'label'             => 'Party Category (Role)',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'party_type',
                'section'           => 'Basic Information',
                'options'           => ['customer', 'vendor', 'both'],
                'default_value'     => 'customer',
                'placeholder'       => 'Select Party Role',
                'help_text'         => 'Select if Customer Only, Vendor Only, or Both',
                'is_required'       => true,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 5,
            ],
            [
                'field_key'         => 'customer_id',
                'label'             => 'Customer Code',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'customer_id',
                'section'           => 'Basic Information',
                'placeholder'       => 'Auto Generated',
                'help_text'         => 'System identifier for the customer (e.g. CUST-0001)',
                'is_required'       => false,
                'is_unique'         => true,
                'is_enabled'        => true,
                'is_readonly'       => true,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 10,
            ],
            [
                'field_key'         => 'customer_type',
                'label'             => 'Customer Type',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'customer_type',
                'section'           => 'Basic Information',
                'placeholder'       => 'Select Customer Type',
                'help_text'         => 'Category e.g. Main Customer, Distributor, Retailer',
                'options'           => ['Main Customer', 'Distributor', 'Retailer', 'Wholesale', 'Walk-in'],
                'is_required'       => true,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => true,
                'visible_in_export' => true,
                'sort_order'        => 20,
            ],
            [
                'field_key'         => 'customer_name',
                'label'             => 'Customer / Business Name',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'customer_name',
                'section'           => 'Basic Information',
                'placeholder'       => 'Enter customer or company name',
                'help_text'         => 'Official customer or entity title',
                'is_required'       => true,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 30,
            ],
            [
                'field_key'         => 'customer_name_ur',
                'label'             => 'Urdu Name (کسٹمر نام)',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'customer_name_ur',
                'section'           => 'Basic Information',
                'placeholder'       => 'یہاں اردو نام درج کریں',
                'help_text'         => 'Optional name in Nastaliq/Urdu script for local printing',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => false,
                'sort_order'        => 40,
            ],
            [
                'field_key'         => 'mobile',
                'label'             => 'Primary Mobile / Phone',
                'field_type'        => 'phone',
                'is_system'         => true,
                'system_column'     => 'mobile',
                'section'           => 'Contact & Location',
                'placeholder'       => '0300-1234567',
                'help_text'         => 'Primary contact number',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 50,
            ],
            [
                'field_key'         => 'zone',
                'label'             => 'Region / Zone',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'zone',
                'section'           => 'Contact & Location',
                'placeholder'       => 'Select Region / Zone',
                'help_text'         => 'Geographical zone or sales territory',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 60,
            ],
            [
                'field_key'         => 'province',
                'label'             => 'Province',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'province',
                'section'           => 'Contact & Location',
                'placeholder'       => 'Select Province',
                'help_text'         => 'Administrative province / state (Optional)',
                'options'           => ['Sindh', 'Punjab', 'Khyber Pakhtunkhwa', 'Balochistan', 'Islamabad Capital Territory', 'Azad Jammu & Kashmir', 'Gilgit-Baltistan'],
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => true,
                'visible_in_export' => true,
                'sort_order'        => 65,
            ],
            [
                'field_key'         => 'city',
                'label'             => 'City',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'city',
                'section'           => 'Contact & Location',
                'placeholder'       => 'e.g. Karachi, Hyderabad',
                'help_text'         => 'City or metropolitan district (Optional)',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => true,
                'visible_in_export' => true,
                'sort_order'        => 66,
            ],
            [
                'field_key'         => 'area',
                'label'             => 'Area / Town',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'area',
                'section'           => 'Contact & Location',
                'placeholder'       => 'e.g. Latifabad, Saddar, Clifton',
                'help_text'         => 'Neighborhood, town or local area (Optional)',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => true,
                'visible_in_export' => true,
                'sort_order'        => 67,
            ],
            [
                'field_key'         => 'address',
                'label'             => 'Address',
                'field_type'        => 'textarea',
                'is_system'         => true,
                'system_column'     => 'address',
                'section'           => 'Contact & Location',
                'placeholder'       => 'Shop #, Street Area, City',
                'help_text'         => 'Physical delivery or billing address',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 70,
            ],
            [
                'field_key'         => 'opening_balance',
                'label'             => 'Opening Balance (Dr)',
                'field_type'        => 'number',
                'is_system'         => true,
                'system_column'     => 'opening_balance',
                'section'           => 'Financial Details',
                'default_value'     => '0',
                'placeholder'       => '0.00',
                'help_text'         => 'Initial debit balance when customer was onboarded',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 80,
            ],
            [
                'field_key'         => 'balance_range',
                'label'             => 'Credit Limit',
                'field_type'        => 'number',
                'is_system'         => true,
                'system_column'     => 'balance_range',
                'section'           => 'Financial Details',
                'default_value'     => '0',
                'placeholder'       => '0 = Unlimited',
                'help_text'         => 'Maximum allowed outstanding balance. Enter 0 for unlimited.',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 90,
            ],
            [
                'field_key'         => 'reminder_day',
                'label'             => 'Payment Reminder Day',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'reminder_day',
                'section'           => 'Financial Details',
                'options'           => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'placeholder'       => 'Select Reminder Day',
                'help_text'         => 'Fixed weekly day for automated payment follow-up reminders',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 100,
            ],
            [
                'field_key'         => 'cnic',
                'label'             => 'CNIC / Identity No',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'cnic',
                'section'           => 'Tax & Legal Details',
                'placeholder'       => '42101-1234567-1',
                'help_text'         => 'National identity card number of proprietor or company',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 110,
            ],
            [
                'field_key'         => 'filer_type',
                'label'             => 'Tax Status (Filer)',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'filer_type',
                'section'           => 'Tax & Legal Details',
                'options'           => ['filer', 'non filer', 'exempt'],
                'placeholder'       => 'Select Tax Status',
                'help_text'         => 'Active Filer, Non Filer or Tax Exempt',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 120,
            ],
            [
                'field_key'         => 'ntn_number',
                'label'             => 'NTN Number',
                'field_type'        => 'text',
                'is_system'         => false,
                'section'           => 'Tax & Legal Details',
                'placeholder'       => '1234567-8',
                'help_text'         => 'National Tax Number for FBR sales tax compliance',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 130,
            ],
            [
                'field_key'         => 'strn_number',
                'label'             => 'STRN Number',
                'field_type'        => 'text',
                'is_system'         => false,
                'section'           => 'Tax & Legal Details',
                'placeholder'       => 'Sales Tax Registration No',
                'help_text'         => 'Sales Tax Registration Number for GST invoices',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 140,
            ],
            [
                'field_key'         => 'contact_person',
                'label'             => 'Contact Person Name',
                'field_type'        => 'text',
                'is_system'         => true,
                'system_column'     => 'contact_person',
                'section'           => 'Additional Information',
                'placeholder'       => 'Manager / Representative Name',
                'help_text'         => 'Name of primary coordinator or branch manager',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 150,
            ],
            [
                'field_key'         => 'email_address',
                'label'             => 'Email Address',
                'field_type'        => 'email',
                'is_system'         => true,
                'system_column'     => 'email_address',
                'section'           => 'Additional Information',
                'placeholder'       => 'customer@example.com',
                'help_text'         => 'Official email address for electronic invoices',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 160,
            ],
            [
                'field_key'         => 'status',
                'label'             => 'Account Status',
                'field_type'        => 'dropdown',
                'is_system'         => true,
                'system_column'     => 'status',
                'section'           => 'Additional Information',
                'options'           => ['active', 'inactive'],
                'default_value'     => 'active',
                'help_text'         => 'Active or Inactive status in customer directory',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => true,
                'visible_in_view'   => true,
                'visible_in_create' => false,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 170,
            ],
            [
                'field_key'         => 'notes',
                'label'             => 'Internal Notes',
                'field_type'        => 'textarea',
                'is_system'         => false,
                'section'           => 'Additional Information',
                'placeholder'       => 'Add any specific terms, references, or instructions',
                'help_text'         => 'Internal ERP remarks visible to authorized staff',
                'is_required'       => false,
                'is_unique'         => false,
                'is_enabled'        => true,
                'is_readonly'       => false,
                'visible_in_list'   => false,
                'visible_in_view'   => true,
                'visible_in_create' => true,
                'visible_in_edit'   => true,
                'visible_in_search' => false,
                'visible_in_export' => true,
                'sort_order'        => 180,
            ],
        ];
        foreach ($defaults as $item) {
            CustomerFieldDefinition::updateOrCreate(
                ['field_key' => $item['field_key']],
                $item
            );
        }

        $this->clearCache();
    }
}
