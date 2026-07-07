<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
    }

    public function index()
    {
        $this->ensureDir();
        $files = collect(glob($this->backupPath . '/*.sql'))
            ->map(fn($f) => [
                'name'     => basename($f),
                'size'     => $this->humanSize(filesize($f)),
                'bytes'    => filesize($f),
                'created'  => date('d M Y, H:i', filemtime($f)),
                'timestamp'=> filemtime($f),
            ])
            ->sortByDesc('timestamp')
            ->values();

        $settings = SystemSetting::group('backup');

        return view('settings.backups', compact('files', 'settings'));
    }

    public function store(Request $request)
    {
        try {
            Artisan::call('backup:run');
            $output = Artisan::output();
            if (str_contains(strtolower($output), 'error') || str_contains(strtolower($output), 'failed')) {
                return back()->with('error', 'Backup failed: ' . trim($output));
            }
            return back()->with('success', 'Backup created successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(string $filename): StreamedResponse
    {
        $path = $this->backupPath . '/' . basename($filename);
        abort_unless(file_exists($path), 404);

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, basename($filename), [
            'Content-Type'        => 'application/octet-stream',
            'Content-Length'      => filesize($path),
            'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"',
        ]);
    }

    public function destroy(string $filename)
    {
        $path = $this->backupPath . '/' . basename($filename);
        if (file_exists($path)) {
            unlink($path);
        }
        return back()->with('success', 'Backup deleted.');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_frequency'      => 'required|in:off,daily,weekly',
            'backup_retention_days' => 'required|integer|min:1|max:365',
        ]);

        SystemSetting::set('backup_frequency', $request->backup_frequency);
        SystemSetting::set('backup_retention_days', $request->backup_retention_days);

        return back()->with('success', 'Backup settings saved.');
    }

    private function ensureDir(): void
    {
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) return round($bytes, 1) . ' ' . $unit;
            $bytes /= 1024;
        }
        return round($bytes, 1) . ' TB';
    }
}
