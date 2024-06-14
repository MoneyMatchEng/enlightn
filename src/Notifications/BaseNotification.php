<?php

namespace Enlightn\Enlightn\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Enlightn\Enlightn\Helpers\Format;
use Enlightn\Enlightn\Reporting\ReportBuilder;

abstract class BaseNotification extends Notification
{

    public function __construct(
        public $event,
    ) {
    }

    public function via(): array
    {
        $notificationChannels = config('enlightn.notifications.notifications.'.static::class);

        return array_filter($notificationChannels);
    }

    public function applicationName(): string
    {
        $name = config('app.name') ?? config('app.url') ?? 'Laravel';
        $env = app()->environment();

        return "{$name} ({$env})";
    }

    protected function enlightnScanProperties(): Collection
    {
        $reportBuilder = $this->reportBuilder();

        $appName = $reportBuilder->getAppName();
        $appEnv = $reportBuilder->getAppEnv();
        $appUrl = $reportBuilder->getAppUrl();
        $githubRepo = $reportBuilder->getGithubRepo();
        $commitId = $reportBuilder->getCommitId();
        $trigger = $reportBuilder->getTrigger();

        return collect([
            'application name' => $appName,
            'application environment' => $appEnv,
            'application url' => $appUrl,
            'github repository' => $githubRepo,
            'commit id' => $commitId,
            'trigger' => $trigger,
        ])->filter();

    }

    public function reportBuilder(): ?ReportBuilder
    {
        if (isset($this->event->reportBuilder)) {
            $reportBuilder = $this->event->reportBuilder;
            return $reportBuilder;
        }
        return null;
    }

}
