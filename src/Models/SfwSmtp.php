<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class SfwSmtp extends Model {   

        // softdeletes
        use SoftDeletes;

        // incrementing
        public $incrementing = false;

        // table
        protected $table = 'sfw_smtps';

        // constants
        const SMTP = 1;
        const AMAZON = 2;

        /**
         * getById
         * get by id
         */
	    public static function getById($id) {

            return self::find($id);

        }
    
    }
