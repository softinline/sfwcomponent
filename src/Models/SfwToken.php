<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwToken extends Model
    {
        use HasSfwRecordAccess;

        protected $table = 'tokens';

        protected $guarded = [];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwToken|false
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
            return self::select('tokens.*');
        }
    }
