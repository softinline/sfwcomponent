<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class LanguagesController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwLanguage::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/languages/edit?id='.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';                  
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.$item->language.'" class="sfwcomponent-delete" title="'.ucfirst(trans('messages.delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);
            
            return $datatable->make(true);            
            
        }
                
        /**
         * add
         */
        public function add() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }
        
        /**
         * create
         */
        public function create() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->submit($this->_sfwconfig, '_create'); //,

        }

        /**
         * _create
         */
        public static function _create() {

            $item = new \Softinline\SfwComponent\Models\SfwLanguage();
            $item->id = uniqid("L");

            if($item->save()) {
                return $item;
            }

            return false;

        }

        /**
         * edit
         */
        public function edit() {    

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwLanguage::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * update
         */
        public function update() {

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwLanguage::getById($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update');
            
        }

        /**
         * update
         * @return bool
         */                
        public static function _update($item) {

            $item->language = \Request::get('language');
            $item->iso = \Request::get('iso');
            
            if($item->save()) {
                return $item;
            }

        }

        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.languages')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.languages'))],
                ]
            ];
                        
            return view('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
            
        }

        /**
         * breadcrumbAdd
         */
        public static function breadcrumbAdd() {

            $data = [
                'title' => ucfirst(trans('messages.languages')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.languages')), url('/sfw/languages')],
                    [trans('messages.add')]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
                        
        }

        /**
         * breadcrumbEdit
         */
        public static function breadcrumbEdit($item) {

            $data = [
                'title' => ucfirst(trans('messages.languages')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.smtps')), url('/sfw/languages')],
                    [$item->smtp]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }

    }