<?php

    namespace Softinline\SfwComponent\Controllers;

    use Softinline\SfwComponent;
    use App\Http\Controllers\Controller;
    use Yajra\DataTables\Facades\DataTables;
    use Softinline\SfwComponent\Models\SfwSmtp;

    class SmtpsController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the index screen.
         *
         * @return mixed
         */
        public function index()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Provide datatable data.
         *
         * @return mixed
         */
        public function data()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/index.json');
            $query = SfwSmtp::getAll();
            if (\Request::get('type') != '') {
                $query->where('type', '=', \Request::get('type'));
            }
            $datatable = Datatables::of($query)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/smtps/'.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" onclick=sfwcomponent.sendSmtpTestEmail("'.$item->id.'")><i class="las la-envelope"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->name ?? '').'" class="sfwcomponent-delete" title="'.ucfirst(trans('sfwcomponent::delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);
            return $datatable->make(true);
        }

        /**
         * Get selector options.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function selectTypes($item = null)
        {
            return [
                1 => 'SMTP',
                2 => 'Amazon',
            ];
        }

        /**
         * Get selector options.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function selectEncriptions($item = null)
        {
            return [
                1 => trans('sfwcomponent::none').' (null)',
                2 => 'tls',
                3 => 'ssl',
            ];
        }

        /**
         * Display the add screen.
         *
         * @return mixed
         */
        public function add()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Handle create form submission.
         *
         * @return mixed
         */
        public function create()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->submit($this->_sfwconfig, '_create', 'frm-sfw-smtps-add');
        }

        /**
         * Create a new record.
         *
         * @return mixed
         */
        public static function _create()
        {
            $item = new SfwSmtp();
            $item->id = uniqid('S');
            $item->name = \Request::get('name') != '' ? \Request::get('name') : null;
            $item->type = \Request::get('type') != '' ? \Request::get('type') : null;
            $item->from_email = \Request::get('from_email') != '' ? \Request::get('from_email') : null;
            $item->is_default = \Request::get('is_default') ? 1 : 0;
            $item->smtp_host = \Request::get('smtp_host') != '' ? \Request::get('smtp_host') : null;
            $item->smtp_port = \Request::get('smtp_port') != '' ? \Request::get('smtp_port') : null;
            $item->smtp_user = \Request::get('smtp_user') != '' ? \Request::get('smtp_user') : null;
            $item->smtp_password = \Request::get('smtp_password') != '' ? \Request::get('smtp_password') : null;
            $item->smtp_encryption = \Request::get('smtp_encryption') != '' ? \Request::get('smtp_encryption') : null;                
            $item->aws_host = \Request::get('aws_host') != '' ? \Request::get('aws_host') : null;
            $item->aws_port = \Request::get('aws_port') != '' ? \Request::get('aws_port') : null;
            $item->aws_user = \Request::get('aws_user') != '' ? \Request::get('aws_user') : null;
            $item->aws_password = \Request::get('aws_password') != '' ? \Request::get('aws_password') : null;
            $item->aws_encryption = \Request::get('aws_encryption') != '' ? \Request::get('aws_encryption') : null;
            $item->aws_key = \Request::get('aws_key') != '' ? \Request::get('aws_key') : null;
            $item->aws_secret = \Request::get('aws_secret') != '' ? \Request::get('aws_secret') : null;
            $item->aws_region = \Request::get('aws_region') != '' ? \Request::get('aws_region') : null;

            if ($item->save()) {
                return $item;
            }

            return false;

        }

        /**
         * Display the edit screen.
         *
         * @param string $id
         * @return mixed
         */
        public function edit($id)
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwSmtp::getByIdOrAbort($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Handle update form submission.
         *
         * @param string $id
         * @return mixed
         */
        public function update($id)
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwSmtp::getByIdOrAbort($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-smtps-edit');
        }

        /**
         * Update a record.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function _update($item)
        {
            \Log::info(print_r(\Request::all(), true));
            $item->name = \Request::get('name') != '' ? \Request::get('name') : null;
            $item->type = \Request::get('type') != '' ? \Request::get('type') : null;
            $item->from_email = \Request::get('from_email') != '' ? \Request::get('from_email') : null;
            $item->is_default = \Request::get('is_default') ? 1 : 0;
            $item->smtp_host = \Request::get('smtp_host') != '' ? \Request::get('smtp_host') : null;
            $item->smtp_port = \Request::get('smtp_port') != '' ? \Request::get('smtp_port') : null;
            $item->smtp_user = \Request::get('smtp_user') != '' ? \Request::get('smtp_user') : null;
            $item->smtp_password = \Request::get('smtp_password') != '' ? \Request::get('smtp_password') : null;
            $item->smtp_encryption = \Request::get('smtp_encryption') != '' ? \Request::get('smtp_encryption') : null;
            $item->aws_host = \Request::get('aws_host') != '' ? \Request::get('aws_host') : null;
            $item->aws_port = \Request::get('aws_port') != '' ? \Request::get('aws_port') : null;
            $item->aws_user = \Request::get('aws_user') != '' ? \Request::get('aws_user') : null;
            $item->aws_password = \Request::get('aws_password') != '' ? \Request::get('aws_password') : null;
            $item->aws_encryption = \Request::get('aws_encryption') != '' ? \Request::get('aws_encryption') : null;
            $item->aws_key = \Request::get('aws_key') != '' ? \Request::get('aws_key') : null;
            $item->aws_secret = \Request::get('aws_secret') != '' ? \Request::get('aws_secret') : null;
            $item->aws_region = \Request::get('aws_region') != '' ? \Request::get('aws_region') : null;

            if ($item->save()) {
                return $item;
            }

            return false;
        }

        /**
         * Delete a record.
         *
         * @param string $id
         * @return mixed
         */
        public function delete($id)
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Smtps/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwSmtp::getByIdOrAbort($id));
            return $sfwcomponent->delete($this->_sfwconfig->getConfig(), '_delete');
        }

        /**
         * Perform record deletion.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function _delete($item)
        {
            return (bool) $item->delete();
        }

        /**
         * Send a test email.
         *
         * @return mixed
         */
        public static function testEmail($id)
        {
            $smtp = SfwSmtp::getById($id) ?: SfwSmtp::getDefault();
            \Softinline\SfwComponent\Services\EmailsService::send('notificaciones@softinline.com', $smtp, 'test', ['name' => 'dani']);
        }

        /**
         * Render the index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::smtps')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::smtps'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
        }

        /**
         * Render the add breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbAdd()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::smtps')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::smtps')), url('/sfw/smtps')],
                    [trans('sfwcomponent::add')]
                ]
            ];
            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
        }

        /**
         * Render the edit breadcrumb.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function breadcrumbEdit($item)
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::smtps')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::smtps')), url('/sfw/smtps')],
                    [$item->name ?? $item->id]
                ]
            ];
            return View('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);
        }

        /**
         * Get selector options.
         *
         * @param mixed $item
         * @return mixed
         */
        /*
        public static function selectDependend1($item = null)
        {
            if (\Request::has('term')) {
                if (\Request::get('term') == 1) {
                    $arr = [
                        ['id'  => 1, 'description' => 'SMTP 1'],
                        ['id'  => 2, 'description' => 'SMTP 2'],
                    ];
                } else {
                    $arr = [
                        ['id'  => 3, 'description' => 'AMAZON 1'],
                        ['id'  => 4, 'description' => 'AMAZON 2'],
                    ];
                }
                return response()->json($arr);
            }
            return [
                1 => 'SMTP 1',
                2 => 'SMTP 2',
            ];
        }
        */

        /**
         * Get selector options.
         *
         * @param mixed $item
         * @return mixed
         */
        /*
        public static function selectDependend2()
        {
            if (\Request::has('term')) {
                $term = \Request::get('term');
                $map = [
                    1 => [[ 'id' => 1, 'description' => 'SMTP 11'], [ 'id' => 2, 'description' => 'SMTP 12']],
                    2 => [[ 'id' => 1, 'description' => 'SMTP 21'], [ 'id' => 2, 'description' => 'SMTP 22']],
                    3 => [[ 'id' => 1, 'description' => 'AMAZON 11'], [ 'id' => 2, 'description' => 'AMAZON 12']],
                    4 => [[ 'id' => 1, 'description' => 'AMAZON 21'], [ 'id' => 2, 'description' => 'AMAZON 22']],
                ];
                return response()->json($map[$term] ?? []);
            }
            return [
                1 => 'SMTP 11',
                2 => 'SMTP 22',
            ];
        }
        */
    }
