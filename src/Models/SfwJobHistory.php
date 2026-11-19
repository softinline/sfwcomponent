<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;

    class SfwJobHistory extends Model
    {
        public $incrementing = false;
        protected $table = 'sfw_job_history';
        protected $keyType = 'string';

        protected $guarded = [];

        protected $casts = [
            'payload' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwJobHistory|null
         */
        public static function getById($id)
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
            return self::select('sfw_job_history.*')->orderByDesc('created_at');
        }
    }
