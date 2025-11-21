<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens;

    protected $fillable = ['name', 'email', 'phone', 'status', 'in_region', 'area_id', 'password'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    /**
     * Attributes hidden from arrays
     */
    protected $hidden = ['password'];
}
