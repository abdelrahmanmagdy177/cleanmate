<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 mb-0 fw-bold text-dark">Orders Analytics</h2>
        <p class="text-muted mb-0">Monitor order performance and zone analytics</p>
    </x-slot>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.admin.orders.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-semibold">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label for="zone_id" class="form-label fw-semibold">Zone Filter</label>
                    <select class="form-select" id="zone_id" name="zone_id">
                        <option value="">All Zones</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ $zoneId == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <a href="{{ route('dashboard.admin.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Orders -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Orders</h6>
                            <h2 class="display-4 mb-0">{{ number_format($orders->count()) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-bag-check-fill fs-2"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success bg-opacity-75">
                            <i class="bi bi-arrow-up"></i> +12.5%
                        </span>
                        <span class="ms-2 small">vs last period</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Revenue</h6>
                            <h2 class="display-4 mb-0">{{ number_format($orders->sum('total_price'), 0) }}</h2>
                            <p class="mb-0 small">EGP</p>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-currency-dollar fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Zones -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Operating Zones</h6>
                            <h2 class="display-4 mb-0">{{ $ordersByZone->count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-geo-alt-fill fs-2"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone Performance Section -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>Zone Performance
                </h5>
                <span class="badge bg-primary">{{ $ordersByZone->count() }} Zones</span>
            </div>
        </div>
        <div class="card-body">
            @if($ordersByZone->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Data Available</h4>
                    <p class="text-muted">Try adjusting your filters to see zone performance.</p>
                </div>
            @else
                <div class="row">
                    @php $maxOrders = $ordersByZone->max('total_orders') ?: 1; @endphp
                    @foreach($ordersByZone as $index => $stat)
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                            <div class="card zone-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $stat->zone_name }}</h6>
                                            <small class="text-muted">Zone #{{ $stat->zone_id }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Orders</small>
                                            <strong>{{ $stat->total_orders }}</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                style="width: {{ ($stat->total_orders / $maxOrders) * 100 }}%"
                                                aria-valuenow="{{ $stat->total_orders }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="{{ $maxOrders }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Revenue</small>
                                            <strong class="text-success">{{ number_format($stat->total_revenue, 0) }} EGP</strong>
                                        </div>
                                    </div>

                                    <a href="{{ route('dashboard.admin.orders.index', ['zone_id' => $stat->zone_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                        class="btn btn-sm btn-outline-primary w-100 mt-3">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-ul text-primary me-2"></i>Recent Orders
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="orders-table" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Location</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#{{ $order->id }}</span>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-2" 
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($order->customer->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $order->customer->name ?? 'Unknown' }}</div>
                                            <small class="text-muted">{{ $order->customer->phone ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customerAddress->area->zone->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $order->customerAddress->area->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $order->service->name ?? 'Service' }}</span>
                                </td>
                                <td>
                                    <strong>{{ number_format($order->total_price, 2) }} EGP</strong>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'completed' => ['class' => 'success', 'icon' => 'check-circle-fill'],
                                            'pending' => ['class' => 'warning', 'icon' => 'clock-fill'],
                                            'cancelled' => ['class' => 'danger', 'icon' => 'x-circle-fill'],
                                            'processing' => ['class' => 'info', 'icon' => 'arrow-repeat'],
                                        ];
                                        $config = $statusConfig[$order->status] ?? ['class' => 'secondary', 'icon' => 'circle-fill'];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }}">
                                        <i class="bi bi-{{ $config['icon'] }} me-1"></i>{{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                pageLength: 10,
                order: [[1, 'desc']],
                language: {
                    search: "Search orders:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ orders"
                }
            });
        });
    </script>
</x-app-layout>
