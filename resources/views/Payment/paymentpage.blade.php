@extends('layout')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center mb-4">Create New Payment</h2>

        <div class="card shadow p-4">
            <form method="POST" action="{{ route('payments.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Payment ID</label>
                    <input type="text" class="form-control" value="Auto-generated" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="paymentDate" class="form-control" value="{{ old('paymentDate', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Select Subjects</label>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
                        @foreach ($subjects as $subject)
                            <div class="col">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input subject-checkbox"
                                        id="subject_{{ $subject->subject_id }}" name="description[]"
                                        value="{{ $subject->subject_Name }}" data-fee="{{ $subject->subject_Fee }}"
                                         {{ $loop->first ? 'required' : '' }}>
                                    <label class="form-check-label" for="subject_{{ $subject->subject_id }}">
                                        {{ $subject->subject_Name }}
                                        <small class="text-muted">(RM {{ number_format($subject->subject_Fee, 2) }})</small>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Payment Total (RM)</label>
                    <input type="number" step="0.01" id="paymentTotal" name="paymentTotal" class="form-control" value="0.00"
                        readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Select Status --</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Add New Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-primary">Go to Payment List</a>
                </div>
            </form>
        </div>
    </div>

    {{-- JS to auto-update total --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxes = document.querySelectorAll(".subject-checkbox");
            const totalInput = document.getElementById("paymentTotal");

            function recalc() {
                let total = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        const fee = parseFloat(cb.dataset.fee || "0");
                        total += isNaN(fee) ? 0 : fee;
                    }
                });
                totalInput.value = total.toFixed(2);
            }

            // Initial calc
            recalc();

            // Recalculate whenever a subject is checked
            checkboxes.forEach(cb => cb.addEventListener("change", recalc));
        });
    </script>
@endsection