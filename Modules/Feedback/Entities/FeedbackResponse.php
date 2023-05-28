<?php

namespace Modules\Feedback\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FeedbackQuestions\Entities\FeedbackQuestions;

class FeedbackResponse extends Model
{
    use HasFactory,SoftDeletes;

    protected $table="feedback_response";
    protected $fillable = ['clients_feedback_id','feedback_question_id','response'];
    
    protected static function newFactory()
    {
        return \Modules\Feedback\Database\factories\FeedbackResponseFactory::new();
    }

    public function question()
    {
        return $this->hasOne(FeedbackQuestions::class,'id','feedback_question_id');
    }
}
