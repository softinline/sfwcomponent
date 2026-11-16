<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Softinline\SfwComponent\Models\SfwFile;
use Softinline\SfwComponent\Services\FilesService;
use Softinline\SfwComponent\Services\MutatorsService;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class FilesController extends Controller
{
    /**
     * Display the index screen.
     *
     * @return mixed
     */
    public function index()
    {
        $sfwcomponent = new SfwComponent(get_class());

        return $sfwcomponent->render(self::loadConfig('index')->getConfig());
    }

    /**
     * Provide datatable data.
     *
     * @return mixed
     */
    public function data()
    {
        $datatable = Datatables::of(SfwFile::getAll())
            ->addColumn('preview', function ($item) {
                return self::previewHtml($item, 48);
            })
            ->editColumn('is_main', function ($item) {
                return !empty($item->is_main) ? '<i class="las la-check-circle text-success"></i>' : '';
            })
            ->editColumn('created_at', function ($item) {
                return MutatorsService::formatDate($item->created_at, 'd/m/Y H:i') ?? '';
            })
            ->editColumn('size', function ($item) {
                return MutatorsService::formatSize($item->size) ?? '';
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
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwFile::getByIdOrAbort($id));

        return $sfwcomponent->render(self::loadConfig('edit')->getConfig());
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

        $path = self::resolvePath($item);
        if (self::isImageFile($item, $path)) {
            $preview = self::imagePreviewHtml($path, $item->mime_type ?? '', $maxSize);
            if ($preview !== '') {
                return $preview;
            }
        }

        if ($maxSize <= 64) {
            return '<i class="las la-file" style="font-size:22px;"></i>';
        }

        return self::fileInfoHtml($item, $path);
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
        $component['value'] = FilesService::baseDir(self::objectName($item));

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
        $component['value'] = $item ? (FilesService::baseDir(self::objectName($item)) . '/' . $item->id) : '';

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
        $path = self::resolvePath($item);
        $component['value'] = self::fileExists($path) ? $path : '';

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
        $component['value'] = MutatorsService::formatYesNo(self::fileExists(self::resolvePath($item))) ?? '';

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
        $component['value'] = MutatorsService::formatYesNo($item && !empty($item->is_main)) ?? '';

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
        $component['value'] = MutatorsService::formatSize($item?->size) ?? '';

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
        $component['value'] = MutatorsService::formatDate($item?->created_at, 'd/m/Y H:i:s') ?? '';

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
        $component['value'] = MutatorsService::formatDate($item?->updated_at, 'd/m/Y H:i:s') ?? '';

        return $component;
    }

    /**
     * Render the index breadcrumb.
     *
     * @return mixed
     */
    public static function breadcrumbIndex()
    {
        return view('sfwcomponent::backoffice.partials.breadcrumb', [
            'data' => [
                'title' => ucfirst(trans('sfwcomponent::files')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::files'))],
                ],
            ],
        ]);
    }

    /**
     * Render the detail breadcrumb.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbDetail($item)
    {
        return view('sfwcomponent::backoffice.partials.breadcrumb', [
            'data' => [
                'title' => ucfirst(trans('sfwcomponent::files')),
                'items' => [
                    [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                    [ucfirst(trans('sfwcomponent::files')), url('/sfw/files')],
                    [$item->file ?? $item->id],
                ],
            ],
        ]);
    }

    /**
     * Load a files define config.
     *
     * @param string $name
     * @return SfwConfig
     */
    private static function loadConfig($name)
    {
        $sfwconfig = new SfwConfig();
        $sfwconfig->load(__DIR__ . '/../Defines/Files/' . $name . '.json');

        return $sfwconfig;
    }

    /**
     * Resolve the object name for a file item.
     *
     * @param mixed $item
     * @return string
     */
    private static function objectName($item)
    {
        if (!$item) {
            return 'generic';
        }

        return $item->table ?: 'generic';
    }

    /**
     * Resolve the filesystem path for a file item.
     *
     * @param mixed $item
     * @return string|null
     */
    private static function resolvePath($item)
    {
        if (!$item) {
            return null;
        }

        return FilesService::pathFor($item);
    }

    /**
     * Check whether a file path exists.
     *
     * @param string|null $path
     * @return bool
     */
    private static function fileExists($path)
    {
        return $path && is_file($path);
    }

    /**
     * Check whether the file item is a readable image.
     *
     * @param mixed $item
     * @param string|null $path
     * @return bool
     */
    private static function isImageFile($item, $path)
    {
        if (!self::fileExists($path)) {
            return false;
        }

        $mime = (string) ($item->mime_type ?? '');
        if (str_starts_with($mime, 'image/')) {
            return true;
        }

        $name = (string) ($item->file ?? '');
        return (bool) preg_match('/\.(jpg|jpeg|png|gif|webp|bmp)$/i', $name);
    }

    /**
     * Build image preview HTML.
     *
     * @param string $path
     * @param string $mime
     * @param int $maxSize
     * @return string
     */
    private static function imagePreviewHtml($path, $mime, $maxSize)
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            return '';
        }

        $src = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode($data);
        $style = 'max-width:' . (int) $maxSize . 'px;max-height:' . (int) $maxSize . 'px;object-fit:cover;border-radius:4px;';

        return '<img src="' . e($src) . '" alt="" style="' . $style . '" />';
    }

    /**
     * Build non-image file info HTML.
     *
     * @param mixed $item
     * @param string|null $path
     * @return string
     */
    private static function fileInfoHtml($item, $path)
    {
        $name = $item->file ?? $item->id;

        return '<div class="mb-3"><span class="badge badge-secondary"><i class="las la-file"></i> ' . e($name) . '</span>'
            . ' <span class="ml-2">' . e(ucfirst(trans('sfwcomponent::file_exists'))) . ': ' . e(MutatorsService::formatYesNo(self::fileExists($path)) ?? '') . '</span></div>';
    }
}
