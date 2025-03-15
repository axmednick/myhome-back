<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createOrder(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $paymentUrl = $this->paymentService->createOrder(
            $user,
            $request->amount,
            "https://api.myhome.az/api/payment/callback",
            'https://myhome.az/panel/balans?payment=cancel',
            'https://myhome.az/panel/balans?payment=error'
        );

        if ($paymentUrl) {
            return response()->json(['paymentUrl' => $paymentUrl], 200);
        }

        return response()->json(['message' => 'Payment failed'], 400);
    }

    public function callbackTransaction(Request $request)
    {


        if ($request->isMethod('get')) {
            return redirect('https://myhome.az/panel/balans?payment=success');
        }


        $payload = $request->all()['payload'] ?? null;
        \Log::error($payload);
        if (!$payload) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $result = $this->paymentService->handleCallback($payload, $request);


        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
