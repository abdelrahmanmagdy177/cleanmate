<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 mb-0 fw-bold text-dark">Areas Management</h2>
        <p class="text-muted mb-0">Manage service areas and their zones</p>
    </x-slot>

    <div class="card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>All Areas
                </h5>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add New Area
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="areas-table" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Area Name</th>
                            <th>Zone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($areas as $area)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $area->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold me-2" 
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $area->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="bi bi-map me-1"></i>{{ $area->zone->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($area->is_active)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle-fill me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
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
            $('#areas-table').DataTable({
                pageLength: 10,
                order: [[0, 'asc']],
                language: {
                    search: "Search areas:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ areas"
                }
            });
        });
    </script>
</x-app-layout>
