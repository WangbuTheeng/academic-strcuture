<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstituteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InstituteSettingsController extends Controller
{
    /**
     * Display institute settings.
     */
    public function index()
    {
        $settings = InstituteSettings::first() ?? new InstituteSettings();
        
        return view('admin.institute-settings.index', compact('settings'));
    }

    /**
     * Update institute settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'principal_name' => 'nullable|string|max:255',
        ]);

        $settings = InstituteSettings::first() ?? new InstituteSettings();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->institution_logo && Storage::disk('public')->exists($settings->institution_logo)) {
                Storage::disk('public')->delete($settings->institution_logo);
            }

            $logoPath = $request->file('logo')->store('institute/logos', 'public');
            $settings->institution_logo = $logoPath;
        }

        // Map form fields to model fields
        $settings->institution_name = $request->institution_name;
        $settings->institution_address = $request->address;
        $settings->institution_phone = $request->phone;
        $settings->institution_email = $request->email;
        $settings->institution_website = $request->website;
        $settings->principal_name = $request->principal_name;

        $settings->save();

        return redirect()->route('admin.institute-settings.index')
                        ->with('success', 'Institute settings updated successfully.');
    }

    /**
     * Display academic settings (now school information).
     */
    public function academic()
    {
        $settings = InstituteSettings::first() ?? new InstituteSettings();

        return view('admin.institute-settings.academic', compact('settings'));
    }

    /**
     * Update academic settings (now school information).
     */
    public function updateAcademic(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'institution_phone' => 'nullable|string|max:20',
            'institution_email' => 'nullable|email|max:255',
            'institution_website' => 'nullable|url|max:255',
            'principal_name' => 'nullable|string|max:255',
            'principal_email' => 'nullable|email|max:255',
        ]);

        $settings = InstituteSettings::first() ?? new InstituteSettings();

        // Map form fields to model fields
        $settings->institution_name = $request->institution_name;
        $settings->institution_address = $request->institution_address;
        $settings->institution_phone = $request->institution_phone;
        $settings->institution_email = $request->institution_email;
        $settings->institution_website = $request->institution_website;
        $settings->principal_name = $request->principal_name;
        $settings->principal_email = $request->principal_email;

        $settings->save();

        return redirect()->route('admin.institute-settings.academic')
                        ->with('success', 'School information updated successfully.');
    }

    /**
     * Remove logo.
     */
    public function removeLogo()
    {
        $settings = InstituteSettings::first();

        if ($settings && $settings->institution_logo) {
            if (Storage::disk('public')->exists($settings->institution_logo)) {
                Storage::disk('public')->delete($settings->institution_logo);
            }

            $settings->institution_logo = null;
            $settings->save();
        }

        return redirect()->route('admin.institute-settings.index')
                        ->with('success', 'Logo removed successfully.');
    }

    /**
     * Remove principal signature.
     */
    public function removeSignature()
    {
        $settings = InstituteSettings::first();

        if ($settings && $settings->institution_seal) {
            if (Storage::disk('public')->exists($settings->institution_seal)) {
                Storage::disk('public')->delete($settings->institution_seal);
            }

            $settings->institution_seal = null;
            $settings->save();
        }

        return redirect()->route('admin.institute-settings.index')
                        ->with('success', 'Principal signature removed successfully.');
    }
}
