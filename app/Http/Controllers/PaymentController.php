<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller {
    public function createOrder(Request $request) {
        $user = \auth('sanctum')->user();


        $paymentData = [
            "body" => [
                "amount" => number_format($request->amount, 2, '.', ''),
                "approveURL" => "https://api.myhome.az/api/payment/callback",
                "cancelURL" => 'https://myhome.az/panel/balans?payment=cancel',
                "cardUuid" => "string",
                "currencyType" => "AZN",
                "declineURL" => 'https://myhome.az/panel/balans?payment=error',
                "description" => "Payment",
                "language" => "AZ",
                "operation" => "PURCHASE",
                "directPay" => false,
                "installmentPeriod" => 0,
                "installmentProductType" => "BIRKART",
                "senderCardUID" => "string"
            ],
            "merchant" => "ES1094008"
        ];

        $response = Http::withHeaders([
            'Authorization' => '09D204A282514037AA78D244363023E5',
            'Content-Type' => 'application/json',
        ])->post('https://api.payriff.com/api/v2/createOrder', $paymentData);

        // Cavabı JSON formatında alırıq
        $responseData = $response->json();
        dd($responseData);
        // Log qeyd edirik
        $paymentLog = PaymentLog::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => $response->successful() ? 'success' : 'failed',
            'transaction_id' => $responseData['transactionId'] ?? null,

            'response' => $responseData,
        ]);

        if ($response->successful()) {
            return response()->json([
                'message' => 'Payment created successfully',
                'data' => $responseData
            ], 200);
        } else {
            return response()->json([
                'message' => 'Payment failed',
                'error' => $responseData
            ], 400);
        }
    }

    public function callbackTransaction(Request $request)
    {
        DB::beginTransaction();

        try {
            $payload = $request->input('payload');
            $orderId = $payload['orderID'];
            $sessionId = $payload['sessionId'];

            // API sorğusunu göndəririk
            $data = [
                'body' => [
                    "languageType" => "EN",
                    "orderId" => $orderId,
                    "sessionId" => $sessionId
                ],
                "merchant" => 'ES1092082'
            ];

            $response = Http::withHeaders([
                'Authorization' => '61A491613E834B329287720E6C13EF03'
            ])->post('https://api.payriff.com/api/v2/getOrderInformation', $data);

            $responseData = json_decode($response->body(), true);

            if (!isset($responseData['payload']['row'])) {
                throw new Exception("Invalid response structure from Payriff API");
            }

            $transactionData = $responseData['payload']['row'];

            // Log qeydini tapırıq
            $log = PaymentLog::where('transaction_id', $orderId)
                ->where('status', 'pending')
                ->firstOrFail();

            // Ödəniş uğurlu olub-olmadığını yoxlayırıq
            if (isset($transactionData['amount']) && $transactionData['orderstatus'] === self::SUCCESS_PAYMENT) {
                $amount = $transactionData['amount'];


                $user = User::findOrFail($log->user_id);

                $user->increment('balance', $amount);

                $log->update([
                    'status' => 'success',
                    'response_data' => $transactionData
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'Payment confirmed successfully',
                    'user_balance' => $user->balance
                ]);

                return redirect('https://myhome.az/panel/balans?payment=success');
            }

            // Əgər ödəniş uğursuz olarsa
            $log->update([
                'status' => 'failed',
                'response_data' => $transactionData
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Payment failed or was not successful',
                'data' => $transactionData
            ], 400);

        } catch (Exception $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Transaction failed',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
