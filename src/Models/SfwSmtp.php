<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwSmtp extends Model
    {
        use SoftDeletes;
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $table = 'sfw_smtps';

        const SMTP = 1;
        const AMAZON = 2;

        protected $guarded = [];

        protected $casts = [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwSmtp|false|null
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
            return self::select('sfw_smtps.*');
        }

        /**
         * Get the default SMTP record.
         *
         * @return \Softinline\SfwComponent\Models\SfwSmtp|null
         */
        public static function getDefault()
        {
            return self::where('is_default', 1)->first();
        }
                
    }