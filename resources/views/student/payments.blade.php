@extends('layouts.superedu')
@section('title','SuperEdu - Profile')
@section('page-title','Profile')

@section('content')
@php($studentId = $student->student_id)

<h1 class="text-xl font-semibold mb-4">Payments</h1>

<div class="grid md:grid-cols-2 gap-6">
    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">Make a payment</h2>
        <form method="POST" action="{{ route('student.pay',$studentId) }}" class="grid gap-3">
            @csrf
            <label class="text-sm">
                <span class="block mb-1">Amount (RM)</span>
                <input name="amount" step="0.01" type="number" class="w-full rounded border px-3 py-2" required>
            </label>
            <label class="text-sm">
                <span class="block mb-1">Date (optional)</span>
                <input name="date" type="date" class="w-full rounded border px-3 py-2">
            </label>
            <button class="rounded bg-indigo-600 text-white px-4 py-2 text-sm hover:bg-indigo-700">Submit Payment</button>
        </form>
        <p class="text-xs text-slate-500 mt-2">Status stays <em>pending</em> until verified by staff.</p>
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h2 class="font-medium mb-3">History</h2>
        @if($payments->isEmpty())
            <p class="text-sm text-slate-600">No payments yet.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="py-2">ID</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($payments as $p)
                        <tr>
                            <td class="py-2">{{ $p->payment_id }}</td>
                            <td>RM {{ number_format($p->paymentTotal,2) }}</td>
                            <td>{{ $p->paymentDate }}</td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-xs
                                    {{ $p->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</div>
@endsection
