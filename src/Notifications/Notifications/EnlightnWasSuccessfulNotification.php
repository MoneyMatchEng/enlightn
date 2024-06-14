<?php

namespace Enlightn\Enlightn\Notifications\Notifications;

use Enlightn\Enlightn\Events\EnlightnWasSuccessful;
use Enlightn\Enlightn\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;

class EnlightnWasSuccessfulNotification extends BaseNotification
{
    public function __construct(
        public EnlightnWasSuccessful $event,
    ) {
    }

    public function toMail(): MailMessage
    {
        $mailMessage = (new MailMessage())
            ->from(config('enlightn.notifications.mail.from.address', config('mail.from.address')), config('enlightn.notifications.mail.from.name', config('mail.from.name')))
            ->subject('Successful enlightn scan of '. $this->applicationName())
            ->line('Great news, a new report of '. $this->applicationName());

        $this->enlightnScanProperties()->each(function ($value, $name) use ($mailMessage) {
            $mailMessage->line("{$name}: $value");
        });

        return $mailMessage;
    }

    public function toSlack(): SlackMessage
    {
        return (new SlackMessage())
            ->success()
            ->from(config('enlightn.notifications.slack.username'), config('enlightn.notifications.slack.icon'))
            ->to(config('enlightn.notifications.slack.channel'))
            ->content('Successful new enlightn scan!')
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields($this->enlightnScanProperties()->toArray());
            });
    }
}
