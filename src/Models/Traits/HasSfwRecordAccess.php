<?php

namespace Softinline\SfwComponent\Models\Traits;

trait HasSfwRecordAccess
{
    protected static ?int $sfwLastAccessHttpCode = null;
    protected static ?string $sfwLastAccessKey = null;

    public static function sfwClearLastAccessError(): void
    {
        static::$sfwLastAccessHttpCode = null;
        static::$sfwLastAccessKey = null;
    }

    public static function sfwDenyAccess(int $httpCode, string $key): void
    {
        static::$sfwLastAccessHttpCode = $httpCode;
        static::$sfwLastAccessKey = $key;
    }

    public static function getLastAccessHttpCode(): ?int
    {
        return static::$sfwLastAccessHttpCode;
    }

    public static function getLastAccessKey(): ?string
    {
        return static::$sfwLastAccessKey;
    }

    public static function getByIdInsecure($id)
    {
        static::sfwClearLastAccessError();
        return static::find($id);
    }

    public static function getByIdInsecureOrAbort($id)
    {
        $item = static::getByIdInsecure($id);
        if ($item) {
            return $item;
        }
        abort(404, 'object_not_exists');
    }

    public static function getByIdOrAbort($id)
    {
        $item = static::getById($id);
        if ($item) {
            return $item;
        }

        $code = static::getLastAccessHttpCode() ?? 404;
        $key = static::getLastAccessKey() ?? 'object_not_exists';
        abort($code, $key);
    }
}
