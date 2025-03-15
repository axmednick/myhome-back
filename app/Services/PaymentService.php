<?php

namespace App\Services;

use App\Models\PaymentLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class PaymentService
{
    protected $merchant = "ES1094008";
    protected $authToken = "09D204A282514037AA78D244363023E5";
    protected $paymentApiUrl = "https://api.payriff.com/api/v2/createOrder";

    /**
     * Yeni ödəniş sifarişi yaradır
     */
    public function createOrder($user, $amount, $approveURL, $cancelURL, $declineURL, $description = "Payment")
    {
        $paymentData = [
            "body" => [
                "amount" => number_format($amount, 2, '.', ''),
                "approveURL" => $approveURL,
                "cancelURL" => $cancelURL,
                "declineURL" => $declineURL,
                "currencyType" => "AZN",
                "description" => $description,
                "operation" => "PURCHASE",
                "language" => "AZ",
                "directPay" => false,
                "installmentPeriod" => 0,
                "installmentProductType" => "BIRKART",
            ],
            "merchant" => $this->merchant
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->authToken,
            'Content-Type' => 'application/json',
        ])->post($this->paymentApiUrl, $paymentData);

        $responseData = $response->json();


        $paymentLog = PaymentLog::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'transaction_id' => $responseData['payload']['orderId'] ?? null,
            'status' => 'pending',
            'response' => $responseData,
        ]);

        return $response->successful() ? ($responseData['payload']['paymentUrl'] ?? null) : null;
    }

    /**
     * Ödəniş callback-i işləyir
     */
    public function handleCallback($payload)
    {
        DB::beginTransaction();

        try {
            $orderId = $payload['orderId'] ?? null;
            $amount = $payload['amount'] ?? null;
            $paymentStatus = $payload['paymentStatus'] ?? null;

            if (!$orderId || !$amount || !$paymentStatus) {
                throw new Exception("Invalid callback payload");
            }

            $log = PaymentLog::where('transaction_id', $orderId)
                ->where('status', 'pending')
                ->firstOrFail();

            $user = User::findOrFail($log->user_id);

            if ($paymentStatus === 'APPROVED') {

                $user->increment('balance', $amount);

                $log->update([
                    'status' => 'success',
                    'response' => json_encode($payload)
                ]);

                DB::commit();
                return ['success' => true, 'message' => 'Payment confirmed successfully', 'user_balance' => $user->balance];
            }


            $log->update([
                'status' => 'failed',
                'response' => json_encode($payload)
            ]);

            DB::commit();
            return ['success' => false, 'message' => 'Payment failed or was not successful'];

        } catch (Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Transaction failed', 'message' => $e->getMessage()];
        }
    }
}
