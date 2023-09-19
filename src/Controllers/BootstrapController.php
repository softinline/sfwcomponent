<?php

    namespace Softinline\SfwComponent\Controllers;    
    use Softinline\SfwComponent;

    class BootstrapController
    {

        /**
         * entry point load
         */
        public function index($object = null, $action = null) {
            
            // 1 - check if we have configured app
            $sfwConfig = \Softinline\SfwComponent\Models\SfwConfig::first();

            if(!$sfwConfig) {

                return \Redirect::to('sfw/config');

            }

            // 2 - if all is ok process
            if($action == null) {
                $action = 'index';
            }

            if($object == null) {
                $object = 'home';
            }
            
            $file = '\\Softinline\\SfwComponent\\Controllers\\'.ucfirst($object).'Controller';
            
            $controllerObject = new $file();

            $response = $controllerObject->$action();

            return $response;

        }
        
    }