<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $zoneId = $request->input('zone_id');

        // Summary: Orders count and Revenue per Zone
        $ordersByZone = Order::query()
            ->join('customer_addresses', 'orders.customer_address_id', '=', 'customer_addresses.id')
            ->join('areas', 'customer_addresses.area_id', '=', 'areas.id')
            ->join('zones', 'areas.zone_id', '=', 'zones.id')
            ->select(
                'zones.id as zone_id',
                'zones.name as zone_name',
                DB::raw('count(orders.id) as total_orders'),
                DB::raw('sum(orders.total_price) as total_revenue')
            )
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('zones.id', 'zones.name')
            ->get();

        // Detailed Orders List
        $orders = Order::query()
            ->with(['customer', 'customerAddress.area.zone', 'service'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->when($zoneId, function($q) use ($zoneId) {
                 $q->whereHas('customerAddress.area', function($sq) use ($zoneId) {
                     $sq->where('zone_id', $zoneId);
                 });
            })
            ->latest('order_date')
            ->get();
            
        $zones = Zone::active()->get();

        return view('dashboard.admin.orders.index', compact('orders', 'zones', 'startDate', 'endDate', 'ordersByZone', 'zoneId'));
    }
}
