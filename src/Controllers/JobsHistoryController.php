<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwJobHistory;
    use Yajra\DataTables\Facades\DataTables;

    class JobsHistoryController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the jobs history screen.
         *
         * @return mixed
         */
        public function index()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Jobs/history.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Provide datatable data for jobs history.
         *
         * @return mixed
         */
        public function data()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Jobs/history.json');

            $query = SfwJobHistory::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('status', function ($item) {
                    $status = (string) ($item->status ?? '');
                    if ($status === 'succeeded') {
                        return '<span class="badge badge-success">'.e(ucfirst(trans('sfwcomponent::succeeded'))).'</span>';
                    }
                    if ($status === 'failed') {
                        return '<span class="badge badge-danger">'.e(ucfirst(trans('sfwcomponent::failed'))).'</span>';
                    }
                    if ($status === 'processing') {
                        return '<span class="badge badge-warning">'.e(ucfirst(trans('sfwcomponent::processing'))).'</span>';
                    }
                    return $status !== '' ? '<span class="badge badge-secondary">'.e($status).'</span>' : '';
                })
                ->editColumn('payload', function ($item) {
                    $payload = $item->payload;
                    if (is_array($payload)) {
                        $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);
                    }
                    if (is_string($payload) && strlen($payload) > 100) {
                        return e(substr($payload, 0, 100)).'...';
                    }
                    return e($payload);
                })
                ->editColumn('exception', function ($item) {
                    $exception = $item->exception;
                    if (is_string($exception) && strlen($exception) > 100) {
                        return e(substr($exception, 0, 100)).'...';
                    }
                    return e($exception);
                })
                ->editColumn('started_at', function ($item) {
                    return $item->started_at ? $item->started_at->format('d/m/Y H:i:s') : '';
                })
                ->editColumn('finished_at', function ($item) {
                    return $item->finished_at ? $item->finished_at->format('d/m/Y H:i:s') : '';
                })
                ->editColumn('created_at', function ($item) {
                    return $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '';
                })
                ->addColumn('actions', function ($item) {
                    return '<a href="'.url('sfw/jobs/history/'.$item->id).'" title="'.ucfirst(trans('sfwcomponent::detail')).'"><i class="las la-eye fa-fw"></i></a>';
                })
                ->rawColumns(['status', 'actions']);

            return $datatable->make(true);
        }

        /**
         * Display history detail screen.
         *
         * @param string $id
         * @return mixed
         */
        public function detail($id)
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Jobs/history_detail.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $item = SfwJobHistory::getById($id);
            if (!$item) {
                abort(404);
            }

            $payload = $item->payload;
            if (is_array($payload)) {
                $item->payload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $sfwcomponent->setItem($item);
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Render the jobs history index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::jobs_history')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::jobs'))],
                    [ucfirst(trans('sfwcomponent::history'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }

        /**
         * Render the jobs history detail breadcrumb.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function breadcrumbDetail($item)
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::jobs_history')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::jobs'))],
                    [ucfirst(trans('sfwcomponent::history')), url('/sfw/jobs/history')],
                    [$item->id]
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
