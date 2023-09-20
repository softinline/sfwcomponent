<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
        
    class HomeController extends Controller
    {

        /**
         * back entry point index
         */
        public function index() {

            return view('sfwcomponent::backoffice.home.index', [
            ]);  
            
        }
        
    }