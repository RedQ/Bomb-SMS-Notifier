<?php

namespace SmsNotifier\Admin;

/**
 * Basic functionality provided for SMS Gateway
 */
trait SmsGatewayInfo
{
    /**
     * Get WooCommerce order status list
     * @return Array $orderStatus - order status list
     */
    public function getOrderStatus()
    {
        $orderStatuses = [];
        $statusData = wc_get_order_statuses();
        foreach ($statusData as $key => $value) {
            $settingsKey = "allowed_status-$key";
            $settingsVal = $this->get_option_value($settingsKey);
            if (isset($settingsVal) && !empty($settingsVal)) {
                array_push($orderStatuses, str_replace('wc-', '', $key));
            }
        }
        return $orderStatuses;
    }

    /**
     * Get all settings option data
     * @param string $key - saved settings key
     * @return array $options - settings data
     */
    public function get_settings_option($key = 'sms_notifier_settings')
    {
        $options = get_option($key);
        return $options;
    }

    /**
     * Get isset value from options
     * @param {array} $options - settings option values
     * @param {string}
     */
    function get_option_value($key, $default = '')
    {
        $options = $this->get_settings_option();

        if (isset($options[$key]) && !empty($options[$key])) {
            return $options[$key];
        }
        return $default;
    }

    /**
     * Get selected sms gateway
     * @method get_active_sms_gateway
     * @return string $gateway - sms gateway
     */
    public function get_active_sms_gateway()
    {
        return $this->get_option_value('gateway');
    }

    /**
     * Get selected SMS Gateway credentials
     * @param string $gateway - selected gateway
     * @return array @credentials - gateway setting values
     */
    public function get_sms_gateway_credentials($gateway)
    {
        switch ($gateway) {
            case 'nexmo':
                $args = ['nexmo_key', 'nexmo_secret_key', 'nexmo_sender_name'];
                return $this->get_credentials($args);
                break;
            case 'twilio':
                $args = ['twilio_account_sid', 'twilio_auth_token', 'twilio_phone_number'];
                return $this->get_credentials($args);
                break;
            default:
                return [];
        }
    }

    /**
     * Get credentials for a gateway
     * @param array $args - array of gateway setting keys
     * @return array $credentials - key value pair of credentials
     */
    private function get_credentials($args)
    {
        $credentials = [];
        foreach ($args as $arg) {
            $credential = $this->get_option_value($arg);
            $credentials[$arg] = $credential;
        }
        return $credentials;
    }

    /**
     * Get phone number of the order user
     * @param object $order - WooCommerce order data
     * @return string $phone_number - billing phone number of the user
     */
    public function get_order_phone_number($order)
    {
        $phone_number = method_exists($order, 'get_billing_phone') ? $order->get_billing_phone() : $order->billing_phone;
        return $phone_number;
    }

    /**
     * Get order message
     * @param $orderId - WooCommerce order id
     * @param $orderStatus - WooCommerce order status
     * @return $message - Message text
     */
    public function get_order_message($orderId, $orderStatus)
    {
        // TODO: provide option to change message based on order status
        $message =  "Your order #" . $orderId . " status has been updated to " . $orderStatus;
        return $message;
    }

    /**
     * Add WooCommerce order note
     * @param Array $response - response contains status(true/false) and error text
     * @param Object $order - order data
     * @param String $orderStatus - order status
     * @param String $phoneNumber - user phone number
     */
    public function add_order_note($response, $order, $orderStatus, $phoneNumber)
    {
        $orderNote = "Unable to notify customer on order status change via SMS.";
        if (isset($response['status']) && !empty($response['status'])) {
            $orderNote = "Customer notified on order status change to " . $orderStatus . " via SMS (" . $phoneNumber . ")";
        }
        if (isset($response['error']) && !empty($response['error'])) {
            $orderNote = "Unable to notify customer on order status change via SMS. Error:" . $response['error'];
        }
        $order->add_order_note($orderNote);
    }
}
