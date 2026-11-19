<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Str;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwScreen extends Model
    {
        use SoftDeletes;
        use HasSfwRecordAccess;

        public $incrementing = false;
        protected $table = 'sfw_screens';

        protected $guarded = [];

        protected $casts = [
            'is_active' => 'boolean',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwScreen|false|null
         */
        public static function getById($id)
        {
            static::sfwClearLastAccessError();
            $item = self::find($id);
            if (!$item) {
                static::sfwDenyAccess(404, 'object_not_exists');
                return false;
            }
            return $item;
        }

        /**
         * Get all root screens.
         *
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAllScreens()
        {
            return self::select('sfw_screens.*')
                ->where('type', '=', 'screen')
                ->where(function ($qq) {
                    $qq->whereNull('parent_id')->orWhere('parent_id', '');
                });
        }

        /**
         * Get children by parent id ordered for rendering.
         *
         * @param string $id
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAllByParentid($id)
        {
            return self::select('sfw_screens.*')->where(function ($qq) use ($id) {
                $qq->where('parent_id', '=', $id);
            })->orderBy('sort_order')->orderBy('id');
        }

        /**
         * Find a root screen by key.
         *
         * @param string $key
         * @return \Softinline\SfwComponent\Models\SfwScreen|null
         */
        public static function findByKey($key)
        {
            return self::where('type', 'screen')
                ->where('key', $key)
                ->where(function ($qq) {
                    $qq->whereNull('parent_id')->orWhere('parent_id', '');
                })
                ->first();
        }

        /**
         * Get decoded meta as array.
         *
         * @return array
         */
        public function getMetaArray()
        {
            $meta = $this->meta;
            if (is_array($meta)) {
                return $meta;
            }
            if ($meta === null || $meta === '') {
                return [];
            }
            $decoded = json_decode((string) $meta, true);
            return is_array($decoded) ? $decoded : [];
        }

        /**
         * Build canonical screen config array from database tree.
         *
         * @return array
         */
        public function toConfigArray()
        {
            $root = $this->getRootScreen();
            return $root->buildNodeConfig();
        }

        /**
         * Get root screen record for this node.
         *
         * @return \Softinline\SfwComponent\Models\SfwScreen
         */
        public function getRootScreen()
        {
            if ($this->type === 'screen' && ($this->parent_id === null || $this->parent_id === '')) {
                return $this;
            }
            if (!empty($this->screen_id) && $this->screen_id !== $this->id) {
                $root = self::find($this->screen_id);
                if ($root) {
                    return $root;
                }
            }
            return $this;
        }

        /**
         * Build config array for this node.
         *
         * @return array
         */
        public function buildNodeConfig()
        {
            $meta = $this->getMetaArray();
            $config = self::normalizeMetaTypes($meta);

            $type = $this->type ?: ($config['type'] ?? null);
            if ($type !== null && $type !== '') {
                $config['type'] = $type;
            }

            if (!array_key_exists('name', $config) && $this->name !== null && $this->name !== '') {
                $config['name'] = $this->name;
            }
            if (!array_key_exists('class', $config) && $this->class !== null && $this->class !== '') {
                $config['class'] = $this->class;
            }
            if ($this->key !== null && $this->key !== '' && !array_key_exists('key', $config)) {
                $config['key'] = $this->key;
            }

            // Normalize translation file key
            if (isset($config['translation_file']) && !isset($config['translationFile'])) {
                $config['translationFile'] = $config['translation_file'];
            }
            unset($config['translation_file']);

            // Normalize root screen defaults
            if (($config['type'] ?? null) === 'screen') {
                if (!isset($config['id']) || $config['id'] === '') {
                    $config['id'] = $this->key ?: $this->id;
                }
                if (isset($config['ajax'])) {
                    $config['ajax'] = self::toBool($config['ajax']);
                }
            }

            $children = self::getAllByParentid($this->id)->get();
            if ($children->count() > 0) {
                $childConfigs = [];
                $columns = [];
                $options = [];
                foreach ($children as $child) {
                    $childConfig = $child->buildNodeConfig();
                    if ($child->type === 'datatable-column') {
                        unset($childConfig['type']);
                        $columns[] = $childConfig;
                        continue;
                    }
                    if ($child->type === 'dropdown-option') {
                        unset($childConfig['type']);
                        $options[] = $childConfig;
                        continue;
                    }
                    $childConfigs[] = $childConfig;
                }

                if (($config['type'] ?? null) === 'datatable') {
                    $config['columns'] = $columns;
                } elseif (($config['type'] ?? null) === 'dropdown') {
                    $config['options'] = $options;
                } else {
                    if (!empty($childConfigs)) {
                        $config['components'] = $childConfigs;
                    }
                }

                // Keep structural children if any for generic containers that also got special children (unlikely)
                if (($config['type'] ?? null) === 'datatable' && !empty($childConfigs)) {
                    $config['components'] = $childConfigs;
                }
                if (($config['type'] ?? null) === 'dropdown' && !empty($childConfigs)) {
                    $config['components'] = $childConfigs;
                }
            }

            return $config;
        }

        /**
         * Replace screen tree with config from JSON array.
         *
         * @param array $config
         * @return bool
         */
        public function replaceFromConfig(array $config)
        {
            $config = self::normalizeScreenConfig($config);
            $rootId = $this->id;

            // Delete existing children
            self::deleteChildrenRecursive($rootId);

            // Update root from config
            $meta = $config;
            unset($meta['components'], $meta['columns'], $meta['options']);

            $this->type = 'screen';
            $this->parent_id = null;
            $this->screen_id = $rootId;
            $this->name = $config['name'] ?? ($config['title'] ?? $this->name);
            $this->key = $config['key'] ?? ($this->key ?: (isset($config['name']) ? Str::slug((string) $config['name'], '_') : null));
            $this->class = $config['class'] ?? null;
            $this->meta = json_encode(self::prepareMetaForStorage($meta));
            $this->sort_order = 0;
            $this->save();

            $this->saveChildrenFromConfig($config, $rootId, $rootId);

            return true;
        }

        /**
         * Save child nodes from config.
         *
         * @param array $config
         * @param string $parentId
         * @param string $rootId
         * @return void
         */
        private function saveChildrenFromConfig(array $config, $parentId, $rootId)
        {
            $sort = 0;

            // Datatable columns
            if (($config['type'] ?? null) === 'datatable' && isset($config['columns']) && is_array($config['columns'])) {
                foreach ($config['columns'] as $column) {
                    if (!is_array($column)) {
                        continue;
                    }
                    $columnConfig = $column;
                    $columnConfig['type'] = 'datatable-column';
                    $this->createNodeFromConfig($columnConfig, $parentId, $rootId, $sort);
                    $sort++;
                }
            }

            // Dropdown options
            if (($config['type'] ?? null) === 'dropdown' && isset($config['options']) && is_array($config['options'])) {
                foreach ($config['options'] as $option) {
                    if (!is_array($option)) {
                        continue;
                    }
                    $optionConfig = $option;
                    $optionConfig['type'] = 'dropdown-option';
                    $this->createNodeFromConfig($optionConfig, $parentId, $rootId, $sort);
                    $sort++;
                }
            }

            // Regular components
            if (isset($config['components']) && is_array($config['components'])) {
                foreach ($config['components'] as $component) {
                    if (!is_array($component)) {
                        continue;
                    }
                    $this->createNodeFromConfig($component, $parentId, $rootId, $sort);
                    $sort++;
                }
            }
        }

        /**
         * Create a node from config.
         *
         * @param array $config
         * @param string|null $parentId
         * @param string $rootId
         * @param int $sort
         * @return \Softinline\SfwComponent\Models\SfwScreen
         */
        private function createNodeFromConfig(array $config, $parentId, $rootId, $sort)
        {
            $node = new self();
            $node->id = self::generateId();
            $node->parent_id = $parentId;
            $node->screen_id = $rootId;
            $node->type = $config['type'] ?? null;
            $node->name = $config['name'] ?? null;
            $node->key = $config['key'] ?? null;
            $node->class = $config['class'] ?? null;
            $node->sort_order = $sort;

            $meta = $config;
            unset($meta['components'], $meta['columns'], $meta['options']);
            $node->meta = json_encode(self::prepareMetaForStorage($meta));

            if (($node->type === 'screen') && ($parentId === null || $parentId === '')) {
                $node->screen_id = $node->id;
                if (empty($node->key) && !empty($node->name)) {
                    $node->key = Str::slug((string) $node->name, '_');
                }
            }

            $node->save();

            $node->saveChildrenFromConfig($config, $node->id, $rootId);

            return $node;
        }

        /**
         * Import config as a new screen tree.
         *
         * @param array $config
         * @return \Softinline\SfwComponent\Models\SfwScreen
         */
        public static function importAsNew(array $config)
        {
            $config = self::normalizeScreenConfig($config);
            $screen = new self();
            $screen->id = self::generateId();
            $screen->type = 'screen';
            $screen->parent_id = null;
            $screen->screen_id = $screen->id;
            $screen->name = $config['name'] ?? ($config['title'] ?? null);
            $screen->key = $config['key'] ?? (!empty($screen->name) ? Str::slug((string) $screen->name, '_') : null);
            $screen->class = $config['class'] ?? null;

            $meta = $config;
            unset($meta['components'], $meta['columns'], $meta['options']);
            $screen->meta = json_encode(self::prepareMetaForStorage($meta));
            $screen->sort_order = 0;
            $screen->save();

            $screen->saveChildrenFromConfig($config, $screen->id, $screen->id);

            return $screen;
        }

        /**
         * Delete all children recursively.
         *
         * @param string $parentId
         * @return void
         */
        public static function deleteChildrenRecursive($parentId)
        {
            $children = self::getAllByParentid($parentId)->get();
            foreach ($children as $child) {
                self::deleteChildrenRecursive($child->id);
                $child->forceDelete();
            }
        }

        /**
         * Delete tree including node.
         *
         * @return bool
         */
        public function deleteTree()
        {
            self::deleteChildrenRecursive($this->id);
            return (bool) $this->forceDelete();
        }

        /**
         * Generate screen node id.
         *
         * @return string
         */
        public static function generateId()
        {
            return uniqid('SC');
        }

        /**
         * Normalize screen config.
         *
         * @param array $config
         * @return array
         */
        public static function normalizeScreenConfig(array $config)
        {
            if (!isset($config['type']) || $config['type'] === '') {
                $config['type'] = 'screen';
            }
            if (isset($config['ajax'])) {
                $config['ajax'] = self::toBool($config['ajax']);
            }
            if (isset($config['translation_file']) && !isset($config['translationFile'])) {
                $config['translationFile'] = $config['translation_file'];
                unset($config['translation_file']);
            }
            if (!isset($config['name']) && isset($config['title'])) {
                $config['name'] = $config['title'];
            }
            return $config;
        }

        /**
         * Prepare meta for storage.
         *
         * @param array $meta
         * @return array
         */
        public static function prepareMetaForStorage(array $meta)
        {
            return self::normalizeMetaTypes($meta);
        }

        /**
         * Normalize meta types.
         *
         * @param array $meta
         * @return array
         */
        public static function normalizeMetaTypes(array $meta)
        {
            $boolKeys = [
                'ajax', 'required', 'disabled', 'selector', 'footer', 'configCols',
                'searchBar', 'showLabel', 'visible', 'translate', 'translationRequired',
                'orderable', 'searchable', 'default', 'is_active', 'collapsed'
            ];

            foreach ($meta as $key => $value) {
                if ($value === null) {
                    unset($meta[$key]);
                    continue;
                }

                if ($key === 'translation_file') {
                    $meta['translationFile'] = $value;
                    unset($meta[$key]);
                    $key = 'translationFile';
                    $value = $meta[$key];
                }

                if (is_string($value)) {
                    $trimmed = trim($value);
                    if (in_array($key, $boolKeys, true)) {
                        $lower = strtolower($trimmed);
                        if ($lower === 'true' || $lower === '1') {
                            $meta[$key] = true;
                            continue;
                        }
                        if ($lower === 'false' || $lower === '0') {
                            $meta[$key] = false;
                            continue;
                        }
                    }

                    if ($key === 'pageLength' && $trimmed !== '' && ctype_digit($trimmed)) {
                        $meta[$key] = (int) $trimmed;
                        continue;
                    }

                    if (($key === 'columns' || $key === 'options' || $key === 'order' || $key === 'childrens') && $trimmed !== '') {
                        $decoded = json_decode($trimmed, true);
                        if (is_array($decoded)) {
                            $meta[$key] = $decoded;
                            continue;
                        }
                    }

                    // dotted order keys
                    if ($key === 'order.col' || $key === 'order.type') {
                        if (!isset($meta['order']) || !is_array($meta['order'])) {
                            $meta['order'] = [];
                        }
                        $meta['order'][substr($key, 6)] = $trimmed;
                        unset($meta[$key]);
                        continue;
                    }
                }
            }

            return $meta;
        }

        /**
         * Convert value to boolean.
         *
         * @param mixed $value
         * @return bool
         */
        public static function toBool($value)
        {
            if (is_bool($value)) {
                return $value;
            }
            $value = strtolower(trim((string) $value));
            return in_array($value, ['1', 'true', 'yes', 'on'], true);
        }
    }
