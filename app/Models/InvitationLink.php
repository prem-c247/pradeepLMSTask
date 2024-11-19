<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationLink extends Model
{
    const REGISTERED = 'registered';
    const INPROGRESS = 'in progress';
    const EXPIRED = 'expired';

    protected $fillable = ['sender_id', 'email', 'status', 'accepted_at', 'expire_at'];
}
