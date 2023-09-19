<?php

    namespace Softinline\SfwComponent\Models;
        
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class SfwConfig extends Model
    {
        // softdeletes
        use SoftDeletes;
        
        // incrementing
        public $incrementing = false;

        // table
        protected $table = 'sfw_configs';

        /**
         * get by id
         */
	    public static function getById($id) {

		    return self::find($id);

        }
    
    }
