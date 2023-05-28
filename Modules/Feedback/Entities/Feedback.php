<?php

namespace Modules\Feedback\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Feedback\Entities\FeedbackResponse;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;
class Feedback extends Model
{
    use HasFactory,Loggable,SoftDeletes;

    protected $table="clients_feedback";
    protected $fillable = ['client_id','feedback_type'];
    
    protected static function newFactory()
    {
        return \Modules\Feedback\Database\factories\FeedbackFactory::new();
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }
    
    public function response()
    {
        return $this->hasMany(FeedbackResponse::class,'clients_feedback_id', 'id');
    }


}
