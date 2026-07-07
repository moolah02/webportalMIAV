<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SystemSettingController extends Controller
{
    // ── Email Settings ────────────────────────────────────────────

    public function emailIndex()
    {
        $settings = SystemSetting::group('mail');
        return view('settings.email', compact('settings'));
    }

    public function emailUpdate(Request $request)
    {
        $keys = ['mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                // Don't overwrite password if left blank
                if ($key === 'mail_password' && $request->input($key) === '') continue;
                SystemSetting::set($key, $request->input($key, ''));
            }
        }
        return redirect()->route('settings.email')->with('success', 'Email settings saved.');
    }

    public function emailTest(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            // Force reload mail config from DB
            $host = SystemSetting::get('mail_host');
            if (empty($host)) {
                return back()->with('error', 'SMTP host is not configured.');
            }

            config([
                'mail.mailers.smtp.host'       => $host,
                'mail.mailers.smtp.port'       => SystemSetting::get('mail_port', 587),
                'mail.mailers.smtp.username'   => SystemSetting::get('mail_username'),
                'mail.mailers.smtp.password'   => SystemSetting::get('mail_password'),
                'mail.mailers.smtp.encryption' => SystemSetting::get('mail_encryption', 'tls'),
                'mail.from.address'            => SystemSetting::get('mail_from_address'),
                'mail.from.name'               => SystemSetting::get('mail_from_name', 'Revival Technologies'),
            ]);

            Mail::raw('This is a test email from Revival Technologies. Your email settings are working correctly.', function ($msg) use ($request) {
                $msg->to($request->test_email)->subject('Test Email – Revival Technologies');
            });

            return back()->with('success', 'Test email sent to ' . $request->test_email);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    // ── Notification Settings ─────────────────────────────────────

    public function notificationsIndex()
    {
        $settings = SystemSetting::group('notifications');
        return view('settings.notifications', compact('settings'));
    }

    public function notificationsUpdate(Request $request)
    {
        $boolKeys = ['notify_new_ticket', 'notify_ticket_status', 'notify_ticket_overdue', 'notify_license_expiry'];
        $otherKeys = ['notify_overdue_hours', 'notify_license_expiry_days', 'notify_admin_roles'];

        foreach ($boolKeys as $key) {
            SystemSetting::set($key, $request->has($key) ? '1' : '0');
        }
        foreach ($otherKeys as $key) {
            if ($request->has($key)) {
                SystemSetting::set($key, $request->input($key));
            }
        }
        return redirect()->route('settings.notifications')->with('success', 'Notification settings saved.');
    }
}
