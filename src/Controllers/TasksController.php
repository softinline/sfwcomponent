<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class TasksController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tasks/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tasks/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwTask::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/tasks/edit?id='.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';                  
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.$item->id.'" class="sfwcomponent-delete" title="'.ucfirst(trans('messages.delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);
            
            return $datatable->make(true);            
            
        }
                                        
        /**
         * edit
         */
        public function edit() {    

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tasks/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwTask::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * update
         */
        public function update() {

            $id = \Request::get('id');

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tasks/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwTask::getById($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update');
            
        }

        /**
         * update
         * @return bool
         */                
        public static function _update($item) {

            $item->status = \Request::get('status');
                        
            if($item->save()) {
                return $item;
            }

        }

        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.tasks')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.tasks'))],                    
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
                'title' => ucfirst(trans('messages.tasks')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.tasks')), url('/sfw/tasks')],
                    [$item->smtp]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }

    }