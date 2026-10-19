<?php

namespace App\Http\Controllers;

use App\Models\CustomerFieldDefinition;
use App\Services\CustomerFieldService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DynamicFieldConfigurationController extends Controller
{
    protected $fieldService;

    public function __construct(CustomerFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
        $this->middleware('auth');
    }

    /**
     * Centralized Dynamic Fields & Module Customizer Hub
     */
    public function index(Request $request)
    {
        $activeModule = $request->get('module', 'customers');

        $modules = [
            'customers' => [
                'id'          => 'customers',
                'name'        => 'Customer Module',
                'icon'        => 'fas fa-users',
                'badge'       => 'Active & Dynamic',
                'badge_class' => 'bg-success text-white',
                'description' => 'Centrally configure customer fields, contact info, CNIC, NTN, STRN, custom attributes, visibility matrices, and exports.',
                'is_active'   => true,
                'fields_count'=> CustomerFieldDefinition::count(),
            ],
            'vendors' => [
                'id'          => 'vendors',
                'name'        => 'Vendor & Supplier Module',
                'icon'        => 'fas fa-truck',
                'badge'       => 'Ready for Dynamic Setup',
                'badge_class' => 'bg-info text-dark',
                'description' => 'Supplier profiles, ledger terms, vendor tax numbers, custom contact persons, and purchasing categories.',
                'is_active'   => false,
                'fields_count'=> 0,
            ],
            'sales' => [
                'id'          => 'sales',
                'name'        => 'Sales & Invoicing Module',
                'icon'        => 'fas fa-file-invoice-dollar',
                'badge'       => 'Planned Next',
                'badge_class' => 'bg-secondary text-white',
                'description' => 'Invoice custom fields, delivery notes, shipping terms, sales remarks, and POS screen custom inputs.',
                'is_active'   => false,
                'fields_count'=> 0,
            ],
            'products' => [
                'id'          => 'products',
                'name'        => 'Products Master & Inventory',
                'icon'        => 'fas fa-boxes',
                'badge'       => 'Planned Next',
                'badge_class' => 'bg-secondary text-white',
                'description' => 'Item custom attributes, batch numbers, manufacturer codes, technical specs, and warranty tags.',
                'is_active'   => false,
                'fields_count'=> 0,
            ],
            'employees' => [
                'id'          => 'employees',
                'name'        => 'HR & Employees',
                'icon'        => 'fas fa-user-tie',
                'badge'       => 'Planned Next',
                'badge_class' => 'bg-secondary text-white',
                'description' => 'Employee emergency contacts, passport/visa records, departmental metadata, and custom HR fields.',
                'is_active'   => false,
                'fields_count'=> 0,
            ],
        ];

        $fields = collect();
        $sections = [];

        if ($activeModule === 'customers') {
            $fields = CustomerFieldDefinition::sorted()->get();
            $sections = [
                'Basic Information',
                'Contact & Location',
                'Financial Information',
                'Tax & Legal Details',
                'Additional Contacts',
                'System Status',
                'Additional Information',
            ];
        }

        return view('admin_panel.dynamic_fields.index', compact('modules', 'activeModule', 'fields', 'sections'));
    }

    /**
     * Store newly defined dynamic field
     */
    public function store(Request $request)
    {
        $module = $request->input('module', 'customers');

        if ($module === 'customers') {
            $data = $request->validate([
                'label'             => 'required|string|max:100',
                'field_key'         => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
                'field_type'        => 'required|in:text,textarea,number,email,phone,date,dropdown,checkbox,url',
                'section'           => 'nullable|string|max:100',
                'placeholder'       => 'nullable|string|max:150',
                'help_text'         => 'nullable|string|max:255',
                'default_value'     => 'nullable|string',
                'options'           => 'nullable|string',
                'is_required'       => 'nullable|boolean',
                'is_unique'         => 'nullable|boolean',
                'is_enabled'        => 'nullable|boolean',
                'is_readonly'       => 'nullable|boolean',
                'visible_in_list'   => 'nullable|boolean',
                'visible_in_view'   => 'nullable|boolean',
                'visible_in_create' => 'nullable|boolean',
                'visible_in_edit'   => 'nullable|boolean',
                'visible_in_search' => 'nullable|boolean',
                'visible_in_export' => 'nullable|boolean',
            ]);

            // Auto-generate key if empty
            if (empty($data['field_key'])) {
                $baseKey = Str::snake(Str::slug($data['label'], '_'));
                $key = $baseKey;
                $i = 1;
                while (CustomerFieldDefinition::where('field_key', $key)->exists()) {
                    $key = $baseKey . '_' . $i++;
                }
                $data['field_key'] = $key;
            } else {
                $exists = CustomerFieldDefinition::where('field_key', $data['field_key'])->exists();
                if ($exists) {
                    return back()->with('error', "Field key '{$data['field_key']}' is already in use.")->withInput();
                }
            }

            // Parse options for dropdown
            if ($data['field_type'] === 'dropdown' && !empty($data['options'])) {
                $raw = explode("\n", str_replace("\r", "", $data['options']));
                $cleanOpts = [];
                foreach ($raw as $line) {
                    $trimmed = trim($line);
                    if ($trimmed !== '') {
                        $cleanOpts[$trimmed] = $trimmed;
                    }
                }
                $data['options'] = $cleanOpts;
            } else {
                $data['options'] = null;
            }

            $data['is_system'] = false;
            $data['sort_order'] = (CustomerFieldDefinition::max('sort_order') ?? 0) + 10;
            $data['section'] = !empty($data['section']) ? $data['section'] : 'Additional Information';

            CustomerFieldDefinition::create($data);
            $this->fieldService->clearCache();

            return redirect()->route('dynamic_fields.index', ['module' => 'customers'])
                ->with('success', "Dynamic field '{$data['label']}' added successfully.");
        }

        return back()->with('error', "Module '{$module}' is not currently active for custom fields.");
    }

    /**
     * Update an existing field definition
     */
    public function update(Request $request, $id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);

        $rules = [
            'label'             => 'required|string|max:100',
            'section'           => 'nullable|string|max:100',
            'placeholder'       => 'nullable|string|max:150',
            'help_text'         => 'nullable|string|max:255',
            'default_value'     => 'nullable|string',
            'options'           => 'nullable|string',
            'is_required'       => 'nullable|boolean',
            'is_unique'         => 'nullable|boolean',
            'is_enabled'        => 'nullable|boolean',
            'is_readonly'       => 'nullable|boolean',
            'visible_in_list'   => 'nullable|boolean',
            'visible_in_view'   => 'nullable|boolean',
            'visible_in_create' => 'nullable|boolean',
            'visible_in_edit'   => 'nullable|boolean',
            'visible_in_search' => 'nullable|boolean',
            'visible_in_export' => 'nullable|boolean',
        ];

        if (!$field->is_system) {
            $rules['field_type'] = 'required|in:text,textarea,number,email,phone,date,dropdown,checkbox,url';
        }

        $data = $request->validate($rules);

        if ($request->has('options')) {
            $raw = explode("\n", str_replace("\r", "", (string)$request->options));
            $cleanOpts = [];
            foreach ($raw as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $cleanOpts[$trimmed] = $trimmed;
                }
            }
            $data['options'] = $cleanOpts;
        }

        $field->update($data);
        $this->fieldService->clearCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Field '{$field->label}' updated successfully."]);
        }

        return redirect()->route('dynamic_fields.index', ['module' => 'customers'])
            ->with('success', "Field '{$field->label}' updated successfully.");
    }

    /**
     * AJAX toggle switch handler
     */
    public function toggle(Request $request, $id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);
        $attr = $request->input('attribute');

        $allowedAttributes = [
            'is_enabled',
            'is_required',
            'is_unique',
            'is_readonly',
            'visible_in_list',
            'visible_in_view',
            'visible_in_create',
            'visible_in_edit',
            'visible_in_search',
            'visible_in_export',
        ];

        if (!in_array($attr, $allowedAttributes)) {
            return response()->json(['success' => false, 'message' => 'Invalid attribute.'], 422);
        }

        $field->{$attr} = (bool) $request->input('value');
        $field->save();
        $this->fieldService->clearCache();

        return response()->json([
            'success'   => true,
            'message'   => "Updated {$field->label} ({$attr})",
            'attribute' => $attr,
            'value'     => $field->{$attr},
        ]);
    }

    /**
     * Reorder fields
     */
    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $fieldId) {
            CustomerFieldDefinition::where('id', $fieldId)->update(['sort_order' => $index + 1]);
        }

        $this->fieldService->clearCache();

        return response()->json(['success' => true, 'message' => 'Field order updated.']);
    }

    /**
     * Delete custom field safely
     */
    public function destroy($id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);

        if ($field->is_system) {
            return back()->with('error', "System field '{$field->label}' cannot be deleted as it is linked to core ERP tables.");
        }

        $label = $field->label;
        $field->delete();
        $this->fieldService->clearCache();

        return redirect()->route('dynamic_fields.index', ['module' => 'customers'])
            ->with('success', "Custom field '{$label}' removed successfully.");
    }

    /**
     * Reset default fields
     */
    public function resetDefaults()
    {
        $this->fieldService->seedDefaultDefinitions();
        $this->fieldService->clearCache();

        return redirect()->route('dynamic_fields.index', ['module' => 'customers'])
            ->with('success', 'Default customer fields have been restored successfully.');
    }
}
