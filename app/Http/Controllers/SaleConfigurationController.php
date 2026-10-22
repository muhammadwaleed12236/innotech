<?php

namespace App\Http\Controllers;

use App\Models\SaleFieldConfiguration;
use App\Services\SaleFieldService;
use Illuminate\Http\Request;

class SaleConfigurationController extends Controller
{
    protected $fieldService;

    public function __construct(SaleFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
        $this->middleware('auth');
    }

    /**
     * Display the Sales Screen & Taxes Configuration Dashboard
     */
    public function index(Request $request)
    {
        $fields = $this->fieldService->getAllConfigurations();
        $grouped = $fields->groupBy('section');

        // Quick statistics
        $stats = [
            'total' => $fields->count(),
            'enabled' => $fields->where('is_enabled', true)->count(),
            'disabled' => $fields->where('is_enabled', false)->count(),
            'taxes_count' => $fields->where('section', 'Taxes & Legal Details')->count(),
            'columns_count' => $fields->where('section', 'Item Table Columns')->count(),
            'summary_count' => $fields->where('section', 'Order Summary & Totals')->count(),
        ];

        // Specific tax settings
        $salesTaxField = $fields->firstWhere('field_key', 'sales_tax');
        $furtherTaxField = $fields->firstWhere('field_key', 'further_tax');
        $strnExemptionField = $fields->firstWhere('field_key', 'strn_exemption');
        $exclusiveGstField = $fields->firstWhere('field_key', 'exclusive_gst_amount');
        $badgesField = $fields->firstWhere('field_key', 'customer_tax_badges');

        return view('admin_panel.sales.configuration.index', compact(
            'fields',
            'grouped',
            'stats',
            'salesTaxField',
            'furtherTaxField',
            'strnExemptionField',
            'exclusiveGstField',
            'badgesField'
        ));
    }

    /**
     * Fast AJAX toggle switch handler
     */
    public function toggle(Request $request, $id)
    {
        $attribute = $request->input('attribute', 'is_enabled');
        $field = $this->fieldService->toggleAttribute((int) $id, $attribute);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Field '{$field->label}' updated.",
                'is_enabled' => $field->is_enabled,
                'attribute' => $attribute,
                'current_value' => $field->{$attribute},
            ]);
        }

        return back()->with('success', "Field '{$field->label}' updated successfully.");
    }

    /**
     * Update an individual configuration field
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'label' => 'required|string|max:150',
            'default_value' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_enabled' => 'nullable|boolean',
            'visible_in_create' => 'nullable|boolean',
            'visible_in_edit' => 'nullable|boolean',
            'visible_in_print' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        $data['visible_in_create'] = $request->boolean('visible_in_create');
        $data['visible_in_edit'] = $request->boolean('visible_in_edit');
        $data['visible_in_print'] = $request->boolean('visible_in_print');

        $field = $this->fieldService->updateField((int) $id, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Configuration for '{$field->label}' saved.",
            ]);
        }

        return back()->with('success', "Configuration for '{$field->label}' saved successfully.");
    }

    /**
     * Update Tax Defaults specifically
     */
    public function updateTaxDefaults(Request $request)
    {
        $data = $request->validate([
            'st_enabled' => 'nullable|boolean',
            'default_st_rate' => 'required|numeric|min:0|max:100',
            'ft_enabled' => 'nullable|boolean',
            'default_ft_rate' => 'required|numeric|min:0|max:100',
            'strn_exemption' => 'nullable|boolean',
            'exclusive_gst_enabled' => 'nullable|boolean',
            'badges_enabled' => 'nullable|boolean',
            'print_tax_enabled' => 'nullable|boolean',
        ]);

        $stField = SaleFieldConfiguration::where('field_key', 'sales_tax')->first();
        if ($stField) {
            $stField->update([
                'is_enabled' => $request->boolean('st_enabled'),
                'default_value' => number_format((float) $request->default_st_rate, 2, '.', ''),
            ]);
        }

        $ftField = SaleFieldConfiguration::where('field_key', 'further_tax')->first();
        if ($ftField) {
            $ftField->update([
                'is_enabled' => $request->boolean('ft_enabled'),
                'default_value' => number_format((float) $request->default_ft_rate, 2, '.', ''),
            ]);
        }

        $strnField = SaleFieldConfiguration::where('field_key', 'strn_exemption')->first();
        if ($strnField) {
            $strnField->update([
                'is_enabled' => $request->boolean('strn_exemption'),
            ]);
        }

        $exclField = SaleFieldConfiguration::where('field_key', 'exclusive_gst_amount')->first();
        if ($exclField) {
            $exclField->update([
                'is_enabled' => $request->boolean('exclusive_gst_enabled'),
            ]);
        }

        $badgeField = SaleFieldConfiguration::where('field_key', 'customer_tax_badges')->first();
        if ($badgeField) {
            $badgeField->update([
                'is_enabled' => $request->boolean('badges_enabled'),
            ]);
        }

        $printTaxField = SaleFieldConfiguration::where('field_key', 'print_tax_breakdown')->first();
        if ($printTaxField) {
            $printTaxField->update([
                'is_enabled' => $request->boolean('print_tax_enabled'),
            ]);
        }

        $this->fieldService->clearCache();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax configuration updated successfully.',
            ]);
        }

        return back()->with('success', 'Tax configuration updated successfully.');
    }

    /**
     * Bulk save all settings
     */
    public function updateBulk(Request $request)
    {
        $items = $request->input('fields', []);
        if (is_array($items) && !empty($items)) {
            $this->fieldService->updateBulk($items);
        }

        return back()->with('success', 'All sale fields & screen configuration saved successfully.');
    }

    /**
     * Reset all configurations back to system defaults
     */
    public function resetDefaults()
    {
        $this->fieldService->resetDefaults();
        return back()->with('success', 'Sale screen & tax configurations have been restored to defaults.');
    }
}
