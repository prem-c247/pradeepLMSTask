<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'school_user_id',
        'teacher_user_id',
        'name',
        'description',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    

    // Relationships
    public function schoolUser()
    {
        return $this->belongsTo(User::class, 'school_user_id');
    }

    public function teacherUser()
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }
}
