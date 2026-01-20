<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_title',
        'vehicle_number',
        'driver_name',     // Migration match
        'driver_phone',    // Migration match
        'fare_amount'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}