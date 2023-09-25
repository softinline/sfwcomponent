<?php

    namespace Softinline\SfwComponent\Models;
        
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class SfwEmailTemplate extends Model
    {
        // softdeletes
        use SoftDeletes;
        
        // incrementing
        public $incrementing = false;

        // table
        protected $table = 'sfw_emails_templates';

        /**
         * get by id
         */
	    public static function getById($id) {

		    return self::find($id);

        }

        /**
         * getByKey
         * get by key
         */
	    public static function getByKey($key) {

            return self::select()
                ->where('key', '=', $key)
                ->first();

        }
    
    }
