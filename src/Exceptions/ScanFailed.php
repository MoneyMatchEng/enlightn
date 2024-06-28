<?php

namespace Enlightn\Enlightn\Exceptions;

use Exception;

/**
 * @method Exception getPrevious()
 */
class ScanFailed extends Exception
{
    public ?Array $report = null;

    public static function from(Exception $exception): static
    {
        return new static($exception->getMessage(), $exception->getCode(), $exception);
    }

    public function destination(Array $report): static
    {
        $this->report = $report;

        return $this;
    }
}
