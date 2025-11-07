<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vessel extends Model
{
    use HasFactory;

    protected $fillable = ['name','type','size'];

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'equipment_vessel');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
