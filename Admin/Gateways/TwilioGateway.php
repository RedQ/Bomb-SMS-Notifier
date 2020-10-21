<?php

namespace SmsNotifier\Admin\Gateways;

use SmsNotifier\Admin\Gateways\SmsGatewayInterface;
use Twilio\Rest\Client;
use Exception;

class TwilioGateway extends ErrorResponse implements SmsGatewayInterface
{

	public $twilioClient;

	public function __construct($credentials)
	{
		$this->twilioClient = new Client($credentials['twilio_account_sid'], $credentials['twilio_auth_token']);
	}

	public function sendSms($credentials, $phoneNumber, $message)
	{
		try {
			// Use the client to do fun stuff like send text messages!
			$response = $this->twilioClient->messages->create(
				// the number you'd like to send the message to
				$phoneNumber,
				[
					// A Twilio phone number you purchased at twilio.com/console
					'from' => $credentials['twilio_phone_number'],
					// the body of the text message you'd like to send
					'body' => $message
				]
			);

			return $this->getResponse($response);
		} catch (Exception $e) {
			return $this->getError($e);
		}
	}
}
