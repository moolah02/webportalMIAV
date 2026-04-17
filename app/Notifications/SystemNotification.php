<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $body;
    public string $type;   // ticket | job | asset | visit | system
    public ?string $url;
    public ?string $icon;

    public function __construct(
        string $title,
        string $body,
        string $type = 'system',
        ?string $url = null,
        ?string $icon = null
    ) {
        $this->title = $title;
        $this->body  = $body;
        $this->type  = $type;
        $this->url   = $url;
        $this->icon  = $icon ?? self::iconFor($type);
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'type'  => $this->type,
            'url'   => $this->url,
            'icon'  => $this->icon,
        ];
    }

    public static function iconFor(string $type): string
    {
        return match ($type) {
            'ticket'  => '🎫',
            'job'     => '📋',
            'asset'   => '📦',
            'visit'   => '📝',
            'employee'=> '👤',
            default   => '🔔',
        };
    }
}
