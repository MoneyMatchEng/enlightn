<?php

namespace Enlightn\Enlightn\Events;

use Enlightn\Enlightn\Reporting\ReportBuilder;
use Spatie\Backup\BackupDestination\BackupDestination;

class EnlightnWasSuccessful
{
    public function __construct(
        public Array $report,
    ) {
    }
}
