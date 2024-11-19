<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationLink extends Model
{
    protected $fillable = ['sender_id','email', 'status', 'expire_at'];
}
