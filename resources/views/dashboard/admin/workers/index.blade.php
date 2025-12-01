<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 mb-0 fw-bold text-dark">Workers Management</h2>
        <p class="text-muted mb-0">Manage service workers and staff</p>
    </x-slot>

    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people-fill text-primary me-2"></i>All Workers
                </h5>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Worker
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="workers-table" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Worker Name</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workers as $worker)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $worker->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-2" 
                                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                            {{ substr($worker->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $worker->name }}</div>
                                            <small class="text-muted">{{ $worker->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="bi bi-telephone-fill text-muted me-1"></i>
                                        <span>{{ $worker->phone }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $status = $worker->status ?? 'Active';
                                        $statusClass = match(strtolower($status)) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            'on leave' => 'warning',
                                            default => 'secondary'
                                        };
                                        $statusIcon = match(strtolower($status)) {
                                            'active' => 'check-circle-fill',
                                            'inactive' => 'x-circle-fill',
                                            'on leave' => 'clock-fill',
                                            default => 'circle-fill'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" title="View Details">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
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
            $('#workers-table').DataTable({
                pageLength: 10,
                order: [[0, 'asc']],
                language: {
                    search: "Search workers:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ workers"
                }
            });
        });
    </script>
</x-app-layout>
