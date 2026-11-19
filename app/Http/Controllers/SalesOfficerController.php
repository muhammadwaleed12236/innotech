<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesOfficer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SalesOfficerController extends Controller
{
    /**
     * Display a listing of order bookers and salesmen.
     */
    public function index(Request $request)
    {
        $typeFilter = $request->get('type', 'all');
        $search = $request->get('q', '');

        $query = SalesOfficer::with(['user'])->latest();

        if ($typeFilter === 'order_booker') {
            $query->whereIn('type', ['order_booker', 'both']);
        } elseif ($typeFilter === 'salesman') {
            $query->whereIn('type', ['salesman', 'both']);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $officers = $query->paginate(25)->withQueryString();

        // Calculate KPI Stats
        $stats = [
            'total_bookers' => SalesOfficer::bookers()->count(),
            'total_salesmen' => SalesOfficer::salesmen()->count(),
            'active_staff' => SalesOfficer::active()->count(),
            'today_bookings_count' => Sale::where('is_booking', 1)->whereDate('created_at', today())->count(),
            'today_bookings_val' => Sale::where('is_booking', 1)->whereDate('created_at', today())->sum('total_net'),
        ];

        $nextBookerCode = SalesOfficer::generateNextCode('order_booker');
        $nextSalesmanCode = SalesOfficer::generateNextCode('salesman');

        return view('admin_panel.sales_officer.index', compact(
            'officers',
            'stats',
            'typeFilter',
            'search',
            'nextBookerCode',
            'nextSalesmanCode'
        ));
    }

    /**
     * Store or update an order booker / salesman.
     */
    public function store(Request $request)
    {
        $isEdit = !empty($request->edit_id);
        $editId = $request->edit_id ?? null;

        $rules = [
            'name' => 'required|string|max:255',
            'name_urdu' => 'nullable|string|max:255',
            'type' => 'required|in:order_booker,salesman,both',
            'reference' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:50',
            'cnic' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
            'code' => [
                'nullable',
                'string',
                'max:50',
                $isEdit ? Rule::unique('sales_officers', 'code')->ignore($editId) : Rule::unique('sales_officers', 'code'),
            ],
            'edit_id' => 'nullable|integer|exists:sales_officers,id',
        ];

        if ($request->boolean('allow_portal_access')) {
            $rules['portal_email'] = [
                'required',
                'string',
                'max:150',
            ];
            if (!$isEdit) {
                $rules['portal_password'] = 'required|string|min:6';
            } else {
                $rules['portal_password'] = 'nullable|string|min:6';
            }
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Determine code
            $type = $validated['type'] ?? 'order_booker';
            $code = !empty($validated['code']) ? trim($validated['code']) : SalesOfficer::generateNextCode($type);

            $commRate = isset($validated['commission_rate']) ? (float)$validated['commission_rate'] : 0.00;
            $status = $request->has('status') ? (bool)$request->status : true;

            $officer = $isEdit ? SalesOfficer::findOrFail($editId) : new SalesOfficer();
            $officer->code = $code;
            $officer->name = $validated['name'];
            $officer->name_urdu = $validated['name_urdu'] ?? null;
            $officer->type = $type;
            $officer->reference = $validated['reference'] ?? null;
            $officer->mobile = $validated['mobile'];
            $officer->cnic = $validated['cnic'] ?? null;
            $officer->email = $validated['email'] ?? ($request->portal_email ?? null);
            $officer->address = $validated['address'] ?? null;
            $officer->commission_rate = $commRate;
            $officer->status = $status;

            // Handle Portal Access User
            if ($request->boolean('allow_portal_access')) {
                $portalEmail = trim($request->portal_email);
                $portalPassword = $request->portal_password;

                $user = null;
                if ($officer->user_id) {
                    $user = User::find($officer->user_id);
                }

                if (!$user) {
                    // Check if existing user by email
                    $user = User::where('email', $portalEmail)->first();
                }

                $userType = ($type === 'salesman') ? 'salesman' : 'order_booker';

                if ($user) {
                    $user->name = $validated['name'];
                    $user->email = $portalEmail;
                    $user->usertype = $userType;
                    if (!empty($portalPassword)) {
                        $user->password = Hash::make($portalPassword);
                    }
                    $user->save();
                } else {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $portalEmail,
                        'password' => Hash::make($portalPassword ?: '123456'),
                        'usertype' => $userType,
                        'branch_id' => auth()->user()->branch_id ?? 1,
                    ]);
                }

                $officer->user_id = $user->id;
            } else {
                // If portal access removed
                if ($request->has('remove_portal_access') && $officer->user_id) {
                    $officer->user_id = null;
                }
            }

            $officer->save();

            DB::commit();

            $message = $isEdit ? 'Staff member updated successfully.' : 'Order Booker / Salesman created successfully.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $officer->load('user'),
                ]);
            }

            return redirect()->route('sales.officer.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                ], 422);
            }
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Return officer details for editing (AJAX).
     */
    public function edit($id)
    {
        $officer = SalesOfficer::with('user')->findOrFail($id);

        return response()->json([
            'id' => $officer->id,
            'code' => $officer->code,
            'name' => $officer->name,
            'name_urdu' => $officer->name_urdu,
            'type' => $officer->type,
            'reference' => $officer->reference,
            'mobile' => $officer->mobile,
            'cnic' => $officer->cnic,
            'email' => $officer->email,
            'address' => $officer->address,
            'commission_rate' => $officer->commission_rate,
            'status' => (bool)$officer->status,
            'has_portal_access' => !empty($officer->user_id),
            'portal_email' => $officer->user ? $officer->user->email : $officer->email,
        ]);
    }

    /**
     * AJAX endpoint to fetch next code for selected type.
     */
    public function getNextCode(Request $request)
    {
        $type = $request->get('type', 'order_booker');
        $code = SalesOfficer::generateNextCode($type);

        return response()->json(['code' => $code]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus($id)
    {
        $officer = SalesOfficer::findOrFail($id);
        $officer->status = !$officer->status;
        $officer->save();

        return response()->json([
            'success' => true,
            'status' => (bool)$officer->status,
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Delete an officer.
     */
    public function destroy(Request $request, $id)
    {
        $officer = SalesOfficer::findOrFail($id);

        // Check if officer has any sales/bookings
        $salesCount = Sale::where('sales_officer_id', $id)
            ->orWhere('order_booker_id', $id)
            ->orWhere('salesman_id', $id)
            ->count();

        if ($salesCount > 0) {
            $msg = "Cannot delete: This staff member is linked to {$salesCount} sales/bookings. You can deactivate them instead.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        $officer->delete();

        $msg = 'Staff member deleted successfully.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('sales.officer.index')->with('success', $msg);
    }
}
