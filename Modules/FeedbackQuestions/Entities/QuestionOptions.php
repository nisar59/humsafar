<?php

namespace Modules\FeedbackQuestions\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionOptions extends Model
{
    use HasFactory;

    protected $table='question_options';
    protected $fillable = ['feedback_question_id','option_value'];
    
    protected static function newFactory()
    {
        return \Modules\FeedbackQuestions\Database\factories\QuestionOptionsFactory::new();
    }
}
