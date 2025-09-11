@extends('layouts.app')

@section('content')
<div class="p-6">

    {{-- 顶部标题区 --}}
    <div class="flex items-start sm:items-center justify-between gap-3 mb-8">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-gradient-to-br from-sky-100 to-blue-300 text-white shadow">📚</div>
            <div>
                <h1 class="text-3xl font-bold leading-tight">Tutor Dashboard</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Quick access to your teaching tools</p>
            </div>
        </div>
    </div>

    {{-- 功能卡片 --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- 我的班级 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-sky-500 to-blue-500 dark:from-sky-700 dark:to-blue-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">My Classes</h2>
            <p class="mb-4 opacity-90">View and manage your assigned classes and schedules.</p>
            <a href="#"
               class="inline-block bg-white text-sky-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                View Classes →
            </a>
        </div>

        {{-- 考勤管理 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-lime-500 to-green-500 dark:from-lime-700 dark:to-green-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Attendance</h2>
            <p class="mb-4 opacity-90">Mark and review attendance records of students.</p>
            <a href="{{ route('tutor.lessons') }}"
               class="inline-block bg-white text-lime-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                Manage Attendance →
            </a>
        </div>

        {{-- 教学资料 --}}
        <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-purple-500 to-indigo-500 dark:from-purple-700 dark:to-indigo-700 text-white transition transform hover:scale-[1.02]">
            <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
            <h2 class="text-xl font-semibold mb-2">Materials</h2>
            <p class="mb-4 opacity-90">Upload and manage teaching materials for your students.</p>
            <a href="{{ route('materials.index') }}"
               class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                Manage Materials →
            </a>
        </div>

    </div>
</div>
@endsection
