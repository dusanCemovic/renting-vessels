<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['code','name','description'];

    public function vessels() : BelongsToMany
    {
        return $this->belongsToMany(Vessel::class, 'equipment_vessel');
    }
}
