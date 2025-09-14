<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Tuition Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 transition-colors duration-300">

@php
    // 支持通配（例如 'admin.subjects.*'），或传入数组任一命中即高亮
    function activeClass($route) {
        $isActive = is_array($route) ? collect($route)->contains(fn($r) => request()->routeIs($r)) : request()->routeIs($route);
        return $isActive
            ? 'bg-gradient-to-r from-sky-500 to-blue-600 text-white shadow-md'
            : 'hover:bg-gradient-to-r hover:from-sky-400 hover:to-blue-500 hover:text-white';
    }
    $user = Auth::user();
    $avatarInitial = strtoupper(mb_substr($user->name ?? 'U', 0, 1, 'UTF-8'));

    $homeUrl = url('/'); // 默认首页
    if (Route::has('home')) {
        $homeUrl = route('home');
    } elseif (auth()->check()) {
        $role = auth()->user()->role ?? null;
        if ($role === 'admin'   && Route::has('admin.dashboard'))   $homeUrl = route('admin.dashboard');
        if ($role === 'tutor'   && Route::has('tutor.dashboard'))   $homeUrl = route('tutor.dashboard');
        if ($role === 'student' && Route::has('student.dashboard')) $homeUrl = route('student.dashboard');
    }
@endphp

<div class="flex h-dvh md:h-screen">

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="w-64 rounded-r-xl shadow-xl 
               bg-gradient-to-b from-white/30 via-gray-50/20 to-gray-100/10
               dark:from-gray-800/40 dark:via-gray-900/30 dark:to-black/20
               backdrop-blur-xl border-r border-gray-200/50 dark:border-gray-700/50
               text-gray-800 dark:text-gray-200
               transform transition-transform duration-300 ease-in-out 
               md:translate-x-0 -translate-x-64 fixed md:relative h-full z-50"
        role="navigation" aria-label="Primary">

        <div class="h-full flex flex-col">

            {{-- Top: Brand + Dark Mode --}}
            <div class="p-4 flex items-center justify-between border-b border-gray-200/50 dark:border-gray-600/50">
                <a href="{{ $homeUrl }}" class="text-lg font-bold flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-sky-400 rounded-md px-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-sky-500 dark:text-sky-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6h4M8 6v6H4m8 0l4 4m0 0l-4 4m4-4H8"/>
                    </svg>
                    <span class="truncate">Smart Tuition</span>
                </a>

                {{-- Dark Mode Toggle --}}
                <button id="toggleDark"
                        class="p-2 rounded-md hover:bg-gray-200/40 dark:hover:bg-gray-700/40 transition focus:outline-none focus:ring-2 focus:ring-sky-400"
                        type="button" aria-label="Toggle dark mode">
                    <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                    </svg>
                    <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 3v1m0 16v1m8.66-13.66l-.707.707M4.05 19.95l-.707.707M21 12h1M2 12H1m16.95 4.95l.707.707M4.05 4.05l.707.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                </button>
            </div>

            {{-- Middle: Nav --}}
            <nav class="mt-4 space-y-1 px-0 flex-1 overflow-y-auto">
                <h2 class="sr-only">Main navigation</h2>
                @auth
                    {{-- Admin Menu --}}
                    @if($user->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('admin.dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.users') }}"
                           class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('admin.users') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A3 3 0 016 17h12a3 3 0 01.879 5.804M15 11a3 3 0 00-6 0v1h6v-1z"/>
                            </svg>
                            <span>Manage Users</span>
                        </a>

                        <a href="{{ route('admin.subjects.index') }}"
                           class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('admin.subjects.*') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6l-2 1-2-1-2 1-2-1v12l2 1 2-1 2 1 2-1 2 1 2-1 2 1 2-1V6l-2 1-2-1-2 1-2-1z"/>
                            </svg>
                            <span>Subjects</span>
                        </a>

                        <a href="{{ route('admin.lessons.index') }}"
                           class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('admin.lessons.*') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h8M8 11h8m-8 4h5M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                            </svg>
                            <span>Lessons</span>
                        </a>
                    @endif

                    {{-- Tutor Menu --}}
                    @if($user->role === 'tutor')
                        <a href="{{ route('tutor.dashboard', [], false) }}"
                        class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('tutor.dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 7v-7"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        {{-- Attendance -> tutor.lessons --}}
                        <a href="{{ route('tutor.lessons') }}"
                        class="flex items-center px-4 py-2 rounded-md transition {{ activeClass(['tutor.lessons','attendance.*']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h8M8 11h8m-8 4h5M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                            </svg>
                            <span>Attendance</span>
                        </a>
                    @endif



                    {{-- Student Menu --}}
                    @if($user->role === 'student')
                        <a href="{{ route('student.dashboard') }}"
                           class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('student.dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 20h9M3 20h9M5 4h14M5 8h14M5 12h14M5 16h14"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="#"
                           class="flex items-center px-4 py-2 rounded-md transition hover:bg-gradient-to-r hover:from-pink-400 hover:to-red-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-pink-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 14l9-5-9-5-9 5 9 5zm0 7v-7"/>
                            </svg>
                            <span>Enrolled Courses</span>
                        </a>
                    @endif

                    {{-- Common Menu --}}
                    <a href="{{ route('account.edit') }}"
                       class="flex items-center px-4 py-2 rounded-md transition {{ activeClass('account.edit') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14c3.866 0 7 3.134 7 7H5c0-3.866 3.134-7 7-7zM12 12a5 5 0 100-10 5 5 0 000 10z"/>
                        </svg>
                        <span>Account Profile</span>
                    </a>
                @endauth
            </nav>

            {{-- Bottom: Logout --}}
            @auth
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 rounded-md flex items-center transition hover:bg-gradient-to-r hover:from-red-500 hover:to-orange-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-16v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @endauth

        </div>
    </aside>

    <!-- Overlay（移动端） -->
    <div id="overlay" class="fixed inset-0 bg-black/50 hidden md:hidden z-40 opacity-0 transition-opacity duration-300"></div>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="sticky top-0 z-30 flex items-center justify-between bg-white/80 dark:bg-gray-800/70 backdrop-blur shadow px-4 md:px-6 py-3 transition-colors duration-300">
            <div class="flex items-center gap-3">
                <button id="menuBtn"
                        class="md:hidden text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-sky-400 rounded-md px-2 py-1"
                        aria-label="Open sidebar" aria-controls="sidebar" aria-expanded="false">☰</button>
                {{-- 可选：面包屑插槽 --}}
                @hasSection('breadcrumb')
                    <nav aria-label="Breadcrumb" class="hidden sm:block">
                        @yield('breadcrumb')
                    </nav>
                @endif
            </div>

            <div class="flex items-center gap-3 min-w-0">
                <div class="h-8 w-8 rounded-full bg-sky-500/90 text-white flex items-center justify-center text-sm font-semibold shadow" aria-hidden="true">
                    {{ $avatarInitial }}
                </div>
                <span class="truncate max-w-[12rem] sm:max-w-xs" title="{{ $user->name ?? 'Guest' }}">{{ $user->name ?? 'Guest' }}</span>
            </div>
        </header>

        <main class="p-4 md:p-6 flex-1 overflow-y-auto transition-colors duration-300">
            @yield('content')
        </main>
    </div>
</div>

<script>
    (function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuBtn = document.getElementById('menuBtn');
        const toggleDark = document.getElementById('toggleDark');
        const moonIcon = document.getElementById('moonIcon');
        const sunIcon = document.getElementById('sunIcon');

        // —— 主题初始化
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            document.documentElement.classList.add('dark');
            moonIcon?.classList.add('hidden'); sunIcon?.classList.remove('hidden');
        }

        // —— 切换主题
        toggleDark?.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            sunIcon?.classList.toggle('hidden', !isDark);
            moonIcon?.classList.toggle('hidden', isDark);
        });

        // —— 打开侧栏
        function openSidebar() {
            sidebar.classList.remove('-translate-x-64');
            overlay.classList.remove('hidden');
            requestAnimationFrame(() => overlay.classList.remove('opacity-0'));
            menuBtn?.setAttribute('aria-expanded', 'true');
        }
        // —— 关闭侧栏
        function closeSidebar() {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 200);
            sidebar.classList.add('-translate-x-64');
            menuBtn?.setAttribute('aria-expanded', 'false');
        }

        menuBtn?.addEventListener('click', () => {
            const isOpen = !sidebar.classList.contains('-translate-x-64');
            isOpen ? closeSidebar() : openSidebar();
        });

        overlay?.addEventListener('click', closeSidebar);

        // —— ESC 关闭（移动端侧栏打开时）
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-64')) {
                closeSidebar();
            }
        });

        // —— 点击主内容时若侧栏打开则关闭（移动端友好）
        document.querySelector('main')?.addEventListener('click', () => {
            if (window.innerWidth < 768 && !sidebar.classList.contains('-translate-x-64')) {
                closeSidebar();
            }
        });
    })();
</script>
    {{-- Pushed page-specific scripts (e.g., dashboard JSON Attendance handler) --}}
    @stack('scripts')
</body>
</html>
