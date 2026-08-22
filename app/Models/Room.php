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

    /**
     * Nombre minimal d'unités requis pour honorer les réservations actives à venir.
     *
     * Calcule le pic de chevauchement (balayage) des réservations confirmées ou en
     * attente non expirées dont le check_out est futur. Une baisse de `quantity`
     * en dessous de ce seuil créerait du surbooking.
     */
    public function minRequiredQuantity(): int
    {
        $reservations = $this->reservations()
            ->where(function ($q) {
                $q->where('status', 'confirmee')
                    ->orWhere(function ($qq) {
                        $qq->where('status', 'en_attente')
                            ->where(function ($w) {
                                $w->whereNull('expires_at')->orWhere('expires_at', '>', now());
                            });
                    });
            })
            ->whereDate('check_out', '>', now()->toDateString())
            ->get(['check_in', 'check_out']);

        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [$reservation->check_in, 1];
            $events[] = [$reservation->check_out, -1];
        }

        // Tri chronologique ; à date égale les fins (-1) avant les débuts (+1)
        usort($events, fn ($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

        $current = $peak = 0;
        foreach ($events as [, $delta]) {
            $current += $delta;
            $peak = max($peak, $current);
        }

        return $peak;
    }
}
