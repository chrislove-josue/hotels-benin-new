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
        'cover_image',
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Nombre d'unités restantes disponibles pour ce type de chambre sur une période donnée.
     *
     * Compte les réservations actives dont les dates chevauchent [checkIn, checkOut] :
     * - confirmee : toujours comptée ;
     * - en_attente : comptée uniquement si non expirée (délai de paiement 24h dépassé = ignorée).
     *
     * @param string $checkIn
     * @param string $checkOut
     * @param int|null $excludeReservationId réservation à ignorer (ex: la sienne)
     * @return int
     */
    public function availableUnitsFor(string $checkIn, string $checkOut, ?int $excludeReservationId = null): int
    {
        $booked = $this->reservations()
            ->where(function ($q) {
                // Confirmée : bloque toujours. En attente : bloque tant qu'elle n'a pas expiré.
                $q->where('status', 'confirmee')
                    ->orWhere(function ($qq) {
                        $qq->where('status', 'en_attente')
                            ->where(function ($w) {
                                $w->whereNull('expires_at')->orWhere('expires_at', '>', now());
                            });
                    });
            })
            ->when($excludeReservationId, fn ($q) => $q->where('id', '!=', $excludeReservationId))
            ->where(function ($q) use ($checkIn, $checkOut) {
                // Chevauchement : réservation existante commence avant la fin demandée
                // ET se termine après le début demandé.
                $q->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })
            ->count();

        return max(0, (int) $this->quantity - $booked);
    }

    /**
     * Indique si la chambre est complète sur la période (plus aucune unité libre).
     */
    public function isUnavailableFor(string $checkIn, string $checkOut, ?int $excludeReservationId = null): bool
    {
        return $this->availableUnitsFor($checkIn, $checkOut, $excludeReservationId) <= 0;
    }
}
