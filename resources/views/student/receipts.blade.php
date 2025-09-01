@extends('layouts.app')

@section('content')
@php($studentId = $student->student_id)

<h1 class="text-xl font-semibold mb-4">Receipts</h1>

<div class="rounded-lg border bg-white p-4">
    @if($receipts->isEmpty())
        <p class="text-sm text-slate-600">No receipts found.</p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500">
                    <th class="py-2">Receipt</th>
                    <th>Payment</th>
                    <th>Subtotal</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($receipts as $r)
                    <tr>
                        <td class="py-2">{{ $r->receipt_id }}</td>
                        <td>{{ $r->payment_id }}</td>
                        <td>RM {{ number_format($r->subTotal,2) }}</td>
                        <td>{{ $r->receiptDate }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
