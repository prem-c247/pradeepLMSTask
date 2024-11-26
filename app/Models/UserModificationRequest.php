<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModificationRequest extends Model
{
    // type
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    // status
    public const PENDING = 'PENDING';
    public const APPROVED = 'APPROVED';
    public const REJECTED = 'REJECTED';

    protected $fillable = [
        'type',
        'requested_by',
        'requested_to',
        'target_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile',
        'address',
        'owner_name',
        'roll_number',
        'parents_name',
        'experience',
        'expertises',
        'user_status',
        'status',
    ];

    protected $casts = [
        'address' => 'array',
        'expertises' => 'array',
    ];


    // Relationship: Requested by user (who made the request)
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Relationship: Requested to user (who the request is made to)
    public function requestedTo()
    {
        return $this->belongsTo(User::class, 'requested_to');
    }

    // Relationship: Target user (the user who is being modified)
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
