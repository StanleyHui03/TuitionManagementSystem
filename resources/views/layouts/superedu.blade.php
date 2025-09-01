{{-- resources/views/layouts/superedu.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','SuperEdu - Tuition Management System')</title>

  {{-- You can keep CDN or move to Vite later --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .sidebar{transition:all .3s ease}
    .sidebar.collapsed{width:70px}
    .sidebar.collapsed .nav-text, .sidebar.collapsed .logo-text{display:none}
    .main-content{transition:margin-left .3s ease}
    .dashboard-card:hover{transform:translateY(-5px);transition:transform .3s ease}
    .active-nav{background-color:#3b82f6;color:#fff!important}
    .active-nav i{color:#fff!important}
  </style>
  @stack('head')
</head>
<body class="bg-gray-100">
<div class="flex h-screen overflow-hidden">

  {{-- Sidebar --}}
  <div class="sidebar bg-white text-gray-800 shadow-lg w-64 flex flex-col">
    <div class="p-4 flex items-center border-b border-gray-200">
      <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
        <i class="fas fa-graduation-cap text-xl"></i>
      </div>
      <span class="logo-text ml-3 text-xl font-bold">SuperEdu</span>
    </div>

    <div class="flex-1 overflow-y-auto py-4">
      {{-- Search --}}
      <div class="px-4 mb-6">
        <div class="relative">
          <input type="text" placeholder="Search..."
                 class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>

      {{-- Nav - convert to Laravel routes --}}
      @php
        // Expect a $student variable when you're in student pages
        $sid = isset($student) ? $student->student_id : 'S0001';
      @endphp
      <nav>
        <ul>
          <li>
            <a href="{{ route('student.dashboard', $sid) }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('student.dashboard') ? 'active-nav' : '' }}">
              <i class="fas fa-tachometer-alt text-gray-500 mr-3"></i>
              <span class="nav-text">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="{{ route('student.profile', $sid) }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('student.profile') ? 'active-nav' : '' }}">
              <i class="fas fa-user-graduate text-gray-500 mr-3"></i>
              <span class="nav-text">Profile</span>
            </a>
          </li>
          <li>
            <a href="{{ route('student.classes', $sid) }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('student.classes') ? 'active-nav' : '' }}">
              <i class="fas fa-calendar-alt text-gray-500 mr-3"></i>
              <span class="nav-text">Classes</span>
            </a>
          </li>
          <li>
            <a href="{{ route('student.payments', $sid) }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('student.payments') ? 'active-nav' : '' }}">
              <i class="fas fa-money-bill-wave text-gray-500 mr-3"></i>
              <span class="nav-text">Payments</span>
            </a>
          </li>
          <li>
            <a href="{{ route('student.receipts', $sid) }}"
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('student.receipts') ? 'active-nav' : '' }}">
              <i class="fas fa-receipt text-gray-500 mr-3"></i>
              <span class="nav-text">Receipts</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="px-4 mt-6">
        <div class="bg-blue-50 rounded-lg p-4">
          <h3 class="text-sm font-semibold text-blue-800 mb-2">New Feature!</h3>
          <p class="text-xs text-gray-600 mb-2">Automated attendance tracking now available.</p>
          <a class="text-xs text-blue-600 font-medium hover:underline" href="#">Learn more</a>
        </div>
      </div>
    </div>

    <div class="p-4 border-t border-gray-200">
      <div class="flex items-center">
        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
          <i class="fas fa-user text-gray-600"></i>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium">Admin User</p>
          <p class="text-xs text-gray-500">admin@superedu.com</p>
        </div>
        <button class="ml-auto text-gray-500 hover:text-gray-700">
          <i class="fas fa-cog"></i>
        </button>
      </div>
    </div>
  </div>

  {{-- Main --}}
  <div class="main-content flex-1 overflow-y-auto">
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center">
          <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 mr-4">
            <i class="fas fa-bars"></i>
          </button>
          <h1 class="text-xl font-semibold text-gray-800">@yield('page-title','Dashboard')</h1>
        </div>
        <div class="flex items-center space-x-4">
          <button class="text-gray-500 hover:text-gray-700 relative">
            <i class="fas fa-bell"></i>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
          </button>
          <button class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-envelope"></i>
          </button>
        </div>
      </div>
    </header>

    <main class="p-6">
      @yield('content')
    </main>
  </div>
</div>

<script>
  // Toggle sidebar
  document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('collapsed');
  });

  // Responsive init
  function handleResize() {
    const s = document.querySelector('.sidebar');
    if (window.innerWidth < 768) s.classList.add('collapsed');
    else s.classList.remove('collapsed');
  }
  window.addEventListener('resize', handleResize);
  handleResize();
</script>
@stack('scripts')
</body>
</html>
