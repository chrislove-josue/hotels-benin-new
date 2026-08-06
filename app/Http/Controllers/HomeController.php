<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentType;
use App\Models\Locality;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featured = Establishment::with(['locality', 'type'])
            ->where('is_featured', true)
            ->where('is_available', true)
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $topRated = Establishment::with(['locality', 'type'])
            ->where('is_available', true)
            ->orderByDesc('rating')
            ->take(8)
            ->get();

        $localities = Locality::withCount('establishments')
            ->orderByDesc('establishments_count')
            ->take(8)
            ->get();

        $types = EstablishmentType::withCount('establishments')->get();

        return view('home', compact('featured', 'topRated', 'localities', 'types'));
    }
}
