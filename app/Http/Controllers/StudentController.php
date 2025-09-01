<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Enrollments;
use App\Models\Lessons;    
use App\Models\Payment;   
use App\Models\Receipt;

class StudentController extends Controller
{
    /**
     * Helper: Get student by URL parameter (student_id like S0001).
     * Keeps controller code tidy and avoids repeating the same query everywhere.
     */
    private function getStudent(string $studentId): Student
    {
        return Student::where('student_id', $studentId)->firstOrFail();
    }

    /**
     * Dashboard: Show a basic list of upcoming lessons for the student.
     */
    public function dashboard(string $student)
    {
        $stu = $this->getStudent($student);

        $classIds = Enrollments::where('student_id', $stu->student_id)->pluck('class_id');

        $upcoming = Lessons::whereIn('class_id', $classIds)
            ->where('start_at', '>=', now()->subDay())
            ->orderBy('start_at', 'asc')
            ->limit(30)
            ->get();

        return view('student.dashboard', [
            'student'  => $stu,
            'upcoming' => $upcoming,
        ]);
    }

    /**
     * Profile: Show and update the student's own basic info.
     * Adds light validation (secure coding) but keeps it simple.
     */
    public function profile(string $student)
    {
        $stu = $this->getStudent($student);
        return view('student.profile', ['student' => $stu]);
    }

    public function updateProfile(Request $request, string $student)
    {
        $stu = $this->getStudent($student);

        // validation
        $request->validate([
            'studentName' => 'required|string|max:150',
            'phoneNum'    => 'nullable|string|max:30',
            'address'     => 'nullable|string|max:255',
            'gender'      => 'nullable|string|max:20',
        ]);

        $stu->studentName = $request->studentName;
        $stu->phoneNum    = $request->phoneNum;
        $stu->address     = $request->address;
        $stu->gender      = $request->gender;
        $stu->save();

        return back()->with('status', '✅ Profile updated successfully.');
    }

    /**
     * Classes page: show my classes and the other classes available.
     */
    public function classes(string $student)
    {
        $stu = $this->getStudent($student);

        $myEnrollments = Enrollments::where('student_id', $stu->student_id)->get();
        $myClassIds    = $myEnrollments->pluck('class_id')->toArray();

        $myClasses = Classes::whereIn('class_id', $myClassIds)->get();
        $available = Classes::whereNotIn('class_id', $myClassIds)->get();

        return view('student.classes', [
            'student'   => $stu,
            'myClasses' => $myClasses,
            'available' => $available,
        ]);
    }

    /**
     * Enroll: adds a record in enrollments for the (student_id, class_id).
     */
    public function enroll(Request $request, string $student)
    {
        $stu = $this->getStudent($student);

        $classId = $request->class_id;
        if (!$classId) {
            return back()->with('error', 'No class selected.');
        }

        // 1) Avoid duplicates (also protected by unique index in your schema)
        $exists = Enrollments::where('class_id', $classId)
                    ->where('student_id', $stu->student_id)
                    ->first();
        if ($exists) {
            return back()->with('status', '✅ Enrolled in class '.$classId.'.');
        }

        // 2) Capacity check (since we cannot edit migrations, use a default)
        $DEFAULT_CAPACITY = 30;
        $enrolledCount = Enrollments::where('class_id', $classId)->count();
        if ($enrolledCount >= $DEFAULT_CAPACITY) {
            return back()->with('error', 'Class is full.');
        }

        // 3) Schedule clash check (basic overlap test)
        $myClassIds = Enrollments::where('student_id', $stu->student_id)->pluck('class_id');

        $myLessons  = Lessons::whereIn('class_id', $myClassIds)->get(['start_at','ends_at']);
        $newLessons = Lessons::where('class_id', $classId)->get(['start_at','ends_at']);

        foreach ($newLessons as $n) {
            foreach ($myLessons as $m) {
                $overlap = ($n->start_at < $m->ends_at) && ($m->start_at < $n->ends_at);
                if ($overlap) {
                    return back()->with('error', '⚠️ Schedule conflict with another class.');
                }
            }
        }

        // If all good, enroll
        Enrollments::create([
            'class_id'   => $classId,
            'student_id' => $stu->student_id,
        ]);

        return back()->with('status', 'Enrolled.');
    }

    /**
     * Unenroll: simple delete of the enrollment.
     */
    public function unenroll(Request $request, string $student)
    {
        $stu = $this->getStudent($student);

        $classId = $request->class_id;
        if (!$classId) {
            return back()->with('error', 'No class selected.');
        }

        Enrollments::where('class_id', $classId)
            ->where('student_id', $stu->student_id)
            ->delete();

        return back()->with('status', 'Unenrolled.');
    }

    /**
     * Payments page: list payments.
     */
    public function payments(string $student)
    {
        $stu = $this->getStudent($student);

        $payments = Payment::where('student_id', $stu->student_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.payments', [
            'student'  => $stu,
            'payments' => $payments,
        ]);
    }

    /**
     * Make payment:
     *  - Auto-creates a simple receipt (like before).
     */
    public function makePayment(Request $request, string $student)
    {
        $stu = $this->getStudent($student);

        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
            'date'   => 'nullable|date',
        ]);

        $p = Payment::create([
            'student_id'   => $stu->student_id,
            'paymentTotal' => $request->amount,
            'paymentDate'  => $request->date ? $request->date : now()->toDateString(),
            'status'       => 'pending', // verified by staff later
        ]);

        // Optional: simple auto-receipt
        Receipt::create([
            'payment_id'  => $p->payment_id,
            'subTotal'    => $p->paymentTotal,
            'receiptDate' => now()->toDateString(),
        ]);

        return back()->with('status', '🧾 Payment submitted. A receipt has been created.');
    }

    /**
     * Receipts page: list receipts (latest first).
     */
    public function receipts(string $student)
    {
        $stu = $this->getStudent($student);

        $paymentIds = Payment::where('student_id', $stu->student_id)->pluck('payment_id');

        $receipts = Receipt::whereIn('payment_id', $paymentIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.receipts', [
            'student'  => $stu,
            'receipts' => $receipts,
        ]);
    }

    /**
     * JSON API (web service requirement):
     * Returns the student's payments as JSON (no auth for now).
     */
    public function paymentsApi(string $student)
    {
        $stu = $this->getStudent($student);

        $payments = Payment::where('student_id', $stu->student_id)
            ->orderBy('paymentDate', 'desc')
            ->get(['payment_id','paymentTotal','paymentDate','status']);

        return response()->json($payments);
    }

    
}
