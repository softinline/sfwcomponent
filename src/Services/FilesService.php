<?php

namespace Softinline\SfwComponent\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Softinline\SfwComponent\Models\SfwFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FilesService
{
    /**
     * Get the base storage directory for an object/table.
     *
     * @param string $object
     * @return string
     */
    public static function baseDir($object = 'generic')
    {
        $object = $object ?: 'generic';

        return storage_path('files/' . $object);
    }

    /**
     * Ensure the storage directory exists.
     *
     * @param string $object
     * @return string
     */
    public static function ensureDir($object = 'generic')
    {
        $dir = self::baseDir($object);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return $dir;
    }

    /**
     * Get the stored file path for a file record.
     *
     * @param SfwFile|null $item
     * @return string|null
     */
    public static function pathFor($item)
    {
        if (!$item) {
            return null;
        }

        $object = $item->table ?: 'generic';
        $dir = self::baseDir($object);
        $path = $dir . '/' . $item->id;
        if (is_file($path)) {
            return $path;
        }

        $legacyPath = storage_path('app/sfw/files/' . $object . '/' . $item->id);
        if (is_file($legacyPath)) {
            return $legacyPath;
        }

        return $path;
    }

    /**
     * Upload and store a file.
     *
     * @param mixed $file
     * @param string $object
     * @param string|null $rowId
     * @param string|null $category
     * @return SfwFile|null
     */
    public static function upload($file, $object = 'generic', $rowId = null, $category = null)
    {
        if (!$file) {
            return null;
        }

        $object = $object ?: 'generic';
        $dir = self::ensureDir($object) . '/';

        $fileObj = new SfwFile();
        $fileObj->id = uniqid('F');
        $fileObj->sfw_user_id = Auth::check() ? (string) Auth::id() : null;
        $fileObj->sfw_language_id = app()->getLocale();
        $fileObj->table = $object;
        $fileObj->row_id = $rowId;
        $fileObj->category = $category;
        $fileObj->file = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : basename((string) $file);
        $fileObj->mime_type = method_exists($file, 'getClientMimeType') ? $file->getClientMimeType() : null;
        $fileObj->size = method_exists($file, 'getSize') ? ($file->getSize() / 1024) : null;
        $fileObj->disk = 'local';
        $fileObj->path = 'files/' . $object;
        $fileObj->is_main = 0;

        if (!$fileObj->save()) {
            return null;
        }

        try {
            $file->move($dir, $fileObj->id);
        } catch (\Throwable $e) {
            Log::error(__METHOD__ . ' | ' . $e->getMessage());
        }

        return $fileObj;
    }

    /**
     * Resolve the main file for an entity/category.
     *
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @return SfwFile|null
     */
    public static function getMainForEntity($object, $entityId, $category = null)
    {
        $q = SfwFile::query()
            ->where('table', $object)
            ->where('row_id', $entityId);

        if ($category !== null && $category !== '') {
            $q->where('category', $category);
        }

        return $q->where('is_main', 1)->first();
    }

