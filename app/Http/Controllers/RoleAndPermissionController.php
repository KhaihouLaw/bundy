<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionController extends Controller
{
    public function roles(Request $request) 
    {
        $roles = Role::get();
        return response()->json(['data' => $roles]);
    }

    public function permissions(Request $request) 
    {
        $permissions = Permission::get();
        return response()->json(['data' => $permissions]);
    }

    public function manageRole(Request $request, User $user)
    {
        $role = $request->get('role');
        $type = $request->get('type');
        
        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel) {
            return response()->json(['error' => 'Invalid Role'], 400);
        }

        switch ($type) {
            case 'add':
                $user->assignRole($role);
                break;
            case 'remove':
                $user->removeRole($role);
                break;
            default:
                break;
        }

        return response()->json(['data' => $user]);
    }


    public function userPermission(Request $request, User $user) 
    {
        $permission = $request->get('permission');
        $type = $request->get('type');

        $permissionModel = Permission::where('name', $permission)->first();
        if (!$permissionModel) {
            return response()->json(['error' => 'Invalid Permission'], 400);
        }

        switch ($type) {
            case 'add':
                $user->givePermissionTo($permission);
                break;
            case 'remove':
                $user->revokePermissionTo($permission);
                break;
            default:
                break;
        }

        return response()->json(['data' => $user]);
    }

    public function rolePermission(Request $request, $role) 
    {
        $permission = $request->get('permission');
        $type = $request->get('type');

        $roleModel = Role::where('name', $role)->first();
        if (!$roleModel) {
            return response()->json(['error' => 'Invalid Role'], 400);
        }

        $permissionModel = Permission::where('name', $permission)->first();
        if (!$permissionModel) {
            return response()->json(['error' => 'Invalid Permission'], 400);
        }

        switch ($type) {
            case 'add':
                $roleModel->givePermissionTo($permission);
                break;
            case 'remove':
                $roleModel->revokePermissionTo($permission);
                break;
            default:
                break;
        }

        return response()->json(['data' => $roleModel]);
    }

}
