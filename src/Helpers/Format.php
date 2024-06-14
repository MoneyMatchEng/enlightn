<?php

namespace Enlightn\Enlightn\Helpers;

use Carbon\Carbon;

class Format
{
    public static function emoji(bool $bool): string
    {
        return $bool
            ? '✅'
            : '❌';
    }

    public static function ageInDays(Carbon $date): string
    {
        return number_format(round($date->diffInMinutes() / (24 * 60), 2), 2).' ('.$date->diffForHumans().')';
    }

    public static function date(): string
    {
        return Carbon::now()->toDateString();
    }

    public static function time(): string
    {
        return Carbon::now()->toTimeString();
    }



}
