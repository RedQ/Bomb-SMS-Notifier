<?php

namespace SmsNotifier\Admin\Gateways;

/**
 * SmsGateway class
 * Bind to SmsGatewayInterface
 */
class SmsGateway
{
    private $gateway;

    public function __construct(SmsGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    public function sendSms($credentials, $phoneNumber, $message)
    {
        return $this->gateway->sendSms($credentials, $phoneNumber, $message);
    }
}
