@extends('layout')

@section('content')
    <div class="container mt-5">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0">Payment List</h2>
            <a href="{{ route('payments.create') }}" class="btn btn-success">
                + Add New Payment
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="GET" action="{{ route('payments.index') }}" class="row g-2 mb-4 justify-content-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by Payment ID or Student ID"
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Filter by Status --</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-2 d-grid">
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Payment ID</th>
                        <th>Student Name</th>
                        <th>Payment Date</th>
                        <th>Payment Total</th>
                        <th>Status</th>
                        <th>Subjects Enrolled</th>
                        <th style="min-width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_id }}</td>
                            <td>
                                <span title="{{ $payment->student?->studentName ?? 'N/A' }}">
                                    {{ $payment->student?->student_id ?? 'N/A' }}
                                </span>
                            </td>

                            <td>{{ $payment->paymentDate->format('Y-m-d') }}</td>
                            <td>RM {{ number_format($payment->paymentTotal, 2) }}</td>
                            <td>
                                <span class="badge
                                                                @if($payment->status === 'Paid') bg-success
                                                                @elseif($payment->status === 'Pending') bg-warning text-dark
                                                                @else bg-danger
                                                                @endif">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td class="text-start">
                                @php
                                    $desc = $payment->description;
                                    if (is_string($desc)) {
                                        $dec = json_decode($desc, true);
                                        $desc = is_array($dec) ? $dec : [];
                                    }
                                @endphp

                                @if (!empty($desc))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($desc as $subject)
                                            <span class="badge bg-info text-dark">{{ $subject }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if ($payment->trashed())
                                    <form action="{{ route('payments.undo', $payment->payment_id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            Undo Delete
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('payments.edit', $payment->payment_id) }}"
                                        class="btn btn-primary btn-sm me-1">
                                        Edit
                                    </a>
                                    <a href="{{ route('payments.view', $payment->payment_id) }}" class="btn btn-info btn-sm me-1">
                                        Receipt
                                    </a>
                                    <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this payment?');">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    </div>

@endsection