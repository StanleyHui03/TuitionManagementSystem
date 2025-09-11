<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Tutor: open mark-attendance page for a lesson & date.
     * GET /lessons/{lesson}/attendance?session_date=YYYY-MM-DD
     */
    public function create(Request $request, Lesson $lesson): View
    {
        // --- Tutor guard: only this lesson's tutor can mark it ---
        $tutorIdFromRel = optional(optional(Auth::user())->tutor)->tutor_id;
        $tutorIdLookup  = DB::table('tutors')->where('user_id', Auth::id())->value('tutor_id');
        $tutorId        = $tutorIdFromRel ?? $tutorIdLookup;

        if (!$tutorId || (string)$tutorId !== (string)$lesson->tutor_id) {
            abort(403, 'This lesson does not belong to you.');
        }

        // --- Pick the session date (default to today if none provided) ---
        $sessionDate = $request->input('session_date') ?: now()->toDateString();

        // --- Enrolled students (adjust pivot/table names if yours differ) ---
        $students = DB::table('lesson_students')
            ->join('students', 'students.student_id', '=', 'lesson_students.student_id')
            ->select('students.student_id', 'students.name')
            ->where('lesson_students.lesson_id', $lesson->lesson_id)
            ->orderBy('students.name')
            ->get();

        // --- Existing attendance for that date (keyed by student_id) ---
        $existing = Attendance::where('lesson_id', $lesson->lesson_id)
            ->whereDate('session_date', $sessionDate)
            ->pluck('status', 'student_id');

        return view('attendance.create', [
            'lesson'      => $lesson,
            'students'    => $students,
            'sessionDate' => $sessionDate,
            'existing'    => $existing,
        ]);
    }

    /**
     * Tutor: save/update attendance for a lesson & date.
     * POST /lessons/{lesson}/attendance
     * body: { session_date: 'YYYY-MM-DD', status: { studentId: 'present' }, note: { studentId: '...' } }
     */
    public function store(Request $request, Lesson $lesson)
    {
        // --- Tutor guard ---
        $tutorIdFromRel = optional(optional(Auth::user())->tutor)->tutor_id;
        $tutorIdLookup  = DB::table('tutors')->where('user_id', Auth::id())->value('tutor_id');
        $tutorId        = $tutorIdFromRel ?? $tutorIdLookup;

        if (!$tutorId || (string)$tutorId !== (string)$lesson->tutor_id) {
            abort(403, 'This lesson does not belong to you.');
        }

        // --- Validate ---
        $data = $request->validate([
            'session_date' => ['required', 'date'],
            'status'       => ['required', 'array'], // status[student_id] => present|absent|late|excused|pending
            'status.*'     => ['nullable', 'in:present,absent,late,excused,pending'],
            'note'         => ['array'],
            'note.*'       => ['nullable', 'string', 'max:500'],
        ]);

        $sessionDate = $data['session_date'];
        $statuses    = $data['status'];
        $notes       = $data['note'] ?? [];

        DB::transaction(function () use ($lesson, $sessionDate, $statuses, $notes, $tutorId) {
            foreach ($statuses as $studentId => $status) {
                $status = $status ?: 'pending';

                $att = Attendance::where('lesson_id', $lesson->lesson_id)
                    ->where('student_id', $studentId)
                    ->whereDate('session_date', $sessionDate)
                    ->first();

                if ($att) {
                    // Update
                    $att->status              = $status;
                    $att->note                = $notes[$studentId] ?? $att->note;
                    $att->marked_by_tutor_id  = $tutorId;
                    $att->marked_at           = now();
                    $att->save();
                } else {
                    // Create (PK attendance_id handled by HasPrefixedId on creating)
                    $att = new Attendance();
                    $att->lesson_id           = $lesson->lesson_id;
                    $att->student_id          = $studentId;
                    $att->session_date        = $sessionDate;
                    $att->status              = $status;
                    $att->note                = $notes[$studentId] ?? null;
                    $att->marked_by_tutor_id  = $tutorId;
                    $att->marked_at           = now();
                    $att->save();
                }
            }
        });

        return redirect()
            ->route('attendance.create', ['lesson' => $lesson->lesson_id, 'session_date' => $sessionDate])
            ->with('status', 'Attendance saved.');
    }

    /**
     * Student: view ONLY their own attendance.
     * GET /student/attendance
     */
    public function studentAttendance(): View
    {
        $user = Auth::user();

        // Map user -> student_id (adjust if your relation differs)
        $studentId = optional($user->student)->student_id
            ?? DB::table('students')->where('user_id', $user->id)->value('student_id');

        if (!$studentId) {
            abort(403, 'Only students can view this page.');
        }

        $attendance = Attendance::with([
                'lesson',
                // Uncomment if you have these relationships on Lesson:
                // 'lesson.class',
                // 'lesson.subject',
                'markedByTutor',
            ])
            ->where('student_id', $studentId)
            ->orderBy('session_date', 'desc')   // <-- important: session_date (matches your model)
            ->get();

       return view('attendance.index', compact('attendance'));
    }
}
