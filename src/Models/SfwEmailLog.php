<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwEmailLog extends Model
    {
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $table = 'sfw_email_logs';

        protected $guarded = [];

        protected $casts = [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwEmailLog|false|null
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
            return self::select('sfw_email_logs.*')
                ->selectRaw('COALESCE(t.key, t.email_template, sfw_email_logs.sfw_email_template_id) as email_template')
                ->leftJoin('sfw_email_templates as t', 't.id', '=', 'sfw_email_logs.sfw_email_template_id')
                ->orderByDesc('sfw_email_logs.created_at');
        }
    }
