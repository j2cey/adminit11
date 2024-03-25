<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('full_path', 'like', "%{$searchQuery}%");
            })
            ->whereNotNull('value')
            ->with(['maingroup','group'])
            ->oldest()
            ->paginate(10);

        return $settings;
    }

    public function fetch() {
        return Setting::getAllGrouped();
    }

    public function groups()
    {
        $group = is_null( request('groupid') ) ? null : Setting::find( (int)request('groupid') );

        if ( $group) {
            $settings = $group->subsettings()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('full_path', 'like', "%{$searchQuery}%");
                })
                ->oldest()
                ->paginate(10);
        } else {
            $settings = Setting::query()
                ->when(request('query'), function ($query, $searchQuery) {
                    $query->where('full_path', 'like', "%{$searchQuery}%");
                })
                ->whereNotNull('value')
                ->with(['maingroup', 'group'])
                ->oldest()
                ->paginate(10);
        }

        return $settings;
    }

    public function edit(Setting $setting) {
        return $setting->load(['maingroup', 'group']);
    }

    public function update(Setting $setting)
    {
        $validated = request()->validate([
            'name' => ['required', 'string'],
            'value' => ['required', 'string'],
            'type' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $setting->update($validated);

        return response()->json([ 'setting' => $setting->load(['maingroup', 'group']), 'success' => true]);
    }
}
