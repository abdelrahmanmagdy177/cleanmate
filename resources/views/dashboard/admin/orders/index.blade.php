<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Orders Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                <form method="GET" action="{{ route('dashboard.admin.orders.index') }}" class="flex flex-wrap gap-6 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 transition duration-150 ease-in-out">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 transition duration-150 ease-in-out">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="zone_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zone</label>
                        <select name="zone_id" id="zone_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-300 transition duration-150 ease-in-out">
                            <option value="">All Zones</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ $zoneId == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md transition duration-150 ease-in-out">
                            Filter
                        </button>
                        <a href="{{ route('dashboard.admin.orders.index') }}" class="px-6 py-2.5 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-md transition duration-150 ease-in-out">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Global Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider">Total Orders</p>
                            <p class="text-3xl font-bold mt-1">{{ $orders->count() }}</p>
                        </div>
                        <div class="p-3 bg-indigo-400 bg-opacity-30 rounded-full">
                            <svg class="w-8 h-8 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">Total Revenue</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($orders->sum('total_price'), 2) }} <span class="text-lg font-normal">EGP</span></p>
                        </div>
                        <div class="p-3 bg-emerald-400 bg-opacity-30 rounded-full">
                            <svg class="w-8 h-8 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium uppercase tracking-wider">Active Zones</p>
                            <p class="text-3xl font-bold mt-1">{{ $ordersByZone->count() }}</p>
                        </div>
                        <div class="p-3 bg-purple-400 bg-opacity-30 rounded-full">
                            <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zones Performance Grid -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Zone Performance
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @php
                        $maxOrders = $ordersByZone->max('total_orders') ?: 1;
                    @endphp
                    @foreach($ordersByZone as $stat)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white truncate" title="{{ $stat->zone_name }}">{{ $stat->zone_name }}</h4>
                                    <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ $stat->total_orders }} Orders
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-500 dark:text-gray-400">Revenue</span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stat->total_revenue, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($stat->total_orders / $maxOrders) * 100 }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <a href="{{ route('dashboard.admin.orders.index', ['zone_id' => $stat->zone_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hover:underline dark:text-indigo-400">
                                        View Details &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($ordersByZone->isEmpty())
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center border border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No Data Found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No orders found for the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detailed Orders Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Detailed Orders List</h3>
                    <div class="overflow-x-auto">
                        <table id="orders-table" class="display w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Customer</th>
                                    <th class="px-4 py-3">Zone</th>
                                    <th class="px-4 py-3">Area</th>
                                    <th class="px-4 py-3">Service</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                        <td class="px-4 py-3">{{ $order->order_date }}</td>
                                        <td class="px-4 py-3">{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ $order->customerAddress->area->zone->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $order->customerAddress->area->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $order->service->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ number_format($order->total_price, 2) }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                ];
                                                $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                responsive: true,
                order: [[ 1, "desc" ]],
                pageLength: 25,
                language: {
                    search: "",
                    searchPlaceholder: "Search orders..."
                }
            });
        });
    </script>
</x-app-layout>
