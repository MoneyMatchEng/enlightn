<?php

namespace Enlightn\Enlightn\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForMail(): string | array
    {
        return config('enlightn.notifications.mail.to');
    }

    public function routeNotificationForSlack(): string
    {
        return config('enlightn.notifications.slack.webhook_url');
    }

    public function getKey(): int
    {
        return 1;
    }
}
