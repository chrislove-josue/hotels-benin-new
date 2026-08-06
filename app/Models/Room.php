<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        'name',
        'description',
        'capacity',
        'price_per_night',
        'quantity',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'price_per_night' => 'decimal:2',
        ];
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}
