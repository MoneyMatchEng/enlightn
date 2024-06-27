<?php

namespace Enlightn\Enlightn\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Enlightn\Enlightn\Helpers\Format;
use Enlightn\Enlightn\Reporting\ReportBuilder;

abstract class BaseNotification extends Notification
{
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

        $applicationName = $this->applicationName();
        $meta = array_get($reportBuilder, 'metadata');
        $analyzerResults = array_get($reportBuilder, 'analyzer_results');
        $analyzerStats = array_get($reportBuilder, 'analyzer_stats');

        return collect([
            'Application' => $applicationName,
            'meta' => $meta,
            'analyzer_results' => $analyzerResults,
            'analyzer_stats' => $analyzerStats,
        ])->filter();

    }

    public function reportBuilder(): ?array
    {
        if (isset($this->event->report)) {
            $reportBuilder = $this->event->report;
            return $reportBuilder;
        }
        return null;
    }

}
