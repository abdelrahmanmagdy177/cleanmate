<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ZoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    protected ZoneService $zoneService;

    public function __construct(ZoneService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function index()
    {
        $zones = $this->zoneService->getAllZones();
        
        return response()->json([
            'data' => $zones
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $zone = $this->zoneService->createZone($request->all());

        return response()->json([
            'message' => 'Zone created successfully',
            'data' => $zone->load('areas')
        ], 201);
    }

    public function show($id)
    {
        $zone = $this->zoneService->getZone($id);
        
        if (!$zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        return response()->json([
            'data' => $zone
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $zone = $this->zoneService->updateZone($id, $request->all());

        if (!$zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        return response()->json([
            'message' => 'Zone updated successfully',
            'data' => $zone->load('areas')
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->zoneService->deleteZone($id);

        if (!$deleted) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        return response()->json([
            'message' => 'Zone deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $zone = $this->zoneService->toggleStatus($id);

        if (!$zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        return response()->json([
            'message' => 'Zone status updated successfully',
            'data' => $zone->load('areas')
        ]);
    }
}
