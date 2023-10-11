<?php

    namespace Softinline\SfwComponent\Controllers;

    use Softinline\SfwComponent;    
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
        
    class SmtpsController extends Controller
    {

        var $_sfwconfig;

        /**
         * index
         */
        public function index() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
            
        }

        /**
         * data
         */
        public function data() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/index.json');
                                    
            $query = \Softinline\SfwComponent\Models\SfwSmtp::all();
                                                
            $datatable = Datatables::of($query)                                
                ->editColumn('created_at', function($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })                
                ->addColumn('actions', function($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/smtps/'.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" onclick=sfwcomponent.sendSmtpTestEmail("'.$item->id.'")><i class="las la-envelope"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.$item->smtp.'" class="sfwcomponent-delete" title="'.ucfirst(trans('messages.delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);
            
            return $datatable->make(true);            
            
        }

        /**
         * selectTypes
         */
        public static function selectTypes($item = null) {

            $return = [
                1 => 'SMTP',
                2 => 'Amazon',                
            ];            

            return $return;

        }

        /**
         * selectEncriptions
         */
        public static function selectEncriptions($item = null) {

            $return = [
                1 => 'ninguna (null)',
                2 => 'tls',
            ];            

            return $return;

        }

        /**
         * add
         */
        public function add() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/add.json');                                    
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

            // samnple to override json config in real time
            // search component
            //$component = $this->_sfwconfig->getByInternalId('fieldSmtp');
            // override params
            //echo print_r($component, true);
            //$component['type'] = 'email';                        
            // set by internal id
            //$this->_sfwconfig->setByInternalId('fieldSmtp', $component);
            //echo print_r($this->_sfwconfig->getConfig(), true);
            //die();

        }
        
        /**
         * create
         */
        public function create() {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            //$config = $this->_sfwconfig->getById('frm-sfw-smtps-add');
            //$config['redirect'] = '/sfw/smtps/{id}';
            //echo print_r($config, true);
            //die();
            //$this->_sfwconfig->setById('frm-sfw-smtps-add', $config);
            return $sfwcomponent->submit($this->_sfwconfig, '_create', 'frm-sfw-smtps-add');

        }

        /**
         * _create
         */
        public static function _create() {

            $item = new \Softinline\SfwComponent\Models\SfwSmtp();
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
        public function edit($id) {

            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwSmtp::getById($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * update
         */
        public function update($id) {
            
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(\Softinline\SfwComponent\Models\SfwSmtp::getById($id));
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
            $item->default = \Request::get('default') ? 1 : 0;
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
         * testEmail
         * send email test
         */
        public static function testEmail($id) {

            // get smtp config
            $smtp = \Softinline\SfwComponent\Services\SmtpsService::getDefault();

            // send email
            \Softinline\SfwComponent\Services\EmailsService::send('notificaciones@softinline.com', $smtp, 'test', ['name' => 'dani']);

        }

        /**
         * breadcrumbIndex
         */
        public static function breadcrumbIndex() {
            
            $data = [
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.smtps'))],                    
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
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.smtps')), url('/sfw/smtps')],
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
                'title' => ucfirst(trans('messages.smtps')),
                'items' => [
                    [ucfirst(trans('messages.dashboard')), url('/sfw')],
                    [ucfirst(trans('messages.smtps')), url('/sfw/smtps')],
                    [$item->smtp]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
                       
        }
        
    }