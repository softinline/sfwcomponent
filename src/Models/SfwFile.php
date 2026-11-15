<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwFile extends Model
    {
        use SoftDeletes;
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $keyType = 'string';
        protected $table = 'sfw_files';

        protected $guarded = [];

        protected $casts = [
            'is_main' => 'boolean',
            'size' => 'float',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwFile|false|null
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
         * Get a record by ID without access checks.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwFile|null
         */
        public static function getByIdInsecure($id)
        {
            return self::find($id);
        }

        /**
         * Get all records query.
         *
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAll()
        {
            return self::select('sfw_files.*');
        }

    }
