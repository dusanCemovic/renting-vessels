<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','type','size'];

    public function equipment() : BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'equipment_vessel');
    }

    public function reservations() : HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function maintenances() : HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
