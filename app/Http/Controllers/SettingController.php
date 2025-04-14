<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => ['required', 'string'],
        ]);

        $setting->update([
            'value' => (string) $validated['value']
        ]);

        return redirect()->route('settings.index')
            ->with('success', __('all.settings.update_success'));
    }
}
