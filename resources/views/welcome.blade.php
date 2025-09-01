@extends('layouts.app')

@section('content')
<div class="grid md:grid-cols-2 gap-6 items-start">
    <div>
        <h1 class="text-2xl font-semibold mb-2">Welcome to SuperEdu</h1>
        <p class="text-slate-600 mb-6">
            A simple tuition management system for students, tutors, payments, and materials.
        </p>

        <div class="rounded-lg border bg-white p-4">
            <h2 class="font-medium mb-2">Quick demo links</h2>
            <p class="text-sm text-slate-600 mb-3">Replace <code class="bg-slate-100 px-1 rounded">S0001</code> with your student_id.</p>
            <ul class="space-y-2 text-sm">
                <li><a class="text-indigo-600 hover:underline" href="/student/S0001/dashboard">Student Dashboard</a></li>
                <li><a class="text-indigo-600 hover:underline" href="/student/S0001/profile">Profile</a></li>
                <li><a class="text-indigo-600 hover:underline" href="/student/S0001/classes">Classes</a></li>
                <li><a class="text-indigo-600 hover:underline" href="/student/S0001/payments">Payments</a></li>
                <li><a class="text-indigo-600 hover:underline" href="/student/S0001/receipts">Receipts</a></li>
            </ul>
        </div>
    </div>

    <div class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-2">System modules</h2>
        <ul class="list-disc ps-5 text-sm text-slate-700 space-y-1">
            <li>Students</li>
            <li>Classes & Lessons</li>
            <li>Enrollments</li>
            <li>Payments & Receipts</li>
            <li>Materials</li>
            <li>Admin/Staff (future)</li>
        </ul>
    </div>
</div>
@endsection
