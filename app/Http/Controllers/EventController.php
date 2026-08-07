<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Liste publique des événements et programmes au Bénin.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['category', 'search']);

        $events = Event::where('is_available', true)
            ->filter($filters)
            ->orderByDesc('is_featured')
            ->orderBy('date_start')
            ->paginate(9)
            ->withQueryString();

        $categories = [
            'festival' => 'Festivals',
            'culture' => 'Culture & Arts',
            'sport' => 'Sport',
            'affaires' => 'Affaires & Conférences',
            'religieux' => 'Religieux',
        ];

        $upcoming = Event::where('is_available', true)
            ->where('date_start', '>=', now()->toDateString())
            ->orderBy('date_start')
            ->take(4)
            ->get();

        return view('events.index', compact('events', 'categories', 'filters', 'upcoming'));
    }

    /**
     * Détail d'un événement.
     */
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

        $similar = Event::where('id', '!=', $event->id)
            ->where('category', $event->category)
            ->where('is_available', true)
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'similar'));
    }
}

