@extends('layouts.app')
@php $api = rtrim(config('services.teammate.base'), '/'); @endphp

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">My Lessons (API)</h1>
        <p class="text-sm text-gray-500 mb-6">These lessons are fetched from the teammate’s REST API.</p>

        @if($error)
            <div class="mb-4 p-3 rounded bg-red-100 text-red-700">
                Failed to load from API:<br>
                {{ $error }}
                @if($raw)
                    <pre class="mt-2 text-xs text-gray-600">{{ $raw }}</pre>
                @endif
            </div>
        @endif

        <div class="overflow-x-auto border rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Lesson ID</th>
                        <th class="px-4 py-2 text-left">Class</th>
                        <th class="px-4 py-2 text-left">Day</th>
                        <th class="px-4 py-2 text-left">Start</th>
                        <th class="px-4 py-2 text-left">End</th>
                        <th class="px-4 py-2 text-left">Room</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $lesson['lesson_id'] }}</td>
                            <td class="px-4 py-2">{{ $lesson['class_id'] }}</td>
                            <td class="px-4 py-2">{{ $lesson['day_of_week'] ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $lesson['start_time'] ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $lesson['end_time'] ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $lesson['room'] ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <a target="_blank" href="{{ $api . '/api/v1/lessons/' . $lesson['lesson_id'] }}"
                                    class="inline-block bg-emerald-100 text-emerald-700 px-3 py-1 rounded hover:bg-emerald-200 transition">
                                    Open JSON
                                </a>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-gray-500">No lessons returned by API.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('tutor.dashboard', [], false) }}" class="inline-block mt-4 text-sky-600 hover:underline">
            ← Back to Tutor Dashboard
        </a>

    </div>
@endsection