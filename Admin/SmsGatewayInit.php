<?php

namespace SmsNotifier\Admin;

use SmsNotifier\Admin\Gateways\SmsGateway;

/**
 * SMS Gateway Handler for WooCommerce
 * use SmsGatewayInfo trait
 */

class SmsGatewayInit
{
    use SmsGatewayInfo;

    public $gateway;

    public $credentials;

    public $smsGateway;

    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }


    /**
     * Add order status action
     */
    public function init()
    {
        $this->gateway = $this->get_active_sms_gateway();
        if (!empty($this->gateway)) {
            $orderStatus = $this->getOrderStatus();
            foreach ($orderStatus as $status) {
                add_action('woocommerce_order_status_' . $status, array($this, 'triggerOrderSms'));
            }
        }
    }

    /**
     * Initialize SMS Gateway on
     * Trigger WooCommerce order status action
     * @param string - WooCommerce order id
     */
    public function triggerOrderSms($orderId)
    {
        $order = wc_get_order($orderId);
        $order_status = $order->get_status();
        $phone_number   = $this->get_order_phone_number($order);
        $message =  $this->get_order_message($orderId, $order_status);
        $this->credentials = $this->get_sms_gateway_credentials($this->gateway);
        $gateWayClass = "SmsNotifier\\Admin\\Gateways\\" . ucfirst($this->gateway) . 'Gateway';
        $this->smsGateway = new SmsGateway(new $gateWayClass($this->credentials));
        $response = $this->smsGateway->sendSms($this->credentials, $phone_number, $message);
        $this->add_order_note($response, $order, $order_status, $phone_number);
    }
}
