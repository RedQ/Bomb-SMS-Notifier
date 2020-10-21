<?php

namespace SmsNotifier\Admin;

use SmsNotifier\Admin\Gateways\SmsGateway;

class AsyncHandler
{
    use SmsGatewayInfo;

    /**
     * Action hook used by the AJAX class.
     *
     * @var string
     */
    const ACTION = 'sms_notifier_ajax';
    /**
     * Action argument used by the nonce validating the AJAX request.
     *
     * @var string
     */
    const NONCE = 'sms_notifier_ajax_nonce';
    /**
     * AsyncHandler constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_' . self::ACTION, array($this, 'handle_ajax'));
        add_action('wp_ajax_nopriv_' . self::ACTION, array($this, 'handle_ajax'));
    }
    public function handle_ajax()
    {
        check_ajax_referer(self::NONCE, 'nonce');

        $actionType = isset($_POST['action_type']) && !empty($_POST['action_type']) ? sanitize_key($_POST['action_type']) : false;
        $phoneNumber = isset($_POST['phone_number']) && !empty($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : false;
        $message = isset($_POST['message']) && !empty($_POST['message']) ? sanitize_textarea_field($_POST['message']) : false;

        switch ($actionType) {
            case 'quick_sms':
                /** check if user is available */
                $this->gateway = $this->get_active_sms_gateway();

                if (!empty($this->gateway)) {
                    $this->credentials = $this->get_sms_gateway_credentials($this->gateway);
                    $gateWayClass = "SmsNotifier\\Admin\\Gateways\\" . ucfirst($this->gateway) . 'Gateway';
                    $this->smsGateway = new SmsGateway(new $gateWayClass($this->credentials));
                    $response = $this->smsGateway->sendSms($this->credentials, $phoneNumber, $message);
                    echo json_encode(array('status' => 200, 'response' => $response, 'message' => esc_html__('Successful', 'sms-notifier')));
                } else {
                    echo json_encode(array('status' => 400, 'response' => '', 'message' => esc_html__('Failed! gateway not found', 'sms-notifier')));
                }
                break;
        }
        wp_die();
    }
}
