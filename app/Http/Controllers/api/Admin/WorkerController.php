<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::all();
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

        $worker = Worker::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Worker created successfully', 'data' => $worker], 201);
    }

    public function update(Request $request, $id)
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json(['error' => 'Worker not found'], 404);
        }

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

        $data = $request->only(['name', 'email', 'phone', 'status']);
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $worker->update($data);

        return response()->json(['message' => 'Worker updated successfully', 'data' => $worker]);
    }

    public function destroy($id)
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return response()->json(['error' => 'Worker not found'], 404);
        }

        $worker->delete();

        return response()->json(['message' => 'Worker deleted successfully']);
    }

    public function assignOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|exists:workers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $worker = Worker::find($request->worker_id);

        // Attach worker to order if not already attached
        if (!$order->workers()->where('worker_id', $worker->id)->exists()) {
            $order->workers()->attach($worker->id, ['status' => 'assigned']);
        }

        // Update order status
        $order->updateStatus('assigned', 'system'); // Or 'admin' if we had admin auth context

        return response()->json(['message' => 'Worker assigned successfully']);
    }
}
