<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Softinline\SfwComponent\Models\SfwRole;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller
{
    var $_sfwconfig;

    /**
     * Display the index screen.
     *
     * @return mixed
     */
    public function index()
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/index.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Provide datatable data.
     *
     * @return mixed
     */
    public function data()
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/index.json');

        $query = SfwRole::getAll();

        $datatable = Datatables::of($query)
            ->editColumn('is_active', function ($item) {
                return $item->is_active ? '<i class="las la-check-circle text-success"></i>' : '';
            })
            ->editColumn('is_system', function ($item) {
                return $item->is_system ? '<i class="las la-check-circle text-success"></i>' : '';
            })
            ->addColumn('actions', function ($item) {
                $return = '';
                $return .= '<a href="' . url('sfw/roles/' . $item->id) . '" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="' . $item->id . '" sfwcomponent-data-url="' . $this->_sfwconfig->getParam('url') . '" sfwcomponent-data-datatable="' . \Request::get('datatable') . '" sfwcomponent-data-title="' . e($item->name ?? $item->key ?? $item->id) . '" class="sfwcomponent-delete" title="' . ucfirst(trans('sfwcomponent::delete')) . '"><i class="las la-trash fa-fw"></i></a>';
                return $return;
            })
            ->rawColumns(['is_active', 'is_system', 'actions']);

        return $datatable->make(true);
    }

    /**
     * Display the add screen.
     *
     * @return mixed
     */
    public function add()
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle create form submission.
     *
     * @return mixed
     */
    public function create()
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->submit($this->_sfwconfig, '_create');
    }

    /**
     * Create a new record.
     *
     * @return mixed
     */
    public static function _create()
    {
        $item = new SfwRole();
        $item->id = uniqid('R');
        $item->name = \Request::get('name');
        $item->key = \Request::get('key') != '' ? \Request::get('key') : null;
        $item->description = \Request::get('description') != '' ? \Request::get('description') : null;
        $item->is_system = \Request::has('is_system') ? true : false;
        $item->is_active = \Request::has('is_active') ? true : false;

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
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwRole::getByIdOrAbort($id));
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
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwRole::getByIdOrAbort($id));
        return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-roles-edit');
    }

    /**
     * Update a record.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function _update($item)
    {
        $item->name = \Request::get('name');
        $item->key = \Request::get('key') != '' ? \Request::get('key') : null;
        $item->description = \Request::get('description') != '' ? \Request::get('description') : null;
        $item->is_system = \Request::has('is_system') ? true : false;
        $item->is_active = \Request::has('is_active') ? true : false;

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
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Roles/index.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwRole::getByIdOrAbort($id));
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
            'title' => ucfirst(trans('sfwcomponent::roles')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::roles'))],
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
            'title' => ucfirst(trans('sfwcomponent::roles')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::roles')), url('/sfw/roles')],
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
            'title' => ucfirst(trans('sfwcomponent::roles')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::roles')), url('/sfw/roles')],
                [$item->name ?? $item->key ?? $item->id]
            ]
        ];

        return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
