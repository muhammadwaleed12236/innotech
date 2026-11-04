<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerTypeController extends Controller
{
    public function index()
    {
        // Auto-create default types if none exist, but as normal editable/deletable types
        if (CustomerType::count() === 0) {
            CustomerType::create([
                'name' => 'Main Customer',
                'description' => 'Default customer type',
                'is_static' => 0,
            ]);
            CustomerType::create([
                'name' => 'Walking Customer',
                'description' => 'Customer type for instant upfront payment',
                'is_static' => 0,
            ]);
        }

        $types = CustomerType::all();
        return view('admin_panel.customer_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:customer_types,name,' . $request->edit_id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }

        // UPDATE
        if ($request->filled('edit_id')) {
            $type = CustomerType::find($request->edit_id);

            if (!$type) {
                return response()->json([
                    'error' => 'Customer Type not found'
                ], 404);
            }

            $oldName = $type->name;
            $type->name = $request->name;
            $type->description = $request->description;
            $type->is_static = false;
            $type->save();

            // Sync updated name to existing customers
            if ($oldName && $oldName !== $request->name) {
                Customer::where('customer_type', $oldName)->update(['customer_type' => $request->name]);
            }

            $message = 'Customer Type Updated Successfully';
        }
        // CREATE
        else {
            $type = new CustomerType();
            $type->name = $request->name;
            $type->description = $request->description;
            $type->is_static = false;
            $type->save();

            $message = 'Customer Type Created Successfully';
        }

        return response()->json([
            'success' => $message,
            'reload' => true
        ]);
    }

    public function destroy($id)
    {
        $type = CustomerType::find($id);

        if (!$type) {
            return response()->json(['error' => 'Customer Type not found'], 404);
        }

        // Unlink associated customers so deletion succeeds cleanly
        Customer::where('customer_type', $type->name)->update(['customer_type' => null]);

        $type->delete();

        return response()->json([
            'success' => 'Customer Type Deleted Successfully',
            'reload' => route('customer-types.index')
        ]);
    }
}
