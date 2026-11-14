<?php

namespace Softinline\SfwComponent\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Softinline\SfwComponent\Models\Traits\HasSfwRecordAccess;

class SfwUser extends Authenticatable
{
    use SoftDeletes;
    use HasSfwRecordAccess;

    public $incrementing = false;
    protected $table = 'sfw_users';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'role',
        'lover_level',
        'status',
        'timezone',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get a record by ID.
     *
     * @param mixed $id
     * @return \Softinline\SfwComponent\Models\SfwUser|false|null
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
        return self::query();
    }

    public function userObjects()
    {
        return $this->hasMany(SfwUserObject::class, 'user_id');
    }
}
