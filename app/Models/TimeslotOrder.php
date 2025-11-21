<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeslotOrder extends Model
{
    protected $table = 'timeslot_orders';
    
    protected $fillable = ['timeslot_id', 'order_id', 'date'];
}
