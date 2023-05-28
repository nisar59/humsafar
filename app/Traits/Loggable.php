<?php 
namespace App\Traits;
use Auth;
trait Loggable{

    public static function boot()
    {
        parent::boot();

        self::saved(function($model){
            
            if(isset($model->changes['lock_screen_token'])){return;}

            if(Auth::check()){
                if(count($model->getOriginal())<1){
                    $log_type="created";
                    $message=Auth::user()->name.' Created This Record on '.now();
                }else{
                    $log_type="updated";
                    $message=Auth::user()->name.' Modified This Record on '.now();
                }

                $data['previous']=$model->getOriginal();
                $data['new']=$model;
                GenerateLog([
                    'log_type'=>$log_type,
                    'user_id'=>Auth::id(),
                    'model'=>$model->getTable(),
                    'model_id'=>$model->id,
                    'message'=>$message,
                    'data'=>json_encode($data),
                ]);
            }
        });

        self::deleted(function($model){
            if(Auth::check()){
                $data['previous']=$model->getOriginal();
                $data['new']=$model;
                GenerateLog([
                    'log_type'=>"deleted",
                    'user_id'=>Auth::id(),
                    'model'=>$model->getTable(),
                    'model_id'=>$model->id,
                    'message'=>Auth::user()->name.' Deleted This Record on '.now(),
                    'data'=>json_encode($data),
                ]);
            }
        });
    }



}


