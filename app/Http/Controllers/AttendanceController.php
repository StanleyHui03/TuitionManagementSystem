<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Lesson;
use Carbon\Carbon;
use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View as ViewContract;

class AttendanceController extends Controller
{
    
    public function create(Request $request, Lesson $lesson)
{
    // Authorize: admin OR the tutor who owns this lesson
    $user = Auth::user();
    $myTutorId = DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');
    if ($user->role !== 'admin' && (!$myTutorId || $myTutorId !== $lesson->tutor_id)) {
        abort(403, 'You do not have permission to take attendance for this lesson.');
    }

    $session_date = $request->query('session_date') ?: Carbon::now()->toDateString();

    // Roster from pivot;
    $roster = $lesson->students()
        ->select('students.student_id', 'students.studentName')
        ->orderBy('students.studentName')
        ->get();

    // Existing attendance for prefill student_id key
    $existing = Attendance::where('lesson_id', $lesson->lesson_id)
        ->whereDate('session_date', $session_date)
        ->get()
        ->keyBy('student_id');

    return view('attendance.take', [
        'lesson'        => $lesson,
        'session_date'  => $session_date,
        'roster'        => $roster,
        'existing'      => $existing,
    ]);
}


    // Save attendance
    public function store(Request $request, Lesson $lesson)
{
    // Authorize
    $user = Auth::user();
    $myTutorId = DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');
    if ($user->role !== 'admin' && (!$myTutorId || $myTutorId !== $lesson->tutor_id)) {
        abort(403, 'You do not have permission to take attendance for this lesson.');
    }

    $data = $request->validate([
        'session_date' => ['nullable', 'date'],
        'attendance'   => ['required', 'array'],
        'attendance.*' => ['in:present,absent,late,excused'],
        'note'         => ['array'],
        'note.*'       => ['nullable', 'string', 'max:255'],
    ]);

    $session_date = $data['session_date'] ?? now()->toDateString();
    $now = now();

    // Use updateOrCreate so the model generates id
    foreach ($data['attendance'] as $studentId => $status) {
        Attendance::updateOrCreate(
            [
                'lesson_id'    => $lesson->lesson_id,
                'student_id'   => $studentId,
                'session_date' => $session_date,
            ],
            [
                'status'             => $status,
                'marked_by_tutor_id' => $myTutorId,
                'marked_at'          => $now,
                'note'               => $data['note'][$studentId] ?? null,
            ]
        );
    }

    return redirect()
        ->route('attendance.create', ['lesson' => $lesson->lesson_id, 'session_date' => $session_date])
        ->with('status', 'Attendance saved.');
}


    /**
     * Student: view ONLY their own attendance.
     */
    public function studentAttendance(): View
    {
        $user = Auth::user();

        // Map user to student_id
        $studentId = optional($user->student)->student_id
            ?? DB::table('students')->where('user_id', $user->id)->value('student_id');

        if (!$studentId) {
            abort(403, 'Only students can view this page.');
        }

        $attendance = Attendance::with([
                'lesson',
                'markedByTutor',
            ])
            ->where('student_id', $studentId)
            ->orderBy('session_date', 'desc')   // <-- important: session_date (matches your model)
            ->get();

       return view('attendance.index', compact('attendance'));
    }

    public function apiUpdateAttendance(Request $request, Lesson $lesson)
    {
        $tutorIdFromRel = optional(optional(Auth::user())->tutor)->tutor_id;
        $tutorIdLookup  = DB::table('tutors')->where('user_id', Auth::id())->value('tutor_id');
        $tutorId        = $tutorIdFromRel ?? $tutorIdLookup;

        if (!$tutorId || (string)$tutorId !== (string)$lesson->tutor_id) {
            return response()->json(['status' => 'fail', 'message' => 'Forbidden'], 403);
        }

        // Validate (Zero-Trust)
        $data = $request->validate([
            'session_date' => ['required', 'date'],
            'student_id'   => ['required', 'string'],
            'status'       => ['required', Rule::in(['present','absent','late','excused','pending'])],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $isEnrolled = DB::table('lesson_students')
            ->where('lesson_id', $lesson->lesson_id)
            ->where('student_id', $data['student_id'])
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Student not enrolled in this lesson'
            ], 422);
        }

        $attendance = Attendance::where('lesson_id', $lesson->lesson_id)
            ->where('student_id', $data['student_id'])
            ->whereDate('session_date', $data['session_date'])
            ->first();

        if ($attendance) {
            $attendance->status             = $data['status'];
            $attendance->note               = $data['note'] ?? $attendance->note;
            $attendance->marked_by_tutor_id = $tutorId;
            $attendance->marked_at          = now();
            $attendance->save();
        } else {
            $attendance = new Attendance();
            $attendance->lesson_id          = $lesson->lesson_id;
            $attendance->student_id         = $data['student_id'];
            $attendance->session_date       = $data['session_date'];
            $attendance->status             = $data['status'];
            $attendance->note               = $data['note'] ?? null;
            $attendance->marked_by_tutor_id = $tutorId;
            $attendance->marked_at          = now();
            $attendance->save();
        }

        return response()->json([
            'status'     => 'success',
            'message'    => 'Attendance updated',
            'attendance' => $attendance,
        ]);
    }

    /**
     * API (Student): retrieve ONLY own attendance history (optionally by date range).
     * Returns: JSON
     */
    public function apiStudentAttendance(Request $request)
    {
        $user = Auth::user();

        // Map user -> student_id
        $studentId = optional($user->student)->student_id
            ?? DB::table('students')->where('user_id', $user->id)->value('student_id');

        if (!$studentId) {
            return response()->json(['status' => 'fail', 'message' => 'Forbidden'], 403);
        }

        $from = $request->query('from');
        $to   = $request->query('to');

        $q = Attendance::with(['lesson', 'markedByTutor'])
            ->where('student_id', $studentId)
            ->orderBy('session_date', 'desc');

        if ($from) { $q->whereDate('session_date', '>=', $from); }
        if ($to)   { $q->whereDate('session_date', '<=', $to);   }

        $records = $q->get(['attendance_id','lesson_id','student_id','session_date','status','note','marked_by_tutor_id','marked_at']);

        return response()->json([
            'status'     => 'success',
            'student_id' => $studentId,
            'attendance' => $records,
        ]);
    }

    /**
     * API (Tutor - optional): view attendance for a lesson for a given date (or all).
     * GET /api/v1/lessons/{lesson}/attendance?session_date=YYYY-MM-DD
     * Returns: JSON
     */




    public function jsonViewer(Request $request, Lesson $lesson): ViewContract
    {
        $user = Auth::user();

        // Allow if:
        // 1) The user is the tutor who owns this lesson, OR
        // 2) The user has an admin role (tweak role names to match your system)
        $isTutorOwner = optional($user->tutor)->tutor_id
                        && (string)optional($user->tutor)->tutor_id === (string)$lesson->tutor_id;

        $role = $user->role ?? $user->type ?? null; // adjust if your role column differs
        $isAdmin = in_array(strtolower((string)$role), ['admin', 'administrator'], true);

        abort_unless($isTutorOwner || $isAdmin, 403, 'You are not authorized to view this lesson’s attendance.');

        return view('attendance.json-viewer', [
            'lesson' => $lesson,
            // default session date = today; the user can change it in the UI
            'defaultDate' => now()->toDateString(),
        ]);
    }
}