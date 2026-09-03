<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Support\CacheKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $data = Cache::remember(CacheKeys::HOME, CacheKeys::TTL, function () {
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

            return compact('featured', 'topRated', 'localities', 'types');
        });

        return view('home', $data);
    }
}
