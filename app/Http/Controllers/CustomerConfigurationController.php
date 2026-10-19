<?php

namespace App\Http\Controllers;

use App\Models\CustomerFieldDefinition;
use App\Services\CustomerFieldService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerConfigurationController extends Controller
{
    protected $fieldService;

    public function __construct(CustomerFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
        // Require customer view/edit permission
        $this->middleware('permission:customers.view|customers.edit');
    }

    /**
     * Display customer fields configuration dashboard
     */
    public function index()
    {
        return redirect()->route('dynamic_fields.index', ['module' => 'customers']);
    }

    /**
     * Store a newly created custom field definition
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'label'             => 'required|string|max:100',
            'field_key'         => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'field_type'        => 'required|in:text,textarea,number,email,phone,date,dropdown,checkbox,url',
            'section'           => 'nullable|string|max:100',
            'default_value'     => 'nullable|string|max:255',
            'placeholder'       => 'nullable|string|max:150',
            'help_text'         => 'nullable|string|max:255',
            'options'           => 'nullable|string', // Raw text or JSON
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
            'sort_order'        => 'nullable|integer',
        ]);

        // Generate field_key if not provided
        $fieldKey = !empty($data['field_key'])
            ? Str::slug($data['field_key'], '_')
            : Str::slug($data['label'], '_');

        // Ensure unique field_key
        $originalKey = $fieldKey;
        $counter = 1;
        while (CustomerFieldDefinition::where('field_key', $fieldKey)->exists()) {
            $fieldKey = $originalKey . '_' . $counter++;
        }
        $data['field_key'] = $fieldKey;

        // Custom field is never a system column
        $data['is_system'] = false;
        $data['system_column'] = null;

        // Process dropdown options if provided
        if (!empty($data['options'])) {
            $rawOpts = explode("\n", str_replace("\r", "", $data['options']));
            $cleanOpts = array_values(array_filter(array_map('trim', $rawOpts)));
            $data['options'] = $cleanOpts;
        } else {
            $data['options'] = null;
        }

        // Booleans
        $data['is_required']       = $request->boolean('is_required');
        $data['is_unique']         = $request->boolean('is_unique');
        $data['is_enabled']        = $request->has('is_enabled') ? $request->boolean('is_enabled') : true;
        $data['is_readonly']       = $request->boolean('is_readonly');
        $data['visible_in_list']   = $request->boolean('visible_in_list');
        $data['visible_in_view']   = $request->has('visible_in_view') ? $request->boolean('visible_in_view') : true;
        $data['visible_in_create'] = $request->has('visible_in_create') ? $request->boolean('visible_in_create') : true;
        $data['visible_in_edit']   = $request->has('visible_in_edit') ? $request->boolean('visible_in_edit') : true;
        $data['visible_in_search'] = $request->boolean('visible_in_search');
        $data['visible_in_export'] = $request->boolean('visible_in_export');
        $data['sort_order']        = (int) ($data['sort_order'] ?? (CustomerFieldDefinition::max('sort_order') + 10));

        CustomerFieldDefinition::create($data);
        $this->fieldService->clearCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Custom field created successfully.']);
        }

        return redirect()->route('customers.configuration.index')
            ->with('success', "Field '{$data['label']}' added successfully.");
    }

    /**
     * Update an existing field definition
     */
    public function update(Request $request, $id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);

        $rules = [
            'label'             => 'required|string|max:100',
            'field_type'        => 'required|in:text,textarea,number,email,phone,date,dropdown,checkbox,url',
            'section'           => 'nullable|string|max:100',
            'default_value'     => 'nullable|string|max:255',
            'placeholder'       => 'nullable|string|max:150',
            'help_text'         => 'nullable|string|max:255',
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
            'sort_order'        => 'nullable|integer',
        ];

        // Only custom fields can change their field_key
        if (!$field->is_system) {
            $rules['field_key'] = 'required|string|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:customer_field_definitions,field_key,' . $field->id;
        }

        $data = $request->validate($rules);

        // Process options
        if (!empty($data['options'])) {
            $rawOpts = explode("\n", str_replace("\r", "", $data['options']));
            $cleanOpts = array_values(array_filter(array_map('trim', $rawOpts)));
            $data['options'] = $cleanOpts;
        } else {
            $data['options'] = null;
        }

        // Booleans
        $data['is_required']       = $request->boolean('is_required');
        $data['is_unique']         = $request->boolean('is_unique');
        $data['is_enabled']        = $request->boolean('is_enabled');
        $data['is_readonly']       = $request->boolean('is_readonly');
        $data['visible_in_list']   = $request->boolean('visible_in_list');
        $data['visible_in_view']   = $request->boolean('visible_in_view');
        $data['visible_in_create'] = $request->boolean('visible_in_create');
        $data['visible_in_edit']   = $request->boolean('visible_in_edit');
        $data['visible_in_search'] = $request->boolean('visible_in_search');
        $data['visible_in_export'] = $request->boolean('visible_in_export');
        $data['sort_order']        = (int) ($data['sort_order'] ?? $field->sort_order);

        // System field protection: cannot change field_key or is_system
        if ($field->is_system) {
            unset($data['field_key'], $data['is_system'], $data['system_column']);
        }

        $field->update($data);
        $this->fieldService->clearCache();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Field '{$field->label}' updated successfully."]);
        }

        return redirect()->route('customers.configuration.index')
            ->with('success', "Field '{$field->label}' updated successfully.");
    }

    /**
     * Quick toggle enabled status
     */
    public function toggle($id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);
        $field->is_enabled = !$field->is_enabled;
        $field->save();

        $this->fieldService->clearCache();

        $state = $field->is_enabled ? 'enabled' : 'disabled';
        return response()->json([
            'success'    => true,
            'is_enabled' => $field->is_enabled,
            'message'    => "Field '{$field->label}' has been {$state}."
        ]);
    }

    /**
     * Bulk reorder fields
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        foreach ($request->order as $sort => $id) {
            CustomerFieldDefinition::where('id', $id)->update(['sort_order' => $sort * 10]);
        }

        $this->fieldService->clearCache();

        return response()->json(['success' => true, 'message' => 'Field order updated successfully.']);
    }

    /**
     * Soft delete a custom field
     */
    public function destroy($id)
    {
        $field = CustomerFieldDefinition::findOrFail($id);

        if ($field->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'System fields cannot be deleted. You can disable this field instead.'
            ], 422);
        }

        // Soft delete definition to retain historical values
        $field->delete();
        $this->fieldService->clearCache();

        return response()->json([
            'success' => true,
            'message' => "Custom field '{$field->label}' deleted successfully."
        ]);
    }

    /**
     * Reset default fields to system factory settings
     */
    public function resetDefaults()
    {
        $this->fieldService->seedDefaultDefinitions();

        return redirect()->route('customers.configuration.index')
            ->with('success', 'Default fields have been restored.');
    }
}
