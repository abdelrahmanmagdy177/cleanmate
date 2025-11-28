<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Areas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table id="areas-table" class="display w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Zone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                                <tr>
                                    <td>{{ $area->id }}</td>
                                    <td>{{ $area->name }}</td>
                                    <td>{{ $area->zone->name ?? 'N/A' }}</td>
                                    <td>{{ $area->is_active ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <button class="text-blue-500 hover:underline">Edit</button>
                                        <button class="text-red-500 hover:underline ml-2">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#areas-table').DataTable();
        });
    </script>
</x-app-layout>
