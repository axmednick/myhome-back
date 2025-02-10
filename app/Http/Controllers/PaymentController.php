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

        // Log qeyd edirik
        $paymentLog = PaymentLog::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'transaction_id' => $responseData['payload']['orderId'] ?? null,

            'response' => $responseData,
        ]);

        if ($response->successful()) {
            return response()->json([
                'paymentUrl' => $responseData['payload']['paymentUrl'] ?? null
            ], 200);
        }
        else {
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
            // Əgər metod GET-dirsə, birbaşa yönləndirmə et
/*            if ($request->isMethod('get')) {
                return redirect('https://myhome.az/panel/balans?payment=success');
            }*/

            // `payload` dəyərini alırıq
            $payload = $request->input('payload');
            \Log::error($payload);


            // Lazım olan məlumatları yoxlayırıq
            if (!isset($payload['paymentStatus'])) {
                throw new Exception("orderId, amount, currencyType və ya paymentStatus mövcud deyil.");
            }

            $orderId = $payload['orderId'];
            $amount = $payload['amount'];
            $currency = $payload['currencyType'];
            $paymentStatus = $payload['paymentStatus'];

            // Əməliyyat məlumatlarını tapırıq
            $log = PaymentLog::where('transaction_id', $orderId)
                ->where('status', 'pending')
                ->firstOrFail();

            // Ödəniş uğurlu olub-olmadığını yoxlayırıq
            if ($paymentStatus === 'APPROVED') {
                $user = User::findOrFail($log->user_id);

                // Balansı artırırıq
                $user->increment('balance', $amount);

                // Log yeniləyirik
                $log->update([
                    'status' => 'success',
                    'response_data' => json_encode($payload) // JSON kimi saxlayırıq
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Payment confirmed successfully',
                    'user_balance' => $user->balance
                ]);
            }

            // Əgər ödəniş uğursuz olarsa
            $log->update([
                'status' => 'failed',
                'response_data' => json_encode($payload)
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Payment failed or was not successful',
                'data' => $payload
            ], 400);

        } catch (Exception $th) {
            \Log::error($th->getMessage());
            DB::rollBack();
            return response()->json([
                'error' => 'Transaction failed',
                'message' => $th->getMessage()
            ], 500);
        }
    }


}
