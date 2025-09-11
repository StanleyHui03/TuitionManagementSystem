@extends('layouts.app')

@section('content')
<div class="p-6">

    {{-- 顶部标题区 --}}
    <div class="flex items-start sm:items-center justify-between gap-3 mb-8">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-gradient-to-br from-cyan-100 to-teal-300 text-white shadow">🎓</div>
            <div>
                <h1 class="text-3xl font-bold leading-tight">Student Dashboard</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Overview of your courses, payments, and updates</p>
            </div>
        </div>
    </div>

    {{-- 功能卡片 --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- 已选课程 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-cyan-500 to-teal-500 dark:from-cyan-700 dark:to-teal-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Enrolled Courses</h2>
            <p class="mb-4 opacity-90">Check your enrolled courses and track progress.</p>
            <a href="#"
               class="inline-block bg-white text-cyan-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                View Courses →
            </a>
        </div>

        {{-- 支付历史 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-fuchsia-500 to-purple-500 dark:from-fuchsia-700 dark:to-purple-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Payment History</h2>
            <p class="mb-4 opacity-90">Review your payment records and receipts.</p>
            <a href="#"
               class="inline-block bg-white text-fuchsia-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                View Payments →
            </a>
        </div>

        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-fuchsia-500 to-purple-500 dark:from-fuchsia-700 dark:to-purple-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Attendance</h2>
            <p class="mb-4 opacity-90">Review your Attendance records.</p>
            <a href="{{ route('student.attendance') }}"
               class="inline-block bg-white text-fuchsia-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                View Attendance →
            </a>
        </div>

        {{-- 公告通知 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-yellow-500 to-amber-500 dark:from-yellow-700 dark:to-amber-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Announcements</h2>
            <p class="mb-4 opacity-90">Stay updated with the latest announcements.</p>
            <a href="#"
               class="inline-block bg-white text-yellow-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                View Announcements →
            </a>
        </div>

    </div>
</div>
@endsection
