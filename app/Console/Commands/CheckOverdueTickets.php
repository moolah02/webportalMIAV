<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Notifications\SystemNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckOverdueTickets extends Command
{
    protected $signature   = 'tickets:check-overdue';
    protected $description = 'Notify admins of tickets that have exceeded their resolution time';

    public function handle(): int
    {
        if (!SystemSetting::get('notify_ticket_overdue', true)) {
            $this->info('Overdue ticket notifications are disabled.');
            return Command::SUCCESS;
        }

        $thresholdHours = (int) SystemSetting::get('notify_overdue_hours', 24);
        $cutoff         = now()->subHours($thresholdHours);

        $overdue = Ticket::whereIn('status', ['open', 'in_progress'])
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue tickets found.');
            return Command::SUCCESS;
        }

        $admins = $this->getAdmins();
        $count  = $overdue->count();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new SystemNotification(
                    "⚠️ {$count} Overdue Ticket" . ($count > 1 ? 's' : ''),
                    "{$count} ticket" . ($count > 1 ? 's have' : ' has') . " been open for more than {$thresholdHours} hours without resolution.",
                    'ticket',
                    route('tickets.index')
                ));

                $fromAddress = SystemSetting::get('mail_from_address');
                if ($fromAddress && filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::raw(
                        "Dear {$admin->name},\n\n{$count} ticket(s) have been open for more than {$thresholdHours} hours:\n\n" .
                        $overdue->map(fn($t) => "- #{$t->ticket_id}: {$t->title} (opened {$t->created_at->diffForHumans()})")->implode("\n") .
                        "\n\nPlease review them at your earliest convenience.",
                        fn($m) => $m->to($admin->email)->subject("⚠️ {$count} Overdue Ticket(s) – Revival Technologies")
                    );
                }
            } catch (\Throwable $e) {
                $this->warn("Could not notify {$admin->email}: " . $e->getMessage());
            }
        }

        $this->info("Notified {$admins->count()} admin(s) about {$count} overdue ticket(s).");
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
