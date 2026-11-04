<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;

class ProvinceController extends Controller
{
    /**
     * Display a listing of provinces.
     */
    public function index()
    {
        $provinces = Province::orderByDesc('id')->get();
        return view('admin_panel.province.index', compact('provinces'));
    }

    /**
     * Store a newly created or updated province in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|in:active,inactive'
        ]);

        $province = Province::updateOrCreate(
            ['id' => $request->edit_id],
            [
                'name' => trim($request->name),
                'status' => $request->status ?? 'active',
            ]
        );

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => $request->edit_id ? 'Province updated successfully.' : 'Province created successfully.',
            'province' => $province,
            'item' => [
                'id' => $province->id,
                'name' => $province->name,
                'status' => $province->status,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified province.
     */
    public function edit($id)
    {
        $province = Province::findOrFail($id);
        return response()->json($province);
    }

    /**
     * Remove the specified province from database.
     */
    public function destroy($id)
    {
        Province::findOrFail($id)->delete();
        return response()->json([
            'status' => 'deleted',
            'success' => 'Province deleted successfully.',
            'reload' => true,
        ]);
    }
}
