<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['school_id', 'user_id', 'parents_name', 'roll_number'];
    protected $hidden = ['created_at', 'updated_at'];


    // =======================++++++++++++==============
    // +++++++++++++ Relations +++++++++++++++++++++
    // =======================++++++++++++==============
    public function school()
    {
        return $this->belongsTo(User::class);
    }
}
