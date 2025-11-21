<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateArea(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:areas,id,is_active,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->area_id = $request->area_id;
        $user->save();

        return response()->json([
            'message' => 'Area updated successfully',
            'user' => $user->load('area')
        ]);
    }
}
