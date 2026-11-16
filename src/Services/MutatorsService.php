<?php

namespace Softinline\SfwComponent\Services;

class MutatorsService
{
    /**
     * Format a date value for display.
     *
     * @param mixed $value
     * @param string $format
     * @return string|null
     */
    public static function formatDate($value, $format = 'd/m/Y H:i:s')
    {
        if ($value === null) {
            return null;
        }

        return $value->format($format);
    }

    /**
     * Format a numeric size value for display.
     *
     * @param mixed $value
     * @param int $decimals
     * @return string|null
     */
    public static function formatSize($value, $decimals = 2)
    {
        if ($value === null) {
            return null;
        }

        return number_format((float) $value, $decimals);
    }

    /**
     * Format a boolean value as yes/no text.
     *
     * @param mixed $value
     * @return string|null
     */
    public static function formatYesNo($value)
    {
        if ($value === null) {
            return null;
        }

        if (!empty($value)) {
            return ucfirst(trans('sfwcomponent::yes'));
        }

        return ucfirst(trans('sfwcomponent::no'));
    }
}