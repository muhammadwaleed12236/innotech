<?php

namespace App\Http\Controllers;

use App\Models\SalesOfficer;
use Illuminate\Http\Request;

class SalesOfficerController extends Controller
{
    public function index()
    {
        $officers = SalesOfficer::latest()->get();
        return view('admin_panel.sales_officer.index', compact('officers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_urdu' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:50',
            'edit_id' => 'nullable|integer|exists:sales_officers,id',
        ]);

        if (!empty($request->edit_id)) {
            $officer = SalesOfficer::findOrFail($request->edit_id);
            $officer->update([
                'name' => $validated['name'],
                'name_urdu' => $validated['name_urdu'] ?? null,
                'mobile' => $validated['mobile'],
            ]);
            $message = 'Sales Officer updated successfully.';
        } else {
            $officer = SalesOfficer::create([
                'name' => $validated['name'],
                'name_urdu' => $validated['name_urdu'] ?? null,
                'mobile' => $validated['mobile'],
            ]);
            $message = 'Sales Officer created successfully.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $officer,
            ]);
        }

        return redirect()->route('sales.officer.index')->with('success', $message);
    }

    public function edit($id)
    {
        $officer = SalesOfficer::findOrFail($id);
        return response()->json($officer);
    }

    public function destroy(Request $request, $id)
    {
        $officer = SalesOfficer::findOrFail($id);
        $officer->delete();

        $message = 'Sales Officer deleted successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('sales.officer.index')->with('success', $message);
    }
}
