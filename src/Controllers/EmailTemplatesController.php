<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwEmailTemplate;
    use Yajra\DataTables\Facades\DataTables;

    class EmailTemplatesController extends Controller
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/index.json');
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/index.json');

            $query = SfwEmailTemplate::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/email-templates/'.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->email_template ?? $item->key ?? $item->id).'" class="sfwcomponent-delete" title="'.ucfirst(trans('sfwcomponent::delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['actions']);

            return $datatable->make(true);
        }

        /**
         * Display the add screen.
         *
         * @return mixed
         */
        public function add()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/add.json');
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/add.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->submit($this->_sfwconfig, '_create');
        }

        /**
         * Create a new record.
         *
         * @return mixed
         */
        public static function _create()
        {
            $item = new SfwEmailTemplate();
            $item->id = uniqid('ET');
            $item->email_template = \Request::get('email_template');
            $item->key = \Request::get('key');
            $item->subject = \Request::get('subject');
            $item->body = \Request::get('body');
            $item->default = \Request::get('default') ? 1 : 0;

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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwEmailTemplate::getByIdOrAbort($id));
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwEmailTemplate::getByIdOrAbort($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-email-templates-edit');
        }

        /**
         * Update a record.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function _update($item)
        {
            $item->email_template = \Request::get('email_template') != '' ? \Request::get('email_template') : null;
            $item->key = \Request::get('key') != '' ? \Request::get('key') : null;
            $item->subject = \Request::get('subject') != '' ? \Request::get('subject') : null;
            $item->body = \Request::get('body') != '' ? \Request::get('body') : null;
            $item->default = \Request::get('default') ? 1 : 0;

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
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailTemplates/edit.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwEmailTemplate::getByIdOrAbort($id));
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
         * Render the index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::email-templates')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::email-templates'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }

        /**
         * Render the add breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbAdd()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::email-templates')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::email-templates')), url('/sfw/email-templates')],
                    [trans('sfwcomponent::add')]
                ]
            ];
            return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
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
                'title' => ucfirst(trans('sfwcomponent::email-templates')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::email-templates')), url('/sfw/email-templates')],
                    [$item->email_template ?? $item->key ?? $item->id]
                ]
            ];
            return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
