<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;
    use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

    class SfwToken extends Model
    {
        use HasSfwRecordAccess;

        protected $table = 'sfw_tokens';

        public $incrementing = false;

        protected $keyType = 'string';

        protected $guarded = [];

        protected $casts = [
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];

        /**
         * Get a record by ID.
         *
         * @param mixed $id
         * @return \Softinline\SfwComponent\Models\SfwToken|false
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
         * Get all records query.
         *
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public static function getAll()
        {
            return self::select('sfw_tokens.*');
        }

        /**
         * Determine if the token is revoked.
         *
         * @return bool
         */
        public function isRevoked()
        {
            return $this->revoked_at !== null;
        }

        /**
         * Determine if the token is expired.
         *
         * @return bool
         */
        public function isExpired()
        {
            return $this->expires_at !== null && $this->expires_at->isPast();
        }

        /**
         * Determine if the token is active.
         *
         * @return bool
         */
        public function isActive()
        {
            return !$this->isRevoked() && !$this->isExpired();
        }

        /**
         * Resolve the related user model instance.
         *
         * @return mixed
         */
        public function resolveUser()
        {
            $userModel = config('auth.providers.users.model');
            if (!$userModel || !class_exists($userModel) || empty($this->user_id)) {
                return null;
            }

            return $userModel::find($this->user_id);
        }
    }
