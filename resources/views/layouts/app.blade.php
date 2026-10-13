<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appName ?? config('app.name', 'Go Clear') }} - @yield('title')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('photo/pickup.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/solid.min.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar-brand.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Mobile-first fixes -->
    <style>
        /* Sidebar hidden by default on mobile, slide in/out */
        @media (max-width: 767px) {
            #sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            #sidebar.open {
                transform: translateX(0);
            }
            /* Overlay when sidebar open */
            #sidebarOverlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
            }
            #sidebarOverlay.active {
                display: block;
            }
        }

        /* Dropdown Animation */
        #profileDropdown {
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.2s ease-in-out;
            pointer-events: none;
        }
        #profileDropdown.active {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        /* Custom Scrollbar for Sidebar */
#sidebar nav::-webkit-scrollbar {
    width: 6px;
}

#sidebar nav::-webkit-scrollbar-track {
    background: #1f2937; /* gray-800 */
}

#sidebar nav::-webkit-scrollbar-thumb {
    background: #4b5563; /* gray-600 */
    border-radius: 3px;
}

#sidebar nav::-webkit-scrollbar-thumb:hover {
    background: #6b7280; /* gray-500 */
}
        
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" class="hidden"></div>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-gray-900 text-white flex flex-col transition-all duration-300 fixed md:relative md:translate-x-0 z-50">
            <!-- Brand + Toggle Button -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('photo/car.svg') }}" alt="{{ $appName ?? config('app.name', 'Go-Clear') }}" class="w-12 h-12">
                    <div>
                        <div class="text-lg font-bold">{{ $appName ?? config('app.name', 'Go-Clear') }}</div>
                        <div class="text-xs text-gray-400">Administrator</div>
                    </div>
                </div>

                <!-- Mobile Close Button (X) -->
                <button id="closeSidebar" class="md:hidden p-2 rounded hover:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-4">
                @include('admin.partials.sidebar-menu')
            </nav>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-400">
                v{{ config('app.version') }} • {{ date('Y') }}

            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:ml-0">
            <!-- Top Bar (Mobile Hamburger + Page Title + User) -->
            <header class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center px-4 md:px-8">
                <div class="flex items-center justify-between w-full">
                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="p-2 rounded-md hover:bg-gray-100 md:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <!-- Notifications Bell -->
            
                    <div class="relative ml-auto mr-2">
                        <button id="notificationBtn" class="relative">
                            🔔
                            <span id="notificationCount"
                                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1 rounded-full hidden">
                            </span>
                        </button>

                        <div id="notificationDropdown"
                            class="hidden absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg z-50">
                            <ul id="notificationList"></ul>
                        </div>
                    </div>

                    <!-- Profile Dropdown Button -->
                    @php $image = Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : null; @endphp
                    
                    <div class="relative">
                        
                        <button id="profileDropdownBtn" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            @if($image)
                                <div class="relative w-10 h-10">
                                    <img src="{{ $image }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-white"></span>
                                </div>
                            @else
                                <div class="relative w-10 h-10 flex items-center justify-center bg-teal-600 rounded-full text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-white"></span>
                                </div>
                            @endif
                            
                            <!-- Hidden on mobile, shown on desktop -->
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>

                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 text-gray-500 transition-transform" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="profileDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <!-- User Info (Mobile Only) -->
                            <div class="px-4 py-3 border-b border-gray-200 md:hidden">
                                <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>

                            <!-- Menu Items -->
                            <a href="{{ route('admin.account.edit') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm">My Profile</span>
                            </a>

                            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm">Settings</span>
                            </a>

                            <div class="border-t border-gray-200 my-2"></div>

                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-2 hover:bg-red-50 text-red-600 transition-colors w-full text-left">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span class="text-sm font-medium">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <x-alert />



            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', () => {

    const btn = document.getElementById('notificationBtn');
    const dropdown = document.getElementById('notificationDropdown');
    const list = document.getElementById('notificationList');
    const badge = document.getElementById('notificationCount');

    btn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    function loadNotifications() {
        fetch('{{ route("admin.notifications.bell") }}')
            .then(res => res.json())
            .then(data => {

                list.innerHTML = '';
                badge.textContent = data.unread;
                badge.classList.toggle('hidden', data.unread === 0);

                data.notifications.forEach(n => {
                    const li = document.createElement('li');
                    li.className = 'p-3 border-b hover:bg-gray-100 cursor-pointer';
                    li.innerHTML = `<strong>${n.title}</strong><br>${n.message}`;

  li.onclick = () => {

    // 1️⃣ Mark as read
    fetch(`/admin/notifications/${n.id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // 2️⃣ Redirect to verification page
    if (n.data && n.data.driver_profile_id) {
        window.location.href = `/admin/verifications/${n.data.driver_profile_id}`;
    }
};


                    list.appendChild(li);
                });
            });
    }

    loadNotifications();
});
</script>



    <!-- Improved Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Sidebar Controls
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openBtn = document.getElementById('mobileMenuBtn');
            const closeBtn = document.getElementById('closeSidebar');

            const openSidebar = () => {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeSidebar = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            };

            openBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            // Dropdown Controls
            const dropdownBtn = document.getElementById('profileDropdownBtn');
            const dropdown = document.getElementById('profileDropdown');
            const dropdownArrow = document.getElementById('dropdownArrow');

            const toggleDropdown = () => {
                dropdown.classList.toggle('active');
                dropdownArrow.style.transform = dropdown.classList.contains('active') 
                    ? 'rotate(180deg)' 
                    : 'rotate(0deg)';
            };

            const closeDropdown = () => {
                dropdown.classList.remove('active');
                dropdownArrow.style.transform = 'rotate(0deg)';
            };

            dropdownBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target) && !dropdownBtn.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (sidebar.classList.contains('open')) {
                        closeSidebar();
                    }
                    if (dropdown.classList.contains('active')) {
                        closeDropdown();
                    }
                }
            });
        });
    </script>


</body>
</html>