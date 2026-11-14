<?php

namespace Softinline\SfwComponent\Models;

use Illuminate\Database\Eloquent\Model;

class SfwUserObject extends Model
{
    public $incrementing = false;
    protected $table = 'sfw_user_objects';
    protected $keyType = 'string';

    protected $guarded = [];

    public function object()
    {
        return $this->belongsTo(SfwObject::class, 'sfw_object_id');
    }

    public static function getAllForUserId($userId)
    {
        return self::query()->where('user_id', '=', $userId);
    }

    public static function getAccessForUserId($userId)
    {
        return SfwObject::select('sfw_objects.*')
            ->selectRaw('(select count(*) from sfw_user_objects where user_id = ? and sfw_object_id = sfw_objects.id) as access', [$userId]);
    }
}
