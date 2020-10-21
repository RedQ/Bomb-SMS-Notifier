<?php

namespace SmsNotifier\Admin;

/**
 * SMS Integration providers
 */
class SmsProviders
{
    public function __construct()
    {
        add_filter('sms_notifier_providers', array($this, 'sms_notifier_providers'), 10, 1);
    }

    /**
     *
     * Provider Lists
     *
     * Add data to the providers via add_filter hook
     * provider 'name', 'value', 'fields' are required
     * fields must have type, name, label, desc key
     * fields must have options for dropdown
     * Check SettingsGenrator class generate_fields method to learn more about
     * supported fields
     *
     * @author redqteam
     * @version  1.0
     * @since  1.0
     *
     * @param $providers
     * @return $providers
     *
     */
    public function sms_notifier_providers($providers = array())
    {
        $providers = array(

            // Start Nexmo provider
            array(
                'name'  => 'Nexmo',
                'value' => 'nexmo',
                'iframe' => '',
                'fields' => array(
                    array(
                        'type'  => 'text',
                        'name'  => 'nexmo_key',
                        'label' => 'Nexmo Key',
                        'desc'  => 'Your Nexmo Account Key'
                    ),
                    array(
                        'type'  => 'text',
                        'name'  => 'nexmo_secret_key',
                        'label' => 'Nexmo Secret Key',
                        'desc'  => 'Your Nexmo Account Secret Key'
                    ),
                    array(
                        'type'  => 'text',
                        'name'  => 'nexmo_sender_name',
                        'label' => 'Enter SMS Sender Name',
                        'desc'  => 'Sender name or number on the SMS'
                    ),
                )
            ),
            // End Nexmo provider

            // Start Twilio provider
            array(
                'name'  => 'Twilio',
                'value' => 'twilio',
                'iframe' => '',
                'fields' => array(
                    array(
                        'type'  => 'text',
                        'name'  => 'twilio_account_sid',
                        'label' => 'Twilio Account SID',
                        'desc'  => 'Your Twilio Account SID'
                    ),
                    array(
                        'type'  => 'password',
                        'name'  => 'twilio_auth_token',
                        'label' => 'Twilio Auth Token',
                        'desc'  => 'Your Twilio Auth Token'
                    ),
                    array(
                        'type'  => 'text',
                        'name'  => 'twilio_phone_number',
                        'label' => 'Twilio Phone Number',
                        'desc'  => 'Twilio phone number (SMS sender number)'
                    ),
                )
            ),
            // End Twilio provider

        );
        return $providers;
    }
}
new SmsProviders();
