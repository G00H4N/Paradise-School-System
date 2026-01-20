<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    // 1. View Matrix
    public function index()
    {
        // Permissions group by module (e.g. Students -> create, view, edit)
        $permissions = Permission::all()->groupBy('module');

        // Fetch current assigned permissions
        $rolePermissions = DB::table('role_permissions')->get();

        return inertia('Settings/Roles', [
            'modules' => $permissions,
            'roles' => ['teacher', 'accountant', 'librarian'], // Fixed Enums
            'assigned' => $rolePermissions
        ]);
    }

    // 2. Update Matrix (Checkbox Click)
    public function update(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'permission_id' => 'required|exists:permissions,id',
            'allowed' => 'required|boolean'
        ]);

        if ($request->allowed) {
            // Permission De do
            DB::table('role_permissions')->updateOrInsert([
                'role' => $request->role,
                'permission_id' => $request->permission_id
            ]);
        } else {
            // Permission Le lo
            DB::table('role_permissions')
                ->where('role', $request->role)
                ->where('permission_id', $request->permission_id)
                ->delete();
        }

        return back()->with('success', 'Permission Updated');
    }
}