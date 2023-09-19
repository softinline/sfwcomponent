<?php

    namespace Softinline\SfwComponent\Models;
        
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class SfwUser extends Authenticatable
    {
        // softdeletes
        use SoftDeletes;

        // user types
        const SUPER = 1;
        const ADMIN = 2;

        // incrementing
        public $incrementing = false;

        // table
        protected $table = 'sfw_users';
    
    }
