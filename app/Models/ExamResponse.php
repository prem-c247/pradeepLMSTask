<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResponse extends Model
{
    protected $fillable = ['exam_id', 'question_id', 'chosen_option'];
    protected $hidden = ['created_at', 'updated_at'];


    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
