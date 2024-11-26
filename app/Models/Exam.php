<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['student_id', 'subject_id'];
    
    protected $hidden = ['created_at', 'updated_at'];



    // Relationships
    public function responses()
    {
        return $this->hasMany(ExamResponse::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
