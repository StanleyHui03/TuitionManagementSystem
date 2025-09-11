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

        // 1) Find tutor_id for this user
        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');
        if (!$tutorId) {
            abort(403, 'Your account is not linked to a tutor.');
        }

        // 2) Filters
        $q    = trim((string) $request->input('q', ''));
        $when = $request->input('when', 'all'); // 'all' | 'upcoming' | 'past'
        $now  = now();

        // 3) Base query: only lessons belonging to this tutor (NO start_at anywhere)
        $base = Lesson::query()
            ->where('tutor_id', $tutorId)
            ->when($q !== '', function ($sub) use ($q) {
                $sub->where(function ($qq) use ($q) {
                    $qq->where('class_id', 'like', "%{$q}%")
                       ->orWhere('lesson_id', 'like', "%{$q}%")
                       ->orWhere('room', 'like', "%{$q}%");
                });
            })
            ->orderedWeekly()    // order by day_of_week, start_time (scope from model)
            ->get();

        // 4) Compute next occurrence for each lesson (date+time) in PHP
        $withComputed = $base->map(function (Lesson $l) {
            $l->next_at = $l->nextOccurrence();  // returns Carbon|null
            return $l;
        });

        // 5) Apply 'when' filter on computed next_at
        if ($when === 'upcoming') {
            $withComputed = $withComputed->filter(fn ($l) => $l->next_at && $l->next_at->greaterThanOrEqualTo(now()))
                                         ->sortBy('next_at');
        } elseif ($when === 'past') {
            $withComputed = $withComputed->filter(fn ($l) => $l->next_at && $l->next_at->lt(now()))
                                         ->sortByDesc('next_at');
        } else { // 'all'
            // Keep weekly ordering; optionally sort by next_at if you prefer:
            // $withComputed = $withComputed->sortBy('next_at');
        }

        // 6) Paginate the collection (so Blade links still work)
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

        // 7) Render
        return view('tutor.lessons', [
            'lessons' => $lessons,
            'filters' => ['q' => $q, 'when' => $when],
        ]);
    }
}
