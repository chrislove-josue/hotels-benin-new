<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'locality_id',
        'establishment_type_id',
        'name',
        'slug',
        'address',
        'description',
        'short_description',
        'price_from',
        'capacity',
        'cover_image',
        'rating',
        'reviews_count',
        'is_featured',
        'is_available',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
            'price_from' => 'decimal:2',
            'rating' => 'decimal:2',
        ];
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function type()
    {
        return $this->belongsTo(EstablishmentType::class, 'establishment_type_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(EstablishmentImage::class)->orderBy('position');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_establishment');
    }

    /**
     * Portée de recherche utilisée par le moteur de recherche simplifié
     * de la page d'accueil et la liste des établissements.
     */
    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['locality'] ?? null, fn ($q, $v) => $q->whereHas(
                'locality',
                fn ($q2) => $q2->where('slug', $v)
            ))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->whereHas(
                'type',
                fn ($q2) => $q2->where('slug', $v)
            ))
            ->when($filters['min_price'] ?? null, fn ($q, $v) => $q->where('price_from', '>=', $v))
            ->when($filters['max_price'] ?? null, fn ($q, $v) => $q->where('price_from', '<=', $v))
            ->when($filters['capacity'] ?? null, fn ($q, $v) => $q->where('capacity', '>=', $v));
    }
}
