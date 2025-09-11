@extends('layout')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Download Receipt</h2>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary">Print</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="text-muted">Payment</h5>
                    <p class="mb-1"><strong>ID:</strong> {{ $payment->payment_id }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ optional($payment->paymentDate)->format('Y-m-d') }}</p>
                    <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge
                            @if($payment->status === 'Paid') bg-success
                            @elseif($payment->status === 'Pending') bg-warning text-dark
                            @else bg-danger
                            @endif">
                            {{ $payment->status }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted">Student</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $payment->student?->studentName ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Student ID:</strong> {{ $payment->student?->student_id ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $payment->student?->phoneNum ?? '-' }}</p>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Enrolled Subjects</h5>
            @php
                $desc = $payment->description;
                if (is_string($desc)) { $dec = json_decode($desc, true); $desc = is_array($dec) ? $dec : []; }
            @endphp
            @if (!empty($desc))
                <ul class="list-group mb-3">
                    @foreach ($desc as $subject)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $subject }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No subjects selected.</p>
            @endif

            <div class="d-flex justify-content-end">
                <div class="text-end">
                    <div class="fs-5"><strong>Total:</strong> RM {{ number_format($payment->paymentTotal, 2) }}</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
