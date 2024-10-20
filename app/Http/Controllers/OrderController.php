<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\User\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as LogModel;

class OrderController extends AppBaseController
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index(Request $request){
        try{
            $user = \auth()->user();
            if ($user) {
                $orders = $this->orderService->getOrders($user);
                if ($orders) {
                    //return $this->sendResponse(OrderResource::collection($orders), 'data retrieved successfully');

                    return apiResponse(true, 'data retrieved successfully', 200, $orders);
                } else {
                    return $this->sendResponse(null, 'success');
                }
            }
            return $this->sendError('user must login', 400);

        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function store(CreateOrderRequest $request)
    {
        try {
            $user = \auth()->user();
            if ($user) {
                $order = $this->orderService->createOrder($request,$user);
                if ($order) {
                    return apiResponse(true, 'order created successfully', 200, new OrderResource($order));
                } else {
                    return $this->sendError('error adding order', 400);
                }
            }
            return $this->sendError('user must login', 400);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function update(Order $order , UpdateOrderRequest $request){
        try {
            $user = \auth()->user();
            if ($user->id == $order->user_id) {
                $updated = $this->orderService->updateOrder($request, $order);
                if ($updated) {
                    return apiResponse(true, 'order updated successfully', 200, new OrderResource($order));
                } else {
                    return $this->sendError('error updating order', 400);
                }
            }
            return $this->sendError('user must login', 400);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function getPayment(Request $request){
        try {
            $user = \auth()->user();
            if ($user) {
                $gateway = $request->input('gateway'); // 'tabby', 'stcpay', or 'tamara'
                $paymentService = app("payment.$gateway");
                $order_data = $this->orderService->getTamaraPaymentData();
                $result = $paymentService->processSession($order_data);
                $redirect_url = $result['checkout_url'];
                LogModel::create([
                    'order_id' => 1,//$order_data['id'],
                    'payment_gateway' => $gateway,
                    'url' => $redirect_url ?? '',
                    'response' => json_encode($result),
                ]);
                if ($redirect_url) {
                    Log::debug('payment', array($result));
                    Log::info($redirect_url);
                    return apiResponse(true, 'data retrived successfully', 200, $redirect_url);
                } else {
                    throw new \Exception(__('messages.payment_url_creation_error'));
                }
            }
            return $this->sendError('user must login', 400);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }
}
