@extends('layouts.app')

@section('content')
@php($studentId = $student->student_id)

<h1 class="text-xl font-semibold mb-6">My Classes</h1>

<div class="grid md:grid-cols-2 gap-6">
    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">Enrolled</h2>
        @if($myClasses->isEmpty())
            <p class="text-sm text-slate-600">Not enrolled in any class.</p>
        @else
            <ul class="divide-y">
                @foreach($myClasses as $c)
                <li class="py-3 flex items-center justify-between text-sm">
                    <div>
                        <div class="font-medium">Class {{ $c->class_id }}</div>
                        <div class="text-slate-600">Subject: {{ optional($c->subject)->subject_Name ?? $c->subject_id }}</div>
                    </div>
                    <form method="POST" action="{{ route('student.unenroll',$studentId) }}">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $c->class_id }}">
                        <button class="px-3 py-1.5 rounded border text-rose-700 border-rose-200 hover:bg-rose-50">Unenroll</button>
                    </form>
                </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">Available classes</h2>
        @if($available->isEmpty())
            <p class="text-sm text-slate-600">No other classes available.</p>
        @else
            <ul class="divide-y">
                @foreach($available as $c)
                <li class="py-3 flex items-center justify-between text-sm">
                    <div>
                        <div class="font-medium">Class {{ $c->class_id }}</div>
                        <div class="text-slate-600">Subject: {{ optional($c->subject)->subject_Name ?? $c->subject_id }}</div>
                    </div>
                    <form method="POST" action="{{ route('student.enroll',$studentId) }}">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $c->class_id }}">
                        <button class="px-3 py-1.5 rounded border text-emerald-700 border-emerald-200 hover:bg-emerald-50">Enroll</button>
                    </form>
                </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
