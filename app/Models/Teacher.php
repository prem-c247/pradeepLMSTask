<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['experience', 'expertises', 'school_id', 'user_id'];
    protected $hidden = ['created_at', 'updated_at'];


    // =======================++++++++++++==============
    // +++++++++++++ Relations +++++++++++++++++++++
    // =======================++++++++++++==============
    public function school()
    {
        return $this->belongsTo(User::class);
    }

}
