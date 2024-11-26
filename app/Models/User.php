<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Status
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';
    public const PENDING = 'PENDING';

    // Roles
    public const ROLE_ADMIN = 1;
    public const ROLE_SCHOOL = 2;
    public const ROLE_TEACHER = 3;
    public const ROLE_STUDENT = 4;


    protected $fillable = [
        'first_name',
        'last_name',
        'role_id',
        'email',
        'phone',
        'profile',
        'address_id',
        'password',
        'status'
    ];

    protected $hidden = ['password', 'remember_token', 'email_verified_at', 'updated_at'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    // Accessors
    public function getProfileAttribute($value)
    {
        if ($value) {
            return asset('uploads/profile-images/' . $value);
        }

        $defaultImg = NO_PROFILE;
        return asset($defaultImg);
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function studentDetails()
    {
        return $this->hasOne(Student::class);
    }

    public function teacherDetails()
    {
        return $this->hasOne(Teacher::class);
    }

    public function schoolDetails()
    {
        return $this->hasOne(School::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Scopes
    public function scopeTeacher($query)
    {
        return $query->where('role_id', self::ROLE_TEACHER);
    }

    public function scopeSchool($query)
    {
        return $query->where('role_id', self::ROLE_SCHOOL);
    }

    public function scopeStudent($query)
    {
        return $query->where('role_id', self::ROLE_STUDENT);
    }

    // Check the logged in user is student
    public function isStudent()
    {
        return $this->role === self::ROLE_STUDENT;
    }
}
