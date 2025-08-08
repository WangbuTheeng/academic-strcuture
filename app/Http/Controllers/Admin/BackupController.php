<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-backups']);
    }

    /**
     * Display backup management dashboard.
     */
    public function index(Request $request)
    {
        $query = BackupRecord::with(['createdBy']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $backups = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get backup statistics
        $stats = [
            'total_backups' => BackupRecord::count(),
            'successful_backups' => BackupRecord::where('status', 'completed')->count(),
            'failed_backups' => BackupRecord::where('status', 'failed')->count(),
            'total_size' => BackupRecord::where('status', 'completed')->sum('file_size'),
        ];

        // Get disk usage
        $diskUsage = $this->getDiskUsage();

        return view('admin.backups.index', compact('backups', 'stats', 'diskUsage'));
    }

    /**
     * Create a new backup.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:1000',
            'include_uploads' => 'boolean',
            'include_logs' => 'boolean',
        ]);

        try {
            $backup = BackupRecord::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'status' => 'processing',
                'created_by' => auth()->id(),
                'started_at' => now(),
            ]);

            // Process backup based on type
            $result = $this->processBackup($backup, $validated);

            if ($result['success']) {
                $backup->update([
                    'status' => 'completed',
                    'file_path' => $result['file_path'],
                    'file_size' => $result['file_size'],
                    'completed_at' => now(),
                ]);

                return back()->with('success', 'Backup created successfully.');
            } else {
                $backup->update([
                    'status' => 'failed',
                    'error_message' => $result['error'],
                    'completed_at' => now(),
                ]);

                return back()->with('error', 'Backup failed: ' . $result['error']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Backup creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(BackupRecord $backup)
    {
        if ($backup->status !== 'completed' || !$backup->file_path) {
            return back()->with('error', 'Backup file is not available for download.');
        }

        $filePath = storage_path('app/backups/' . $backup->file_path);

        if (!File::exists($filePath)) {
            return back()->with('error', 'Backup file not found.');
        }

        // Log download activity
        activity()
            ->performedOn($backup)
            ->causedBy(auth()->user())
            ->log('Downloaded backup: ' . $backup->name);

        return response()->download($filePath, $backup->name . '.zip');
    }

    /**
     * Delete a backup.
     */
    public function destroy(BackupRecord $backup)
    {
        try {
            // Delete the backup file
            if ($backup->file_path) {
                $filePath = storage_path('app/backups/' . $backup->file_path);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }

            // Delete the record
            $backup->delete();

            // Log deletion activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['backup_name' => $backup->name])
                ->log('Deleted backup: ' . $backup->name);

            return back()->with('success', 'Backup deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    /**
     * Show restore interface.
     */
    public function restore(BackupRecord $backup)
    {
        if ($backup->status !== 'completed' || !$backup->file_path) {
            return back()->with('error', 'Backup is not available for restore.');
        }

        $filePath = storage_path('app/backups/' . $backup->file_path);

        if (!File::exists($filePath)) {
            return back()->with('error', 'Backup file not found.');
        }

        return view('admin.backups.restore', compact('backup'));
    }

    /**
     * Process restore operation.
     */
    public function processRestore(Request $request, BackupRecord $backup)
    {
        $validated = $request->validate([
            'confirm_restore' => 'required|accepted',
            'restore_type' => 'required|in:full,database_only,files_only',
        ]);

        try {
            $result = $this->performRestore($backup, $validated['restore_type']);

            if ($result['success']) {
                // Log restore activity
                activity()
                    ->performedOn($backup)
                    ->causedBy(auth()->user())
                    ->withProperties(['restore_type' => $validated['restore_type']])
                    ->log('Restored backup: ' . $backup->name);

                return redirect()->route('admin.backups.index')
                               ->with('success', 'System restored successfully from backup.');
            } else {
                return back()->with('error', 'Restore failed: ' . $result['error']);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Restore operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Schedule automatic backups.
     */
    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'boolean',
            'frequency' => 'required_if:enabled,true|in:daily,weekly,monthly',
            'time' => 'required_if:enabled,true|date_format:H:i',
            'type' => 'required_if:enabled,true|in:full,database,files',
            'retention_days' => 'required_if:enabled,true|integer|min:1|max:365',
        ]);

        // Store schedule configuration
        $settings = [
            'backup_schedule_enabled' => $validated['enabled'] ?? false,
            'backup_frequency' => $validated['frequency'] ?? 'weekly',
            'backup_time' => $validated['time'] ?? '02:00',
            'backup_type' => $validated['type'] ?? 'full',
            'backup_retention_days' => $validated['retention_days'] ?? 30,
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Backup schedule updated successfully.');
    }

    /**
     * Clean up old backups.
     */
    public function cleanup(Request $request)
    {
        $validated = $request->validate([
            'older_than_days' => 'required|integer|min:1|max:365',
        ]);

        $cutoffDate = now()->subDays($validated['older_than_days']);

        $oldBackups = BackupRecord::where('created_at', '<', $cutoffDate)->get();
        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($oldBackups as $backup) {
            if ($backup->file_path) {
                $filePath = storage_path('app/backups/' . $backup->file_path);
                if (File::exists($filePath)) {
                    $freedSpace += File::size($filePath);
                    File::delete($filePath);
                }
            }
            $backup->delete();
            $deletedCount++;
        }

        // Log cleanup activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'deleted_count' => $deletedCount,
                'freed_space' => $freedSpace,
                'older_than_days' => $validated['older_than_days']
            ])
            ->log("Cleaned up {$deletedCount} old backups");

        $freedSpaceMB = round($freedSpace / 1024 / 1024, 2);

        return back()->with('success', "Deleted {$deletedCount} old backups and freed {$freedSpaceMB} MB of space.");
    }

    /**
     * Process backup based on type.
     */
    private function processBackup(BackupRecord $backup, array $options): array
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = Str::slug($backup->name) . '_' . $timestamp . '.zip';
        $zipPath = $backupDir . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return ['success' => false, 'error' => 'Could not create backup archive'];
        }

        try {
            switch ($backup->type) {
                case 'full':
                    $this->addDatabaseToZip($zip);
                    $this->addFilesToZip($zip, $options);
                    break;
                case 'database':
                    $this->addDatabaseToZip($zip);
                    break;
                case 'files':
                    $this->addFilesToZip($zip, $options);
                    break;
            }

            $zip->close();

            return [
                'success' => true,
                'file_path' => $filename,
                'file_size' => File::size($zipPath)
            ];

        } catch (\Exception $e) {
            $zip->close();
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Add database dump to zip.
     */
    private function addDatabaseToZip(ZipArchive $zip): void
    {
        $dumpPath = storage_path('app/temp/database_dump.sql');

        // Create temp directory if it doesn't exist
        $tempDir = dirname($dumpPath);
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Generate database dump
        $dbConfig = config('database.connections.' . config('database.default'));
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dumpPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database dump failed');
        }

        $zip->addFile($dumpPath, 'database.sql');
    }

    /**
     * Add files to zip.
     */
    private function addFilesToZip(ZipArchive $zip, array $options): void
    {
        // Add storage files
        if ($options['include_uploads'] ?? true) {
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/');
        }

        // Add logs if requested
        if ($options['include_logs'] ?? false) {
            $this->addDirectoryToZip($zip, storage_path('logs'), 'logs/');
        }

        // Add configuration files
        $zip->addFile(base_path('.env'), 'config/.env');
    }

    /**
     * Add directory to zip recursively.
     */
    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipDir): void
    {
        if (!File::exists($dir)) {
            return;
        }

        $files = File::allFiles($dir);
        foreach ($files as $file) {
            $relativePath = $zipDir . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }
    }

    /**
     * Perform restore operation.
     */
    private function performRestore(BackupRecord $backup, string $restoreType): array
    {
        $zipPath = storage_path('app/backups/' . $backup->file_path);
        $extractPath = storage_path('app/temp/restore_' . time());

        // Extract backup
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            return ['success' => false, 'error' => 'Could not open backup file'];
        }

        $zip->extractTo($extractPath);
        $zip->close();

        try {
            if ($restoreType === 'full' || $restoreType === 'database_only') {
                $this->restoreDatabase($extractPath);
            }

            if ($restoreType === 'full' || $restoreType === 'files_only') {
                $this->restoreFiles($extractPath);
            }

            // Clean up extracted files
            File::deleteDirectory($extractPath);

            return ['success' => true];

        } catch (\Exception $e) {
            // Clean up on failure
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Restore database from backup.
     */
    private function restoreDatabase(string $extractPath): void
    {
        $sqlFile = $extractPath . '/database.sql';

        if (!File::exists($sqlFile)) {
            throw new \Exception('Database backup file not found');
        }

        $dbConfig = config('database.connections.' . config('database.default'));
        $command = sprintf(
            'mysql -h%s -u%s -p%s %s < %s',
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $sqlFile
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database restore failed');
        }
    }

    /**
     * Restore files from backup.
     */
    private function restoreFiles(string $extractPath): void
    {
        // Restore storage files
        $storageSource = $extractPath . '/storage';
        if (File::exists($storageSource)) {
            File::copyDirectory($storageSource, storage_path('app/public'));
        }

        // Restore configuration
        $envSource = $extractPath . '/config/.env';
        if (File::exists($envSource)) {
            File::copy($envSource, base_path('.env'));
        }
    }

    /**
     * Get disk usage information.
     */
    private function getDiskUsage(): array
    {
        $backupDir = storage_path('app/backups');
        $totalSize = 0;
        $fileCount = 0;

        if (File::exists($backupDir)) {
            $files = File::allFiles($backupDir);
            foreach ($files as $file) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }

        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        $diskUsed = $diskTotal - $diskFree;

        return [
            'backup_size' => $totalSize,
            'backup_count' => $fileCount,
            'disk_free' => $diskFree,
            'disk_total' => $diskTotal,
            'disk_used' => $diskUsed,
            'disk_usage_percent' => $diskTotal > 0 ? ($diskUsed / $diskTotal) * 100 : 0,
        ];
    }
}
