@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Take Attendance</h1>
        <p class="text-gray-500 mt-1">Mark attendance for students enrolled in this lesson’s class.</p>
    </div>

    @php
        $dow = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $lesson->start_time)->format('H:i');
        $end   = \Carbon\Carbon::createFromFormat('H:i:s', $lesson->end_time)->format('H:i');
    @endphp

    <div class="mb-6 grid gap-3 sm:grid-cols-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-200/70 dark:ring-gray-700 p-4">
            <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">Lesson</div>
            <div class="font-semibold">{{ $lesson->lesson_id }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-200/70 dark:ring-gray-700 p-4">
            <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">Class</div>
            <div class="font-semibold">{{ $lesson->class_id }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-200/70 dark:ring-gray-700 p-4">
            <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">Schedule</div>
            <div class="font-semibold">
                {{ $dow[$lesson->day_of_week] }} • {{ $start }} — {{ $end }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Session date: <span class="font-medium">{{ $session_date }}</span>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 border border-green-200 p-3">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 text-red-800 border border-red-200 p-3">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.store', $lesson->lesson_id) }}" id="attendance-form">
        @csrf

        {{-- REQUIRED by controller validation --}}
        {{-- Option A: keep hidden (fixed to the date passed by controller) --}}
        <input type="hidden" name="session_date" value="{{ $session_date }}">

        {{-- Option B: let tutor pick another date (uncomment to use)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session date</label>
            <input type="date" name="session_date" value="{{ $session_date }}"
                   class="mt-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-lime-500 focus:border-lime-500">
        </div>
        --}}

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200/70 dark:ring-gray-700">
            <div class="p-4 sm:p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="flex gap-3 items-center">
                    <div class="relative">
                        <input id="student-search" type="text" placeholder="Search student…"
                               class="w-64 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-lime-500 focus:border-lime-500 pl-10">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Mark all as</span>
                        <select id="mark-all" class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-lime-500 focus:border-lime-500">
                            <option value="">— Choose —</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="excused">Excused</option>
                        </select>
                        <button type="button" id="apply-mark-all"
                                class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800">
                            Apply
                        </button>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    Students: <span id="count-total">{{ $roster->count() }}</span>
                </div>
            </div>

            @if ($roster->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm" id="attendance-table">
                        <thead>
                            <tr class="text-left bg-gray-50 dark:bg-gray-900/40 border-y border-gray-100 dark:border-gray-700">
                                <th class="px-5 py-3 font-semibold">Student</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($roster as $student)
                                @php $prev = $existing[$student->student_id] ?? null; @endphp
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-900/50"
                                    data-student-row
                                    data-name="{{ \Illuminate\Support\Str::lower($student->studentName) }} {{ \Illuminate\Support\Str::lower($student->student_id) }}">
                                    <td class="px-5 py-3 font-medium">
                                        {{ $student->student_id }} — {{ $student->studentName }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <select name="attendance[{{ $student->student_id }}]"
                                                class="status-select w-44 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-lime-500 focus:border-lime-500"
                                                required>
                                            @foreach (['present','absent','late','excused'] as $opt)
                                                <option value="{{ $opt }}" @selected(optional($prev)->status === $opt)>{{ ucfirst($opt) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-5 py-3">
                                        <input type="text"
                                               name="note[{{ $student->student_id }}]"
                                               value="{{ old("note.{$student->student_id}", optional($prev)->note) }}"
                                               maxlength="255"
                                               placeholder="Optional note"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-lime-500 focus:border-lime-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center">
                    <div class="mx-auto w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5V4H2v16h5M7 20V10m5 10V8m5 12V6"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-semibold">No students enrolled</h3>
                    <p class="mt-1 text-gray-500">This lesson’s class has no enrollments yet.</p>
                </div>
            @endif

            <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Tip: Use “Mark all as” to set a default, then tweak individual rows.
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-lime-600 text-white hover:bg-lime-700">
                    Save Attendance
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    const applyMarkAllBtn = document.getElementById('apply-mark-all');
    const markAllSelect   = document.getElementById('mark-all');
    const searchInput     = document.getElementById('student-search');

    applyMarkAllBtn?.addEventListener('click', () => {
        const value = markAllSelect.value;
        if (!value) return;
        document.querySelectorAll('.status-select').forEach(sel => { sel.value = value; });
    });

    searchInput?.addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll('[data-student-row]').forEach(row => {
            const hay = row.getAttribute('data-name');
            row.style.display = hay.includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
