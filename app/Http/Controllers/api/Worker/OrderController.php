<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Services\Worker\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $worker = $request->user();
        $orders = $this->orderService->getWorkerOrders($worker);

        return response()->json(['data' => $orders]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:worker_on_way,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->orderService->updateOrderStatus($request->user(), $id, $request->status);
            return response()->json(['message' => 'Order status updated successfully']);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 404); // Or 422/404 depending on error. Service throws if order not found.
        }
    }
}
