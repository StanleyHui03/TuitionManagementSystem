<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentReportController extends Controller
{
    // This will return the Blade page
    public function show()
    {
        return view('payments.report');
    }

    // This will return JSON data
    public function data(Request $request)
    {
        $payments = \App\Models\Payments::with(['student', 'tutor', 'classroom'])
            ->orderByDesc('created_at')
            ->paginate(20);

        // return only the current page items (array), no meta
        return response()->json($payments->items());
    }
}
