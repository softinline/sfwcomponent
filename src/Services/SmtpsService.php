<?php

    namespace Softinline\SfwComponent\Services;
    
    use Softinline\SfwComponent;
        
    class SmtpsService
    {

        /**
         * getDefault
         * return the default smtp config
         */
        public static function getDefault() {

            $item = \Softinline\SfwComponent\Models\SfwSmtp::select()
                ->where('default', '=', 1)
                ->first();

            return $item;

        }

    }