<?php

namespace Enlightn\Enlightn\Notifications;

use Enlightn\Enlightn\Events\EnlightnHasFailed;
use Enlightn\Enlightn\Events\EnlightnWasSuccessful;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Enlightn\Enlightn\Exceptions\NotificationCouldNotBeSent;

class EventHandler
{
    public function __construct(
        protected Repository $config
    ) {
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen($this->allEnlightnEventClasses(), function ($event) {
            $notifiable = $this->determineNotifiable();

            $notification = $this->determineNotification($event);

            $notifiable->notify($notification);
        });
    }

    protected function determineNotifiable()
    {
        $notifiableClass = $this->config->get('enlightn.notifications.notifiable');

        return app($notifiableClass);
    }

    protected function determineNotification($event): Notification
    {
        $lookingForNotificationClass = class_basename($event) . "Notification";

        $notificationClass = collect($this->config->get('enlightn.notifications.notifications'))
            ->keys()
            ->first(fn (string $notificationClass) => class_basename($notificationClass) === $lookingForNotificationClass);

        if (! $notificationClass) {
            throw NotificationCouldNotBeSent::noNotificationClassForEvent($event);
        }

        return new $notificationClass($event);
    }

    protected function allEnlightnEventClasses(): array
    {
        return [
            EnlightnHasFailed::class,
            EnlightnWasSuccessful::class,
        ];
    }
}
