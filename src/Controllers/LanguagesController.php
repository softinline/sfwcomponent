<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwLanguage;
    use Softinline\SfwComponent\SfwComponent;
    use Softinline\SfwComponent\SfwConfig;
    use Yajra\DataTables\Facades\DataTables;

    class LanguagesController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the index screen.
         *
         * @return mixed
         */
        public function index() {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/index.json');
            $sfwcomponent = new SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * Provide datatable data.
         *
         * @return mixed
         */
        public function data() {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/index.json');

            $query = SfwLanguage::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at != '' ? $item->created_at->format('d/m/Y') : '';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/languages/'.$item->id).'" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                    $return .= '&nbsp;';
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->language ?? $item->name ?? '').'" class="sfwcomponent-delete" title="'.ucfirst(trans('sfwcomponent::delete')).'"><i class="las la-trash fa-fw"></i></a>';
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
        public function add() {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/add.json');
            $sfwcomponent = new SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * Handle create form submission.
         *
         * @return mixed
         */
        public function create() {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/add.json');
            $sfwcomponent = new SfwComponent(get_class());
            return $sfwcomponent->submit($this->_sfwconfig, '_create');

        }

        /**
         * Create a new record.
         *
         * @return mixed
         */
        public static function _create() {
            $item = new SfwLanguage();
            $item->id = uniqid('L');
            $item->language = \Request::get('language') != '' ? \Request::get('language') : null;
            $item->name = \Request::get('name') != '' ? \Request::get('name') : null;
            $item->iso = \Request::get('iso') != '' ? \Request::get('iso') : null;
            $item->locale = \Request::get('locale') != '' ? \Request::get('locale') : null;

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
        public function edit($id) {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/edit.json');
            $sfwcomponent = new SfwComponent(get_class());
            $sfwcomponent->setItem(SfwLanguage::getByIdOrAbort($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * Handle update form submission.
         *
         * @param string $id
         * @return mixed
         */
        public function update($id) {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/edit.json');
            $sfwcomponent = new SfwComponent(get_class());
            $sfwcomponent->setItem(SfwLanguage::getByIdOrAbort($id));
            return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-languages-edit');

        }

        /**
         * Update a record.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function _update($item) {

            $item->language = \Request::get('language') != '' ? \Request::get('language') : null;
            $item->name = \Request::get('name') != '' ? \Request::get('name') : null;
            $item->iso = \Request::get('iso') != '' ? \Request::get('iso') : null;
            $item->locale = \Request::get('locale') != '' ? \Request::get('locale') : null;

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
        public function delete($id) {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Languages/index.json');
            $sfwcomponent = new SfwComponent(get_class());
            $sfwcomponent->setItem(SfwLanguage::getByIdOrAbort($id));
            return $sfwcomponent->delete($this->_sfwconfig->getConfig(), '_delete');

        }

        /**
         * Perform record deletion.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function _delete($item) {
            return (bool) $item->delete();
        }

        /**
         * Render the index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex() {

            $data = [
                'title' => ucfirst(trans('sfwcomponent::languages')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::languages'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);

        }

        /**
         * Render the add breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbAdd() {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::languages')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::languages')), url('/sfw/languages')],
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
        public static function breadcrumbEdit($item) {

            $data = [
                'title' => ucfirst(trans('sfwcomponent::languages')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::languages')), url('/sfw/languages')],
                    [$item->language ?? $item->name ?? $item->id]
                ]
            ];

            return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
            
        }
    }
