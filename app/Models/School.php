<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['user_id', 'owner_name'];
    protected $hidden = ['created_at', 'updated_at'];
}
