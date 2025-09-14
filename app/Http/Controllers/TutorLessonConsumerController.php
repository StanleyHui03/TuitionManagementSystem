<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use \App\Http\Controllers\Api\LessonApiController;
use Throwable;

class TutorLessonConsumerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Resolve tutor_id
        $tutorId = optional(optional($user)->tutor)->tutor_id
            ?? DB::table('tutors')->where('user_id', $user->id)->value('tutor_id');

        if (!$tutorId) {
            abort(403, 'Your account is not linked to a tutor.');
        }

        try {
            // Build a "fake" request for the API method
            $fake = Request::create('/api/v1/lessons', 'GET', [
                'tutor_id' => $tutorId,
            ]);
            $fake->headers->set('Accept', 'application/json');

            // friend's controller lives under App\Http\Controllers\Api\...
            $apiResponse = app()->call([ LessonApiController::class, 'index' ], [
                'request' => $fake
            ]);

            $data = $this->normalizeApiResponse($apiResponse, $fake);

            // {data}
            $rows    = is_array($data) && array_key_exists('data', $data) ? $data['data'] : $data;
            $lessons = collect($rows)->filter(function ($l) use ($tutorId) {
                $arr = is_array($l) ? $l : (array) $l;
                return (string)($arr['tutor_id'] ?? '') === (string)$tutorId;
            })->values();

            return view('tutor.api-lessons', [
                'lessons' => $lessons,
                'error'   => null,
                'raw'     => null,
            ]);

        } catch (Throwable $e) {
            // try internal dispatch if direct call failed like class not found
            try {
                $fake = Request::create('/api/v1/lessons', 'GET', ['tutor_id' => $tutorId]);
                $fake->headers->set('Accept', 'application/json');
                $fake->setUserResolver(fn () => $user);

                $response = app()->handle($fake);
                $status   = $response->getStatusCode();
                $content  = $response->getContent();
                $data     = json_decode($content, true);

                if ($status >= 400 || !is_array($data)) {
                    return view('tutor.api-lessons', [
                        'lessons' => collect(),
                        'error'   => "API HTTP $status",
                        'raw'     => $content,
                    ]);
                }

                $rows    = array_key_exists('data', $data) ? $data['data'] : $data;
                $lessons = collect($rows)->filter(function ($l) use ($tutorId) {
                    $arr = is_array($l) ? $l : (array) $l;
                    return (string)($arr['tutor_id'] ?? '') === (string)$tutorId;
                })->values();

                return view('tutor.api-lessons', [
                    'lessons' => $lessons,
                    'error'   => null,
                    'raw'     => null,
                ]);

            } catch (Throwable $e2) {
                return view('tutor.api-lessons', [
                    'lessons' => collect(),
                    'error'   => $e2->getMessage(),
                    'raw'     => null,
                ]);
            }
        }
    }

    /**
     * Normalize any possible API controller return type to a plain array.
     */
    protected function normalizeApiResponse($apiResponse, Request $fake): array
    {
        if ($apiResponse instanceof JsonResponse) {
            return $apiResponse->getData(true);
        }
        if (is_object($apiResponse) && method_exists($apiResponse, 'toResponse')) {
            $jr = $apiResponse->toResponse($fake); // JsonResource/Collection
            return $jr->getData(true);
        }
        if (is_array($apiResponse)) {
            return $apiResponse;
        }
        $json = @json_encode($apiResponse);
        if ($json !== false) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return ['data' => []];
    }
}
