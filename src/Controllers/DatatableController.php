<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
        
    class DatatableController extends Controller
    {

        /**
         * back entry point index
         */
        public function configColumnsSave() {
                        
            \Log::info('Config Received -> '.print_r(\Request::all(), true));

            $config = [];
            $sortables = explode(',', \Request::get('sortable'));
            foreach($sortables as $sortable) {
                \Log::info('Sortable -> '.$sortable);
                $key = 'json-'.$sortable;
                if(\Request::has($key)) {
                    $params = json_decode(\Request::get($key), true);
                    if(!\Request::get('check-'.$sortable)) {
                        $params['default'] = false;
                    }                    
                    else {
                        $params['default'] = true;
                    }
                    $config[] = $params;
                }
            }
            \Log::info('config -> '.print_r($config, true));

            if(\Auth::user()) {

                $datatableConfigCols = \Softinline\SfwComponent\Models\SfwDatatable::select()
                    ->where('datatable', '=', \Request::get('name'))
                    ->where('user_id', '=', \Auth::user()->id)
                    ->first();

            }
            else {

                $datatableConfigCols = \Softinline\SfwComponent\Models\SfwDatatable::select()
                    ->where('datatable', '=', \Request::get('name'))                
                    ->first();

            }

            if(!$datatableConfigCols) {

                $datatableConfigCols = new \Softinline\SfwComponent\Models\SfwDatatable();
                $datatableConfigCols->id = uniqid("DTC");
                $datatableConfigCols->user_id = \Auth::user() ? \Auth::user()->id : null;
                $datatableConfigCols->datatable = \Request::get('name');
                                
            }
                        
            $datatableConfigCols->config = json_encode($config);
            $datatableConfigCols->save();

            return 'ok';
            
        }
        
    }