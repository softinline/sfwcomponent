<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class AuditsController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwAudit::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/audits/'.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);
            
            return $datatable->make(true);            
            
        }
                                        
        /**
         * edit
         */
        public function edit($id) {    

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwAudit::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }
               
        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.audits')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.audits'))],                    
                ]
            ];
                        
            return view('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
            
        }
        
        /**
         * breadcrumbEdit
         */
        public static function breadcrumbEdit($item) {

            $data = [
                'title' => ucfirst(trans('messages.audits')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.audits')), url('/sfw/audits')],
                    [$item->id]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }

    }