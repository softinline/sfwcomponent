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
     * Upload and store a file (uploaded file or raw content).
     *
     * @param mixed $source
     * @param string $object
     * @param string|null $rowId
     * @param array|string|null $options
     * @return SfwFile|null
     */
    public static function upload($source, $object = 'generic', $rowId = null, $options = null)
    {
        if ($source === null && !(is_array($options) && array_key_exists('content', $options))) {
            return null;
        }

        if (!is_array($options)) {
            $options = ['category' => $options];
        }

        $object = $object ?: 'generic';
        $setMain = !empty($options['is_main']) || !empty($options['main']);
        $category = $options['category'] ?? null;

        if ($setMain) {
            $q = SfwFile::query()->where('table', $object)->where('row_id', $rowId)->where('is_main', 1);
            if ($category !== null && $category !== '') {
                $q->where('category', $category);
            }
            foreach ($q->get() as $existing) {
                self::delete($existing->id);
            }
        }

        $dir = self::ensureDir($object);
        $newId = $options['id'] ?? uniqid('F');
        $ownerId = $options['owner_user_id'] ?? (Auth::check() ? (string) Auth::id() : null);

        $isUploadedFile = is_object($source) && (method_exists($source, 'getClientOriginalName') || method_exists($source, 'move'));
        $hasContent = array_key_exists('content', $options);

        if ($isUploadedFile && !$hasContent) {
            $restrictions = [];
            foreach (['maxSize', 'max_size', 'accept', 'extensions', 'mimes'] as $rk) {
                if (array_key_exists($rk, $options)) {
                    $restrictions[$rk] = $options[$rk];
                }
            }
            if (!empty($restrictions) && self::validateUpload($source, $restrictions) !== null) {
                return null;
            }
        }

        if ($hasContent || !$isUploadedFile) {
            $content = $hasContent ? $options['content'] : $source;
            $originalName = $options['filename'] ?? $options['original_name'] ?? (is_string($source) && !$hasContent ? basename($source) : 'file');
            $mime = $options['mime'] ?? $options['mime_type'] ?? null;
            $size = $options['size'] ?? strlen((string) $content);
        } else {
            $originalName = method_exists($source, 'getClientOriginalName') ? $source->getClientOriginalName() : basename((string) $source);
            $mime = method_exists($source, 'getClientMimeType') ? $source->getClientMimeType() : ($options['mime'] ?? $options['mime_type'] ?? null);
            $size = method_exists($source, 'getSize') ? $source->getSize() : ($options['size'] ?? null);
            $content = null;
        }

        $fileObj = new SfwFile();
        $fileObj->id = $newId;
        $fileObj->table = $object;
        $fileObj->row_id = $rowId;
        $fileObj->category = $category;
        $fileObj->collection = $options['collection'] ?? null;
        $fileObj->disk = $options['disk'] ?? 'local';
        $fileObj->path = $options['path'] ?? ('files/' . $object);
        $fileObj->file = $newId;
        $fileObj->original_name = $originalName;
        $fileObj->title = $options['title'] ?? null;
        $fileObj->description = $options['description'] ?? null;
        $fileObj->mime_type = $mime;
        $fileObj->size = $size;
        $fileObj->is_main = $setMain ? 1 : 0;
        $fileObj->sort_order = $options['sort_order'] ?? 0;
        $fileObj->owner_user_id = $ownerId;
        $fileObj->checksum = $options['checksum'] ?? null;

        if (!$fileObj->save()) {
            return null;
        }

        try {
            if ($hasContent || !$isUploadedFile) {
                file_put_contents($dir . '/' . $newId, $content);
            } else {
                $source->move($dir, $newId);
            }
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
     * @param string|null $title
     * @param string|null $description
     * @return bool
     */
    public static function addInfo($id, $title = null, $description = null)
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            return false;
        }

        $item->title = ($title === '' || $title === null) ? null : $title;
        $item->description = ($description === '' || $description === null) ? null : $description;

        $item->save();

        \Softinline\SfwComponent\Services\TranslationsService::setTranslation('sfw_files', $item->id, ['title', 'description']);

        return true;
    }

    /**
     * Set file sort order.
     *
     * @param string $id
     * @param int $order
     * @return bool
     */
    public static function setOrder($id, $order)
    {
        $item = SfwFile::getByIdInsecure($id);
        if (!$item) {
            return false;
        }

        $item->sort_order = (int) $order;

        return (bool) $item->save() || SfwFile::where('id', $id)->update(['sort_order' => (int) $order]) >= 0;
    }

    /**
     * Validate an uploaded file against restrictions.
     *
     * @param mixed $file
     * @param array $restrictions
     * @return string|null
     */
    public static function validateUpload($file, array $restrictions = [])
    {
        if (!$file) {
            return 'file_required';
        }

        $maxSize = $restrictions['maxSize'] ?? ($restrictions['max_size'] ?? null);
        if ($maxSize) {
            $limitBytes = self::parseSizeLimit($maxSize);
            $fileSize = method_exists($file, 'getSize') ? (int) $file->getSize() : null;
            if ($limitBytes !== null && $fileSize !== null && $fileSize > $limitBytes) {
                return 'file_too_large';
            }
        }

        $accept = $restrictions['accept'] ?? ($restrictions['extensions'] ?? ($restrictions['mimes'] ?? null));
        if ($accept) {
            $allowed = is_array($accept) ? $accept : preg_split('/[,\s]+/', (string) $accept, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($allowed)) {
                $name = method_exists($file, 'getClientOriginalName') ? (string) $file->getClientOriginalName() : '';
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $mime = method_exists($file, 'getClientMimeType') ? (string) $file->getClientMimeType() : '';
                $ok = false;
                foreach ($allowed as $rule) {
                    $rule = strtolower(trim((string) $rule));
                    if ($rule === '') {
                        continue;
                    }
                    if ($rule === $mime || $rule === $ext) {
                        $ok = true;
                        break;
                    }
                    if (str_starts_with($rule, '.')) {
                        if ('.' . $ext === $rule) {
                            $ok = true;
                            break;
                        }
                    } elseif (!str_contains($rule, '/') && $ext === $rule) {
                        $ok = true;
                        break;
                    } elseif (str_ends_with($rule, '/*')) {
                        $prefix = substr($rule, 0, -1);
                        if ($mime !== '' && str_starts_with($mime, $prefix)) {
                            $ok = true;
                            break;
                        }
                    }
                }
                if (!$ok) {
                    return 'file_type_not_allowed';
                }
            }
        }

        return null;
    }

    /**
     * Parse size limit string (e.g. 600kb, 6Mb) to bytes.
     *
     * @param string|int $size
     * @return int|null
     */
    public static function parseSizeLimit($size)
    {
        if (is_numeric($size)) {
            return (int) $size;
        }
        $size = trim((string) $size);
        if ($size === '') {
            return null;
        }
        if (!preg_match('/^(\d+(?:\.\d+)?)\s*(b|kb|mb|gb)?$/i', $size, $m)) {
            return null;
        }
        $num = (float) $m[1];
        $unit = strtolower($m[2] ?? 'b');
        $mul = ['b' => 1, 'kb' => 1024, 'mb' => 1024 * 1024, 'gb' => 1024 * 1024 * 1024];
        return (int) ($num * ($mul[$unit] ?? 1));
    }

    /**
     * Delete a file by id.
     *
     * @param string $id
     * @param bool $deleteForRelatedObject
     * @return bool
     */
    public static function delete($id, $deleteForRelatedObject = false)
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

        \Softinline\SfwComponent\Services\TranslationsService::deleteTranslations('sfw_files', $id);

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
            self::delete($f->id);
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

    /**
     * Get the main file for an entity or fallback to the first file.
     *
     * @param string $object
     * @param string $entityId
     * @param string|null $category
     * @return SfwFile|null
     */
    public static function getMainOrFirst($object, $entityId, $category = null)
    {
        $item = self::getMainForEntity($object, $entityId, $category);
        if ($item) {
            return $item;
        }

        $q = SfwFile::query()
            ->where('table', $object)
            ->where('row_id', $entityId);

        if ($category !== null && $category !== '') {
            $q->where('category', $category);
        }

        return $q->orderBy('sort_order')->orderBy('created_at')->first();
    }


}