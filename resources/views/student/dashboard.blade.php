@extends('layouts.app')

@section('content')
@php($studentId = $student->student_id)

<h1 class="text-xl font-semibold mb-4">Hi, {{ $student->studentName }}</h1>

<div class="grid md:grid-cols-2 gap-6">
    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">Upcoming lessons</h2>
        @if($upcoming->isEmpty())
            <p class="text-sm text-slate-600">No lessons scheduled.</p>
        @else
            <ul class="divide-y">
                @foreach($upcoming as $l)
                    <li class="py-3 text-sm">
                        <div class="font-medium">Lesson {{ $l->lesson_id }}</div>
                        <div class="text-slate-600">
                            {{ \Carbon\Carbon::parse($l->start_at)->format('d M Y, g:i A') }}
                            — {{ \Carbon\Carbon::parse($l->ends_at)->format('g:i A') }}
                            @if($l->room) • room {{ $l->room }} @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">Shortcuts</h2>
        <div class="flex flex-wrap gap-2">
            <a class="px-3 py-2 text-sm rounded border hover:bg-slate-50" href="{{ route('student.classes',$studentId) }}">Manage Classes</a>
            <a class="px-3 py-2 text-sm rounded border hover:bg-slate-50" href="{{ route('student.payments',$studentId) }}">View Payments</a>
            <a class="px-3 py-2 text-sm rounded border hover:bg-slate-50" href="{{ route('student.profile',$studentId) }}">Edit Profile</a>
        </div>
    </section>
</div>
@endsection
