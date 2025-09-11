<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accept only genuine PDFs:
 * - server MIME (finfo)
 * - .pdf extension
 * - magic bytes "%PDF-"
 */
class Pdf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || !method_exists($value, 'getMimeType')) {
            $fail('Invalid file.');
            return;
        }

        $mime = $value->getMimeType(); // server-detected
        if (!in_array($mime, ['application/pdf', 'application/x-pdf'], true)) {
            $fail('The file must be a PDF (invalid MIME).');
            return;
        }

        if (strtolower($value->getClientOriginalExtension()) !== 'pdf') {
            $fail('The file must have a .pdf extension.');
            return;
        }

        $h = @fopen($value->getRealPath(), 'rb');
        if ($h === false) {
            $fail('Unable to read the file.');
            return;
        }
        $head = fread($h, 5);
        fclose($h);

        if ($head !== '%PDF-') {
            $fail('The file is not a valid PDF.');
        }
    }
}
