<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('variants.prices')->where('active', true)->get();
        return response()->json($services);
    }

    public function show($id)
    {
        $service = Service::with('variants.prices')->where('active', true)->findOrFail($id);
        return response()->json($service);
    }
}
