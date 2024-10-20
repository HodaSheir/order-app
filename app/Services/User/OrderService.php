<?php

namespace App\Services\User;


use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderService
{

  private $orderModel;

  public function __construct( Order $orderModel) {
    $this->orderModel = $orderModel;
  }

  public function getOrders($user)
  {
    try {
      $orders = $user->orders()->orderBy('created_at', 'desc')->paginate()->toArray();
      return $orders;

    } catch (\Throwable $exception) {
      Log::error($exception->getMessage());
      throw $exception;
    }
  }

  public function createOrder($request , $user){
    try{
      $data = $request->all();
      $data['user_id'] = $user->id;
      return $this->orderModel->create($data);

    } catch (\Throwable $exception) {
      Log::error($exception->getMessage());
      throw $exception;
    }
  }

  public function updateOrder($request , $order){
    try {
      $data = $request->all();
      return $order->update($data);
    } catch (\Throwable $exception) {
      Log::error($exception->getMessage());
      throw $exception;
    }
  }

  public function getTamaraPaymentData(){
    try {
     
        // $items = $order->orderDetails->map(function ($detail) {
        //   return [
        //     'name' => $detail->product->name,
        //     'type' => 'Digital',
        //     'reference_id' => $detail->product_id,
        //     'sku' => 'not_defined',
        //     'quantity' => $detail->quantity,
        //     'discount_amount' => [
        //       'amount' => (float)($detail->price_before_discount - $detail->price_after_discount) ?? 0,
        //       'currency' => 'SAR',
        //     ],
        //     'tax_amount' => [
        //       'amount' =>$detail->tax_amount ?? 0,
        //       'currency' => 'SAR',
        //     ],
        //     'unit_price' => [
        //       'amount' => $detail->price_after_discount ?? 0,
        //       'currency' => 'SAR',
        //     ],
        //     'total_amount' => [
        //       'amount' =>(float)($detail->quantity * $detail->price_after_discount) ?? 0,
        //       'currency' => 'SAR',
        //     ],
        //   ];
        // })->toArray();

        // return [
        //   'id' => $order->id,
        //   "total_amount" => [
        //     "amount" =>$order->total,
        //     "currency" => "SAR"
        //   ],
        //   "shipping_amount" => [
        //     "amount" =>$order->delivery_fees,
        //     "currency" => "SAR"
        //   ],
        //   "tax_amount" => [
        //     "amount" => 0,
        //     "currency" => "SAR"
        //   ],
        //   "order_reference_id" => (string)$order->id . $order->order_number,
        //   "order_number" => (string)$order->order_number,
        //   "discount" => [
        //     "amount" => [
        //       "amount" => $order->discount,
        //       "currency" => "SAR"
        //     ],
        //     "name" => "discount"
        //   ],
        //   "items" =>$items,
        //   "consumer" => [
        //     "email" => $order->user->email,
        //     "first_name"=>$order->user->name,
        //     "last_name" => $order->user->name,
        //     "phone_number" => $order->user->phone
        //   ],
        //   "country_code" => "SA",
        //   "description" => "order description",
        //   "merchant_url" => [
        //     "cancel" => route('tamara_payment_url_cancel'),
        //     "failure" => route('tamara_payment_url_failure'),
        //     "success" => route('tamara_payment_url_success'),
        //     "notification" =>route('tamara_payment_url_notification')
        //   ],
        //   "payment_type" => "PAY_NOW",
        //   "instalments" => 3,
        //   // "billing_address" => [
        //   //   "city" => "Riyadh",
        //   //   "country_code" => "SA",
        //   //   "first_name" => "Mona",
        //   //   "last_name" => "Lisa",
        //   //   "line1" => "3764 Al Urubah Rd",
        //   //   "line2" => "string",
        //   //   "phone_number" => "532298658",
        //   //   "region" => "As Sulimaniyah"
        //   // ],
        //   "shipping_address" => [
        //     "city" => $order->address->city,
        //     "country_code" => "SA",
        //     "first_name" => $order->user->name,
        //     "last_name" => $order->user->name,
        //     "line1" => $order->address->address,
        //     "line2" => "",
        //     "phone_number" => $order->user->phone,
        //     "region" => ""
        //   ],
        //   // "platform" => "platform name here",
        //   // "is_mobile" => false,
        //   // "locale" => "en_US",
        //   // "risk_assessment" => [
        //   //   "customer_age" => 22,
        //   //   "customer_dob" => "31-01-2000",
        //   //   "customer_gender" => "Male",
        //   //   "customer_nationality" => "SA",
        //   //   "is_premium_customer" => true,
        //   //   "is_existing_customer" => true,
        //   //   "is_guest_user" => true,
        //   //   "account_creation_date" => "31-01-2019",
        //   //   "platform_account_creation_date" => "string",
        //   //   "date_of_first_transaction" => "31-01-2019",
        //   //   "is_card_on_file" => true,
        //   //   "is_COD_customer" => true,
        //   //   "has_delivered_order" => true,
        //   //   "is_phone_verified" => true,
        //   //   "is_fraudulent_customer" => true,
        //   //   "total_ltv" => 501.5,
        //   //   "total_order_count" => 12,
        //   //   "order_amount_last3months" => 301.5,
        //   //   "order_count_last3months" => 2,
        //   //   "last_order_date" => "31-01-2021",
        //   //   "last_order_amount" => 301.5,
        //   //   "reward_program_enrolled" => true,
        //   //   "reward_program_points" => 300,
        //   //   "phone_verified" => false
        //   // ],
        //   // "additional_data" => [
        //   //   "delivery_method" => "home delivery",
        //   //   "pickup_store" => "Store A",
        //   //   "store_code" => "Store code A",
        //   //   "vendor_amount" => 0,
        //   //   "merchant_settlement_amount" => 0,
        //   //   "vendor_reference_code" => "AZ1234"
        //   // ]
        // ];

        return json_decode(
          '{
            "total_amount": {
              "amount": 300,
              "currency": "SAR"
            },
            "shipping_amount": {
              "amount": 0,
              "currency": "SAR"
            },
            "tax_amount": {
              "amount": 0,
              "currency": "SAR"
            },
            "order_reference_id": "1231234123-abda-fdfe--afd31241",
            "order_number": "S12356",
            "discount": {
              "amount": {
                "amount": 200,
                "currency": "SAR"
              },
              "name": "Christmas 2020"
            },
            "items": [
              {
                "name": "Lego City 8601",
                "type": "Digital",
                "reference_id": "123",
                "sku": "SA-12436",
                "quantity": 1,
                "discount_amount": {
                  "amount": 100,
                  "currency": "SAR"
                },
                "tax_amount": {
                  "amount": 10,
                  "currency": "SAR"
                },
                "unit_price": {
                  "amount": 490,
                  "currency": "SAR"
                },
                "total_amount": {
                  "amount": 100,
                  "currency": "SAR"
                }
              }
            ],
            "consumer": {
              "email": "customer@email.com",
              "first_name": "Mona",
              "last_name": "Lisa",
              "phone_number": "566027755"
            },
            "country_code": "SA",
            "description": "lorem ipsum dolor",
            "merchant_url": {
              "cancel": "http://awesome-qa-tools.s3-website.me-south-1.amazonaws.com/#/cancel",
              "failure": "http://awesome-qa-tools.s3-website.me-south-1.amazonaws.com/#/fail",
              "success": "http://awesome-qa-tools.s3-website.me-south-1.amazonaws.com/#/success",
              "notification": "https://store-demo.com/payments/tamarapay"
            },
            "payment_type": "PAY_BY_INSTALMENTS",
            "instalments": 3,
            "billing_address": {
              "city": "Riyadh",
              "country_code": "SA",
              "first_name": "Mona",
              "last_name": "Lisa",
              "line1": "3764 Al Urubah Rd",
              "line2": "string",
              "phone_number": "532298658",
              "region": "As Sulimaniyah"
            },
            "shipping_address": {
              "city": "Riyadh",
              "country_code": "SA",
              "first_name": "Mona",
              "last_name": "Lisa",
              "line1": "3764 Al Urubah Rd",
              "line2": "string",
              "phone_number": "532298658",
              "region": "As Sulimaniyah"
            },
            "platform": "platform name here",
            "is_mobile": false,
            "locale": "en_US",
            "risk_assessment": {
              "customer_age": 22,
              "customer_dob": "31-01-2000",
              "customer_gender": "Male",
              "customer_nationality": "SA",
              "is_premium_customer": true,
              "is_existing_customer": true,
              "is_guest_user": true,
              "account_creation_date": "31-01-2019",
              "platform_account_creation_date": "string",
              "date_of_first_transaction": "31-01-2019",
              "is_card_on_file": true,
              "is_COD_customer": true,
              "has_delivered_order": true,
              "is_phone_verified": true,
              "is_fraudulent_customer": true,
              "total_ltv": 501.5,
              "total_order_count": 12,
              "order_amount_last3months": 301.5,
              "order_count_last3months": 2,
              "last_order_date": "31-01-2021",
              "last_order_amount": 301.5,
              "reward_program_enrolled": true,
              "reward_program_points": 300,
              "phone_verified": false
            },
            "additional_data": {
              "delivery_method": "home delivery",
              "pickup_store": "Store A",
              "store_code": "Store code A",
              "vendor_amount": 0,
              "merchant_settlement_amount": 0,
              "vendor_reference_code": "AZ1234"
            }
          }',
          true);
      
    } catch (\Throwable $exception) {
      Log::error($exception->getMessage());
      throw $exception;
    }
  }

}
