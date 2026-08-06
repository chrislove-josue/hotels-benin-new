<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function establishments()
    {
        return $this->belongsToMany(Establishment::class, 'amenity_establishment');
    }
}
