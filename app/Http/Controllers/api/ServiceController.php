<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $areaId = null;
        if ($request->has('area_id')) {
            $areaId = $request->area_id;
        } elseif ($user = $request->user('sanctum')) {
            $areaId = $user->area_id;
        }

        // Enforce area_id requirement
        if (!$areaId) {
            return response()->json([
                'error' => 'Area ID is required. Please provide area_id parameter or set your area in your profile.'
            ], 422);
        }

        $services = Service::where('active', true)
            ->where('area_id', $areaId)
            ->with(['variants.prices' => function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            }])
            ->get();

        return response()->json($services);
    }

    public function show(Request $request, $id)
    {
        $areaId = $request->query('area_id');
        
        // Check authenticated user's area if not provided
        if (!$areaId && $user = $request->user('sanctum')) {
            $areaId = $user->area_id;
        }

        // Enforce area_id requirement
        if (!$areaId) {
            return response()->json([
                'error' => 'Area ID is required. Please provide area_id parameter or set your area in your profile.'
            ], 422);
        }

        $service = Service::where('active', true)
            ->where('id', $id)
            ->where('area_id', $areaId)
            ->with(['variants.prices' => function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            }])
            ->firstOrFail();

        return response()->json($service);
    }
}
