<?php

namespace Enlightn\Enlightn\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Enlightn\Enlightn\Events\EnlightnHasFailed;
use Enlightn\Enlightn\Notifications\BaseNotification;

class EnlightnHasFailedNotification extends BaseNotification
{
    public function __construct(
        public EnlightnHasFailed $event,
    ) {
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->error()
            ->from(config('enlightn.notifications.mail.from.address', config('mail.from.address')), config('enlightn.notifications.mail.from.name', config('mail.from.name')))
            ->subject("Failed scan of". $this->applicationName())
            ->line('Important: An error occurred while scanning up ' . $this->applicationName())
            ->line('Exception message: ' . $this->event->exception->getMessage())
            ->line('Exception trace: :trace ' . $this->event->exception->getTraceAsString());

        $this->enlightnScanProperties()->each(fn ($value, $name) => $mailMessage->line("{$name}: $value"));

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage())
            ->error()
            ->from(config('enlightn.notifications.slack.username'), config('enlightn.notifications.slack.icon'))
            ->to(config('enlightn.notifications.slack.channel'))
            ->content('Failed enlightn scan of ' . $this->applicationName())
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title('Exception message')
                    ->content($this->event->exception->getMessage());
            })
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title('Exception trace')
                    ->content($this->event->exception->getTraceAsString());
            })
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->enlightnScanProperties()->toArray());
            });
    }
}
