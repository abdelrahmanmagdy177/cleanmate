<div class="flex flex-col w-64 h-screen px-4 py-8 bg-white border-r dark:bg-gray-900 dark:border-gray-700">
    <h2 class="text-3xl font-semibold text-gray-800 dark:text-white text-center">CleanMate</h2>

    <div class="flex flex-col justify-between flex-1 mt-6">
        <nav>
            @foreach($menuItems as $item)
                <a class="flex items-center px-4 py-2 mt-5 text-gray-600 transition-colors duration-300 transform rounded-md dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 {{ $item['active'] ? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' : '' }}" href="{{ $item['url'] }}">
                    <span class="mx-4 font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="flex items-center px-4 -mx-2">
            <div class="mx-2">
                <h4 class="font-medium text-gray-800 dark:text-gray-200 hover:underline cursor-pointer">{{ Auth::user()->name }}</h4>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:underline">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
