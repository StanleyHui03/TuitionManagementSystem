@extends('layouts.app')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="flex items-start sm:items-center justify-between gap-3 mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 rounded-xl grid place-items-center bg-gradient-to-br from-sky-100 to-blue-300 text-white shadow">
                    📚</div>
                <div>
                    <h1 class="text-3xl font-bold leading-tight">Tutor Dashboard</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Quick access to your teaching tools</p>
                </div>
            </div>
        </div>

        {{-- Feature cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- My Classes --}}
            <div class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-cyan-500 to-sky-500 text-white">
                <h2 class="text-xl font-semibold mb-2">My Lessons (API)</h2>
                <p class="mb-4 opacity-90">Consume teammate’s REST API and list my lessons.</p>
                <a href="{{ route('tutor.apiLessons') }}"
                   class="inline-block bg-white text-sky-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                    View API Lessons →
                </a>
            </div>

            {{-- Attendance (Manage via Blade) --}}
            <div
                class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-lime-500 to-green-500 dark:from-lime-700 dark:to-green-700 text-white transition transform hover:scale-[1.02]">
                <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
                <h2 class="text-xl font-semibold mb-2">Attendance</h2>
                <p class="mb-4 opacity-90">Mark and review attendance records of students.</p>
                <a href="{{ route('tutor.lessons') }}"
                    class="inline-block bg-white text-lime-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                    Manage Attendance →
                </a>
            </div>

            {{-- Attendance (JSON Viewer) --}}
            <div
                class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-500 dark:from-emerald-700 dark:to-teal-700 text-white transition transform hover:scale-[1.02]">
                <div class="absolute -top-8 -right-8 h-32 w-32 rounded-full bg-white/20 blur-2xl"></div>
                <h2 class="text-xl font-semibold mb-2">Attendance (JSON)</h2>
                <p class="mb-4 opacity-90">View lesson attendance using the JSON API.</p>

                {{-- Lesson picker + date --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3 mb-4">
                    <div class="flex-1">
                        <label for="jsonLessonSelect" class="block text-sm font-medium mb-1 text-white/90">Select
                            Lesson</label>
                        <select id="jsonLessonSelect" class="text-black rounded-lg px-3 py-2 w-full">
                            <option value="">— Select a lesson —</option>
                            @forelse(($lessons ?? []) as $l)
                                <option value="{{ $l->lesson_id }}">
                                    {{ $l->lesson_id }}
                                </option>
                            @empty
                                <option value="" disabled>(No lessons found)</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label for="jsonSessionDate" class="block text-sm font-medium mb-1 text-white/90">Session
                            Date</label>
                        <input id="jsonSessionDate" type="date" class="text-black rounded-lg px-3 py-2"
                            value="{{ now()->toDateString() }}">
                    </div>
                </div>

                <button id="goJsonAttendance"
                    class="inline-block bg-white text-emerald-600 px-4 py-2 rounded-lg shadow hover:bg-gray-100 transition">
                    JSON Attendance →
                </button>

                <button id="openApiBtn" type="button" class="inline-block bg-white/90 text-emerald-700 px-4 py-2 rounded-lg shadow transition
                         opacity-60 cursor-not-allowed">
                    Open API (JSON)
                </button>

                <script>
                    (function () {
                        const sel = document.getElementById('jsonLessonSelect');
                        const dateI = document.getElementById('jsonSessionDate');
                        const viewB = @json(route('attendance.json.viewer', ['lesson' => 'LESSON_ID_PLACEHOLDER']));
                        const apiB = @json(url('/api/v1'));
                        const goBtn = document.getElementById('goJsonAttendance');
                        const apiBtn = document.getElementById('openApiBtn');

                        function buildApiUrl() {
                            const id = sel && sel.value ? sel.value : '';
                            const d = dateI && dateI.value ? dateI.value : '';
                            if (!id) return '';
                            return `${apiB}/lessons/${encodeURIComponent(id)}/attendance` + (d ? `?session_date=${encodeURIComponent(d)}` : '');
                        }

                        function sync() {
                            const url = buildApiUrl();
                            const disabled = !url;
                            apiBtn.disabled = disabled;
                            apiBtn.classList.toggle('opacity-60', disabled);
                            apiBtn.classList.toggle('cursor-not-allowed', disabled);
                            apiBtn.dataset.url = url; // keep latest
                            // console.debug('API URL =', url); // uncomment to debug
                        }

                        goBtn?.addEventListener('click', function () {
                            const id = sel?.value;
                            if (!id) { alert('Please select a lesson.'); return; }
                            const date = dateI?.value || '';
                            const url = viewB.replace('LESSON_ID_PLACEHOLDER', encodeURIComponent(id));
                            window.location.href = date ? `${url}?session_date=${encodeURIComponent(date)}` : url;
                        });

                        apiBtn?.addEventListener('click', function () {
                            const url = apiBtn.dataset.url || '';
                            if (!url) return;
                            window.open(url, '_blank', 'noopener');
                        });

                        sel?.addEventListener('change', sync);
                        dateI?.addEventListener('change', sync);
                        sync(); // init
                    })();
                </script>
            </div>



            {{-- Materials --}}
            <div
                class="group relative p-6 rounded-2xl shadow-lg overflow-hidden bg-gradient-to-r from-purple-500 to-indigo-500 dark:from-purple-700 dark:to-indigo-700 text-white transition transform hover:scale-[1.02] md:col-span-3">
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

@push('scripts')
    <script>
        (function () {
            const btn = document.getElementById('goJsonAttendance');
            const sel = document.getElementById('jsonLessonSelect');
            const dateI = document.getElementById('jsonSessionDate');

            const base = @json(route('attendance.json.viewer', ['lesson' => 'LESSON_ID_PLACEHOLDER']));

            btn?.addEventListener('click', function () {
                const id = sel?.value;
                if (!id) { alert('Please select a lesson.'); return; }
                const date = dateI?.value || '';
                const url = base.replace('LESSON_ID_PLACEHOLDER', encodeURIComponent(id));
                window.location.href = date ? `${url}?session_date=${encodeURIComponent(date)}` : url;
            });
        })();
    </script>


@endpush