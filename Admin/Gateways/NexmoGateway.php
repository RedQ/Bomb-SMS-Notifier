<?php

namespace SmsNotifier\Admin\Gateways;

use SmsNotifier\Admin\Gateways\SmsGatewayInterface;
use Nexmo;
use Exception;

class NexmoGateway extends ErrorResponse implements SmsGatewayInterface
{

    public $nexmoClient;

    public function __construct($credentials)
    {
        $this->nexmoClient = new Nexmo\Client(new Nexmo\Client\Credentials\Basic($credentials['nexmo_key'], $credentials['nexmo_secret_key']));
    }

    public function sendSms($credentials, $phoneNumber, $message)
    {
        try {
            $message = $this->nexmoClient->message()->send([
                'to' => (int)$phoneNumber,
                'from' => $credentials['nexmo_sender_name'],
                'text' => $message
            ]);
            $response = $message->getResponseData();

            if ($response['messages'][0]['status'] == 0) {
                return $this->getResponse(true);
            } else {
                $message =  $response['messages'][0]['status'];
                return $this->getResponse(false, $message);
            }
        } catch (Exception $e) {
            return $this->getError($e);
        }
    }
}
