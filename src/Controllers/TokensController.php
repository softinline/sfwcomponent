<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwToken;
    use Softinline\SfwComponent\SfwComponent;
    use Softinline\SfwComponent\SfwConfig;
    use Yajra\DataTables\Facades\DataTables;

    class TokensController extends Controller
    {
        var $_sfwconfig;

        /**
         * Display the index screen.
         *
         * @return mixed
         */
        public function index() {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tokens/index.json');
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
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tokens/index.json');

            $query = SfwToken::getAll();

            $datatable = Datatables::of($query)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at != '' ? $item->created_at->format('d/m/Y H:i') : '';
                })
                ->addColumn('actions', function ($item) {
                    $return = '';
                    $return .= '<a href="'.url('sfw/tokens/'.$item->id.'/edit').'" title="'.ucfirst(trans('sfwcomponent::detail', [], 'en') ?: 'Detail').'"><i class="las la-eye fa-fw"></i></a>';

                    return $return;
                })
                ->rawColumns(['actions']);

            return $datatable->make(true);

        }

        /**
         * Display the token detail screen.
         *
         * @param mixed $id
         * @return mixed
         */
        public function edit($id) {

            $this->_sfwconfig = new SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Tokens/edit.json');
            $sfwcomponent = new SfwComponent(get_class());
            $sfwcomponent->setItem(SfwToken::getByIdOrAbort($id));
            return $sfwcomponent->render($this->_sfwconfig->getConfig());

        }

        /**
         * Render user email in the detail form.
         *
         * @param array $component
         * @param mixed $item
         * @return array
         */
        public static function beforeRenderUserEmail($component, $item) {

            $email = null;

            if ($item && isset($item->user_id)) {
                $userModel = config('auth.providers.users.model');
                if ($userModel && class_exists($userModel)) {
                    $user = $userModel::find($item->user_id);
                    if ($user) {
                        $email = $user->email ?? null;
                    }
                }
            }

            $component['value'] = $email;

            return $component;

        }

        /**
         * Render the index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex() {

            $data = [
                'title' => ucfirst(trans('sfwcomponent::tokens', [], 'en') ?: 'Tokens'),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::tokens', [], 'en') ?: 'Tokens')],
                ]
            ];

            return view('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);

        }

        /**
         * Render the edit breadcrumb.
         *
         * @param mixed $item
         * @return mixed
         */
        public static function breadcrumbEdit($item) {

            $data = [
                'title' => ucfirst(trans('sfwcomponent::tokens', [], 'en') ?: 'Tokens'),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::tokens', [], 'en') ?: 'Tokens'), url('/sfw/tokens')],
                    [$item->id ?? '']
                ]
            ];

            return view('sfwcomponent::backoffice.partials.breadcrumb', [
                'data' => $data
            ]);

        }
    }
