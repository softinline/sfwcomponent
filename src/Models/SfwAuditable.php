<?php

    namespace Softinline\SfwComponent\Models;

    use Illuminate\Database\Eloquent\Model;

    class SfwAuditable extends Model 
    {
                
        /**
         * capture models events
         */
        public static function boot() {
            
            // event update
            static::updating(function ($model) {                
                $audit = new \Softinline\SfwComponent\Models\sfwAudit();                                
                $audit->id = uniqid("A", true);
                $audit->user_id = \Auth::user() ? \Auth::user()->id : null;
                $audit->action = 'u';
                $audit->row_id = $model->id;
                $audit->table = $model->getTable();
                $audit->before = json_encode($model->getOriginal());
                $audit->after = json_encode($model->getAttributes());                                                
                $audit->save();    
            });

            // event delete
            static::deleting(function ($model) {                
                $audit = new \Softinline\SfwComponent\Models\sfwAudit();                                
                $audit->id = uniqid("A", true);
                $audit->user_id = \Auth::user() ? \Auth::user()->id : null;
                $audit->action = 'd';
                $audit->row_id = $model->id;
                $audit->table = $model->getTable();
                $audit->before = json_encode($model->getAttributes());               
                $audit->save();    
            });

            parent::boot();
            
        }
        
    }
