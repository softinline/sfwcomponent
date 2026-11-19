<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwQueueJob;
    use Yajra\DataTables\Facades\DataTables;

    class JobsController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the jobs queue screen.
         *
         * @return mixed
         */
        public function index()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Jobs/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Provide datatable data for queued jobs.
         *
         * @return mixed
         */
        public function data()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Jobs/index.json');

            $query = SfwQueueJob::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('payload', function ($item) {
                    $payload = $item->payload;
                    if (is_string($payload) && strlen($payload) > 120) {
                        return e(substr($payload, 0, 120)).'...';
                    }
                    return e($payload);
                })
                ->addColumn('status', function ($item) {
                    if (!empty($item->reserved_at)) {
                        return '<span class="badge badge-warning">'.e(ucfirst(trans('sfwcomponent::processing'))).'</span>';
                    }
                    return '<span class="badge badge-info">'.e(ucfirst(trans('sfwcomponent::pending'))).'</span>';
                })
                ->editColumn('reserved_at', function ($item) {
                    return $item->reserved_at ? date('d/m/Y H:i', (int) $item->reserved_at) : '';
                })
                ->editColumn('available_at', function ($item) {
                    return $item->available_at ? date('d/m/Y H:i', (int) $item->available_at) : '';
                })
                ->editColumn('created_at', function ($item) {
                    return $item->created_at ? date('d/m/Y H:i', (int) $item->created_at) : '';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->id).'" class="sfwcomponent-delete" title="'.ucfirst(trans('sfwcomponent::delete')).'"><i class="las la-trash fa-fw"></i></a>';
                    return $return;
                })
                ->rawColumns(['status', 'actions']);

            return $datatable->make(true);
        }

        /**
         * Delete a queued job.
         *
         * @param mixed $id
         * @return mixed
         */
        public function delete($id)
        {
            $item = SfwQueueJob::getById($id);
            if (!$item) {
                if (\Request::expectsJson()) {
                    return response()->json(['success' => false, 'message' => ucfirst(trans('sfwcomponent::deleted_error'))], 404);
                }
                abort(404);
            }

            $deleted = (bool) $item->delete();

            if (\Request::expectsJson()) {
                return response()->json([
                    'success' => $deleted,
                    'message' => ucfirst(trans($deleted ? 'sfwcomponent::deleted_ok' : 'sfwcomponent::deleted_error')),
                ]);
            }

            return redirect()->to(url('sfw/jobs'))->with($deleted ? 'message_success' : 'message_error', ucfirst(trans($deleted ? 'sfwcomponent::deleted_ok' : 'sfwcomponent::deleted_error')));
        }

        /**
         * Render the jobs index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::jobs_queue')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::jobs'))],
                    [ucfirst(trans('sfwcomponent::queue'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
