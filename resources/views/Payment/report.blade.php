@extends('layouts.layout')

@section('title', 'Payment Report')
@section('page-title', 'Payment Report')

@section('content')
  <div class="card">
    <div class="card-body">
      <table class="table table-striped" id="payments-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Student</th>
            <th>Class</th>
            <th>Tutor</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  @push('scripts')
    <script>
      async function loadPayments() {
        const res  = await fetch("{{ route('payments.report.json') }}");
        const json = await res.json();
        const rows = Array.isArray(json) ? json : (json.data || []); // works if you later switch to array-only
        const tbody = document.querySelector('#payments-table tbody');
        tbody.innerHTML = '';
        rows.forEach(p => {
          tbody.innerHTML += `
          <tr>
            <td>${p.payment_id}</td>
            <td>${p.paymentTotal}</td>
            <td>${p.status}</td>
            <td>${p.student?.studentName ?? '-'}</td>
            <td>${p.classroom?.class_id ?? '-'}</td>
            <td>${p.tutor?.tutor_name ?? '-'}</td>
          </tr>`;
        });
      }

      loadPayments();
    </script>
  @endpush
@endsection