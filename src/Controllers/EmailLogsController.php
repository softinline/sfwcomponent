<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwEmailLog;
    use Yajra\DataTables\Facades\DataTables;

    class EmailLogsController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the email logs index screen.
         *
         * @return mixed
         */
        public function index()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailLogs/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Provide datatable data for email logs.
         *
         * @return mixed
         */
        public function data()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/EmailLogs/index.json');

            $query = SfwEmailLog::getAll();

            $datatable = Datatables::of($query)
                ->addColumn('email', function ($item) {
                    return $item->to ?? '';
                })
                ->editColumn('status', function ($item) {
                    $status = (string) ($item->status ?? '');
                    if ($status === 'sent') {
                        return '<span class="badge badge-success">'.e(ucfirst(trans('sfwcomponent::sent'))).'</span>';
                    }
                    if ($status === 'error') {
                        return '<span class="badge badge-danger" title="'.e($item->error ?? '').'">'.e(ucfirst(trans('sfwcomponent::error'))).'</span>';
                    }
                    return $status !== '' ? '<span class="badge badge-secondary">'.e($status).'</span>' : '';
                })
                ->editColumn('sent_at', function ($item) {
                    return $item->sent_at ? $item->sent_at->format('d/m/Y H:i') : '';
                })
                ->editColumn('created_at', function ($item) {
                    return $item->created_at ? $item->created_at->format('d/m/Y H:i') : '';
                })
                ->addColumn('actions', function ($item) {
                    return '';
                })
                ->rawColumns(['status', 'actions']);

            return $datatable->make(true);
        }

        /**
         * Render the email logs index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::email-logs')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::email-logs'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
