<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Models\Material;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    /**
     * List materials with role-based visibility:
     * - Student: only materials uploaded by their own tutor for enrolled classes (subject + tutor match)
     * - Tutor: all materials for subjects they teach (even if uploaded by other tutors)
     * - Others (guest/admin): all materials (adjust if you want auth-only)
     */
    public function index()
    {
        // For upload dropdown
        $subjects = Subject::orderBy('subject_Name')->get(['subject_id','subject_Name']);

        // Student view: restrict by subject AND tutor (own tutor only)
        if (auth()->check() && auth()->user()->student_id) {
            $studentId = auth()->user()->student_id;

            $materials = Material::query()
                ->join('classes', function ($j) {
                    $j->on('classes.subject_id', '=', 'materials.subject_id')
                      ->on('classes.tutor_id',   '=', 'materials.tutor_id'); // match the uploader tutor
                })
                ->join('enrollments', function ($j) use ($studentId) {
                    $j->on('enrollments.class_id', '=', 'classes.class_id')
                      ->where('enrollments.student_id', '=', $studentId);
                })
                ->select('materials.*')
                ->latest('materials.created_at')
                ->paginate(12);

            return view('materials.material', compact('materials','subjects'));
        }

        // Tutor view: see all materials for any subject they teach
        if (auth()->check() && auth()->user()->tutor_id) {
            $tutorId = auth()->user()->tutor_id;

            $subjectIds = DB::table('classes')
                ->where('tutor_id', $tutorId)
                ->pluck('subject_id');

            $materials = Material::whereIn('subject_id', $subjectIds)
                ->latest('created_at')
                ->paginate(12);

            return view('materials.material', compact('materials','subjects'));
        }

        // Guest/admin (adjust to your policy): show all
        $materials = Material::latest('created_at')->paginate(12);
        return view('materials.material', compact('materials','subjects'));
    }

    /** Helper: does a given student have access to this material (same subject + same tutor)? */
    protected function studentHasAccess(?string $studentId, Material $material): bool
    {
        if (!$studentId) return false;

        return DB::table('enrollments')
            ->join('classes', 'classes.class_id', '=', 'enrollments.class_id')
            ->where('enrollments.student_id', $studentId)
            ->where('classes.subject_id', $material->subject_id)
            ->where('classes.tutor_id',  $material->tutor_id) // must match uploader tutor
            ->exists();
    }

    /** Helper: does this tutor teach this subject (can view all materials for it)? */
    protected function tutorTeachesSubject(?string $tutorId, string $subjectId): bool
    {
        if (!$tutorId) return false;

        return DB::table('classes')
            ->where('tutor_id', $tutorId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    /** Meta page. Tutors who teach the subject are allowed; students need studentHasAccess. */
    public function show(Material $material)
    {
        if (auth()->check() && auth()->user()->tutor_id) {
            abort_unless($this->tutorTeachesSubject(auth()->user()->tutor_id, $material->subject_id), 403);
        } elseif (auth()->check() && auth()->user()->student_id) {
            abort_unless($this->studentHasAccess(auth()->user()->student_id, $material), 403);
        }
        // Guests/admin allowed here per current policy; add middleware if you want auth-only
        return view('materials.show', compact('material'));
    }

    /** Secure inline preview (iframe) from PRIVATE disk. */
    public function preview(Material $material)
    {
        if (auth()->check() && auth()->user()->tutor_id) {
            abort_unless($this->tutorTeachesSubject(auth()->user()->tutor_id, $material->subject_id), 403);
        } elseif (auth()->check() && auth()->user()->student_id) {
            abort_unless($this->studentHasAccess(auth()->user()->student_id, $material), 403);
        }

        abort_unless(Storage::disk('local')->exists($material->file_path), 404);

        $abs = Storage::disk('local')->path($material->file_path);
        return response()->file($abs, [
            'Content-Type'           => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-transform',
        ]);
    }

    /** Secure download (attachment) from PRIVATE disk. */
    public function download(Material $material)
    {
        if (auth()->check() && auth()->user()->tutor_id) {
            abort_unless($this->tutorTeachesSubject(auth()->user()->tutor_id, $material->subject_id), 403);
        } elseif (auth()->check() && auth()->user()->student_id) {
            abort_unless($this->studentHasAccess(auth()->user()->student_id, $material), 403);
        }

        abort_unless(Storage::disk('local')->exists($material->file_path), 404);

        $abs = Storage::disk('local')->path($material->file_path);
        $name = $material->original_file_name ?: (Str::slug($material->title, '_') . '.pdf');

        return response()->download($abs, $name, [
            'Content-Type'           => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Tutor upload (PDF-only, randomized filename, private storage). */
    public function store(StoreMaterialRequest $request)
    {
        $safeName = (string) Str::uuid() . '.pdf';
        $path = $request->file('file')->storeAs('materials', $safeName, 'local'); // PRIVATE disk

        Material::create([
            'tutor_id'           => auth()->user()->tutor_id ?? null,
            'subject_id'         => $request->validated()['subject_id'],
            'title'              => $request->validated()['title'],
            'file_path'          => $path, // e.g. materials/{uuid}.pdf
            'original_file_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return redirect()->route('materials.index')->with('success', 'Material uploaded.');
    }

    /** Tutor delete (consider restricting to owner: auth()->user()->tutor_id === $material->tutor_id). */
    public function destroy(Material $material)
    {
        // Uncomment to enforce owner-only deletion:
        // abort_if(auth()->user()?->tutor_id !== $material->tutor_id, 403);

        Storage::disk('local')->delete($material->file_path);
        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material deleted.');
    }
}
