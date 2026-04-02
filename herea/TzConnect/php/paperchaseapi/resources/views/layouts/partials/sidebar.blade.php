<!-- Sidebar -->
<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="flex items-center h-16 px-6 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i data-lucide="book-open" class="w-5 h-5 text-white"></i>
            </div>
            <span class="text-xl font-bold text-gray-900">PaperChase</span>
        </a>
        <button 
            @click="closeSidebar"
            class="ml-auto lg:hidden text-gray-500 hover:text-gray-700"
        >
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 px-3">
        <ul class="space-y-1">
            
            <!-- Dashboard -->
            <li>
                <a 
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Dashboard
                </a>
            </li>

            <!-- Exams (Editor/Admin) -->
            @can('manage-content')
            <li x-data="{ examsOpen: {{ request()->routeIs('exams.*') ? 'true' : 'false' }} }">
                <button 
                    @click="examsOpen = !examsOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('exams.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span class="flex-1 text-left">Exams</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="examsOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="examsOpen" x-collapse class="mt-1 ml-4 space-y-1">
                    <a 
                        href="{{ route('exams.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('exams.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="list" class="w-4 h-4"></i>
                        All Exams
                    </a>
                    <a 
                        href="{{ route('exams.create') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('exams.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Create New
                    </a>
                </div>
            </li>

            <!-- Subjects (Editor/Admin) -->
            <li x-data="{ subjectsOpen: {{ request()->routeIs('categories.*') ? 'true' : 'false' }} }">
                <button 
                    @click="subjectsOpen = !subjectsOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <i data-lucide="folder-open" class="w-5 h-5"></i>
                    <span class="flex-1 text-left">Subjects</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="subjectsOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="subjectsOpen" x-collapse class="mt-1 ml-4 space-y-1">
                    <a 
                        href="{{ route('categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('categories.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="folder" class="w-4 h-4"></i>
                        All Subjects
                    </a>
                    <a 
                        href="{{ route('categories.create') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('categories.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Create New
                    </a>
                </div>
            </li>
            @endcan

            <!-- Users (Admin Only) -->
            @can('manage-users')
            <li>
                <a 
                    href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Users
                </a>
            </li>
            @endcan

            <!-- Settings -->
            <li x-data="{ settingsOpen: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }">
                <button 
                    @click="settingsOpen = !settingsOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span class="flex-1 text-left">Settings</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="settingsOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="settingsOpen" x-collapse class="mt-1 ml-4 space-y-1">
                    <a 
                        href="{{ route('settings.profile') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('settings.profile') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Profile
                    </a>
                    <a 
                        href="{{ route('settings.notifications') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('settings.notifications') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        Notifications
                    </a>
                    <a 
                        href="{{ route('settings.security') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                        {{ request()->routeIs('settings.security') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    >
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        Security
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- User Info -->
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit"
                    class="p-2 text-gray-500 hover:text-red-600 transition-colors"
                    title="Logout"
                >
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>

