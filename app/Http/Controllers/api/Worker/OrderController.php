<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $worker = $request->user();
        $orders = $worker->orders()
            ->with(['customer', 'customerAddress', 'service', 'variant'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function updateStatus(Request $request, $id)
    {
        $worker = $request->user();
        $order = $worker->orders()->where('order_id', $id)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or not assigned to you'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:worker_on_way,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newStatus = $request->status;
        $currentStatus = $order->status; // This is the order status, not pivot

        // Validate transitions (basic logic)
        // assigned -> worker_on_way
        // worker_on_way -> in_progress
        // in_progress -> completed
        
        // We can add strict checks if needed, but for now let's allow moving forward.
        
        // Update Order status and history
        $order->updateStatus($newStatus, 'worker', $worker->id);

        // Update Pivot status
        $order->pivot->status = $newStatus == 'worker_on_way' ? 'accepted' : ($newStatus == 'completed' ? 'completed' : 'accepted');
        $order->pivot->save();

        return response()->json(['message' => 'Order status updated successfully']);
    }
}
