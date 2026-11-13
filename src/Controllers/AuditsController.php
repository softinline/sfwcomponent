<?php

    namespace Softinline\SfwComponent\Controllers;
    
    use Softinline\SfwComponent;
    use App\Http\Controllers\Controller;    
    use Yajra\DataTables\Facades\DataTables;
    use Softinline\SfwComponent\Models\SfwAudit;
        
    class AuditsController extends Controller
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/index.json');
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/index.json');

            $query = SfwAudit::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at != '' ? $item->created_at->format("d/m/Y H:i") : '';
                })
                ->editColumn('event', function ($item) {
                    if ($item->event == 'u') {
                        return '<span class="badge badge-warning">'.ucfirst(trans('sfwcomponent::update')).'</span>';
                    }

                    if ($item->event == 'd') {
                        return '<span class="badge badge-danger">'.ucfirst(trans('sfwcomponent::delete')).'</span>';
                    }

                    return '<span class="badge badge-secondary">'.e($item->event).'</span>';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/audits/'.$item->id).'" title="Ver"><i class="las la-eye fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['event', 'actions']);

            return $datatable->make(true);
        }

        /**
         * Display the detail screen.
         *
         * @param string $id
         * @return mixed
         */
        public function edit($id)
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Audits/detail.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(SfwAudit::getByIdOrAbort($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Prepare before field value for detail rendering.
         *
         * @param array $component
         * @param mixed $item
         * @return array
         */
        public static function beforeRenderBefore($component, $item)
        {
            $component['value'] = self::formatAuditJson($item ? $item->before : null);

            return $component;
        }

        /**
         * Prepare after field value for detail rendering.
         *
         * @param array $component
         * @param mixed $item
         * @return array
         */
        public static function beforeRenderAfter($component, $item)
        {
            $component['value'] = self::formatAuditJson($item ? $item->after : null);

            return $component;
        }

        /**
         * Format audit JSON data for display.
         *
         * @param mixed $value
         * @return string
         */
        private static function formatAuditJson($value)
        {
            if ($value === null || $value === '') {
                return '';
            }

            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                return $value;
            }

            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        /**
         * Render the index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::audits')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::audits'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
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
                'title' => ucfirst(trans('sfwcomponent::audits')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::audits')), url('/sfw/audits')],
                    [$item->id]
                ]
            ];
            return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
