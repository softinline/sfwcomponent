<?php

    namespace Softinline\SfwComponent\Models;
            
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class sfwFile extends Model
    {
        // softdeletes
        use SoftDeletes;

        // incrementing
        public $incrementing = false;

        // table
        protected $table = 'sfw_files';

        /**
         * get by id
         */
	    public static function getById($id) {

		    return self::find($id);

        }
    
    }
