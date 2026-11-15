<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Softinline\SfwComponent\Models\SfwFile;
use Softinline\SfwComponent\Services\FilesService;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class FilesController extends Controller
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Files/index.json');
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
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Files/index.json');

        $query = SfwFile::getAll();

        $datatable = Datatables::of($query)
            ->addColumn('preview', function ($item) {
                return self::previewHtml($item, 48);
            })
            ->editColumn('is_main', function ($item) {
                return !empty($item->is_main) ? '<i class="las la-check-circle text-success"></i>' : '';
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at != '' ? $item->created_at->format('d/m/Y H:i') : '';
            })
            ->editColumn('size', function ($item) {
                return number_format((float) ($item->size ?? 0), 2);
            })
            ->addColumn('actions', function ($item) {
                return '<a href="' . url('sfw/files/' . $item->id) . '" title="' . ucfirst(trans('sfwcomponent::detail')) . '"><i class="las la-eye fa-fw"></i></a>';
            })
            ->rawColumns(['preview', 'is_main', 'actions']);

        return $datatable->make(true);
    }

    /**
     * Display the detail screen.
     *
     * @param string $id
     * @return mixed
     */
    public function detail($id)
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__ . '/../Defines/Files/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwFile::getByIdOrAbort($id));

        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Build preview HTML for a file item.
     *
     * @param mixed $item
     * @param int $maxSize
     * @return string
     */
    public static function previewHtml($item, $maxSize = 240)
    {
        if (!$item) {
            return '';
        }

        $path = FilesService::pathFor($item);
        $mime = $item->mime_type ?? '';
        $name = $item->file ?? '';

        $isImage = $path && is_file($path) && (str_starts_with((string) $mime, 'image/') || preg_match('/\.(jpg|jpeg|png|gif|webp|bmp)$/i', (string) $name));

        if ($isImage) {
            $data = @file_get_contents($path);
            if ($data !== false) {
                $mimeHeader = $mime ?: 'image/jpeg';
                $src = 'data:' . $mimeHeader . ';base64,' . base64_encode($data);
                $style = 'max-width:' . (int) $maxSize . 'px;max-height:' . (int) $maxSize . 'px;object-fit:cover;border-radius:4px;';
                return '<img src="' . e($src) . '" alt="" style="' . $style . '" />';
            }
        }

        if ($maxSize <= 64) {
            return '<i class="las la-file" style="font-size:22px;"></i>';
        }

        $exists = ($path && is_file($path)) ? ucfirst(trans('sfwcomponent::yes')) : ucfirst(trans('sfwcomponent::no'));
        return '<div class="mb-3"><span class="badge badge-secondary"><i class="las la-file"></i> ' . e($name ?: $item->id) . '</span> <span class="ml-2">' . e(ucfirst(trans('sfwcomponent::file_exists'))) . ': ' . e($exists) . '</span></div>';
    }

    /**
     * Show file preview for detail screen.
     *
     * @param mixed $item
     * @return string
     */
    public static function showPreview($item)
    {
        return self::previewHtml($item, 320);
    }

    /**
     * Prepare expected folder value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderExpectedFolder($component, $item)
    {
        $object = $item ? ($item->table ?: 'generic') : 'generic';
        $component['value'] = FilesService::baseDir($object);

        return $component;
    }

    /**
     * Prepare expected path value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderExpectedPath($component, $item)
    {
        $object = $item ? ($item->table ?: 'generic') : 'generic';
        $component['value'] = $item ? (FilesService::baseDir($object) . '/' . $item->id) : '';

        return $component;
    }

    /**
     * Prepare actual path value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderActualPath($component, $item)
    {
        $path = $item ? FilesService::pathFor($item) : null;
        $component['value'] = ($path && is_file($path)) ? $path : '';

        return $component;
    }

    /**
     * Prepare file exists value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderFileExists($component, $item)
    {
        $path = $item ? FilesService::pathFor($item) : null;
        $exists = $path && is_file($path);
        $component['value'] = $exists ? ucfirst(trans('sfwcomponent::yes')) : ucfirst(trans('sfwcomponent::no'));

        return $component;
    }

    /**
     * Prepare is_main value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderIsMain($component, $item)
    {
        $isMain = $item && !empty($item->is_main);
        $component['value'] = $isMain ? ucfirst(trans('sfwcomponent::yes')) : ucfirst(trans('sfwcomponent::no'));

        return $component;
    }

    /**
     * Prepare size value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderSize($component, $item)
    {
        $component['value'] = $item ? number_format((float) ($item->size ?? 0), 2) : '';

        return $component;
    }

    /**
     * Prepare created_at value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderCreatedAt($component, $item)
    {
        $component['value'] = ($item && $item->created_at != '') ? $item->created_at->format('d/m/Y H:i:s') : '';

        return $component;
    }

    /**
     * Prepare updated_at value for detail rendering.
     *
     * @param array $component
     * @param mixed $item
     * @return array
     */
    public static function beforeRenderUpdatedAt($component, $item)
    {
        $component['value'] = ($item && $item->updated_at != '') ? $item->updated_at->format('d/m/Y H:i:s') : '';

        return $component;
    }

    /**
     * Render the index breadcrumb.
     *
     * @return mixed
     */
    public static function breadcrumbIndex()
    {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::files')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::files'))],
            ]
        ];

        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Render the detail breadcrumb.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbDetail($item)
    {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::files')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::files')), url('/sfw/files')],
                [$item->file ?? $item->id]
            ]
        ];

        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
