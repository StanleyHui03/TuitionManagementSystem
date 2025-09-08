<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Models\Subject;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        if (session()->has('pending_delete')) {
            $pending = session('pending_delete');
            $payment = Payments::withTrashed()->find($pending['id']);

            if ($payment && $payment->trashed()) {
                if ($pending['stage'] == 1) {
                    session()->put('pending_delete', ['id' => $pending['id'], 'stage' => 2]);
                } else {
                    $payment->forceDelete();
                    session()->forget('pending_delete');
                }
            } else {
                session()->forget('pending_delete');
            }
        }

        $query = Payments::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('payment_id', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $payments = $query->orderByDesc('paymentDate')
            ->paginate(10)
            ->appends($request->query());

        return view('Payment/paymentlist', compact('payments'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('subject_Name')->pluck('subject_Name')->all();

        return view('Payment/paymentpage', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|exists:students,student_id',
            'paymentDate' => 'required|date',
            'paymentTotal' => 'required|numeric|min:0',
            'status' => 'required|in:Pending,Paid,Cancelled',
            'description' => 'nullable|array',
            'description.*' => 'string',
        ]);

        Payments::create([
            'student_id' => $validated['student_id'],
            'paymentDate' => $validated['paymentDate'],
            'paymentTotal' => $validated['paymentTotal'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? [],
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment created successfully!');
    }

    public function edit(Payments $payment)
    {
        $subjects = Subject::orderBy('subject_Name')->pluck('subject_Name')->all();

        return view('Payment/paymentedit', compact('payment', 'subjects'));
    }

    public function update(Request $request, Payments $payment)
    {
        $validated = $request->validate([
            'paymentTotal' => 'required|numeric',
            'paymentDate' => 'required|date',
            'status' => 'required|in:Pending,Paid,Cancelled',
            'description' => 'nullable|array',
            'description.*' => 'string',
        ]);

        $validated['description'] = $validated['description'] ?? [];

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
    }


    public function destroy(Payments $payment)
    {
        $payment->delete();

        session()->put('pending_delete', ['id' => $payment->payment_id, 'stage' => 1]);

        return redirect()
            ->route('payments.index')
            ->with(
                'success',
                'Payment deleted successfully. <form action="' . route('payments.undo', $payment->payment_id) . '"
                  method="POST" style="display:inline;margin-left:8px">'
                . csrf_field() .
                method_field('PATCH') .
                '<button type="submit" class="btn btn-warning btn-sm">Undo</button>
                 </form>'
            );
    }

    public function undoDelete($id)
    {
        $payment = Payments::withTrashed()->findOrFail($id);
        $payment->restore();

        session()->forget('pending_delete');

        return redirect()->route('payments.index')->with('success', 'Payment restored successfully!');
    }
}
