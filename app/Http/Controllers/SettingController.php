<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // 1. Show Settings Page
    public function index()
    {
        // Hamesha first row fetch karein (Single School Setup for MVP)
        $settings = Setting::first() ?? new Setting();
        return inertia('Settings/General', ['settings' => $settings]);
    }

    // 2. Update Settings
    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string',
            'current_session' => 'required|string', // 2026-2027
        ]);

        $settings = Setting::first();
        if (!$settings) {
            $settings = new Setting();
        }

        $data = $request->except('logo');

        // Logo Upload Logic
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('school', 'public');
            $data['logo_path'] = $path;
        }

        $settings->fill($data)->save();

        return back()->with('success', 'Settings Updated!');
    }
}