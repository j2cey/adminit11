<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Admin\DashboardStatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/api/abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });

    Route::get('permissions', PermissionController::class);
    Route::get('/api/permissions/', [PermissionController::class, 'index']);
    Route::get('/api/permissions/count/', [PermissionController::class, 'count']);
    Route::get('roles', RoleController::class);
    Route::get('/api/roles/', [RoleController::class, 'index']);
    Route::post('/api/roles/', [RoleController::class, 'store']);
    Route::get('/api/roles/{role}/edit', [RoleController::class, 'edit']);
    Route::put('/api/roles/{role}', [RoleController::class, 'update']);
    Route::patch('/api/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::patch('/api/roles/{role}/revoke-permissions', [RoleController::class, 'revokePermissions']);

    Route::get('/api/stats/appointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    Route::get('/api/users/', [UserController::class, 'index']);
    Route::post('/api/users/', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::delete('/api/users/{user}', [UserController::class, 'destory']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);

    Route::get('/api/settings/fetch', [SettingController::class, 'fetch']);
    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::put('/api/settings/{setting}', [SettingController::class, 'update']);
    Route::get('/api/settinggroups', [SettingController::class, 'groups']);
    Route::get('/api/settings/{setting}/edit', [SettingController::class, 'edit']);

    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController::class, 'uploadImage']);
    Route::post('/api/change-user-password', [ProfileController::class, 'changePassword']);
});
Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
