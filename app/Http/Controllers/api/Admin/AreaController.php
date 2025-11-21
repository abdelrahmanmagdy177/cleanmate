<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::with('zone');

        // Filter by zone if provided
        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        $areas = $query->orderBy('name')->get();
        
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

        $area = Area::create($request->all());

        return response()->json([
            'message' => 'Area created successfully',
            'data' => $area->load('zone')
        ], 201);
    }

    public function show($id)
    {
        $area = Area::with('zone')->findOrFail($id);
        
        return response()->json([
            'data' => $area
        ]);
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'zone_id' => 'sometimes|exists:zones,id',
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area->update($request->all());

        return response()->json([
            'message' => 'Area updated successfully',
            'data' => $area->load('zone')
        ]);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return response()->json([
            'message' => 'Area deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $area = Area::findOrFail($id);
        $area->update(['is_active' => !$area->is_active]);

        return response()->json([
            'message' => 'Area status updated successfully',
            'data' => $area->load('zone')
        ]);
    }
}
