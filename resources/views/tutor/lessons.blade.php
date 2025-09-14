@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">My Lessons</h1>
        <p class="text-gray-500 mt-1">Find your upcoming and past lessons, then take attendance.</p>
    </div>

    {{-- Search (optional) --}}
    <form method="GET" class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700">
        <div class="relative w-full md:w-1/2">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                   placeholder="Search lesson, class, or room…"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100
                          focus:ring-lime-500 focus:border-lime-500 pl-10 pr-4 py-2.5">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
            </svg>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200/70 dark:ring-gray-700">
        @if ($lessons->count())
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left bg-gray-50 dark:bg-gray-900/40 border-y border-gray-100 dark:border-gray-700">
                            <th class="px-5 py-3 font-semibold">Lesson ID</th>
                            <th class="px-5 py-3 font-semibold">Class ID</th>
                            <th class="px-5 py-3 font-semibold">Day</th>
                            <th class="px-5 py-3 font-semibold">Start</th>
                            <th class="px-5 py-3 font-semibold">End</th>
                            <th class="px-5 py-3 font-semibold">Next Date</th>
                            <th class="px-5 py-3 font-semibold">Room</th>
                            <th class="px-5 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $dow = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                        @endphp
                        @foreach ($lessons as $lesson)
                            @php
                                $next = method_exists($lesson, 'nextOccurrence') ? $lesson->nextOccurrence() : null;
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-900/50">
                                <td class="px-5 py-3 font-medium">{{ $lesson->lesson_id }}</td>
                                <td class="px-5 py-3">{{ $lesson->class_id }}</td>
                                <td class="px-5 py-3">{{ $dow[$lesson->day_of_week] ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $lesson->start_time)->format('H:i') }}
                                </td>
                                <td class="px-5 py-3">
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $lesson->end_time)->format('H:i') }}
                                </td>
                                <td class="px-5 py-3">
                                    {{ $next ? $next->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="px-5 py-3">{{ $lesson->room ?: '—' }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('attendance.create', $lesson->lesson_id) }}"
                                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-lime-600 text-white hover:bg-lime-700">
                                        Take Attendance
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $lessons->links() }}
            </div>
        @else
            <div class="p-10 text-center">
                <h3 class="mt-3 text-lg font-semibold">No lessons found</h3>
                <p class="mt-1 text-gray-500">Adjust your filters or wait until new lessons are scheduled.</p>
            </div>
        @endif
    </div>
</div>
@endsection