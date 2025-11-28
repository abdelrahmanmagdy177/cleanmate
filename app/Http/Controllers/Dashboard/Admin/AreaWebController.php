<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AreaService;
use Illuminate\Http\Request;

class AreaWebController extends Controller
{
    protected AreaService $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
    }

    public function index(Request $request)
    {
        $areas = $this->areaService->getAllAreas($request->zone_id);
        return view('dashboard.admin.areas.index', compact('areas'));
    }
}
