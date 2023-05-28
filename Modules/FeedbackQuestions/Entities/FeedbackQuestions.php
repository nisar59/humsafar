<?php

namespace Modules\FeedbackQuestions\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FeedbackQuestions\Entities\QuestionOptions;
use App\Traits\Loggable;
use Modules\Logs\Entities\Logs;

class FeedbackQuestions extends Model
{
    use HasFactory,SoftDeletes,Loggable;

    protected $table="feedback_questions";
    protected $fillable = ['feedback_type', 'question','question_type','status'];
    
    protected static function newFactory()
    {
        return \Modules\FeedbackQuestions\Database\factories\FeedbackQuestionsFactory::new();
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'model_id','id')->where('model',$this->getTable());
    }
    
    public function options()
    {
       return $this->hasMany(QuestionOptions::class, 'feedback_question_id','id')->select('id','feedback_question_id', 'option_value');
    }


}
