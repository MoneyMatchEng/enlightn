<?php

namespace Enlightn\Enlightn\Notifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Enlightn\Enlightn\Events\EnlightnHasFailed;
use Enlightn\Enlightn\Notifications\BaseNotification;

class EnlightnHasFailedNotification extends BaseNotification
{
    private array $scanProperties;

    public function __construct(
        public EnlightnHasFailed $event,
    ) {
        $this->scanProperties = $this->enlightnScanProperties()->toArray();
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->subject('Application Analysis Report of '.$this->applicationName().' Failed')
            ->line('**Application:** ' .$this->scanProperties['Application'])
            ->error()
            ->from(config('enlightn.notifications.mail.from.address', config('mail.from.address')), config('enlightn.notifications.mail.from.name', config('mail.from.name')));

            $mailMessage
                ->line('Exception message: ' . $this->event->exception->getMessage())
                ->line('Exception trace: :trace ' . $this->event->exception->getTraceAsString());
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
