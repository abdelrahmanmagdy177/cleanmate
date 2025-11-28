<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ZoneService;
use Illuminate\Http\Request;

class ZoneWebController extends Controller
{
    protected ZoneService $zoneService;

    public function __construct(ZoneService $zoneService)
    {
        $this->zoneService = $zoneService;
    }

    public function index()
    {
        $zones = $this->zoneService->getAllZones();
        return view('dashboard.admin.zones.index', compact('zones'));
    }
}