    /**
     * View a file inline by file id.
     *
     * @param string $id
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function view($id, $size = 'original')
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            abort(404, 'File not found');
        }

        return self::respond($item, $size, 'inline');
    }

    /**
     * Download a file by file id.
     *
     * @param string $id
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function download($id, $size = 'original')
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            abort(404, 'File not found');
        }

        return self::respond($item, $size, 'attachment');
    }

    /**
     * View the main file for an entity/category.
     *
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function viewByEntity($object, $entityId, $category = null, $size = 'original')
    {
        $item = self::getMainForEntity($object, $entityId, $category);
        if (!$item) {
            abort(404, 'File not found');
        }

        return self::respond($item, $size, 'inline');
    }

    /**
     * Download the main file for an entity/category.
     *
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function downloadByEntity($object, $entityId, $category = null, $size = 'original')
    {
        $item = self::getMainForEntity($object, $entityId, $category);
        if (!$item) {
            abort(404, 'File not found');
        }

        return self::respond($item, $size, 'attachment');
    }

    /**
     * Serve a file action using URL params (action, object, entity id, category, size).
     *
     * @param string $action
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function serve($action, $object, $entityId, $category = null, $size = 'original')
    {
        if ($action === 'download') {
            return self::downloadByEntity($object, $entityId, $category, $size);
        }

        return self::viewByEntity($object, $entityId, $category, $size);
    }

    /**
     * Build a file response.
     *
     * @param SfwFile $item
     * @param string $size
     * @param string $disposition
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected static function respond($item, $size, $disposition)
    {
        $path = self::pathFor($item);
        if (!$path || !is_file($path)) {
            abort(404, 'File not found');
        }

        $name = $item->file ?: basename($path);
        $mime = $item->mime_type ?: (function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream');

        if (self::isImage($mime, $name) && $size && $size !== 'original') {
            $resized = self::resizeImage($path, $size);
            if ($resized !== null) {
                return response($resized['content'], 200, [
                    'Content-Type' => $resized['mime'],
                    'Content-Disposition' => $disposition . '; filename="' . addslashes($name) . '"',
                ]);
            }
        }

        if ($disposition === 'attachment') {
            return response()->download($path, $name, ['Content-Type' => $mime]);
        }

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
        ]);
    }

    /**
     * Determine if a file is an image.
     *
     * @param string|null $mime
     * @param string|null $name
     * @return bool
     */
    protected static function isImage($mime, $name = null)
    {
        if ($mime && str_starts_with($mime, 'image/')) {
            return true;
        }
        $ext = strtolower(pathinfo((string) $name, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true);
    }

    /**
     * Resize an image on-the-fly.
     *
     * @param string $path
     * @param string $size
     * @return array|null
     */
    protected static function resizeImage($path, $size)
    {
        $dimensions = self::parseSize($size);
        if (!$dimensions) {
            return null;
        }

        try {
            $manager = ImageManager::usingDriver(GdDriver::class);
            $image = $manager->read($path);

            $width = $dimensions['width'];
            $height = $dimensions['height'];

            if ($width && $height) {
                $image->scale($width, $height);
            } elseif ($width) {
                $image->scale(width: $width);
            } elseif ($height) {
                $image->scale(height: $height);
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $encoded = null;
            $mime = 'image/jpeg';

            switch ($ext) {
                case 'png':
                    $encoded = $image->toPng();
                    $mime = 'image/png';
                    break;
                case 'gif':
                    $encoded = $image->toGif();
                    $mime = 'image/gif';
                    break;
                case 'webp':
                    $encoded = $image->toWebp();
                    $mime = 'image/webp';
                    break;
                case 'bmp':
                    $encoded = $image->toBitmap();
                    $mime = 'image/bmp';
                    break;
                default:
                    $encoded = $image->toJpeg();
                    $mime = 'image/jpeg';
                    break;
            }

            return [
                'content' => (string) $encoded,
                'mime' => $mime,
            ];
        } catch (\Throwable $e) {
            Log::error(__METHOD__ . ' | ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Parse a size parameter (w600, h600, 600x400, 600-400).
     *
     * @param string $size
     * @return array|null
     */
    protected static function parseSize($size)
    {
        $size = trim((string) $size);
        if ($size === '' || $size === 'original') {
            return null;
        }

        if (preg_match('/^w(\d+)$/i', $size, $m)) {
            return ['width' => (int) $m[1], 'height' => null];
        }
        if (preg_match('/^h(\d+)$/i', $size, $m)) {
            return ['width' => null, 'height' => (int) $m[1]];
        }
        if (preg_match('/^(\d+)[x\-](\d+)$/i', $size, $m)) {
            return ['width' => (int) $m[1], 'height' => (int) $m[2]];
        }

        return null;
    }

    /**
     * Set the main file by id within its object/row/category scope.
     *
     * @param string $id
     * @return bool
     */
    public static function setMain($id)
    {
        return self::setMainById($id);
    }

    /**
     * Set the main file by id.
     *
     * @param string $id
     * @return bool
     */
    public static function setMainById($id)
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            return false;
        }

        $q = SfwFile::query()
            ->where('table', $item->table)
            ->where('row_id', $item->row_id);

        if ($item->category !== null && $item->category !== '') {
            $q->where('category', $item->category);
        }

        $q->update(['is_main' => 0]);
        $item->is_main = 1;

        return (bool) $item->save();
    }

    /**
     * Add or update title/description info with translations.
     *
     * @param string $id
     * @param array|string|null $title
     * @param array|string|null $description
     * @param string|null $locale
     * @return bool
     */
    public static function addInfo($id, $title = null, $description = null, $locale = null)
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            return false;
        }

        $locale = $locale ?: app()->getLocale();

        if ($title !== null) {
            $item->title = is_array($title) ? ($title[$locale] ?? reset($title)) : $title;
        }
        if ($description !== null) {
            $item->description = is_array($description) ? ($description[$locale] ?? reset($description)) : $description;
        }

        $item->save();

        $fields = [];
        if ($title !== null) {
            $fields['title'] = $title;
        }
        if ($description !== null) {
            $fields['description'] = $description;
        }

        if (!empty($fields)) {
            self::saveTranslations($item->id, $fields);
        }

        return true;
    }

    /**
     * Save translations for a file.
     *
     * @param string $fileId
     * @param array $fields
     * @return void
     */
    protected static function saveTranslations($fileId, array $fields)
    {
        $languages = \Softinline\SfwComponent\Models\SfwLanguage::getAll();
        $table = 'sfw_files';

        foreach ($fields as $field => $value) {
            $translations = is_array($value) ? $value : [app()->getLocale() => $value];

            $existing = \Softinline\SfwComponent\Models\SfwTranslation::query()
                ->where('table', '=', $table)
                ->where('row_id', '=', $fileId)
                ->where('field', '=', $field)
                ->first();

            $aTranslations = [];
            foreach ($languages as $language) {
                $langKey = $language->id;
                $isoKey = $language->iso ?? null;
                $val = null;
                if (array_key_exists($langKey, $translations)) {
                    $val = $translations[$langKey];
                } elseif ($isoKey && array_key_exists($isoKey, $translations)) {
                    $val = $translations[$isoKey];
                }
                if ($val !== null && $val !== '') {
                    $aTranslations[$langKey] = $val;
                }
            }

            if (count($aTranslations) > 0) {
                if (!$existing) {
                    $existing = new \Softinline\SfwComponent\Models\SfwTranslation();
                    $existing->id = uniqid('TR');
                    $existing->table = $table;
                    $existing->row_id = $fileId;
                    $existing->field = $field;
                }
                $existing->translation = json_encode($aTranslations);
                $existing->save();
            } elseif ($existing) {
                $existing->delete();
            }
        }
    }

    /**
     * Delete a file by id.
     *
     * @param string $id
     * @return bool
     */
    public static function delete($id)
    {
        return self::deleteById($id);
    }

    /**
     * Delete a file by id.
     *
     * @param string $id
     * @param bool $deleteForRelatedObject
     * @return bool
     */
    public static function deleteById($id, $deleteForRelatedObject = false)
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            return false;
        }

