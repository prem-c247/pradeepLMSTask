<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'question_text',
        'options',
        'correct_answer'
    ];

    protected $casts = [
        'options' => 'array', // Automatically handle json encoding/decoding
    ];

    protected $hidden = ['created_at', 'updated_at'];


    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
