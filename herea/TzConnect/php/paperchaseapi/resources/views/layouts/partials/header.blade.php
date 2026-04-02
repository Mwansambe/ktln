<!-- Header -->
<div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
    <!-- Left side - Mobile menu button -->
    <div class="flex items-center gap-4">
        <button 
            @click="toggleSidebar"
            class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg"
        >
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
        
        <!-- Breadcrumb -->
        <nav class="hidden sm:flex" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="home" class="w-4 h-4"></i>
                    </a>
                </li>
                @hasSection('breadcrumb')
                    <li class="text-gray-400">/</li>
                    @yield('breadcrumb')
                @endif
            </ol>
        </nav>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-2 sm:gap-4">
        <!-- Search -->
        <div class="hidden md:block relative">
            <input 
                type="text" 
                placeholder="Search..." 
                class="w-64 pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors"
            >
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
        </div>

        <!-- Notifications -->
        <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg relative">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- User Menu -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button 
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-sm font-semibold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <span class="hidden sm:block text-sm font-medium text-gray-700">
                    {{ Auth::user()->name }}
                </span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
            </button>
            
            <!-- Dropdown Menu -->
            <div 
                x-show="userMenuOpen"
                @click.away="userMenuOpen = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                x-cloak
            >
                <a href="{{ route('settings.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    Profile
                </a>
                <a href="{{ route('settings.security') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    Security
                </a>
                <hr class="my-1 border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

