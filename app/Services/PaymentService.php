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
    public function __construct(protected AnnouncementService $announcementService)
    {
    }

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
    public function handleCallback($payload, Request $request)
    {
        DB::beginTransaction();

        try {
            $orderId = $payload['orderId'] ?? null;
            $amount = $payload['amount'] ?? null;
            $paymentStatus = $payload['paymentStatus'] ?? null;

            if (!$orderId || !$amount || !$paymentStatus) {
                throw new Exception("Invalid callback payload");
            }

            // Log-u tapırıq
            $log = PaymentLog::where('transaction_id', $orderId)
                ->where('status', 'pending')
                ->firstOrFail();

            $user = User::findOrFail($log->user_id);

            // Callback URL parametrlərini request-dən alırıq
            $announcementId = $request->query('announcement_id');  // Request-də 'announcement_id' parametri
            $paidServiceId = $request->query('option_id');          // Request-də 'option_id' parametri
            $serviceType = $request->query('type');                 // Request-də 'type' parametri

            if ($paymentStatus === 'APPROVED') {

                // Elan növünü və xidməti yoxlayırıq və uyğun əməliyyatı icra edirik
                if ($serviceType === 'boost') {
                    $boost = $this->announcementService->boostAnnouncement($announcementId, $paidServiceId, $user);
                    if (!$boost) {
                        return ['success' => false, 'message' => 'Payment confirmed but insufficient balance for boost'];
                    }
                    $message = 'Elan uğurla irəli çəkildi.';
                } elseif (in_array($serviceType, ['vip', 'premium'])) {
                    $vipPremium = $this->announcementService->makeVipOrPremiumAnnouncement($announcementId, $paidServiceId, $user);
                    if (!$vipPremium) {
                        return ['success' => false, 'message' => 'Payment confirmed but insufficient balance for VIP/Premium'];
                    }
                    $message = "Elan uğurla $serviceType oldu.";
                } else {
                    // Adi balans artırma
                    $user->increment('balance', $amount);
                    $message = 'Balance updated successfully';
                }

                // Log-u yeniləyirik
                $log->update([
                    'status' => 'success',
                    'response' => json_encode($payload)
                ]);

                DB::commit();
                return ['success' => true, 'message' => $message, 'user_balance' => $user->balance];
            }

            // Əgər ödəniş uğursuz olubsa
            $log->update([
                'status' => 'failed',
                'response' => json_encode($payload)
            ]);

            DB::commit();
            return ['success' => false, 'message' => 'Payment failed or was not successful'];

        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return ['success' => false, 'error' => 'Transaction failed', 'message' => $e->getMessage()];
        }
    }




}
