<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class RolesController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwRole::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/roles/edit?id='.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';                  
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.$item->role.'" class="sfwcomponent-delete" title="'.ucfirst(trans('messages.delete')).'"><i class="las la-trash fa-fw"></i></a>';
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

            $item = new \Softinline\SfwComponent\Models\SfwRole();
            $item->id = uniqid("R");            
            $item->role = \Request::get('role');            

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
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwRole::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * update
         */
        public function update() {

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(app_path().'/Defines/Sfw/Roles/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwRole::getById($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update');
            
        }

        /**
         * update
         * @return bool
         */                
        public static function _update($item) {

            $item->role = \Request::get('role');

            if($item->save()) {
                return $item;
            }

        }

        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.roles')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/sfw')],
                    [ucfirst(trans('messages.roles')), url('/back/sfw/roles')],                    
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
                'title' => ucfirst(trans('messages.roles')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/sfw')],
                    [ucfirst(trans('messages.roles')), url('/back/sfw/roles')],
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
                'title' => ucfirst(trans('messages.roles')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/back/sfw')],
                    [ucfirst(trans('messages.roles')), url('/back/sfw/roles')],
                    [$item->smtp]
                ]
            ];

            return View('back.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }

    }