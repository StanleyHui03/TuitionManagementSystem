@extends('layout')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Create New Payment</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow p-4">
            <form method="POST" action="{{ route('payments.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Payment ID</label>
                    <input type="text" class="form-control" value="Auto-generated" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="paymentDate" class="form-control" value="{{ old('paymentDate') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Total (RM)</label>
                    <input type="number" step="0.01" name="paymentTotal" class="form-control"
                        value="{{ old('paymentTotal') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Select Status --</option>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Description (Subjects)</label>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
                        @foreach ($subjects as $subjectName)
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="desc_{{ Str::slug($subjectName, '_') }}"
                                        name="description[]" value="{{ $subjectName }}" {{ is_array(old('description')) && in_array($subjectName, old('description')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="desc_{{ Str::slug($subjectName, '_') }}">
                                        {{ $subjectName }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>


                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Add New Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-primary">Go to Payment List</a>
                </div>
            </form>
        </div>
    </div>
@endsection