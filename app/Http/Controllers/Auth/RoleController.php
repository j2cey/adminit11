<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:role-list', only: ['index']),
            new Middleware('permission:role-create', only: ['store']),
            new Middleware('permission:role-update', only: ['update','assignPermissions','revokePermissions']),
            new Middleware('permission:role-destory', only: ['destory','bulkDelete']),
        ];
    }
    /**
     * Display a listing of roles from current logged user.
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        return auth()->user()->getRoleNames();
    }

    public function index()
    {
        $roles = Role::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->with('permissions')
            ->latest()
            ->paginate(5);

        return $roles;
    }

    public function store() {
        request()->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'required',
        ]);

        return Role::create([
            'name' => request('name'),
            'guard_name' => request('guard_name'),
        ]);
    }

    public function edit(Role $role)
    {
        return $role->load(['permissions','users']);
    }

    public function update(Role $role)
    {
        request()->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'guard_name' => 'required',
        ]);

        $role->update([
            'name' => request('name'),
            'email' => request('guard_name'),
        ]);

        return response()->json(['success' => true]);
    }

    public function assignPermissions(Role $role)
    {
        $permissions = Permission::whereIn('id', request('permissionsids'))
            ->whereNotIn('id', $role->permissions()->pluck('id'))
            ->get();
        $nb = count($permissions);
        $role->givePermissionTo($permissions->pluck('name'));

        foreach ($permissions as $permission) {
            $permission->is_in_role = true;
        }

        return response()->json(['success' => true, 'permissions' => $permissions,'message' => $nb === 0 ? 'NO permission to assign' : $nb . ' Permission(s) assign to Role successfully!']);
    }

    public function revokePermissions(Role $role)
    {
        $permissions = Permission::whereIn('id', request('permissionsids'))
            ->whereIn('id', $role->permissions()->pluck('id'))
            ->get();
        $nb = 0;
        foreach ($permissions as $permission) {
            $role->revokePermissionTo($permission->name);
            $nb++;
        }

        return response()->json(['success' => true, 'permissions' => $permissions,'message' => $nb === 0 ? 'NO permission to revoke' : $nb . ' Permission(s) revoke to Role successfully!']);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->noContent();
    }

    public function bulkDelete()
    {
        Role::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Roles deleted successfully!']);
    }
}
