<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;

    class SfwQueueJob extends Model
    {
        protected $table = 'jobs';
        public $timestamps = false;
        protected $guarded = [];

        /**
         * Get all queued jobs.
         *
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAll()
        {
            return self::select('jobs.*')->orderBy('id');
        }

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwQueueJob|null
         */
        public static function getById($id)
        {
            return self::find($id);
        }
    }
