{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.superedu')

@section('title','SuperEdu - Dashboard')
@section('page-title','Dashboard')

@section('content')
  {{-- Replace the cards with dynamic values if you want --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Example card component inline (you can extract later) --}}
    <div class="dashboard-card bg-white rounded-lg shadow p-6 flex items-center hover:shadow-md">
      <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
        <i class="fas fa-user-graduate text-xl"></i>
      </div>
      <div>
        <p class="text-gray-500 text-sm">My Upcoming Lessons</p>
        <h3 class="text-2xl font-bold">{{ $upcoming->count() }}</h3>
        <p class="text-xs text-gray-400">Next: {{ optional($upcoming->first())->start_at?->format('D, d M H:i') ?? '—' }}</p>
      </div>
    </div>

    {{-- …add your other 3 cards, or keep them static for now --}}
  </div>

  {{-- Recent Activity / Upcoming classes – you can paste your HTML here unchanged,
       or loop with Blade if you have the data ready. --}}
@endsection
