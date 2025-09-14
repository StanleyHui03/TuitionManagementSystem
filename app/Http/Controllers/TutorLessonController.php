<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TutorLessonController extends Controller
{
    /**
     * Show all lessons for the logged-in tutor, with optional search and time filter.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');
        if (!$tutorId) {
            abort(403, 'Your account is not linked to a tutor.');
        }

        // Filters
        $q    = trim((string) $request->input('q', ''));
        $when = $request->input('when', 'all'); // 'all' | 'upcoming' | 'past'
        $now  = now();

        // only lessons belonging to this tutor
        $base = Lesson::query()
            ->where('tutor_id', $tutorId)
            ->when($q !== '', function ($sub) use ($q) {
                $sub->where(function ($qq) use ($q) {
                    $qq->where('class_id', 'like', "%{$q}%")
                       ->orWhere('lesson_id', 'like', "%{$q}%")
                       ->orWhere('room', 'like', "%{$q}%");
                });
            })
            ->orderedWeekly()    // order by day_of_week, start_time 
            ->get();

        // Compute next occurrence for each lesson 
        $withComputed = $base->map(function (Lesson $l) {
            $l->next_at = $l->nextOccurrence(); 
            return $l;
        });

        // Apply 'when' filter on computed next_at
        if ($when === 'upcoming') {
            $withComputed = $withComputed->filter(fn ($l) => $l->next_at && $l->next_at->greaterThanOrEqualTo(now()))
                                         ->sortBy('next_at');
        } elseif ($when === 'past') {
            $withComputed = $withComputed->filter(fn ($l) => $l->next_at && $l->next_at->lt(now()))
                                         ->sortByDesc('next_at');
        } else { 
        }

        // Paginate the collection
        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $total   = $withComputed->count();
        $items   = $withComputed->slice(($page - 1) * $perPage, $perPage)->values();
        $lessons = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Render
        return view('tutor.lessons', [
            'lessons' => $lessons,
            'filters' => ['q' => $q, 'when' => $when],
        ]);
    }

    public function dashboard(): View
{
    $user = Auth::user();

    $tutorId = optional($user->tutor)->tutor_id
        ?? DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');

    if (!$tutorId) {
        abort(403, 'Your account is not linked to a tutor.');
    }

    // remove 'subject_id' from the select list
    $lessons = Lesson::where('tutor_id', $tutorId)
        ->orderBy('lesson_id')
        ->get(); // or ->get(['lesson_id','class_id']); if you prefer explicit columns

    return view('tutor.dashboard', compact('lessons'));
}


}