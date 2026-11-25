<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        $users = User::where('email', '!=' ,'admin@admin.com')->get();
        return view('admin_panel.branch.branch', compact('branches', 'users')); 
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
         $validator = Validator::make($request->all(), [
            'name' => 'required|unique:branches,name,'.$request->edit_id,
            'address' => 'required',
            'number' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }


      
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Step 2: Check for user_id uniqueness (exclude self in edit)
        $userExists = Branch::where('user_id', $request->user_id)
            ->when($editId, fn($q) => $q->where('id', '!=', $editId))
            ->exists();

        if ($userExists) {
            return response()->json([
                'errors' => [
                    'user_id' => ['This user is already assigned to another branch.']
                ]
            ]);
        }

        // Step 3: Save or update logic
        $oldUserId = null;
        if (!empty($editId)) {
            $branch = Branch::find($editId);
            $oldUserId = $branch ? $branch->user_id : null;
            $msg = [
                'success' => 'Branch Updated Successfully',
                'reload' => true
            ];
        } else {
            $branch = new Branch();
            $msg = [
                'success' => 'Branch Created Successfully',
                'redirect' => route('branch.index')
            ];
        }

        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->number = $request->number;
        $branch->user_id = $request->user_id;
        $branch->save();

        // Keep users.branch_id in sync
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'branch_id')) {
            if ($oldUserId && $oldUserId != $request->user_id) {
                User::where('id', $oldUserId)->where('branch_id', $branch->id)->update(['branch_id' => null]);
            }
            if (!empty($request->user_id)) {
                User::where('id', $request->user_id)->update(['branch_id' => $branch->id]);
            }
        }

        return response()->json($msg);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $branch = Branch::findOrFail($id);

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'branch_id')) {
            User::where('branch_id', $branch->id)->update(['branch_id' => null]);
        }

        $branch->delete();

        return redirect()->route('branch.index')->with('success', 'Branch deleted successfully.');

    }
}
