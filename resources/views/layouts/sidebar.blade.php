<div class="col-md-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-4 px-3">
        <!-- Brand -->
        <div class="text-center mb-4">
            <h3 class="text-white fw-bold">
                <i class="bi bi-droplet-fill"></i> CleanMate
            </h3>
            <p class="text-white-50 small">Admin Dashboard</p>
        </div>

        <!-- Navigation -->
        <nav class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            @foreach($menuItems as $item)
                <a href="{{ $item['url'] }}" class="nav-link {{ $item['active'] ? 'active' : '' }}">
                    <i class="bi bi-{{ $item['icon'] ?? 'circle' }} me-2"></i> {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- User Section -->
        <div class="mt-auto pt-4 border-top border-white border-opacity-25">
            <div class="d-flex align-items-center text-white mb-2">
                <div class="rounded-circle bg-white bg-opacity-25 p-2 me-2">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold small">{{ Auth::user()->name }}</div>
                    <div class="text-white-50" style="font-size: 0.7rem;">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
