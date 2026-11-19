<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Softinline\SfwComponent\Models\SfwScreen;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwConfig;
use Yajra\DataTables\Facades\DataTables;

class ScreensController extends Controller
{
    var $_sfwconfig;

    /**
     * Display index screen.
     *
     * @return mixed
     */
    public function index() {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/index.json');
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
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/index.json');
        $query = SfwScreen::getAllScreens();
        $datatable = Datatables::of($query)
            ->editColumn('created_at', function($item) {
                return $item->created_at != '' ? $item->created_at->format("d/m/Y") : '';
            })
            ->addColumn('actions', function($item) {
                $return = '';
                $return .= '<a href="'.url('sfw/screens/'.$item->id).'" title="'.e(ucfirst(trans('sfwcomponent::edit'))).'"><i class="las la-edit fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="'.url('sfw/screens/'.$item->id.'/detail').'" title="'.e(ucfirst(trans('sfwcomponent::detail'))).'"><i class="las la-sitemap fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="'.url('sfw/screens/'.$item->id.'/duplicate').'" title="'.e(ucfirst(trans('sfwcomponent::duplicate'))).'"><i class="las la-copy fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="'.url('sfw/screens/'.$item->id.'/export').'" title="'.e(ucfirst(trans('sfwcomponent::export'))).'"><i class="las la-download fa-fw"></i></a>';
                $return .= '&nbsp;';
                $return .= '<a href="javascript:void(0)" sfwcomponent-data-id="'.$item->id.'" sfwcomponent-data-url="'.$this->_sfwconfig->getParam('url').'" sfwcomponent-data-datatable="'.\Request::get('datatable').'" sfwcomponent-data-title="'.e($item->name ?? $item->key ?? '').'" class="sfwcomponent-delete" title="'.e(ucfirst(trans('sfwcomponent::delete'))).'"><i class="las la-trash fa-fw"></i></a>';
                return $return;
            })
            ->rawColumns(['actions']);
        return $datatable->make(true);
    }

    /**
     * Get selectable component types.
     *
     * @param mixed $item
     * @return array
     */
    public static function selectTypes($item = null) {
        return [
            'autocomplete' => 'autocomplete',
            'button' => 'button',
            'checkbox-multiple' => 'checkbox-multiple',
            'checkbox' => 'checkbox',
            'ckeditor' => 'ckeditor',
            'colorpicker' => 'colorpicker',
            'datatable' => 'datatable',
            'datatable-column' => 'datatable column',
            'date' => 'date',
            'datetime' => 'datetime',
            'div' => 'div',
            'dropdown' => 'dropdown',
            'dropdown-option' => 'dropdown option',
            'email' => 'email',
            'fieldset' => 'fieldset',
            'file' => 'file',
            'form' => 'form',
            'function' => 'function',
            'h' => 'h',
            'hidden' => 'hidden',
            'hr' => 'hr',
            'image' => 'image',
            'json' => 'json',
            'mapbox' => 'mapbox',
            'number' => 'number',
            'password' => 'password',
            'select-multiple' => 'select-multiple',
            'select-popup' => 'select-popup',
            'select' => 'select',
            'select2' => 'select2',
            'tab' => 'tab',
            'tabs' => 'tabs',
            'text' => 'text',
            'textarea' => 'textarea',
            'time-simple' => 'time-simple',
            'time' => 'time',
            'upload-multiple' => 'upload-multiple',
            'view' => 'view',
        ];
    }

    /**
     * Get ajax options for screen forms.
     *
     * @param mixed $item
     * @return array
     */
    public static function selectAjaxOptions($item = null) {
        return [
            'false' => 'false',
            'true' => 'true',
        ];
    }

