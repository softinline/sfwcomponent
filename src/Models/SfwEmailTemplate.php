<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwEmailTemplate extends Model
    {
        use SoftDeletes;
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $table = 'sfw_email_templates';

        protected $guarded = [];

        protected $casts = [
            'is_active' => 'boolean',
            'default' => 'boolean',
            'meta' => 'array',
        ];

        /**
         * Get an email template by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwEmailTemplate|false
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
         * Get all email templates query.
         *
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAll()
        {
            return self::select('sfw_email_templates.*');
        }

        /**
         * Get an email template by key.
         *
         * @param string $key
         * @return \Softinline\SfwComponent\Models\SfwEmailTemplate|null
         */
        public static function getByKey($key)
        {
            return self::select()->where('key', '=', $key)->first();
        }
    }
