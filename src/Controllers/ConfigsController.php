<?php

    namespace Softinline\SfwComponent\Controllers;

    use App\Http\Controllers\Controller;
    use Softinline\SfwComponent\Models\SfwConfig;

    class ConfigsController extends Controller
    {
        /**
         * Retention config keys managed by this screen.
         *
         * @var array
         */
        protected static $retentionKeys = [
            'sfw_jobs.retention.history.days',
            'sfw_email_logs.retention.days',
            'sfw_jobs.retention.failed_jobs.days',
        ];

        var $_sfwconfig;

        /**
         * Display the configs screen.
         *
         * @return mixed
         */
        public function index()
        {
            $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
            $this->_sfwconfig->load(__DIR__.'/../Defines/Configs/index.json');
            $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
            $sfwcomponent->setItem(self::buildItem());
            return $sfwcomponent->render($this->_sfwconfig->getConfig());
        }

        /**
         * Update retention config values.
         *
         * @return mixed
         */
        public function update()
        {
            foreach (self::$retentionKeys as $key) {
                $field = self::keyToField($key);
                $value = \Request::get($field, 0);
                if ($value === '' || $value === null) {
                    $value = 0;
                }
                SfwConfig::setValue($key, (int) $value);
            }

            if (\Request::expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst(trans('sfwcomponent::updated_ok')),
                ]);
            }

            return redirect()->to(url('sfw/configs'))->with('message_success', ucfirst(trans('sfwcomponent::updated_ok')));
        }

        /**
         * Build a virtual item with current config values.
         *
         * @return object
         */
        protected static function buildItem()
        {
            $item = new \stdClass();
            foreach (self::$retentionKeys as $key) {
                $field = self::keyToField($key);
                $item->{$field} = SfwConfig::getInt($key, 0);
            }
            return $item;
        }

        /**
         * Convert config key to form field name.
         *
         * @param string $key
         * @return string
         */
        protected static function keyToField($key)
        {
            return str_replace('.', '_', $key);
        }

        /**
         * Render the configs index breadcrumb.
         *
         * @return mixed
         */
        public static function breadcrumbIndex()
        {
            $data = [
                'title' => ucfirst(trans('sfwcomponent::configs')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::configs'))],
                ]
            ];
            return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
        }
    }
