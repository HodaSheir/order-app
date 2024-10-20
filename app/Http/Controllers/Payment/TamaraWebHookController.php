<?php

namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Log as LogModel;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;

class TamaraWebHookController extends Controller
{
    public $apiUrl;
    public $apiKey;

    public function __construct()
    {
        $this->apiUrl = app()->environment('local') ?  config('services.tamara.tamara_api_url_test') :  config('services.tamara.tamara_api_url');
        $this->apiKey = config('services.tamara.tamara_merchant_key');
    }
    public function handleWebhook(Request $request)
    {
        // Get the webhook payload
        $payload = $request->all();
        Log::info('Tamara Webhook received: ', $payload);

        // Find the order in your database
        // $order = Order::where('order_number', $payload['order_number'])->first();
        $order = Order::findOrFail(1);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Extract data from the payload
        $orderReferenceId = $payload['order_reference_id'] ?? null;
        $orderStatus = $payload['event_type'] ?? null;

        if (!$orderReferenceId || !$orderStatus) {
            Log::debug('Tamara Webhook - Missing order reference or status', [
                'order_reference_id' => $orderReferenceId,
                'order_status' => $orderStatus,
            ]);
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        //log webhook payload in log table to facilitate debug
        LogModel::create([
            'order_id' => $order->id,
            'payment_gateway' => 'tamara',
            'request_payload' => json_encode($payload),
            'status' => $orderStatus,
        ]);

        // Update order status based on Tamara webhook
        switch ($orderStatus) {
            case 'order_approved':
                //ensures that once the customer has successfully completed the payment on Tamara’s checkout page,
                //the order status is properly updated in Tamara's system and confirmed by the merchant
                $this->authoriseOrder($payload['order_id']);
                $this->handleSuccess($order ,$payload);
                break;
            case 'order_declined':
                $order->status = 'declined';
                $order->save();
                break;
            case 'order_canceled':
                $order->status = 'cancelled';
                $order->save();
                break;
            default:
                return response()->json(['error' => 'Unknown status'], 400);
        }
        // Return success response
        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }

    public function authoriseOrder($orderReferenceId)
    {
        $client = new Client();
        $response = $client->post("{$this->apiUrl}/orders/{$orderReferenceId}/authorise", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'order_reference_id' => $orderReferenceId,
            ],
        ]);

        $statusCode = $response->getStatusCode();
     
        if ($statusCode === 200) {
            Log::info('Order Successfully Authorised', ['order_reference_id' => $orderReferenceId]);
        } else {
            Log::error('Failed to Authorise Order', [
                'order_reference_id' => $orderReferenceId,
                'status_code' => $statusCode,
                'response_body' => $response->getBody()->getContents(),
            ]);
        }
    }

    public function handleSuccess($order, $payload){

        DB::beginTransaction();
        //update order status
        $order->payment_status = 'paid';
        $order->save();
        // Store the transaction for debugging
        PaymentTransaction::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'transaction_id' => $payload['order_reference_id'],
            'payment_gateway' => 'tamara',
            'amount' => (float)$order->quantity*$order->price,
            'payment_status' => 'order_approved',
            'payment_data' => json_encode($payload),
        ]);

        DB::commit();
    }

}
