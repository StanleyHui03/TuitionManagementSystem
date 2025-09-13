<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Models\Subject;
use App\Models\Enrollments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $subjects = Subject::orderBy('subject_Name')
            ->get(['subject_id', 'subject_Name', 'subject_Fee']);

        return view('Payment.paymentpage', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|string|exists:students,student_id',
            'paymentDate'  => 'required|date',
            'status'       => 'required|in:Pending,Paid,Cancelled',
            'description'  => 'nullable|array', 
            'description.*'=> 'string',
        ]);

        $selectedSubjects = $validated['description'] ?? [];

        $total = Subject::whereIn('subject_Name', $selectedSubjects)->sum('subject_Fee');

        DB::transaction(function () use ($validated, $selectedSubjects, $total) {
            $payment = Payments::create([
                'student_id'   => $validated['student_id'],
                'paymentDate'  => $validated['paymentDate'],
                'paymentTotal' => $total,
                'status'       => $validated['status'],
                'description'  => $selectedSubjects, 
            ]);

            // If created as Paid, grant access
            if ($payment->status === 'Paid' && !empty($selectedSubjects)) {
                $this->grantOneMonthForSubjects($payment->student_id, $selectedSubjects);
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment created successfully!');
    }

    public function edit(Payments $payment)
    {
        if ($payment->status === 'Paid') {
            return redirect()->route('payments.index')
                ->with('error', 'Paid payments cannot be edited.');
        }

        $subjects = Subject::orderBy('subject_Name')->pluck('subject_Name')->all();

        return view('Payment/paymentedit', compact('payment', 'subjects'));
    }

    public function update(Request $request, Payments $payment)
    {
        $validated = $request->validate([
            'paymentTotal'   => 'required|numeric',
            'paymentDate'    => 'required|date',
            'status'         => 'required|in:Pending,Paid,Cancelled',
            'description'    => 'nullable|array',
            'description.*'  => 'string',
        ]);

        $validated['description'] = $validated['description'] ?? [];

        $wasPaid = $payment->status === 'Paid';

        DB::transaction(function () use ($payment, $validated, $wasPaid) {
            $payment->update($validated);

            $justBecamePaid = !$wasPaid && $payment->status === 'Paid';
            if ($justBecamePaid) {

                $subjects = is_array($payment->description)
                    ? $payment->description
                    : (is_string($payment->description)
                        ? (json_decode($payment->description, true) ?: [])
                        : []);

                if (!empty($subjects)) {
                    $this->grantOneMonthForSubjects($payment->student_id, $subjects);
                }
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy(Payments $payment)
    {

        if ($payment->status === 'Paid') {
            return redirect()->route('payments.index')
                ->with('error', 'Paid payments cannot be deleted.');
        }

        // soft delete
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

        return redirect()->route('payments.index')
            ->with('success', 'Payment restored successfully!');
    }

    public function view(Payments $payment)
    {
        $payment->load('student');
        return view('Payment.view', compact('payment'));
    }


    private function grantOneMonthForSubjects(string $studentId, array $subjectNames): void
    {
        $enrollments = Enrollments::with(['classroom.subject'])
            ->where('student_id', $studentId)
            ->whereHas('classroom.subject', function ($q) use ($subjectNames) {
                $q->whereIn('subject_Name', $subjectNames);
            })
            ->get();

        foreach ($enrollments as $enroll) {

            $currentEnd = $enroll->period_end instanceof \Carbon\Carbon
                ? $enroll->period_end
                : ($enroll->period_end ? \Carbon\Carbon::parse($enroll->period_end) : null);

            $start = ($currentEnd && $currentEnd->isFuture())
                ? $currentEnd->copy()
                : now();

            $end = $start->copy();
            if (method_exists($end, 'addMonthsNoOverflow')) {
                $end = $end->addMonthsNoOverflow(1);
            } else {
                $dayBefore = (int) $start->day;
                $end = $end->addMonth();
                if ($end->day !== $dayBefore) {
                    $end->day($end->daysInMonth);
                }
            }
//////////////Ignore error
            $enroll->period_start = $start;
            $enroll->period_end   = $end;

            if ($enroll->isFillable('status')) {
                $enroll->status = 'Active';
            }

            $enroll->save();
        }
    }
}
