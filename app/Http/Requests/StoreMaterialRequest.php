<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Pdf;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // controller routes also use auth+verified
    }

    public function rules(): array
    {
        return [
            'title'      => ['required','string','max:200'],
            'subject_id' => ['required','string','max:10','exists:subjects,subject_id'],
            'file'       => [
                'required','file','mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
                'max:20480', // 20 MB
                new Pdf(),
            ],
        ];
    }
}
