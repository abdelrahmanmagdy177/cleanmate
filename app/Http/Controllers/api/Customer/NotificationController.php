<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('email', $request->customer_email)->first();
        
        $notifications = $customer->notifications()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $customer->notifications()->unread()->count();

        return response()->json([
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('email', $request->customer_email)->first();
        
        $customer->notifications()->unread()->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
