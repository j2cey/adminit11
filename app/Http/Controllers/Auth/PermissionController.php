<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:permission-list', only: ['index']),
        ];
    }

    /**
     * Display a listing of permissions from current logged user.
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        return auth()->user()->getAllPermissions()->pluck('name');
    }

    public function index()
    {
        $role = is_null( request('roleid') ) ? null : Role::findById( (int)request('roleid') );

        if ( request('permstatus') === "out_role" ) {
            $role_permissions_ids = $role->permissions()->pluck('id')->unique();
            $permissions = Permission::query()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%");
                })
                ->whereNotIn('id', $role_permissions_ids)
                ->latest()
                ->paginate(10);
        } elseif ( request('permstatus') === "in_role" ) {
            $permissions = $role->permissions()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%");
                })
                ->latest()
                ->paginate(10);
        } else {
            $permissions = Permission::query()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%");
                })
                ->latest()
                ->paginate(10);
        }

        if ( $role) {
            foreach ($permissions as $permission) {
                if ($role->hasPermissionTo($permission->name)) {
                    $permission->is_in_role = true;
                }
            }
        }

        return $permissions;
    }

    public function count()
    {
        return Permission::get()->count();
    }
}
