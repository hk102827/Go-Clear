<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public function init(Request $request)
    {
        $request->validate([
            'job_id'=>'required|exists:jobs,id',
            'amount'=>'required|numeric'
        ]);

        return response()->json([
            'payment_intent'=>'FAKE_INTENT_'.uniqid()
        ]);
    }

    public function confirm(Request $request)
    {
        return response()->json([
            'success'=>true,
            'message'=>'Payment Successful'
        ]);
    }
}

