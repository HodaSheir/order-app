<?php

namespace App\Payment\Gateways;

interface PaymentGatewayInterface
{
  public function createSession(array $data);
  public function getSession($sessionId);

}














?>

