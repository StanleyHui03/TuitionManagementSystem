<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SuperEdu</title>

    {{-- If you use Vite+Tailwind already, keep @vite; otherwise use CDN for quick start --}}
    {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-full bg-slate-50 text-slate-800">
    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-semibold tracking-wide text-indigo-600">SuperEdu</a>

            {{-- Top nav for quick student shortcuts (you can hide if you’ll add auth later) --}}
            @isset($studentId)
            <nav class="flex gap-2 text-sm">
                <a class="px-3 py-1.5 rounded hover:bg-slate-100 {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700' : '' }}"
                   href="{{ route('student.dashboard',$studentId) }}">Dashboard</a>
                <a class="px-3 py-1.5 rounded hover:bg-slate-100 {{ request()->routeIs('student.profile') ? 'bg-indigo-50 text-indigo-700' : '' }}"
                   href="{{ route('student.profile',$studentId) }}">Profile</a>
                <a class="px-3 py-1.5 rounded hover:bg-slate-100 {{ request()->routeIs('student.classes') ? 'bg-indigo-50 text-indigo-700' : '' }}"
                   href="{{ route('student.classes',$studentId) }}">Classes</a>
                <a class="px-3 py-1.5 rounded hover:bg-slate-100 {{ request()->routeIs('student.payments') ? 'bg-indigo-50 text-indigo-700' : '' }}"
                   href="{{ route('student.payments',$studentId) }}">Payments</a>
                <a class="px-3 py-1.5 rounded hover:bg-slate-100 {{ request()->routeIs('student.receipts') ? 'bg-indigo-50 text-indigo-700' : '' }}"
                   href="{{ route('student.receipts',$studentId) }}">Receipts</a>
            </nav>
            @endisset
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        {{-- Flash messages --}}
        @if (session('status'))
            <div class="mb-4 rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded border border-rose-300 bg-rose-50 px-4 py-3 text-rose-800">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-amber-800">
                <ul class="list-disc ps-5">
                    @foreach ($errors->all() as $msg) <li>{{ $msg }}</li> @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-8 text-center text-xs text-slate-500">
        © {{ date('Y') }} SuperEdu. All rights reserved.
    </footer>
</body>
</html>
