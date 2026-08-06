<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'department'];

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }
}
