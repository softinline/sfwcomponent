<?php

namespace Softinline\SfwComponent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

class SfwTranslation extends Model
{
    use SoftDeletes;
    use HasSfwRecordAccess;

    public $incrementing = false;
    protected $table = 'sfw_translations';
    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * Get a record by ID.
     *
     * @param mixed $id
     * @return \Softinline\SfwComponent\Models\SfwTranslation|false|null
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
     * Get all records query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getAll()
    {
        return self::select('sfw_translations.*');
    }
}
