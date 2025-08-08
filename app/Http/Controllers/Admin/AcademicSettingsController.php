<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InstituteSettings;
use App\Models\AcademicYear;
use App\Models\Level;
use Illuminate\Support\Facades\Storage;

class AcademicSettingsController extends Controller
{
    /**
     * Show school information settings
     */
    public function schoolInfo()
    {
        $settings = InstituteSettings::first() ?? new InstituteSettings();
        
        return view('admin.academic-settings.school-info', compact('settings'));
    }

    /**
     * Update school information
     */
    public function updateSchoolInfo(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string|max:500',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'nullable|email|max:255',
            'school_website' => 'nullable|url|max:255',
            'principal_name' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'school_motto' => 'nullable|string|max:500',
            'established_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'affiliation' => 'nullable|string|max:255',
        ]);

        $settings = InstituteSettings::first() ?? new InstituteSettings();

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            if ($settings->school_logo && Storage::disk('public')->exists($settings->school_logo)) {
                Storage::disk('public')->delete($settings->school_logo);
            }

            $logoPath = $request->file('school_logo')->store('logos', 'public');
            $validated['school_logo'] = $logoPath;
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()->route('admin.academic-settings.school-info')
                        ->with('success', 'School information updated successfully.');
    }

    /**
     * Show academic year settings
     */
    public function academicYear()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        return view('admin.academic-settings.academic-year', compact('academicYears', 'currentYear'));
    }

    /**
     * Show grading system settings
     */
    public function grading()
    {
        $levels = Level::with(['gradingScales' => function($query) {
            $query->where('is_active', true);
        }])->get();
        
        return view('admin.academic-settings.grading', compact('levels'));
    }

    /**
     * Show backup and restore settings
     */
    public function backup()
    {
        // Get list of existing backups
        $backups = collect(Storage::disk('local')->files('backups'))
                    ->filter(function ($file) {
                        return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
                    })
                    ->map(function ($file) {
                        return [
                            'name' => basename($file),
                            'size' => Storage::disk('local')->size($file),
                            'modified' => Storage::disk('local')->lastModified($file),
                        ];
                    })
                    ->sortByDesc('modified')
                    ->values();

        return view('admin.academic-settings.backup', compact('backups'));
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Create backups directory if it doesn't exist
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $path
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return redirect()->route('admin.academic-settings.backup')
                                ->with('success', 'Database backup created successfully.');
            } else {
                return redirect()->route('admin.academic-settings.backup')
                                ->with('error', 'Failed to create database backup.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.academic-settings.backup')
                            ->with('error', 'Error creating backup: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $path = 'backups/' . $filename;
        
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }

        return redirect()->route('admin.academic-settings.backup')
                        ->with('error', 'Backup file not found.');
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        $path = 'backups/' . $filename;
        
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return redirect()->route('admin.academic-settings.backup')
                            ->with('success', 'Backup deleted successfully.');
        }

        return redirect()->route('admin.academic-settings.backup')
                        ->with('error', 'Backup file not found.');
    }
}
