<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string | int | bool | password | json
            $table->string('label');
            $table->string('group'); // mail | notifications | backup
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $now = now();
        $rows = [
            // ── Mail ──────────────────────────────────────────────────
            ['key'=>'mail_host',         'value'=>'',         'type'=>'string',   'label'=>'SMTP Host',           'group'=>'mail', 'description'=>'e.g. smtp.gmail.com'],
            ['key'=>'mail_port',         'value'=>'587',      'type'=>'int',      'label'=>'SMTP Port',           'group'=>'mail', 'description'=>'Usually 587 (TLS) or 465 (SSL)'],
            ['key'=>'mail_username',     'value'=>'',         'type'=>'string',   'label'=>'SMTP Username',       'group'=>'mail', 'description'=>'Your email / SMTP login'],
            ['key'=>'mail_password',     'value'=>'',         'type'=>'password', 'label'=>'SMTP Password',       'group'=>'mail', 'description'=>'SMTP password or app password'],
            ['key'=>'mail_encryption',   'value'=>'tls',      'type'=>'string',   'label'=>'Encryption',          'group'=>'mail', 'description'=>'tls or ssl'],
            ['key'=>'mail_from_address', 'value'=>'',         'type'=>'string',   'label'=>'From Address',        'group'=>'mail', 'description'=>'Sender email address'],
            ['key'=>'mail_from_name',    'value'=>'Revival Technologies', 'type'=>'string', 'label'=>'From Name', 'group'=>'mail', 'description'=>'Sender display name'],

            // ── Notifications ─────────────────────────────────────────
            ['key'=>'notify_new_ticket',          'value'=>'1',  'type'=>'bool',  'label'=>'New Ticket Created',        'group'=>'notifications', 'description'=>'Email admins & assigned tech when a ticket is opened'],
            ['key'=>'notify_ticket_status',       'value'=>'1',  'type'=>'bool',  'label'=>'Ticket Status Changed',     'group'=>'notifications', 'description'=>'Email the ticket creator when status changes'],
            ['key'=>'notify_ticket_overdue',      'value'=>'1',  'type'=>'bool',  'label'=>'Ticket Overdue Alert',      'group'=>'notifications', 'description'=>'Email admins when a ticket exceeds its resolution time'],
            ['key'=>'notify_overdue_hours',       'value'=>'24', 'type'=>'int',   'label'=>'Overdue Threshold (hours)', 'group'=>'notifications', 'description'=>'Hours after creation before a ticket is considered overdue'],
            ['key'=>'notify_license_expiry',      'value'=>'1',  'type'=>'bool',  'label'=>'License Expiry Warning',    'group'=>'notifications', 'description'=>'Email admins before a license expires'],
            ['key'=>'notify_license_expiry_days', 'value'=>'30', 'type'=>'int',   'label'=>'Expiry Warning (days)',     'group'=>'notifications', 'description'=>'How many days before expiry to send the warning'],
            ['key'=>'notify_admin_roles',         'value'=>'admin,manager', 'type'=>'string', 'label'=>'Admin Role Names', 'group'=>'notifications', 'description'=>'Comma-separated role names that receive admin alerts'],

            // ── Backup ────────────────────────────────────────────────
            ['key'=>'backup_frequency',      'value'=>'daily', 'type'=>'string', 'label'=>'Backup Frequency',   'group'=>'backup', 'description'=>'off | daily | weekly'],
            ['key'=>'backup_retention_days', 'value'=>'14',    'type'=>'int',    'label'=>'Keep Backups (days)', 'group'=>'backup', 'description'=>'Backups older than this are deleted automatically'],
        ];

        foreach ($rows as $row) {
            DB::table('system_settings')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
