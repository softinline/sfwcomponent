<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Softinline\SfwComponent\Models\SfwTranslation;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class TranslationsController extends Controller
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Translations/index.json');
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Translations/index.json');

        $query = SfwTranslation::getAll();

        $datatable = Datatables::of($query)
            ->editColumn('created_at', function ($item) {
                return $item->created_at != '' ? $item->created_at->format('d/m/Y') : '';
            })
            ->addColumn('actions', function ($item) {
                $return = '';
                $return .= '<a href="' . url('sfw/translations/' . $item->id) . '" title="Editar"><i class="las la-edit fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="' . $item->id . '" sfwcomponent-data-url="' . $this->_sfwconfig->getParam('url') . '" sfwcomponent-data-datatable="' . \Request::get('datatable') . '" sfwcomponent-data-title="' . e($item->id) . '" class="sfwcomponent-delete" title="' . ucfirst(trans('sfwcomponent::delete')) . '"><i class="las la-trash fa-fw"></i></a>';
                return $return;
            })
            ->rawColumns(['actions']);

        return $datatable->make(true);
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Translations/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwTranslation::getByIdOrAbort($id));
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Translations/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwTranslation::getByIdOrAbort($id));
        return $sfwcomponent->submit($this->_sfwconfig, '_update', 'frm-sfw-translations-edit');
    }

    /**
     * Update a record.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function _update($item)
    {
        foreach (['table', 'row_id', 'field', 'translation'] as $f) {
            if (\Request::has($f)) {
                $item->{$f} = \Request::get($f);
            }
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Translations/index.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwTranslation::getByIdOrAbort($id));
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
            'title' => ucfirst(trans('sfwcomponent::translations')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::translations'))],
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
            'title' => ucfirst(trans('sfwcomponent::translations')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::translations')), url('/sfw/translations')],
                [$item->id]
            ]
        ];

        return View('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
