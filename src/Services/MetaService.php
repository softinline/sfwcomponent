<?php

namespace Softinline\SfwComponent\Services;

/**
 * Service for reading and writing JSON meta values on Eloquent records.
 */
class MetaService
{
    /**
     * Get all meta values from a record.
     *
     * @param object $record
     * @return array|null
     */
    public static function getAll($record): ?array
    {
        $meta = self::readMeta($record);

        if ($meta === null || $meta === []) {
            return null;
        }

        return $meta;
    }

    /**
     * Get a meta value by key from a record.
     *
     * @param object $record
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($record, string $key, $default = null)
    {
        $meta = self::readMeta($record);

        if ($meta === null || !array_key_exists($key, $meta)) {
            return $default;
        }

        return $meta[$key];
    }

    /**
     * Set meta values on a record.
     *
     * @param object $record
     * @param array $values
     * @return bool
     */
    public static function set($record, array $values): bool
    {
        $meta = self::readMeta($record);
        if ($meta === null) {
            $meta = [];
        }

        foreach ($values as $key => $value) {
            if ($value === null) {
                unset($meta[$key]);
            } else {
                $meta[$key] = $value;
            }
        }

        if ($meta === []) {
            $meta = null;
        }

        return self::persistMeta($record, $meta);
    }

    /**
     * Delete a meta value by key from a record.
     *
     * @param object $record
     * @param string $key
     * @return bool
     */
    public static function delete($record, string $key): bool
    {
        $meta = self::readMeta($record);

        if ($meta === null || !array_key_exists($key, $meta)) {
            return true;
        }

        unset($meta[$key]);

        if ($meta === []) {
            $meta = null;
        }

        return self::persistMeta($record, $meta);
    }

    /**
     * Read and normalize meta data from a record.
     *
     * @param mixed $record
     * @return array|null
     */
    private static function readMeta($record): ?array
    {
        if (is_array($record)) {
            $meta = $record['meta'] ?? null;
        } elseif (is_object($record) && method_exists($record, 'getAttribute')) {
            $meta = $record->getAttribute('meta');
        } elseif (is_object($record)) {
            $meta = $record->meta ?? null;
        } else {
            return null;
        }

        if ($meta === null || $meta === '') {
            return null;
        }

        if (is_string($meta)) {
            $decoded = json_decode($meta, true);
            $meta = is_array($decoded) ? $decoded : null;
        }

        if (is_object($meta)) {
            $meta = (array) $meta;
        }

        if (!is_array($meta) || $meta === []) {
            return null;
        }

        return $meta;
    }

    /**
     * Persist normalized meta data to a record.
     *
     * @param mixed $record
     * @param array|null $meta
     * @return bool
     */
    private static function persistMeta($record, ?array $meta): bool
    {
        if (is_array($record)) {
            return false;
        }

        if (is_object($record) && method_exists($record, 'setAttribute')) {
            $record->setAttribute('meta', $meta);
        } elseif (is_object($record)) {
            $record->meta = $meta;
        } else {
            return false;
        }

        if (method_exists($record, 'saveQuietly')) {
            return (bool) $record->saveQuietly();
        }

        if (method_exists($record, 'save')) {
            return (bool) $record->save();
        }

        return false;
    }
}
