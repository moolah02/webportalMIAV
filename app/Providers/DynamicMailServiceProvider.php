<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DynamicMailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            if (!Schema::hasTable('system_settings')) return;

            $host = SystemSetting::get('mail_host');
            if (empty($host)) return; // Not configured yet — keep env defaults

            config([
                'mail.default'                      => 'smtp',
                'mail.mailers.smtp.host'            => $host,
                'mail.mailers.smtp.port'            => SystemSetting::get('mail_port', 587),
                'mail.mailers.smtp.username'        => SystemSetting::get('mail_username'),
                'mail.mailers.smtp.password'        => SystemSetting::get('mail_password'),
                'mail.mailers.smtp.encryption'      => SystemSetting::get('mail_encryption', 'tls'),
                'mail.from.address'                 => SystemSetting::get('mail_from_address') ?: config('mail.from.address'),
                'mail.from.name'                    => SystemSetting::get('mail_from_name', config('app.name')),
            ]);
        } catch (\Throwable) {
            // Never crash the app if DB is unavailable
        }
    }
}
