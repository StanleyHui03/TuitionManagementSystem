@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit Payment</h2>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">← Back to List</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('payments.update', $payment->payment_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Payment Total (RM)</label>
                    <input
                        type="number"
                        step="0.01"
                        name="paymentTotal"
                        class="form-control"
                        value="{{ old('paymentTotal', $payment->paymentTotal) }}"
                        required
                    >
                </div>

                @php
                    $dateValue = old('paymentDate',
                        optional(\Illuminate\Support\Arr::get($payment->getAttributes(), 'paymentDate')
                            ? \Carbon\Carbon::parse($payment->paymentDate)
                            : null
                        )?->format('Y-m-d')
                    );
                @endphp
                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input
                        type="date"
                        name="paymentDate"
                        class="form-control"
                        value="{{ $dateValue }}"
                        required
                    >
                </div>

                @php $currentStatus = old('status', $payment->status); @endphp
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <div class="btn-group" role="group" aria-label="Status">
                        <input type="radio" class="btn-check" name="status" id="stPending" value="Pending" {{ $currentStatus === 'Pending' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning" for="stPending">Pending</label>

                        <input type="radio" class="btn-check" name="status" id="stPaid" value="Paid" {{ $currentStatus === 'Paid' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="stPaid">Paid</label>

                        <input type="radio" class="btn-check" name="status" id="stCancelled" value="Cancelled" {{ $currentStatus === 'Cancelled' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger" for="stCancelled">Cancelled</label>
                    </div>
                </div>

                @php
                    $selectedSubjects = collect(old('description', $payment->description ?? []));
                @endphp
                <div class="mb-4">
                    <label class="form-label d-block">Subjects</label>
                    @if(!empty($subjects))
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
                            @foreach ($subjects as $subjectName)
                                <div class="col">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="subject_{{ $loop->index }}"
                                            name="description[]"
                                            value="{{ $subjectName }}"
                                            {{ $selectedSubjects->contains($subjectName) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="subject_{{ $loop->index }}">
                                            {{ $subjectName }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Pick one or more subjects linked to this payment.</div>
                    @else
                        <div class="alert alert-warning mb-0">
                            No subjects found. Please add subjects first.
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
