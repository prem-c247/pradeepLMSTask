<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'address_1', 'address_2', 'street', 'city', 'state', 'postal_code', 'country', 'latitude', 'longitude', 'is_primary'];
}
