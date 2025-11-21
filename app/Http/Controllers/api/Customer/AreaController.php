<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Get all active areas with their zones.
     * This allows customers to see all available service areas.
     */
    public function index()
    {
        // Return only active areas from active zones
        $areas = Area::active()
            ->with(['zone' => function ($query) {
                $query->where('is_active', true);
            }])
            ->whereHas('zone', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'data' => $areas
        ]);
    }
}
