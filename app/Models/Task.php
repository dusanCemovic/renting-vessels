<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','vessel_id','start_at','end_at','required_equipment','status'];

    protected $casts = [
        'required_equipment' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    // maybe to add required equipment as relationship
}
