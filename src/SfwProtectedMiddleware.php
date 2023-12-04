<?php

    namespace Softinline\SfwComponent;

    use Closure;
    use Illuminate\Http\Request;
    
    class SfwProtectedMiddleware
    {
        

        /**
         * Handle an incoming request.
         *
         * @param Request $request
         * @param Closure $next
         *
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            
            if(!\Session::get('sfw-user-logged')) {
                
                if($request->ajax()) {
    
                    return response('Unauthorized.', 401);
    
                } 
                else {
                                    
                    return \Redirect::to('/sfw/auth/login');
    
                }
                    
            }
            
            return $next($request);

        }

    }