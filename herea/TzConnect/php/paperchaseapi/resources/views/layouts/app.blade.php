<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PaperChase - Exam Management System">
    <title>@yield('title', 'PaperChase')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        [x-cloak] {
            display: none !important;
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen" x-data="layoutManager()">
    
    <div class="flex min-h-screen">
        <!-- Mobile Overlay -->
        <div 
            x-show="sidebarOpen && isMobile" 
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeSidebar"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
            x-cloak
        ></div>

        <!-- Sidebar -->
        <aside 
            x-show="shouldShowSidebar"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full lg:translate-x-0"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full lg:translate-x-0"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            @include('layouts.partials.sidebar')
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                @include('layouts.partials.header')
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden">
                <div class="p-4 sm:p-6 lg:p-8">
                    <div class="mx-auto max-w-7xl">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile Menu Button -->
        <button 
            x-show="shouldShowSidebar && isMobile && !sidebarOpen"
            @click="toggleSidebar"
            class="fixed bottom-6 right-6 z-50 bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-colors"
            aria-label="Open navigation menu"
        >
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script>
        function layoutManager() {
            return {
                sidebarOpen: false,
                isMobile: window.innerWidth < 1024,
                userRole: '{{ auth()->user()->role ?? "VIEWER" }}',
                
                get shouldShowSidebar() {
                    return this.userRole !== 'VIEWER';
                },
                
                init() {
                    this.checkScreenSize();
                    window.addEventListener('resize', () => this.checkScreenSize());
                    
                    // Close sidebar on route change for mobile
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                },
                
                checkScreenSize() {
                    this.isMobile = window.innerWidth < 1024;
                    if (!this.isMobile) {
                        this.sidebarOpen = true;
                    }
                },
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                
                closeSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                }
            }
        }

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    
    @yield('scripts')
</body>
</html>

