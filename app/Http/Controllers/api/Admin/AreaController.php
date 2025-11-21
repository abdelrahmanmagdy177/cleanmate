<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AreaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    protected AreaService $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
    }

    public function index(Request $request)
    {
        $areas = $this->areaService->getAllAreas($request->zone_id);
        
        return response()->json([
            'data' => $areas
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area = $this->areaService->createArea($request->all());

        return response()->json([
            'message' => 'Area created successfully',
            'data' => $area->load('zone')
        ], 201);
    }

    public function show($id)
    {
        $area = $this->areaService->getArea($id);
        
        if (!$area) {
            return response()->json(['error' => 'Area not found'], 404);
        }

        return response()->json([
            'data' => $area
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'sometimes|exists:zones,id',
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area = $this->areaService->updateArea($id, $request->all());

        if (!$area) {
            return response()->json(['error' => 'Area not found'], 404);
        }

        return response()->json([
            'message' => 'Area updated successfully',
            'data' => $area->load('zone')
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->areaService->deleteArea($id);

        if (!$deleted) {
            return response()->json(['error' => 'Area not found'], 404);
        }

        return response()->json([
            'message' => 'Area deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $area = $this->areaService->toggleStatus($id);

        if (!$area) {
            return response()->json(['error' => 'Area not found'], 404);
        }

        return response()->json([
            'message' => 'Area status updated successfully',
            'data' => $area->load('zone')
        ]);
    }
}
