<?php

namespace SmsNotifier\Admin;

use SmsNotifier\Admin\SmsProviders;

/**
 * Generate settings view
 */
class SettingsGenerator
{
    public function __construct()
    {
    }

    /**
     * Create settings field array
     * and load the dynamic settings view
     */
    public function show_fields()
    {
        $smsProviders = new SmsProviders();
        // get sms providers data
        $allProviderData = $smsProviders->sms_notifier_providers();
        // create gateway provider for select box
        $gatewayFields = array(
            array(
                'type'  => 'select',
                'name'  => 'gateway',
                'label' => 'SMS Gateway',
                'desc'    => 'Choose your SMS Gateway',
                'provider' => 'gateway',
                'options' => array()
            )
        );
        // add option field for the gateway provider
        foreach ($allProviderData as $key => $provider) {
            $gatewayFields[0]['options'][$provider['value']] = $provider['name'];
            foreach ($provider['fields'] as $index => $field) {
                $field['provider'] = $provider['value'];
                array_push($gatewayFields, $field);
            }
        }
        // order status select box
        $orderStatuses = wc_get_order_statuses();

        foreach ($orderStatuses as $key => $status) {
            $allowedOrder = array(
                'type'  => 'checkbox',
                'name'  => "allowed_status-$key",
                'label' => "Order Status $status",
                'desc'    => 'Choose order status for which you want to send sms',
                'provider' => 'general', //general provider is for static setting fields
            );
            array_push($gatewayFields, $allowedOrder);
        }

        // load settings view
        $this->load_view($gatewayFields);
    }

    /**
     * Load settings view
     * @param {Array} $gatewayFields - Settings fields array data
     */
    public function load_view($gatewayFields)
    {
        if (!empty($_POST) && check_admin_referer('nonce_sms_notifier_settings', 'sms_notifier_settings')) {

            $settings_values = [];
            foreach ($gatewayFields as $key => $value) {
                if(isset($gatewayFields[$key]['name']) && !empty($gatewayFields[$key]['name'])) {
                    $settings_key = sanitize_key($gatewayFields[$key]['name']);
                    $settings_value = isset($_POST[$settings_key]) && !empty($_POST[$settings_key]) ? sanitize_text_field($_POST[$settings_key]) : '';
                    $settings_values[$settings_key] = $settings_value;
                }
            }

            update_option('sms_notifier_settings', $settings_values);
        }
        $sms_notifier_settings = get_option('sms_notifier_settings', true);
        $provider = 'nexmo';
        $gateway = $this->get_option_value($sms_notifier_settings, 'gateway');
        if ($gateway) {
            $provider = $gateway;
        }
?>
        <div class="rq-sms-notifier-settings-wrap">
            <h1 class="la-page-title"><?php esc_html_e('SMS Notifier Settings', 'sms-notifier') ?></h1>
            <div class="rq-sms-notifier-instruction-block">
                <!-- load instructions view here for each provider -->
            </div>
            <div class="rq-sms-notifier-video-instruction-block">
                <!-- load video iframe view here -->
            </div>
            <!-- start dynamic settings form -->
            <form method="post">
                <!-- <div class="form-table rq-sms-notifier-settings-from">
                    <h4 class="la-page-title-notice">
                        <?php echo esc_html__('Settings Form :', 'sms-notifier'); ?>
                    </h4>
                </div> -->
                <?php
                foreach ($gatewayFields as $key => $field) {
                    $selectedProvider = $provider !== $field['provider'] && $field['provider'] !== 'gateway' && $field['provider'] !== 'general' ? 'display:none' : 'display:block';
                ?>
                    <div style=<?php esc_html_e($selectedProvider, 'sms-provider')  ?> data-id="<?php esc_html_e($field['provider'], 'sms-notifier') ?>" class="la-input-group">
                        <div class="la-input-wrapper">

                            <!-- Start field title on the left side -->
                            <span class="la-input-title">
                                <?php esc_html_e($field['label'], 'sms-notifier') ?>
                            </span>
                            <!-- End field title on the left side -->

                            <!-- Start dynamic field type input on the right side -->
                            <div class="la-input-field">
                                <?php $this->generate_fields($sms_notifier_settings, $field); ?>
                            </div>
                            <!-- End dynamic field type input on the right side -->

                        </div>
                    </div>

                <?php
                }
                ?>
                <?php
                wp_nonce_field('nonce_sms_notifier_settings', 'sms_notifier_settings');
                ?>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e("Save Changes", 'sms-notifier') ?>">
                </p>
            </form>
            <!-- end dynamic settings form -->
        <?php
    }

