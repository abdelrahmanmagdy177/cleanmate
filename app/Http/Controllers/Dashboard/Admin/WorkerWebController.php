<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\WorkerService;
use Illuminate\Http\Request;

class WorkerWebController extends Controller
{
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function index()
    {
        $workers = $this->workerService->getAllWorkers();
        return view('dashboard.admin.workers.index', compact('workers'));
    }
}
