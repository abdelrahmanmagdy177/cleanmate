<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Workers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table id="workers-table" class="display w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workers as $worker)
                                <tr>
                                    <td>{{ $worker->id }}</td>
                                    <td>{{ $worker->name }}</td>
                                    <td>{{ $worker->email }}</td>
                                    <td>{{ $worker->phone }}</td>
                                    <td>{{ $worker->status ?? 'Active' }}</td>
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
            $('#workers-table').DataTable();
        });
    </script>
</x-app-layout>
