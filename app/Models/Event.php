<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'description',
        'date_start',
        'date_end',
        'location',
        'cover_image',
        'is_featured',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Libellé lisible de la catégorie.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'festival' => 'Festival',
            'culture' => 'Culture & Arts',
            'sport' => 'Sport',
            'affaires' => 'Affaires & Conférences',
            'religieux' => 'Événement religieux',
            default => 'Événement',
        };
    }

    /**
     * Icône Bootstrap selon la catégorie.
     */
    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'festival' => 'bi-music-note-beamed',
            'culture' => 'bi-palette',
            'sport' => 'bi-trophy',
            'affaires' => 'bi-briefcase',
            'religieux' => 'bi-buildings',
            default => 'bi-calendar-event',
        };
    }

    /**
     * Filtrage par catégorie et recherche.
     */
    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->where('category', $v))
            ->when($filters['search'] ?? null, function ($q, $v) {
                $q->where(function ($q2) use ($v) {
                    $q2->where('title', 'like', "%{$v}%")
                        ->orWhere('location', 'like', "%{$v}%")
                        ->orWhere('short_description', 'like', "%{$v}%");
                });
            });
    }
}

