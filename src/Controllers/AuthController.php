<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
        
    class AuthController extends Controller
    {

        /**
         * login
         */
        public function login() {
                        
            if(\Request::isMethod('get')) {

                return view('sfwcomponent::backoffice.auth.login', [
                ]);  

            }
            else {

                if(\Request::get('email') == 'admin@sfw.com' && \Request::get('password') == '1234') {

                    \Session::put('sfw-user-logged', true);
          
                    return \Redirect::to('/sfw');

                }
                else {

                    echo 'User and Password, not found';

                }

            }
            
        }

        public function logoff() {
            
            \Session::forget('sfw-user-logged');
          
            return \Redirect::to('/sfw');

        }
        
    }