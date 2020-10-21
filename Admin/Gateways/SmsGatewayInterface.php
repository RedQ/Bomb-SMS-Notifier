<?php

namespace SmsNotifier\Admin\Gateways;

interface SmsGatewayInterface
{

	/**
	 * Create Client for SMS GateWay
	 * @method createClient
	 * @param  Array $credentials - api_key, api_secret based on sms gateway
	 * @return Object $client - Client for SMS gateway
	 */
	// public function __construct($credentials);


	/**
	 * Send SMS using SMS GateWay
	 * @method sendSms
	 * @param  Array $credentials - api_key, api_secret based on sms gateway
	 * @param String $phoneNumber - phone number where sms will be sent
	 * @param String $message - message text for the sms
	 * @return Object $response - send sms response by the gateway
	 */
	public function sendSms($credentials, $phoneNumber, $message);
}
