<?php

use Carbon\Carbon;

if (!function_exists('fmt_number')) {
    function fmt_number(float|int|null $value, int $decimals = 0): string
    {
        if ($value === null) {
            return '—';
        }
        $locale = app()->getLocale();
        [$dec, $thou] = $locale === 'it' ? [',', '.'] : ['.', ','];
        return number_format((float) $value, $decimals, $dec, $thou);
    }
}

if (!function_exists('fmt_date')) {
    function fmt_date(Carbon|string|null $date, string $format = 'd M Y'): string
    {
        if ($date === null) {
            return '—';
        }
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $carbon->locale(app()->getLocale())->translatedFormat($format);
    }
}
