<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Softinline\SfwComponent\Models\SfwObject;
use Softinline\SfwComponent\Models\SfwUser;
use Softinline\SfwComponent\Models\SfwUserObject;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/index.json');
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/index.json');

        $sql = SfwUser::getAll();

        return DataTables::of($sql)
            ->editColumn('created_at', function ($item) {
                return $item->created_at ? $item->created_at->format('d/m/Y H:i') : '';
            })
            ->addColumn('actions', function ($item) {
                $return = '';
                $return .= '<a href="' . url('sfw/users/' . $item->id . '/edit') . '" title="Editar"><i class="las la-edit fa-fw"></i></a> ';
                $return .= '<a href="' . url('sfw/users/' . $item->id . '/objects-access') . '" title="Permisos"><i class="las la-lock fa-fw"></i></a> ';
                $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="' . $item->id . '" sfwcomponent-data-url="' . $this->_sfwconfig->getParam('url') . '" sfwcomponent-data-datatable="' . \Request::get('datatable') . '" sfwcomponent-data-title="' . e($item->email ?? $item->name ?? $item->id) . '" class="sfwcomponent-delete" title="' . ucfirst(trans('sfwcomponent::delete')) . '"><i class="las la-trash fa-fw"></i></a>';
                return $return;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Display the add screen.
     *
     * @return mixed
     */
    public function add()
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/add.json');
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/add.json');
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
        $item = new SfwUser();
        $item->id = uniqid('U');
        $item->name = \Request::get('name');
        $item->email = \Request::get('email');
        $item->role = \Request::get('role') ?: 'user';
        $item->status = \Request::get('status') ?: 'pending';
        $item->lover_level = \Request::get('lover_level') != '' ? \Request::get('lover_level') : null;
        $item->timezone = \Request::get('timezone') != '' ? \Request::get('timezone') : null;
        $password = \Request::get('password');
        if (!empty($password)) {
            $item->password = Hash::make($password);
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
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwUser::getByIdOrAbort($id));
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwUser::getByIdOrAbort($id));
        return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-users-edit');
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
        $item->email = \Request::get('email');
        $item->role = \Request::get('role') ?: $item->role;
        $item->status = \Request::get('status') ?: $item->status;
        $item->lover_level = \Request::get('lover_level') != '' ? \Request::get('lover_level') : null;
        $item->timezone = \Request::get('timezone') != '' ? \Request::get('timezone') : null;
        $password = \Request::get('password');
        if (!empty($password)) {
            $item->password = Hash::make($password);
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
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/index.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwUser::getByIdOrAbort($id));
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
     * Display the objects access screen.
     *
     * @param string $id
     * @return mixed
     */
    public function objectsAccess($id)
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/objects-access.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwUser::getByIdOrAbort($id));
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Provide objects access datatable data.
     *
     * @param string $id
     * @return mixed
     */
    public function objectsAccessData($id)
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Users/objects-access.json');

        $sql = SfwUserObject::getAccessForUserId($id)
            ->orderBy('sfw_objects.key')
            ->orderBy('sfw_objects.name');

        return DataTables::of($sql)
            ->addColumn('access', function ($item) use ($id) {
                $checked = ($item->access > 0 ? 'checked' : '');
                return '<input type="checkbox" ' . $checked . ' onclick="sfwUsers.objectAccess(\'' . $id . '\',\'' . $item->id . '\')">';
            })
            ->rawColumns(['access'])
            ->make(true);
    }

    /**
     * Toggle object access for a user.
     *
     * @param string $userId
     * @return mixed
     */
    public function objectsAccessExec($userId)
    {
        $userObject = SfwUserObject::query()
            ->where('user_id', '=', $userId)
            ->where('sfw_object_id', '=', \Request::get('objectId'))
            ->first();

        if (!$userObject) {
            $userObject = new SfwUserObject();
            $userObject->id = uniqid('UO');
            $userObject->user_id = $userId;
            $userObject->sfw_object_id = \Request::get('objectId');
            $userObject->save();
        } else {
            $userObject->delete();
        }

        return $userObject;
    }

    /**
     * Add or remove all object privileges for a user.
     *
     * @param string $userId
     * @return mixed
     */
    public function addAllPrivilegesObjects($userId)
    {
        $objects = SfwObject::query()->select('sfw_objects.id')->get();
        $mode = \Request::get('mode');

        foreach ($objects as $object) {
            $userObject = SfwUserObject::query()
                ->where('user_id', '=', $userId)
                ->where('sfw_object_id', '=', $object->id)
                ->first();

            if ($mode == 'add') {
                if (!$userObject) {
                    $userObject = new SfwUserObject();
                    $userObject->id = uniqid('UO');
                    $userObject->user_id = $userId;
                    $userObject->sfw_object_id = $object->id;
                    $userObject->save();
                }
            } elseif ($mode == 'quit') {
                if ($userObject) {
                    $userObject->delete();
                }
            } else {
                if (!$userObject) {
                    $userObject = new SfwUserObject();
                    $userObject->id = uniqid('UO');
                    $userObject->user_id = $userId;
                    $userObject->sfw_object_id = $object->id;
                    $userObject->save();
                } else {
                    $userObject->delete();
                }
            }
        }

        if (\Request::get('return') == true) {
            return \Redirect::to(url('sfw/users/' . $userId . '/objects-access'))
                ->with('message_success', 'Permisos actualizados');
        }

        return \Redirect::to(url('sfw/users'))
            ->with('message_success', 'Permisos actualizados');
    }

    /**
     * Render the index breadcrumb.
     *
     * @return mixed
     */
    public static function breadcrumbIndex()
    {
        $data = [
            'title' => 'Users',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Users'],
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
            'title' => 'Users',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Users', url('/sfw/users')],
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
            'title' => 'Users',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Users', url('/sfw/users')],
                [$item->email ?? $item->name ?? $item->id]
            ]
        ];
        return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Render the objects access breadcrumb.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbObjectsAccess($item)
    {
        $data = [
            'title' => 'Users',
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                ['Users', url('/sfw/users')],
                ['Permisos'],
                [$item->email ?? $item->name ?? $item->id]
            ]
        ];
        return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
