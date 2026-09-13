<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin - ActiveCourt')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#1d4ed8',
                            700: '#1e3a5f',
                            800: '#1e2d4f',
                            900: '#0f1b33',
                            950: '#080f1f',
                        },
                        accent: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            @if(session('info'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('info') }}"
                });
            @endif

            @if($errors->any())
                Toast.fire({
                    icon: 'error',
                    title: "{{ $errors->first() }}"
                });
            @endif

            // Global confirmation handler for forms using data-confirm attribute
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.dataset.confirmed) {
                    return;
                }
                
                const confirmMessage = form.getAttribute('data-confirm');
                if (confirmMessage) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: confirmMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1d4ed8',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-primary-900 border-r border-primary-800 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.1)]">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-primary-800">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-white flex items-center gap-2">
                    <i class="fas fa-layer-group text-primary-500 text-2xl"></i> ActiveCourt
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Admin Info -->
            <div class="px-6 py-5 border-b border-primary-800 bg-primary-800/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full border-2 border-primary-700 shadow-sm overflow-hidden bg-primary-800 flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name_users ?? 'Admin') }}&background=3b82f6&color=fff&bold=true" class="w-full h-full object-cover">
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name_users ?? 'Admin' }}</p>
                        <p class="text-[11px] font-medium text-primary-300 uppercase tracking-wider mt-0.5">{{ Auth::user()->role ?? 'Administrator' }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5 scrollbar-thin scrollbar-thumb-primary-700">
                <div class="px-3 pb-2 pt-1 text-[11px] font-bold text-primary-400 uppercase tracking-wider">Main Menu</div>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-primary-800 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'bg-primary-800/50 text-slate-400' }}">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    Dashboard
                </a>
                
                <a href="{{ route('admin.fields') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.fields*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-primary-800 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.fields*') ? 'bg-white/20 text-white' : 'bg-primary-800/50 text-slate-400' }}">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    Field Management
                </a>
                
                <a href="{{ route('admin.users') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-primary-800 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.users*') ? 'bg-white/20 text-white' : 'bg-primary-800/50 text-slate-400' }}">
                        <i class="fas fa-users"></i>
                    </div>
                    User Management
                </a>
                
                <a href="{{ route('admin.schedules') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.schedules*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-primary-800 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.schedules*') ? 'bg-white/20 text-white' : 'bg-primary-800/50 text-slate-400' }}">
                        <i class="fas fa-clock"></i>
                    </div>
                    Schedule
                </a>
                
                <a href="{{ route('admin.bookings') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.bookings*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-primary-800 hover:text-white' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.bookings*') ? 'bg-white/20 text-white' : 'bg-primary-800/50 text-slate-400' }}">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    Bookings
                </a>
            </nav>

            <!-- Bottom Actions -->
            <div class="p-4 border-t border-primary-800 bg-primary-800/30">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:bg-primary-800 hover:text-white transition-all duration-200">
                    <i class="fas fa-external-link-alt w-5 text-center text-slate-400"></i>
                    View Website
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all duration-200">
                        <i class="fas fa-sign-out-alt w-5 text-center text-red-400"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 flex-shrink-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-primary-600 transition-colors focus:outline-none lg:hidden bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-primary-600 transition-colors focus:outline-none hidden lg:flex bg-slate-50 w-10 h-10 rounded-full items-center justify-center">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">@yield('page-title', 'Dashboard')</h2>
                </div>
                
                <div class="flex items-center gap-5">
                    <div class="hidden sm:flex items-center gap-2 text-sm font-medium text-slate-500 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                        <i class="far fa-calendar-alt text-slate-400"></i>
                        {{ now()->format('d M Y') }}
                    </div>
                    
                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2.5 focus:outline-none group">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-bold text-slate-800 leading-tight group-hover:text-primary-600 transition-colors">{{ Auth::user()->name_users ?? 'Admin' }}</p>
                                <p class="text-xs text-slate-500 font-medium">Administrator</p>
                            </div>
                            <div class="w-10 h-10 rounded-full border-2 border-primary-100 overflow-hidden bg-slate-200 group-hover:border-primary-300 transition-colors shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name_users ?? 'Admin') }}&background=1d4ed8&color=fff&bold=true" class="w-full h-full object-cover">
                            </div>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                             class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-slate-100 py-2 z-50" style="display: none;">

                            <div class="px-4 py-3 border-b border-slate-100 mb-1 md:hidden">
                                <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name_users ?? 'Admin' }}</p>
                                <p class="text-xs text-slate-500 font-medium truncate">{{ Auth::user()->email ?? 'admin@activecourt.com' }}</p>
                            </div>

                            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500"><i class="fas fa-external-link-alt text-xs"></i></div>
                                View Website
                            </a>

                            <div class="h-px bg-slate-100 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                    <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center text-red-500"><i class="fas fa-sign-out-alt text-xs"></i></div>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
