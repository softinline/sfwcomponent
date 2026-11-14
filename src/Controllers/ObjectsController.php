<?php

namespace Softinline\SfwComponent\Controllers;

use Softinline\SfwComponent;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Softinline\SfwComponent\Models\SfwObject;

class ObjectsController extends Controller
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/index.json');
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/index.json');

        $query = SfwObject::getAll();

        $datatable = Datatables::of($query)
            ->editColumn('is_active', function ($item) {
                return $item->is_active ? '1' : '0';
            })
            ->addColumn('actions', function ($item) {
                $return = '';
                $return .= '<a href="'.url('sfw/objects/'.$item->id).'" title="'.ucfirst(trans('sfwcomponent::edit')).'"><i class="las la-edit fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->key ?? $item->name ?? '').'" class="sfwcomponent-delete" title="'.ucfirst(trans('sfwcomponent::delete')).'"><i class="las la-trash fa-fw"></i></a>';
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/add.json');
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/add.json');
        $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
        return $sfwcomponent->submit($this->_sfwconfig, '_create', 'frm-sfw-objects-add');
    }

    /**
     * Create a new record.
     *
     * @return mixed
     */
    public static function _create()
    {
        $item = new SfwObject();
        $item->id = uniqid('O');

        foreach (['key', 'name', 'type', 'route', 'parent_id'] as $f) {
            if (\Request::has($f)) {
                $item->{$f} = \Request::get($f) !== '' ? \Request::get($f) : null;
            }
        }
        if (\Request::has('sort_order')) {
            $item->sort_order = (int) \Request::get('sort_order');
        }
        if (\Request::has('is_active')) {
            $item->is_active = \Request::get('is_active') ? 1 : 0;
        }

        return $item->save() ? $item : false;
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/edit.json');
        $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
        $sfwcomponent->setItem(SfwObject::getByIdOrAbort($id));
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/edit.json');
        $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
        $sfwcomponent->setItem(SfwObject::getByIdOrAbort($id));
        return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-objects-edit');
    }

    /**
     * Update a record.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function _update($item)
    {
        foreach (['key', 'name', 'type', 'route', 'parent_id'] as $f) {
            if (\Request::has($f)) {
                $item->{$f} = \Request::get($f) !== '' ? \Request::get($f) : null;
            }
        }
        if (\Request::has('sort_order')) {
            $item->sort_order = (int) \Request::get('sort_order');
        }
        if (\Request::has('is_active')) {
            $item->is_active = \Request::get('is_active') ? 1 : 0;
        }

        return $item->save() ? $item : false;
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Objects/index.json');
        $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
        $sfwcomponent->setItem(SfwObject::getByIdOrAbort($id));
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
            'title' => ucfirst(trans('sfwcomponent::objects', [], 'en') ?: 'Objects'),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Objects'],
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
            'title' => 'Objects',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Objects', url('/sfw/objects')],
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
            'title' => 'Objects',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Objects', url('/sfw/objects')],
                [$item->key ?? $item->name ?? $item->id]
            ]
        ];
        return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
