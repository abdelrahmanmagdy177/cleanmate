<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 mb-0 fw-bold text-dark">Dashboard</h2>
        <p class="text-muted mb-0">Welcome to CleanMate Admin Dashboard</p>
    </x-slot>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Orders</h6>
                            <h2 class="display-5 mb-0">{{ \App\Models\Order::count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-bag-check-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Zones</h6>
                            <h2 class="display-5 mb-0">{{ \App\Models\Zone::count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-geo-alt-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Areas</h6>
                            <h2 class="display-5 mb-0">{{ \App\Models\Area::count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-map-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase mb-2">Total Workers</h6>
                            <h2 class="display-5 mb-0">{{ \App\Models\Worker::count() }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ url('dashboard/admin/orders') }}" class="btn btn-outline-primary btn-lg text-start">
                            <i class="bi bi-bar-chart-fill me-2"></i>View Orders Analytics
                        </a>
                        <a href="{{ url('dashboard/admin/zones') }}" class="btn btn-outline-success btn-lg text-start">
                            <i class="bi bi-map-fill me-2"></i>Manage Zones
                        </a>
                        <a href="{{ url('dashboard/admin/areas') }}" class="btn btn-outline-info btn-lg text-start">
                            <i class="bi bi-geo-alt-fill me-2"></i>Manage Areas
                        </a>
                        <a href="{{ url('dashboard/admin/workers') }}" class="btn btn-outline-warning btn-lg text-start">
                            <i class="bi bi-people-fill me-2"></i>Manage Workers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @php
                            $recentOrders = \App\Models\Order::with('customer')->latest()->take(5)->get();
                        @endphp
                        @forelse($recentOrders as $order)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Order #{{ $order->id }}</h6>
                                        <small class="text-muted">{{ $order->customer->name ?? 'Unknown' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ number_format($order->total_price, 2) }} EGP</div>
                                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6"></i>
                                <p class="mb-0 mt-2">No recent orders</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <i class="bi bi-calendar-check text-primary fs-3"></i>
                            <h6 class="mt-2 mb-0">Today's Date</h6>
                            <p class="text-muted">{{ now()->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-clock text-success fs-3"></i>
                            <h6 class="mt-2 mb-0">Current Time</h6>
                            <p class="text-muted">{{ now()->format('h:i A') }}</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-person-circle text-info fs-3"></i>
                            <h6 class="mt-2 mb-0">Logged in as</h6>
                            <p class="text-muted">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-shield-check text-warning fs-3"></i>
                            <h6 class="mt-2 mb-0">System Status</h6>
                            <p class="text-muted"><span class="badge bg-success">Online</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
