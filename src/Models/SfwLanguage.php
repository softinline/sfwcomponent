<?php

namespace Softinline\SfwComponent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

class SfwLanguage extends Model
{
    use SoftDeletes;
    use HasSfwRecordAccess;

    public $incrementing = false;
    protected $table = 'sfw_languages';
    protected $keyType = 'string';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get a record by ID.
     *
     * @param mixed $id
     * @return \Softinline\SfwComponent\Models\SfwLanguage|false|null
     */
    public static function getById($id)
    {
        static::sfwClearLastAccessError();
        $item = self::find($id);
        if (!$item) {
            static::sfwDenyAccess(404, 'object_not_exists');
            return false;
        }
        return $item;
    }

    /**
     * Get all records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return self::query()->orderByDesc('is_default')->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * Get all active records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllActive()
    {
        return self::query()->where('is_active', 1)->orderByDesc('is_default')->orderBy('sort_order')->orderBy('id')->get();
    }

    /**
     * Get default language.
     *
     * @return \Softinline\SfwComponent\Models\SfwLanguage|null
     */
    public static function getDefault()
    {
        return self::query()->orderByDesc('is_default')->orderBy('sort_order')->orderBy('id')->first();
    }
}
