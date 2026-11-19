<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwConfig extends Model
    {
        use SoftDeletes;
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $table = 'sfw_configs';
        protected $keyType = 'string';

        protected $guarded = [];

        protected $casts = [
            'meta' => 'array',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwConfig|false|null
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
            return self::select('sfw_configs.*')->orderBy('key');
        }

        /**
         * Get the first config record.
         *
         * @return \Softinline\SfwComponent\Models\SfwConfig|null
         */
        public static function getFirst()
        {
            return self::select('sfw_configs.*')->first();
        }

        /**
         * Get a config record by key.
         *
         * @param string $key
         * @return \Softinline\SfwComponent\Models\SfwConfig|null
         */
        public static function getByKey($key)
        {
            return self::where('key', $key)->first();
        }

        /**
         * Get a config value by key.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public static function getValue($key, $default = null)
        {
            $item = self::getByKey($key);
            if (!$item) {
                return $default;
            }
            $value = $item->value;
            return $value === null || $value === '' ? $default : $value;
        }

        /**
         * Get a config value as integer.
         *
         * @param string $key
         * @param int $default
         * @return int
         */
        public static function getInt($key, $default = 0)
        {
            $value = self::getValue($key, null);
            if ($value === null || $value === '') {
                return (int) $default;
            }
            return (int) $value;
        }

        /**
         * Set a config value by key (upsert).
         *
         * @param string $key
         * @param mixed $value
         * @param array|null $meta
         * @return \Softinline\SfwComponent\Models\SfwConfig|false
         */
        public static function setValue($key, $value, $meta = null)
        {
            $item = self::getByKey($key);
            if (!$item) {
                $item = new self();
                $prefix = config('sfw.id_prefixes.sfw_configs', 'CF');
                $item->id = uniqid($prefix);
                $item->key = $key;
            }

            $item->value = $value;
            if ($meta !== null) {
                $item->meta = $meta;
            }

            if ($item->save()) {
                return $item;
            }

            return false;
        }
    }
