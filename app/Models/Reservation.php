<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'establishment_id',
        'room_id',
        'reference',
        'check_in',
        'check_out',
        'guests',
        'amount',
        'commission',
        'payment_method',
        'status',
        'expires_at',
    ];

    /**
     * Nombre de nuits du séjour (minimum 1).
     */
    public static function nightsBetween(string $checkIn, string $checkOut): int
    {
        $in = \Illuminate\Support\Carbon::parse($checkIn);
        $out = \Illuminate\Support\Carbon::parse($checkOut);

        // Carbon 3 : diffInDays() retourne une valeur signée.
        // $in->diffInDays($out) donne un nombre positif (4 pour 01/02 → 05/02).
        return max(1, $in->diffInDays($out));
    }

    /**
     * Nombre de nuits de cette réservation.
     */
    public function nights(): int
    {
        if (! $this->check_in || ! $this->check_out) {
            return 1;
        }

        return self::nightsBetween($this->check_in, $this->check_out);
    }

    /**
     * Génère une référence unique pour une réservation.
     */
    public static function generateReference(): string
    {
        return 'HB-'.strtoupper(\Illuminate\Support\Str::random(8));
    }

    /**
     * Annule les réservations en attente dont le délai de paiement (24h) est dépassé
     * et marque leurs transactions comme échouées. Retourne le nombre de réservations annulées.
     */
    public static function expireOverdue(): int
    {
        $overdue = self::with('transaction')
            ->where('status', 'en_attente')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $overdue->each(function (self $reservation) {
            if ($reservation->transaction && $reservation->transaction->status !== 'reussie') {
                $reservation->transaction->update(['status' => 'echouee']);
            }
            $reservation->update(['status' => 'annulee']);
        });

        return $overdue->count();
    }

    /**
     * Synchronise la transaction locale après un changement de statut décidé en
     * back-office (hôte/admin), pour garder les données et les revenus cohérents.
     *
     * - confirmée : paiement reçu hors plateforme (espèces, mobile money sur place)
     *   => la transaction passe à « reussie » (méthode « sur_place » si absente).
     * - annulée : la transaction passe à « echouee » si elle n'était pas réussie.
     */
    public function syncTransactionWithStatus(string $status): void
    {
        $transaction = $this->transaction;

        if (! $transaction || $transaction->status === 'reussie') {
            return;
        }

        if ($status === 'confirmee') {
            $transaction->update([
                'status' => 'reussie',
                'method' => $transaction->method ?? 'sur_place',
            ]);
        } elseif ($status === 'annulee') {
            $transaction->update(['status' => 'echouee']);
        }
    }

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
