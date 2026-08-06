<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        'user_id',
        'rating',
        'title',
        'comment',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
