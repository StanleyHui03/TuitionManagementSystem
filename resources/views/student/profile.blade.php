@extends('layouts.app')

@section('content')
@php($studentId = $student->student_id)

<h1 class="text-xl font-semibold mb-4">My Profile</h1>

<form method="POST" action="{{ route('student.profile.update', $studentId) }}" class="rounded-lg border bg-white p-4 max-w-lg">
    @csrf
    <div class="grid gap-4">
        <label class="text-sm">
            <span class="block mb-1">Name</span>
            <input name="studentName" value="{{ old('studentName',$student->studentName) }}" class="w-full rounded border px-3 py-2">
        </label>

        <label class="text-sm">
            <span class="block mb-1">Phone</span>
            <input name="phoneNum" value="{{ old('phoneNum',$student->phoneNum) }}" class="w-full rounded border px-3 py-2">
        </label>

        <label class="text-sm">
            <span class="block mb-1">Address</span>
            <input name="address" value="{{ old('address',$student->address) }}" class="w-full rounded border px-3 py-2">
        </label>

        <label class="text-sm">
            <span class="block mb-1">Gender</span>
            <input name="gender" value="{{ old('gender',$student->gender) }}" class="w-full rounded border px-3 py-2">
        </label>
    </div>

    <div class="mt-4">
        <button class="rounded bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">Save changes</button>
    </div>
</form>
@endsection
