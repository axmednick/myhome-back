<?php

namespace App\Services\Payment;


use App\Constant\Currency;
use App\Constant\PaymentConstant;
use App\Http\Requests\ParacoinRequest;
use App\Http\Requests\PayriffCallbackRequest;
use App\Http\Requests\SquareCallbackRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\StripeCallbackRequest;
use App\Http\Traits\ApiFormatterTrait;
use App\Models\BigLog;
use App\Models\MillikartLog;
use App\Models\PaymentLog;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;
use Square\Models\Payment;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Square\SquareClient;

class PayriffService
{

    use ApiFormatterTrait;

    const SUCCESS_PAYMENT = 'APPROVED';

    /**
     * @throws Exception
     */
    public function createTransaction(StoreTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $currency = $user->currency;
            $amount = $request->get('amount');
            $unix_time = time() - 86400;

            $data = [

                'body' => [
                    "amount" => $amount,
                    "approveURL" => env('APP_URL') . '/api/payment/payriff/callback',
                    "cancelURL" => env('FRONT_URL') . '/payment/cancel',
                    "cardUuid" => "string",
                    "currencyType" => 'AZN',
                    // "currency" => $currency,
                    "declineURL" => env('FRONT_URL') . '/dashboard?payment=decline',
                    "description" => "Payment",
                    "language" => 'EN',
                    "operation" => "PURCHASE",
                    //"cardSave" => true,
                    "directPay" => false,
                    "installmentPeriod" => 0,
                    "installmentProductType" => "BIRKART",
                    "senderCardUID" => "string"
                ],

                "merchant" => 'ES1092082',

            ];


            $response = Http::withHeaders([
                'Authorization' => '61A491613E834B329287720E6C13EF03'
            ])->post('https://api.payriff.com/api/v2/' . PaymentConstant::CREATE_ORDER, $data);


            $response = json_decode($response->body(), true);


            PaymentLog::query()->create([
                'reference' => $response['payload']['orderId'],
                'user_id' => $user->id,
                'amount' => $request->get('amount'),
                'description' => 'Balance Increase with payriff',
                'referrer' => 'payriff',
                'unix_time' => $unix_time
            ]);

            DB::commit();
            return ['paymentUrl' => $response['payload']['paymentUrl']];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public
    function callbackTransaction(PayriffCallbackRequest $request)
    {

        DB::beginTransaction();

        try {

            $orderId = $request->get('payload')['orderID'];
            $sessionId = $request->get('payload')['sessionId'];



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
            ])->post('https://api.payriff.com/api/v2/getOrderInformation',$data);



            $response = json_decode($response->body(), true)['payload'];




            $log = PaymentLog::query()
                ->whereReferrer('payriff')
                ->whereReference($orderId)
                ->whereStatus(false)
                ->firstOrFail();


            if ($response['row']['amount'] && $response['row']['orderstatus'] === self::SUCCESS_PAYMENT) {
                $amount = $response['row']['amount'];
                $user = User::query()->findOrFail($log->user_id);

                if ($user->currency !== $response['row']['currency']) {
                    $amount = \AmrShawky\Currency::convert()
                        ->from($response['currencyType'])
                        ->to($user->currency)
                        ->amount($amount)
                        ->get();
                }

                $user->update(['money' => $user->money += $amount]);
                $log->update(['status' => true]);

                DB::commit();
                return $user;
            }
        } catch (Exception $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }


}