    /**
     * Display add screen.
     *
     * @return mixed
     */
    public function add() {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle screen create.
     *
     * @return mixed
     */
    public function create() {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->submit($this->_sfwconfig, '_create');
    }

    /**
     * Create screen record.
     *
     * @return mixed
     */
    public static function _create() {
        $meta = self::buildScreenMetaFromRequest();
        $item = new SfwScreen();
        $item->id = SfwScreen::generateId();
        $item->type = 'screen';
        $item->parent_id = null;
        $item->screen_id = $item->id;
        $item->name = \Request::get('name');
        $item->key = \Request::get('key') ?: (Str::slug((string) \Request::get('name'), '_') ?: null);
        $item->meta = json_encode(SfwScreen::prepareMetaForStorage($meta));
        $item->sort_order = 0;
        return $item->save() ? $item : false;
    }

    /**
     * Display edit screen.
     *
     * @param string $id
     * @return mixed
     */
    public function edit($id) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $item = SfwScreen::getByIdOrAbort($id);
        self::hydrateScreenItemForEdit($item);
        $sfwcomponent->setItem($item);
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle screen update.
     *
     * @param string $id
     * @return mixed
     */
    public function update($id) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwScreen::getByIdOrAbort($id));
        return $sfwcomponent->submit($this->_sfwconfig, '_update');
    }

    /**
     * Update screen record.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function _update($item) {
        $meta = self::buildScreenMetaFromRequest($item->getMetaArray());
        $item->name = \Request::get('name');
        $item->key = \Request::get('key') ?: $item->key;
        $item->meta = json_encode(SfwScreen::prepareMetaForStorage($meta));
        return $item->save() ? $item : false;
    }

    /**
     * Display detail screen.
     *
     * @param string $id
     * @return mixed
     */
    public function detail($id) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/detail.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwScreen::getByIdOrAbort($id));
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Render tree detail view.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function generateTree($item) {
        $tree = self::_geRecursiveComponentsToHtml($item, $item->id);
        return view('sfwcomponent::backoffice.screens.detail', [
            'tree' => $tree,
            'screen' => $item,
        ]);
    }

    /**
     * Display global import screen.
     *
     * @return mixed
     */
    public function import() {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/import.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle global import.
     *
     * @return mixed
     */
    public function importExec() {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/import.json');
        $sfwcomponent = new SfwComponent(get_class());
        return $sfwcomponent->submit($this->_sfwconfig, '_importExec');
    }

    /**
     * Execute global import as new screen.
     *
     * @return mixed
     */
    public static function _importExec() {
        $config = self::readUploadedScreenConfig();
        if ($config === false) {
            return false;
        }
        $screen = SfwScreen::importAsNew($config);
        return $screen ? $screen : false;
    }

    /**
     * Import and overwrite a specific screen.
     *
     * @param string $id
     * @return mixed
     */
    public function importScreen($id) {
        $screen = SfwScreen::getByIdOrAbort($id);
        $config = self::readUploadedScreenConfig();
        if ($config === false) {
            return redirect()->to(url('sfw/screens/'.$id.'/detail'))->with('message_error', ucfirst(trans('sfwcomponent::invalid_json')));
        }
        $screen->replaceFromConfig($config);
        return redirect()->to(url('sfw/screens/'.$id.'/detail'))->with('message_success', ucfirst(trans('sfwcomponent::imported_ok')));
    }

    /**
     * Duplicate a screen.
     *
     * @param string $id
     * @return mixed
     */
    public function duplicate($id) {
        $screen = SfwScreen::getByIdOrAbort($id);
        $config = $screen->toConfigArray();

        $baseName = $screen->name ?: ($config['name'] ?? 'screen');
        $config['name'] = $baseName.' ('.trans('sfwcomponent::copy').')';

        $baseKey = $screen->key ?: ($config['key'] ?? Str::slug((string) $baseName, '_'));
        $config['key'] = self::generateUniqueScreenKey($baseKey.'_copy');

        $newScreen = SfwScreen::importAsNew($config);

        return redirect()->to(url('sfw/screens/'.$newScreen->id))->with('message_success', ucfirst(trans('sfwcomponent::duplicated_ok')));
    }

    /**
     * Generate a unique screen key.
     *
     * @param string $baseKey
     * @return string
     */
    private static function generateUniqueScreenKey($baseKey)
    {
        $key = Str::slug((string) $baseKey, '_');
        if ($key === '') {
            $key = 'screen_copy';
        }

        $candidate = $key;
        $i = 2;
        while (SfwScreen::where('key', $candidate)->where('type', 'screen')->exists()) {
            $candidate = $key.'_'.$i;
            $i++;
        }

        return $candidate;
    }

    /**
     * Export screen as JSON file.
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export($id) {
        $screen = SfwScreen::getByIdOrAbort($id);
        $config = $screen->toConfigArray();
        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $filename = ($screen->key ?: $screen->name ?: $screen->id).'.json';
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Display add component screen.
     *
     * @param string $id
     * @param string|null $parentId
     * @return mixed
     */
    public function componentsAdd($id, $parentId = null) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/ScreensComponents/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        $screen = SfwScreen::getByIdOrAbort($id);
        $sfwcomponent->setItem($screen);
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle add component.
     *
     * @param string $id
     * @param string|null $parentId
     * @return mixed
     */
    public function componentsCreate($id, $parentId = null) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/ScreensComponents/add.json');
        $sfwcomponent = new SfwComponent(get_class());
        $screen = SfwScreen::getByIdOrAbort($id);
        $sfwcomponent->setItem($screen);
        return $sfwcomponent->submit($this->_sfwconfig, '_componentsCreate');
    }

    /**
     * Create component record.
     *
     * @param mixed $screen
     * @return mixed
     */
    public static function _componentsCreate($screen) {
        $type = \Request::get('type');
        if (!$type) {
            return false;
        }
        $parentId = \Request::get('parent_id') ?: $screen->id;
        $rootId = $screen->id;
        $meta = ['type' => $type];
        $input = \Request::except(['_token', '_method']);
        foreach ($input as $k => $v) {
            if ($k === 'parent_id') {
                continue;
            }
            if (is_string($v)) {
                $v = trim($v);
            }
            if ($v === '' || $v === null) {
                continue;
            }
            $meta[$k] = $v;
        }
        $meta = self::normalizeComponentMetaForSave($meta);
        $meta['type'] = $type;

        $component = new SfwScreen();
        $component->id = SfwScreen::generateId();
        $component->parent_id = $parentId;
        $component->screen_id = $rootId;
        $component->type = $type;
        $component->name = isset($meta['name']) && $meta['name'] !== '' ? $meta['name'] : null;
        $component->class = isset($meta['class']) && $meta['class'] !== '' ? $meta['class'] : null;
        $component->key = isset($meta['key']) && $meta['key'] !== '' ? $meta['key'] : null;
        $component->meta = json_encode(SfwScreen::prepareMetaForStorage($meta));
        $component->sort_order = (int) SfwScreen::where('parent_id', $parentId)->count();
        return $component->save() ? $component : false;
    }

    /**
     * Render component edit screen.
     *
     * @param string $id
     * @param string $componentId
     * @return mixed
     */
    public function componentsEdit($id, $componentId)
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/ScreensComponents/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $item = SfwScreen::getByIdOrAbort($componentId);
        self::hydrateComponentItemForEdit($item);
        $sfwcomponent->setItem($item);
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Update a screen component.
     *
     * @param string $id
     * @param string $componentId
     * @return mixed
     */
    public function componentsUpdate($id, $componentId)
    {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/ScreensComponents/edit.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwScreen::getByIdOrAbort($componentId));
        return $sfwcomponent->submit($this->_sfwconfig, '_componentsUpdate');
    }

    /**
     * Persist component changes.
     *
     * @param SfwScreen $item
     * @return mixed
     */
    public static function _componentsUpdate($item)
    {
        $meta = $item->getMetaArray();
        $input = \Request::except(['_token', '_method']);

        foreach ($input as $k => $v) {
            if (is_string($v)) {
                $v = trim($v);
            }
            if ($v === '' || $v === null) {
                unset($meta[$k]);
                continue;
            }
            $meta[$k] = $v;
        }

        $meta = self::normalizeComponentMetaForSave($meta);

        $type = \Request::get('type') ?: ($item->type ?: ($meta['type'] ?? null));
        if ($type) {
            $item->type = $type;
            $meta['type'] = $type;
        }
        $item->name = array_key_exists('name', $meta) ? ($meta['name'] !== '' ? $meta['name'] : null) : (\Request::has('name') ? (\Request::get('name') ?: null) : $item->name);
        $item->class = array_key_exists('class', $meta) ? ($meta['class'] !== '' ? $meta['class'] : null) : (\Request::has('class') ? (\Request::get('class') ?: null) : $item->class);
        if (isset($meta['key'])) {
            $item->key = $meta['key'] !== '' ? $meta['key'] : null;
        }

        $item->meta = json_encode(SfwScreen::prepareMetaForStorage($meta));
        return $item->save() ? $item : false;
    }

    /**
     * Delete a screen.
     *
     * @param string $id
     * @return mixed
     */
    public function delete($id) {
        $this->_sfwconfig = new SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Screens/index.json');
        $sfwcomponent = new SfwComponent(get_class());
        $sfwcomponent->setItem(SfwScreen::getByIdOrAbort($id));
        return $sfwcomponent->delete($this->_sfwconfig->getConfig(), '_delete');
    }

    /**
     * Delete screen record.
     *
     * @param mixed $item
     * @return bool
     */
    public static function _delete($item) {
        SfwScreen::deleteChildrenRecursive($item->id);
        return (bool) $item->forceDelete();
    }

    /**
     * Delete a component node.
     *
     * @param string $id
     * @param string $componentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function componentsDelete($id, $componentId)
    {
        $item = SfwScreen::getByIdOrAbort($componentId);
        SfwScreen::deleteChildrenRecursive($item->id);
        $item->forceDelete();
        if (\Request::ajax() || \Request::wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->to(url('sfw/screens/'.$id.'/detail'));
    }

    /**
     * Persist screen tree order.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function order($id)
    {
        $screen = SfwScreen::getByIdOrAbort($id);
        $tree = json_decode((string) \Request::get('tree', '[]'), true);
        if (!is_array($tree)) {
            return response()->json(['ok' => false, 'message' => 'Invalid tree payload'], 422);
        }
        try {
            $nodes = $tree;
            if (count($nodes) === 1 && is_array($nodes[0] ?? null) && (($nodes[0]['id'] ?? null) === $screen->id)) {
                $nodes = is_array($nodes[0]['children'] ?? null) ? $nodes[0]['children'] : [];
            }
            self::_saveTreeOrder($nodes, $screen->id, $screen->id);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Error saving screen tree order: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save tree order recursively.
     *
     * @param array $nodes
     * @param string $parentId
     * @param string $rootScreenId
     * @return void
     */
    private static function _saveTreeOrder(array $nodes, $parentId, $rootScreenId)
    {
        $sort = 0;
        foreach ($nodes as $node) {
            $nodeId = is_array($node) ? ($node['id'] ?? null) : null;
            if (!$nodeId) {
                continue;
            }
            $item = SfwScreen::getById($nodeId);
            if (!$item) {
                continue;
            }
            $item->parent_id = $parentId;
            $item->screen_id = ($item->type === 'screen') ? $item->id : $rootScreenId;
            $item->sort_order = $sort;
            $item->save();
            $sort++;
            $children = $node['children'] ?? null;
            if (is_array($children) && !empty($children)) {
                self::_saveTreeOrder($children, $item->id, $rootScreenId);
            }
        }
    }

    /**
     * Read uploaded screen config from request.
     *
     * @return array|false
     */
    private static function readUploadedScreenConfig()
    {
        $file = \Request::file('file');
        if (!$file) {
            return false;
        }
        $content = File::get($file->getRealPath());
        $config = json_decode($content, true);
        if (!is_array($config)) {
            return false;
        }
        return $config;
    }

    /**
     * Build screen meta from request.
     *
     * @param array $base
     * @return array
     */
    private static function buildScreenMetaFromRequest(array $base = [])
    {
        $meta = $base;
        $meta['ajax'] = SfwScreen::toBool(\Request::get('ajax'));
        $meta['layout'] = \Request::get('layout');
        $meta['url'] = \Request::get('url');
        $meta['translationFile'] = \Request::get('translation_file') ?: \Request::get('translationFile');
        if (\Request::has('beforeLoad')) {
            $meta['beforeLoad'] = \Request::get('beforeLoad');
        }
        if (\Request::has('jsFile')) {
            $meta['jsFile'] = \Request::get('jsFile');
        }
        if (\Request::get('key')) {
            $meta['key'] = \Request::get('key');
        }
        if (\Request::get('name')) {
            $meta['name'] = \Request::get('name');
        }
        return $meta;
    }

    /**
     * Hydrate screen item for edit form.
     *
     * @param SfwScreen $item
     * @return void
     */
    private static function hydrateScreenItemForEdit($item)
    {
        $meta = $item->getMetaArray();
        $item->setAttribute('layout', $meta['layout'] ?? null);
        $item->setAttribute('ajax', isset($meta['ajax']) ? (SfwScreen::toBool($meta['ajax']) ? 'true' : 'false') : 'false');
        $item->setAttribute('url', $meta['url'] ?? null);
        $item->setAttribute('translation_file', $meta['translationFile'] ?? ($meta['translation_file'] ?? null));
        $item->setAttribute('key', $item->key ?: ($meta['key'] ?? null));
    }

    /**
     * Hydrate component item for edit form.
     *
     * @param SfwScreen $item
     * @return void
     */
    private static function hydrateComponentItemForEdit($item)
    {
        $meta = $item->getMetaArray();
        foreach ($meta as $k => $v) {
            if ($k === 'components' || $k === 'columns' || $k === 'options') {
                continue;
            }
            if (is_bool($v)) {
                $item->setAttribute($k, $v ? '1' : '0');
                continue;
            }
            if (is_array($v) || is_object($v)) {
                $item->setAttribute($k, json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                continue;
            }
            $item->setAttribute($k, $v);
        }
        if (array_key_exists('ajax', $meta)) {
            $item->setAttribute('ajax', SfwScreen::toBool($meta['ajax']) ? '1' : '0');
        }
        if (isset($meta['translationFile'])) {
            $item->setAttribute('translation_file', $meta['translationFile']);
        }
        $item->setAttribute('type', $item->type ?: ($meta['type'] ?? null));
        $item->setAttribute('name', $item->name ?? ($meta['name'] ?? null));
        $item->setAttribute('class', $item->class ?? ($meta['class'] ?? null));
    }

    /**
     * Normalize component meta for save.
     *
     * @param array $meta
     * @return array
     */
    private static function normalizeComponentMetaForSave(array $meta)
    {
        if (isset($meta['translation_file']) && !isset($meta['translationFile'])) {
            $meta['translationFile'] = $meta['translation_file'];
            unset($meta['translation_file']);
        }
        if (isset($meta['order.col']) || isset($meta['order.type'])) {
            if (!isset($meta['order']) || !is_array($meta['order'])) {
                $meta['order'] = [];
            }
            if (isset($meta['order.col']) && $meta['order.col'] !== '') {
                $meta['order']['col'] = $meta['order.col'];
            }
            if (isset($meta['order.type']) && $meta['order.type'] !== '') {
                $meta['order']['type'] = $meta['order.type'];
            }
            unset($meta['order.col'], $meta['order.type']);
        }
        return SfwScreen::normalizeMetaTypes($meta);
    }

    /**
     * Build recursive HTML tree.
     *
     * @param SfwScreen $screen
     * @param string $id
     * @param bool $isRoot
     * @return string
     */
    private static function _geRecursiveComponentsToHtml($screen, $id, $isRoot = true)
    {
        $temp = SfwScreen::getById($id);
        if (!$temp) {
            return '';
        }
        $arr = $temp->getMetaArray();
        $str = '';
        if ($isRoot) {
            $str .= '<ul class="uls" id="tree">';
        }
        $str .= '<li id="'.e($temp->id).'" class="lis s-l-open">';
        $str .= '<div class="divs">';
        $str .= '<strong>'.e($temp->type).'</strong>';
        $str .= ' <a href="'.url('sfw/screens/'.$screen->id.'/components/'.$temp->id.'/edit').'" class="clickable ml-1" title="'.e(ucfirst(trans('sfwcomponent::edit'))).'"><i class="la la-pencil"></i></a>';
        if ($temp->type !== 'screen') {
            $str .= ' <a href="javascript:void(0)" class="clickable ml-1 text-danger" onclick="sfwDeleteScreenComponent(\''.$screen->id.'\',\''.$temp->id.'\')" title="'.e(ucfirst(trans('sfwcomponent::delete'))).'"><i class="la la-trash"></i></a>';
        }
        $str .= ' <a href="'.url('sfw/screens/'.$screen->id.'/components/add?parent_id='.$temp->id).'" class="clickable ml-1" title="'.e(ucfirst(trans('sfwcomponent::add'))).'"><i class="la la-plus"></i></a>';

        $details = [];
        if (($temp->name ?? null)) {
            $details[] = 'name ['.$temp->name.']';
        }
        if ($temp->type == 'function' && !empty($arr['function'])) {
            $details[] = 'function ['.$arr['function'].']';
        }
        if (($temp->type == 'button' || $temp->type == 'dropdown-option') && !empty($arr['title'])) {
            $details[] = 'title ['.$arr['title'].']';
        }
        if (!empty($arr['field'])) {
            $details[] = 'field ['.$arr['field'].']';
        }
        if (!empty($arr['class'])) {
            $details[] = 'class ['.$arr['class'].']';
        }
        if (!empty($arr['key'])) {
            $details[] = 'key ['.$arr['key'].']';
        }
        if (!empty($arr['action'])) {
            $details[] = 'action ['.$arr['action'].']';
        }
        if (!empty($details)) {
            $str .= ' <small class="text-muted">| '.e(implode(' | ', $details)).'</small>';
        }
        $str .= '</div>';

        $childrens = SfwScreen::getAllByParentid($temp->id)->get();
        if ($childrens->count() > 0) {
            $str .= '<ul class="uls">';
            foreach ($childrens as $children) {
                $str .= self::_geRecursiveComponentsToHtml($screen, $children->id, false);
            }
            $str .= '</ul>';
        }
        $str .= '</li>';
        if ($isRoot) {
            $str .= '</ul>';
        }
        return $str;
    }

    /**
     * Breadcrumb for index.
     *
     * @return mixed
     */
    public static function breadcrumbIndex() {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens'))],
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Breadcrumb for add.
     *
     * @return mixed
     */
    public static function breadcrumbAdd() {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens')), url('/sfw/screens')],
                [trans('sfwcomponent::add')]
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Breadcrumb for edit.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbEdit($item) {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens')), url('/sfw/screens')],
                [$item->name ?: $item->id]
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Breadcrumb for detail.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbDetail($item) {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens')), url('/sfw/screens')],
                [($item->name ?: $item->id).' - '.ucfirst(trans('sfwcomponent::detail'))]
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Breadcrumb for import.
     *
     * @return mixed
     */
    public static function breadcrumbImport() {
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens')), url('/sfw/screens')],
                [trans('sfwcomponent::import')]
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }

    /**
     * Breadcrumb for component edit.
     *
     * @param mixed $item
     * @return mixed
     */
    public static function breadcrumbComponentsEdit($item) {
        $rootId = $item->screen_id ?: $item->id;
        $data = [
            'title' => ucfirst(trans('sfwcomponent::screens')),
            'items' => [
                [ucfirst(trans('sfwcomponent::dashboard')), url('/sfw')],
                [ucfirst(trans('sfwcomponent::screens')), url('/sfw/screens')],
                [ucfirst(trans('sfwcomponent::detail')), url('/sfw/screens/'.$rootId.'/detail')],
                [ucfirst(trans('sfwcomponent::edit')).' - '.($item->type ?: '')]
            ]
        ];
        return view('sfwcomponent::backoffice.partials.breadcrumb', ['data' => $data]);
    }
}
