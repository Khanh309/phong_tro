<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Property;

class HomeController extends Controller
{
    /**
     * Trang chủ công khai: Tìm kiếm và xem danh sách phòng trọ còn trống
     */
    public function index(Request $request)
    {
        $properties = Property::withCount([
            'rooms as available_rooms_count' => fn($q) => $q->where('status', 'available'),
        ])->get();

        $propertyId = $request->query('property_id');
        $priceRange = $request->query('price_range');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $minArea = $request->query('min_area');
        $search = $request->query('search');

        $query = Room::where('status', 'available')
            ->with(['property', 'assets', 'fees']);

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        if ($priceRange === 'under_3m') {
            $query->where('price', '<', 3000000);
        } elseif ($priceRange === '3m_5m') {
            $query->whereBetween('price', [3000000, 5000000]);
        } elseif ($priceRange === 'above_5m') {
            $query->where('price', '>', 5000000);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($minArea) {
            $query->where('area', '>=', $minArea);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                  });
            });
        }

        $sort = $request->query('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'area_desc') {
            $query->orderBy('area', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $vacantRooms = $query->paginate(10)->withQueryString();
        $totalVacantCount = Room::where('status', 'available')->count();

        return view('home', compact(
            'vacantRooms',
            'properties',
            'propertyId',
            'priceRange',
            'minPrice',
            'maxPrice',
            'minArea',
            'search',
            'sort',
            'totalVacantCount'
        ));
    }
}
