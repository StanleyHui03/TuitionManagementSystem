{{-- resources/views/student/profile.blade.php --}}
@extends('layouts.superedu')
@section('title','SuperEdu - Profile')
@section('page-title','Profile')

@section('content')
  <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    @if(session('status'))
      <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('student.profile.update', $student->student_id) }}">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-gray-600">Name</label>
          <input name="studentName" value="{{ old('studentName',$student->studentName) }}"
                 class="w-full mt-1 border rounded px-3 py-2">
          @error('studentName')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="text-sm text-gray-600">Phone</label>
          <input name="phoneNum" value="{{ old('phoneNum',$student->phoneNum) }}"
                 class="w-full mt-1 border rounded px-3 py-2">
        </div>
        <div class="md:col-span-2">
          <label class="text-sm text-gray-600">Address</label>
          <input name="address" value="{{ old('address',$student->address) }}"
                 class="w-full mt-1 border rounded px-3 py-2">
        </div>
        <div>
          <label class="text-sm text-gray-600">Gender</label>
          <select name="gender" class="w-full mt-1 border rounded px-3 py-2">
            <option {{ old('gender',$student->gender)=='Male'?'selected':'' }}>Male</option>
            <option {{ old('gender',$student->gender)=='Female'?'selected':'' }}>Female</option>
            <option {{ old('gender',$student->gender)=='Other'?'selected':'' }}>Other</option>
          </select>
        </div>
      </div>

      <div class="mt-6">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
      </div>
    </form>
  </div>
@endsection
