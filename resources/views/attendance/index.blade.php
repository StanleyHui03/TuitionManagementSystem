@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">My Attendance</h1>

    @if(session('status'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Lesson</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Note</th>
                    <th class="px-4 py-3 text-left">Marked By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $record)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                        <!-- Session Date -->
                        <td class="px-4 py-3">
                            {{ optional($record->session_date)->format('Y-m-d') }}
                        </td>

                        <!-- Lesson -->
                        <td class="px-4 py-3">
                            {{ $record->lesson->lesson_id ?? '—' }}
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            @switch($record->status)
                                @case('present')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Present</span>
                                    @break
                                @case('absent')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Absent</span>
                                    @break
                                @case('late')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Late</span>
                                    @break
                                @case('excused')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Excused</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Pending</span>
                            @endswitch
                        </td>

                        <!-- Note -->
                        <td class="px-4 py-3">
                            {{ $record->note ?? '—' }}
                        </td>

                        <!-- Marked By Tutor -->
                        <td class="px-4 py-3">
                            {{ $record->markedByTutor->name ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center px-4 py-6 text-gray-500">
                            No attendance records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection