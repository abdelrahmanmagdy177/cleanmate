<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with('areas')->orderBy('name')->get();
        
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

        $zone = Zone::create($request->all());

        return response()->json([
            'message' => 'Zone created successfully',
            'data' => $zone->load('areas')
        ], 201);
    }

    public function show($id)
    {
        $zone = Zone::with('areas')->findOrFail($id);
        
        return response()->json([
            'data' => $zone
        ]);
    }

    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $zone->update($request->all());

        return response()->json([
            'message' => 'Zone updated successfully',
            'data' => $zone->load('areas')
        ]);
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json([
            'message' => 'Zone deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->update(['is_active' => !$zone->is_active]);

        return response()->json([
            'message' => 'Zone status updated successfully',
            'data' => $zone->load('areas')
        ]);
    }
}
