<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunBackup extends Command
{
    protected $signature   = 'backup:run';
    protected $description = 'Create a MySQL database backup and store it in storage/app/backups';

    public function handle(): int
    {
        $frequency = SystemSetting::get('backup_frequency', 'daily');
        if ($frequency === 'off') {
            $this->info('Backups are disabled.');
            return Command::SUCCESS;
        }
        if ($frequency === 'weekly' && now()->dayOfWeek !== 0) { // 0 = Sunday
            $this->info('Weekly backup skipped (not Sunday).');
            return Command::SUCCESS;
        }

        $dir = storage_path('app/backups');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $db       = config('database.connections.mysql');
        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path     = $dir . '/' . $filename;

        $process = new Process([
            'mysqldump',
            '--user=' . $db['username'],
            '--host=' . ($db['host'] ?? '127.0.0.1'),
            '--port=' . ($db['port'] ?? 3306),
            '--single-transaction',
            '--routines',
            '--triggers',
            $db['database'],
        ], null, ['MYSQL_PWD' => $db['password']]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('mysqldump failed: ' . $process->getErrorOutput());
            return Command::FAILURE;
        }

        file_put_contents($path, $process->getOutput());
        $this->info("Backup saved: {$filename}");

        // Prune old backups
        $retainDays = (int) SystemSetting::get('backup_retention_days', 14);
        $cutoff     = time() - ($retainDays * 86400);
        foreach (glob($dir . '/*.sql') as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $this->info('Pruned old backup: ' . basename($file));
            }
        }

        return Command::SUCCESS;
    }
}
