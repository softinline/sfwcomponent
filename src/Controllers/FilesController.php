<?php

    namespace Softinline\SfwComponent\Controllers;

    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class FilesController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Files/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Files/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwFile::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/files/edit?id='.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';                  
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.$item->file.'" class="sfwcomponent-delete" title="'.ucfirst(trans('messages.delete')).'"><i class="las la-trash fa-fw"></i></a>';
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
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/add.json');                                    
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }
        
        /**
         * create
         */
        public function create() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->submit($this->_sfwconfig, '_create');

        }

        /**
         * _create
         */
        public static function _create() {

            $item = new \App\Models\SfwSmtp();
            $item->id = uniqid("S");            
            $item->smtp = \Request::get('smtp');            
            $item->type = \Request::get('type');
            $item->from_address = \Request::get('from_address');
            $item->smtp_host = \Request::get('smtp_host');
            $item->smtp_port = \Request::get('smtp_port');
            $item->smtp_user = \Request::get('smtp_user');
            $item->smtp_password = \Request::get('smtp_password');
            $item->smtp_encription = \Request::get('smtp_encription');
            $item->aws_host = \Request::get('aws_host');
            $item->aws_port = \Request::get('aws_port');
            $item->aws_user = \Request::get('aws_user');
            $item->aws_encription = \Request::get('aws_encription');
            $item->aws_key = \Request::get('aws_key');
            $item->aws_secret = \Request::get('aws_secret');
            $item->aws_region = \Request::get('aws_region');

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
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\App\Models\SfwSmtp::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * update
         */
        public function update() {

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\App\Models\SfwSmtp::getById($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update');
            
        }

        /**
         * update
         * @return bool
         */                
        public static function _update($item) {

            $item->smtp = \Request::get('smtp');
            $item->type = \Request::get('type');
            $item->from_address = \Request::get('from_address');
            $item->smtp_host = \Request::get('smtp_host');
            $item->smtp_port = \Request::get('smtp_port');
            $item->smtp_user = \Request::get('smtp_user');
            $item->smtp_password = \Request::get('smtp_password');
            $item->smtp_encription = \Request::get('smtp_encription');
            $item->aws_host = \Request::get('aws_host');
            $item->aws_port = \Request::get('aws_port');
            $item->aws_user = \Request::get('aws_user');
            $item->aws_encription = \Request::get('aws_encription');
            $item->aws_key = \Request::get('aws_key');
            $item->aws_secret = \Request::get('aws_secret');
            $item->aws_region = \Request::get('aws_region');

            if($item->save()) {
                return $item;
            }

        }

        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/super')],
                    [ucfirst(trans('messages.smtps')), url('/back/super/smtps')],                    
                ]
            ];
                        
            return view('back.partials.breadcrumb', [
                'data' => $data
            ]);
            
        }

        /**
         * breadcrumbAdd
         */
        public static function breadcrumbAdd() {

            $data = [
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/super')],
                    [ucfirst(trans('messages.smtps')), url('/back/super/smtps')],
                    [trans('messages.add')]
                ]
            ];

            return View('back.partials.breadcrumb', [
                'data' => $data
            ]);
                        
        }

        /**
         * breadcrumbEdit
         */
        public static function breadcrumbEdit($item) {

            $data = [
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/super')],
                    [ucfirst(trans('messages.smtps')), url('/back/super/smtps')],
                    [$item->smtp]
                ]
            ];

            return View('back.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }

    }