        $path = self::pathFor($item);
        $ok = (bool) $item->delete();
        if ($ok && $path && is_file($path)) {
            @unlink($path);
        }

        // Remove translations
        \Softinline\SfwComponent\Models\SfwTranslation::query()
            ->where('table', '=', 'sfw_files')
            ->where('row_id', '=', $id)
            ->delete();

        return $ok;
    }

    /**
     * Delete all files for an entity.
     *
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @return void
     */
    public static function deleteByEntity($object, $entityId, $category = null)
    {
        $q = SfwFile::query()
            ->where('table', $object)
            ->where('row_id', $entityId);

        if ($category !== null && $category !== '') {
            $q->where('category', $category);
        }

        $files = $q->get();
        foreach ($files as $f) {
            self::deleteById($f->id);
        }
    }

    /**
     * Get a file record by id.
     *
     * @param string $id
     * @return SfwFile|null|false
     */
    public static function get($id)
    {
        return SfwFile::getByIdInsecure($id);
    }

    /**
     * Get files for an entity.
     *
     * @param string $object
     * @param string $entityId
     * @param bool $main
     * @param string|null $category
     * @return mixed
     */
    public static function viewByRowId($object, $entityId, $main = true, $category = null)
    {
        $q = SfwFile::query()
            ->where('table', $object)
            ->where('row_id', $entityId);

        if ($category !== null && $category !== '') {
            $q->where('category', $category);
        }

        if ($main) {
            return $q->where('is_main', 1)->first();
        }

        return $q->orderBy('sort_order')->orderBy('created_at')->get();
    }
}