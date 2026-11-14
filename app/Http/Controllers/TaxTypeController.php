<?php

namespace App\Http\Controllers;

use App\Models\TaxType;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaxTypeController extends Controller
{
    /**
     * Display a listing of Tax Types
     */
    public function index(Request $request)
    {
        $query = TaxType::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == '1');
        }

        if ($request->filled('tax_applied_on')) {
            $query->where('tax_applied_on', $request->tax_applied_on);
        }

        $taxTypes = $query->withCount(['products', 'purchaseItems'])
            ->orderBy('is_default', 'desc')
            ->orderBy('code', 'asc')
            ->get();

        $stats = [
            'total' => TaxType::count(),
            'active' => TaxType::where('status', true)->count(),
            'third_schedule' => TaxType::where(function($q) {
                $q->where('tax_applied_on', 'retail_value')->orWhere('mrp_tax_active', true);
            })->count(),
            'standard' => TaxType::where('tax_applied_on', 'trade_value')->where('st_purchase_active', true)->count(),
            'exempt' => TaxType::where('st_purchase_active', false)->count(),
        ];

        return view('admin_panel.tax_types.index', compact('taxTypes', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Suggest next code (e.g. 0015)
        $lastCode = TaxType::orderBy('id', 'desc')->value('code');
        $num = (int) preg_replace('/[^0-9]/', '', (string)$lastCode);
        $nextCode = str_pad($num + 1, 4, '0', STR_PAD_LEFT);

        return view('admin_panel.tax_types.create', compact('nextCode'));
    }

    /**
     * Store new Tax Type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'                       => 'required|string|max:50|unique:tax_types,code',
            'title'                      => 'required|string|max:255',
            'st_purchase_active'         => 'nullable|boolean',
            'st_purchase_rate'           => 'nullable|numeric|min:0|max:100',
            'st_sale_active'             => 'nullable|boolean',
            'st_sale_rate'               => 'nullable|numeric|min:0|max:100',
            'mrp_tax_active'             => 'nullable|boolean',
            'mrp_tax_rate'               => 'nullable|numeric|min:0|max:100',
            'unreg_st_active'            => 'nullable|boolean',
            'unreg_st_rate'              => 'nullable|numeric|min:0|max:100',
            'unreg_st_applied_on'        => 'required|in:purchases,sales,both',
            'unreg_client_mode'          => 'required|in:add_unreg,less_reg',
            'excise_duty_active'         => 'nullable|boolean',
            'excise_duty_rate'           => 'nullable|numeric|min:0|max:100',
            'adv_tax_purchase_active'    => 'nullable|boolean',
            'adv_tax_purchase_rate'      => 'nullable|numeric|min:0|max:100',
            'adv_tax_sale_active'        => 'nullable|boolean',
            'adv_tax_sale_filer_rate'    => 'nullable|numeric|min:0|max:100',
            'adv_tax_sale_non_filer_rate'=> 'nullable|numeric|min:0|max:100',
            'adv_tax_applied_on'         => 'required|in:gross_amount,net_tp,gross_tp,net_retail,gross_retail',
            'applied_from'               => 'nullable|date',
            'include_special_disc'       => 'nullable|boolean',
            'tax_applied_on'             => 'required|in:trade_value,retail_value',
            'is_default'                 => 'nullable|boolean',
            'status'                     => 'nullable|boolean',
            'notes'                      => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Booleans fallback
            $data = $this->formatBooleanData($request);

            if (!empty($data['is_default'])) {
                TaxType::where('is_default', true)->update(['is_default' => false]);
            }

            $taxType = TaxType::create($data);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tax Type successfully created!',
                    'data' => $taxType
                ]);
            }

            return redirect()->route('tax-types.index')->with('success', "Tax Type [{$taxType->code} - {$taxType->title}] created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Error creating Tax Type: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(TaxType $taxType)
    {
        return view('admin_panel.tax_types.edit', compact('taxType'));
    }

    /**
     * Update Tax Type
     */
    public function update(Request $request, TaxType $taxType)
    {
        $validated = $request->validate([
            'code'                       => ['required', 'string', 'max:50', Rule::unique('tax_types')->ignore($taxType->id)],
            'title'                      => 'required|string|max:255',
            'st_purchase_active'         => 'nullable|boolean',
            'st_purchase_rate'           => 'nullable|numeric|min:0|max:100',
            'st_sale_active'             => 'nullable|boolean',
            'st_sale_rate'               => 'nullable|numeric|min:0|max:100',
            'mrp_tax_active'             => 'nullable|boolean',
            'mrp_tax_rate'               => 'nullable|numeric|min:0|max:100',
            'unreg_st_active'            => 'nullable|boolean',
            'unreg_st_rate'              => 'nullable|numeric|min:0|max:100',
            'unreg_st_applied_on'        => 'required|in:purchases,sales,both',
            'unreg_client_mode'          => 'required|in:add_unreg,less_reg',
            'excise_duty_active'         => 'nullable|boolean',
            'excise_duty_rate'           => 'nullable|numeric|min:0|max:100',
            'adv_tax_purchase_active'    => 'nullable|boolean',
            'adv_tax_purchase_rate'      => 'nullable|numeric|min:0|max:100',
            'adv_tax_sale_active'        => 'nullable|boolean',
            'adv_tax_sale_filer_rate'    => 'nullable|numeric|min:0|max:100',
            'adv_tax_sale_non_filer_rate'=> 'nullable|numeric|min:0|max:100',
            'adv_tax_applied_on'         => 'required|in:gross_amount,net_tp,gross_tp,net_retail,gross_retail',
            'applied_from'               => 'nullable|date',
            'include_special_disc'       => 'nullable|boolean',
            'tax_applied_on'             => 'required|in:trade_value,retail_value',
            'is_default'                 => 'nullable|boolean',
            'status'                     => 'nullable|boolean',
            'notes'                      => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $data = $this->formatBooleanData($request);

            if (!empty($data['is_default'])) {
                TaxType::where('id', '!=', $taxType->id)->where('is_default', true)->update(['is_default' => false]);
            }

            $taxType->update($data);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tax Type updated successfully!',
                    'data' => $taxType
                ]);
            }

            return redirect()->route('tax-types.index')->with('success', "Tax Type [{$taxType->code}] updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Error updating Tax Type: ' . $e->getMessage());
        }
    }

    /**
     * Remove Tax Type
     */
    public function destroy(TaxType $taxType)
    {
        $attachedProducts = Product::where('tax_type_id', $taxType->id)->count();
        if ($attachedProducts > 0) {
            return back()->with('error', "Cannot delete Tax Type [{$taxType->code}]. It is attached to {$attachedProducts} product(s). Please reassign them first.");
        }

        $taxType->delete();
        return redirect()->route('tax-types.index')->with('success', "Tax Type [{$taxType->code}] deleted successfully.");
    }

    /**
     * Toggle status via AJAX
     */
    public function toggleStatus(Request $request, TaxType $taxType)
    {
        $taxType->status = !$taxType->status;
        $taxType->save();

        return response()->json([
            'success' => true,
            'status' => (bool)$taxType->status,
            'message' => 'Tax Type status updated successfully'
        ]);
    }

    /**
     * API JSON list of active tax types for front-end JS
     */
    public function apiList()
    {
        $types = TaxType::active()
            ->orderBy('code', 'asc')
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'code' => $t->code,
                    'title' => $t->title,
                    'label' => "[{$t->code}] {$t->title}",
                    'st_purchase_active' => (bool) $t->st_purchase_active,
                    'st_purchase_rate' => (float) $t->st_purchase_rate,
                    'st_sale_active' => (bool) $t->st_sale_active,
                    'st_sale_rate' => (float) $t->st_sale_rate,
                    'mrp_tax_active' => (bool) $t->mrp_tax_active,
                    'mrp_tax_rate' => (float) $t->mrp_tax_rate,
                    'unreg_st_active' => (bool) $t->unreg_st_active,
                    'unreg_st_rate' => (float) $t->unreg_st_rate,
                    'adv_tax_purchase_active' => (bool) $t->adv_tax_purchase_active,
                    'adv_tax_purchase_rate' => (float) $t->adv_tax_purchase_rate,
                    'adv_tax_sale_filer_rate' => (float) $t->adv_tax_sale_filer_rate,
                    'adv_tax_sale_non_filer_rate' => (float) $t->adv_tax_sale_non_filer_rate,
                    'tax_applied_on' => $t->tax_applied_on, // 'trade_value' or 'retail_value'
                    'is_third_schedule' => $t->isThirdSchedule(),
                    'is_default' => (bool) $t->is_default,
                    'effective_purchase_gst' => $t->getPurchaseGstRate(),
                    'effective_purchase_wht' => $t->getPurchaseAdvTaxRate(),
                ];
            });

        return response()->json([
            'success' => true,
            'tax_types' => $types
        ]);
    }

    /**
     * Prepare data array from request with booleans and numeric sanitization
     */
    protected function formatBooleanData(Request $request): array
    {
        return [
            'code'                        => trim($request->code),
            'title'                       => trim($request->title),
            'st_purchase_active'          => $request->boolean('st_purchase_active'),
            'st_purchase_rate'            => (float) $request->input('st_purchase_rate', 0),
            'st_sale_active'              => $request->boolean('st_sale_active'),
            'st_sale_rate'                => (float) $request->input('st_sale_rate', 0),
            'mrp_tax_active'              => $request->boolean('mrp_tax_active'),
            'mrp_tax_rate'                => (float) $request->input('mrp_tax_rate', 0),
            'unreg_st_active'             => $request->boolean('unreg_st_active'),
            'unreg_st_rate'               => (float) $request->input('unreg_st_rate', 0),
            'unreg_st_applied_on'         => $request->input('unreg_st_applied_on', 'sales'),
            'unreg_client_mode'           => $request->input('unreg_client_mode', 'add_unreg'),
            'excise_duty_active'          => $request->boolean('excise_duty_active'),
            'excise_duty_rate'            => (float) $request->input('excise_duty_rate', 0),
            'adv_tax_purchase_active'     => $request->boolean('adv_tax_purchase_active'),
            'adv_tax_purchase_rate'       => (float) $request->input('adv_tax_purchase_rate', 0),
            'adv_tax_sale_active'         => $request->boolean('adv_tax_sale_active'),
            'adv_tax_sale_filer_rate'     => (float) $request->input('adv_tax_sale_filer_rate', 0),
            'adv_tax_sale_non_filer_rate' => (float) $request->input('adv_tax_sale_non_filer_rate', 0),
            'adv_tax_applied_on'          => $request->input('adv_tax_applied_on', 'net_tp'),
            'applied_from'                => $request->input('applied_from'),
            'include_special_disc'        => $request->boolean('include_special_disc', true),
            'tax_applied_on'              => $request->input('tax_applied_on', 'trade_value'),
            'is_default'                  => $request->boolean('is_default'),
            'status'                      => $request->boolean('status', true),
            'notes'                       => $request->input('notes'),
            'created_by'                  => auth()->id(),
        ];
    }
}