    public function generate_fields($settings, $args = array())
    {
        extract($args);
        $std = '';
        $id = $name;
        $value = esc_attr($this->get_option_value($settings, $name, ''));

        switch ($type) {

            case 'checkbox':
                echo '<input class="checkbox" type="checkbox"  id="' . esc_attr($name) . '" name="' . esc_attr($name) . '"   value="1" ' . checked($value, 1, false) . ' /> <label for="' . esc_attr($id) . '">' . esc_html($desc) . '</label>';
                break;

            case 'select':
                echo '<select class="select"  id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" >';
                foreach ($options as $key => $label) {
                    echo '<option value="' . esc_attr($key) . '"' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';

                if ($desc != '')
                    echo '<br /><span class="description">' . esc_html($desc) . '</span>';
                break;

            case 'radio':
                $i = 0;
                foreach ($options as $key => $label) {
                    echo '<input class="radio" type="radio" id="' . esc_attr($name) . '[' . esc_attr($key) . ']" name="' . esc_attr($name) . '"   value="' . esc_attr($key) . '" ' . checked($value, $key, false) . '> <label for="' . esc_attr($name) . '[' . esc_attr($key) . ']" >' . esc_html($label) . '</label>';
                    if ($i < count($options) - 1)
                        echo '<br /> <br/>';
                    $i++;
                }
                if ($desc != '')
                    echo '<br /><span class="description">' . esc_html($desc) . '</span>';
                break;

            case 'textarea':
                echo '<textarea class=""  id="' . esc_attr($name) . '" name="' . esc_attr($name) . '"    placeholder="' . esc_attr($std) . '" rows="5" cols="30">' . esc_html($value) . '</textarea>';
                if ($desc != '')
                    echo '<br /><span class="description">' . esc_html($desc) . '</span>';
                break;

            case 'password':
                echo '<input class="regular-text" type="password" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" placeholder="' . esc_attr($std) . '" value="' . esc_attr($value) . '" />';
                if ($desc != '')
                    echo '<br /><span class="description">' . esc_html($desc) . '</span>';
                break;

            case 'text':

            default:
                echo '<input class="regular-text" type="text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" placeholder="' . esc_attr($std) . '" value="' . esc_attr($value) . '" />';
                if ($desc != '')
                    echo '<br /><span class="description">' . esc_html($desc) . '</span>';
                break;
        }
    }

    /**
     * Get isset value from options
     * @param {array} $options - settings option values
     * @param {string}
     */
    function get_option_value($options, $name, $default = '')
    {
        if (isset($options[$name])) {
            return $options[$name];
        }
        return $default;
    }

    function show_quick_sms_view()
    {
        ?>
            <div class="rq-sms-notifier-settings-wrap rq-quick-sms-area">
                <h1 class="la-page-title"><?php esc_html_e('Send Quick SMS', 'sms-notifier') ?></h1>
                <div class="la-input-group">
                    <div class="la-input-wrapper">
                        <span class="la-input-title">
                            <?php esc_html_e('Number', 'sms-notifier') ?>
                        </span>
                        <div class="la-input-field">
                            <input type="text" id="quick-sms-number" name="quick-sms-number" placeholder="<?php esc_html_e('+17084122957', 'sms-notifier') ?>" />
                        </div>
                    </div>
                </div>
                <div class="la-input-group">
                    <div class="la-input-wrapper">
                        <span class="la-input-title">
                            <?php esc_html_e('Message', 'sms-notifier') ?>
                        </span>
                        <div class="la-input-field">
                            <textarea type="textarea" id="message-text" name="message-text" rows="4" cols="50" placeholder="<?php esc_html_e('Enter your message', 'sms-notifier') ?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="submit">
                    <button type="submit" id="send-message" class="button button-primary rq-sms-btn">
                        <span class="rq-sms-btn-text">
                            <?php esc_html_e("Send", 'sms-notifier') ?>
                        </span>
                        <span class="rq-sms-mini-loader">
                            <span class="dot1"></span>
                            <span class="dot2"></span>
                            <span class="dot3"></span>
                        </span>
                    </button>
                </div>
            </div>
    <?php
    }
}
