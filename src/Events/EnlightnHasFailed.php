<?php

namespace Enlightn\Enlightn\Events;

use Enlightn\Enlightn\Reporting\ReportBuilder;
use Exception;

class EnlightnHasFailed
{
    public function __construct(
        public Exception $exception,
        public ?Array $report = null,
    ) {
    }
}
