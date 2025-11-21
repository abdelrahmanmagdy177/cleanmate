<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\WorkerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkerController extends Controller
{
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function index()
    {
        $workers = $this->workerService->getAllWorkers();
        return response()->json(['data' => $workers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:workers,email',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $worker = $this->workerService->createWorker($request->all());

        return response()->json(['message' => 'Worker created successfully', 'data' => $worker], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'email|unique:workers,email,' . $id,
            'phone' => 'string',
            'password' => 'string|min:6',
            'status' => 'in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $worker = $this->workerService->updateWorker($id, $request->all());

        if (!$worker) {
            return response()->json(['error' => 'Worker not found'], 404);
        }

        return response()->json(['message' => 'Worker updated successfully', 'data' => $worker]);
    }

    public function destroy($id)
    {
        $deleted = $this->workerService->deleteWorker($id);

        if (!$deleted) {
            return response()->json(['error' => 'Worker not found'], 404);
        }

        return response()->json(['message' => 'Worker deleted successfully']);
    }

    public function assignOrder(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|exists:workers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->workerService->assignOrder($orderId, $request->worker_id);
            return response()->json(['message' => 'Worker assigned successfully']);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
