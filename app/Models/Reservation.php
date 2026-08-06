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
    ];

    /**
     * Nombre de nuits du séjour (minimum 1).
     */
    public static function nightsBetween(string $checkIn, string $checkOut): int
    {
        $in = \Illuminate\Support\Carbon::parse($checkIn);
        $out = \Illuminate\Support\Carbon::parse($checkOut);

        return max(1, $out->diffInDays($in));
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

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
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
