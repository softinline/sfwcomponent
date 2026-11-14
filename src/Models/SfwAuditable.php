<?php

namespace Softinline\SfwComponent\Models;

use Illuminate\Database\Eloquent\Model;

class SfwAuditable extends Model
{
    /**
     * Boot auditable model events.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            $audit = new SfwAudit();
            $audit->id = uniqid('A');
            $audit->user_id = \Auth::user() ? \Auth::user()->id : null;
            $audit->event = 'u';
            $audit->row_id = $model->id;
            $audit->setAttribute('table', $model->getTable());
            $audit->before = json_encode($model->getOriginal());
            $audit->after = json_encode($model->getAttributes());
            $audit->save();
        });

        static::deleting(function ($model) {
            $audit = new SfwAudit();
            $audit->id = uniqid('A');
            $audit->user_id = \Auth::user() ? \Auth::user()->id : null;
            $audit->event = 'd';
            $audit->row_id = $model->id;
            $audit->setAttribute('table', $model->getTable());
            $audit->before = json_encode($model->getAttributes());
            $audit->after = null;
            $audit->save();
        });
    }
}

