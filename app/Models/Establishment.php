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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_establishment');
    }

    /**
     * Recalcule `price_from` à partir du prix minimum réel des chambres.
     *
     * `price_from` représente le « prix à partir de » affiché dans les listings
     * et utilisé pour le tri / le filtrage par fourchette de prix. On le
     * synchronise donc sur le coût le plus bas des chambres (source de vérité).
     *
     * La colonne étant NOT NULL, on laisse la valeur existante (celle saisie
     * par l'hôte) inchangée s'il ne reste plus aucune chambre.
     *
     * Appelé à l'approbation d'une création/modification/suppression de chambre.
     */
    public function refreshPriceFrom(): void
    {
        $min = $this->rooms()->min('price_per_night');

        if ($min !== null) {
            $this->update([
                'price_from' => (float) $min,
            ]);
        }
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
