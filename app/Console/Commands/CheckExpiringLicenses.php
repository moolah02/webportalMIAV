<?php

namespace App\Console\Commands;

use App\Models\BusinessLicense;
use App\Models\Employee;
use App\Models\SystemSetting;
use App\Notifications\SystemNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckExpiringLicenses extends Command
{
    protected $signature   = 'licenses:check-expiry';
    protected $description = 'Notify admins of licenses expiring within the configured warning window';

    public function handle(): int
    {
        if (!SystemSetting::get('notify_license_expiry', true)) {
            $this->info('License expiry notifications are disabled.');
            return Command::SUCCESS;
        }

        $days    = (int) SystemSetting::get('notify_license_expiry_days', 30);
        $expiring = BusinessLicense::whereNotNull('expiry_date')
            ->whereNotIn('status', ['cancelled', 'expired'])
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->get();

        if ($expiring->isEmpty()) {
            $this->info('No licenses expiring within ' . $days . ' days.');
            return Command::SUCCESS;
        }

        $admins = $this->getAdmins();
        $count  = $expiring->count();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new SystemNotification(
                    "⚠️ {$count} License" . ($count > 1 ? 's' : '') . " Expiring Soon",
                    "{$count} license" . ($count > 1 ? 's expire' : ' expires') . " within {$days} days.",
                    'system',
                    route('licenses.index')
                ));

                $fromAddress = SystemSetting::get('mail_from_address');
                if ($fromAddress && filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::raw(
                        "Dear {$admin->name},\n\nThe following license(s) expire within {$days} days:\n\n" .
                        $expiring->map(fn($l) => "- {$l->license_name} (expires {$l->expiry_date})")->implode("\n") .
                        "\n\nPlease review and arrange renewal as needed.",
                        fn($m) => $m->to($admin->email)->subject("⚠️ {$count} License(s) Expiring Soon – Revival Technologies")
                    );
                }
            } catch (\Throwable $e) {
                $this->warn("Could not notify {$admin->email}: " . $e->getMessage());
            }
        }

        $this->info("Notified {$admins->count()} admin(s) about {$count} expiring license(s).");
        return Command::SUCCESS;
    }

    private function getAdmins(): \Illuminate\Support\Collection
    {
        $roleNames = collect(explode(',', SystemSetting::get('notify_admin_roles', 'admin,manager')))
            ->map(fn($r) => trim($r))->filter()->values();

        return Employee::whereHas('roles', fn($q) => $q->whereIn('name', $roleNames))
            ->where('status', 'active')
            ->get();
    }
}
