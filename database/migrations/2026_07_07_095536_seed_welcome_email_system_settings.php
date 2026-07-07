<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $rows = [
            ['key'=>'welcome_email_enabled',     'value'=>'1',    'type'=>'bool',   'label'=>'Send Welcome Email',          'group'=>'mail', 'description'=>'Email new employees their login credentials when their account is created'],
            ['key'=>'welcome_email_force_reset',  'value'=>'1',    'type'=>'bool',   'label'=>'Require Password Reset on First Login', 'group'=>'mail', 'description'=>'Force the employee to change their password after first login'],
        ];
        foreach ($rows as $row) {
            DB::table('system_settings')->insertOrIgnore(array_merge($row, [
                'created_at' => $now, 'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', ['welcome_email_enabled','welcome_email_force_reset'])->delete();
    }
};
