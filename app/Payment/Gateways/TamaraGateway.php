<?php

namespace App\Payment\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;


class TamaraGateway implements PaymentGatewayInterface
{
  protected $client;
  protected $apiKey;
  protected $apiUrl;

  public function __construct()
  {
    $this->client = new Client();
    $this->apiUrl = app()->environment('local') ? env('TAMARA_API_URL_TEST') : env('TAMARA_API_URL');
    $this->apiKey = env('TAMARA_MERCHANT_KEY');
  }


  public function createSession($order)
  {
    $client = new Client();
    $response = $client->post("{$this->apiUrl}/checkout", [
      'headers' => [
        'Authorization' => "Bearer {$this->apiKey}",
        'Content-Type' => 'application/json',
      ],
      'json' =>  $order
    ]);
    return json_decode($response->getBody(), true);
  }

  public function getSession($payment_id)
  {
    $http = Http::withToken($this->apiKey)->baseUrl($this->apiUrl);

    $url = 'checkout/' . $payment_id;

    $response = $http->get($url);

    return $response->object();
  }

}
