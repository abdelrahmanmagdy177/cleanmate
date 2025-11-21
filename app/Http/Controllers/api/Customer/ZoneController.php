<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index()
    {
        // Only return active zones with active areas
        $zones = Zone::active()
            ->with(['areas' => function ($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'data' => $zones
        ]);
    }
}
