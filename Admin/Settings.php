<?php

namespace SmsNotifier\Admin;

use SmsNotifier\Admin\SettingsGenerator;

class Settings
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'add_scripts'));
        add_action('admin_menu', array($this, 'sms_notifier_settings_panel'));
    }

    public function add_scripts()
    {
        wp_enqueue_script('sms-notifier', SMS_NOTIFIER_JS . 'sms-notifier.js', array('jquery', 'underscore'), false, true);
        wp_enqueue_style('sms-notifier', SMS_NOTIFIER_CSS . 'sms-notifier.css',  array(), false, 'all');
        wp_localize_script('sms-notifier', 'SMS_NOTIFIER_DATA', array(
            'action'        => 'sms_notifier_ajax',
            'nonce'         => wp_create_nonce('sms_notifier_ajax_nonce'),
            'ajaxUrl'      => admin_url('admin-ajax.php'),
        ));
    }

    public function sms_notifier_settings_panel()
    {
        $page_title = esc_html__('SMS Notifier', 'sms-notifier');
        $menu_title = esc_html__('SMS Notifier', 'sms-notifier');
        $capability = 'manage_options';
        $menu_slug  = 'sms-notifier-settings';
        $function   = array($this, 'sms_notifier_settings_func');
        $icon_url   = 'dashicons-media-code';
        $position   = 80;
        add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            $icon_url,
            $position
        );

        add_submenu_page(
            $menu_slug,
            'Quick SMS',
            'Quick SMS',
            'manage_options',
            'sms-notifier-quick-sms',
            array($this, 'sms_notifier_quick_sms_func')
        );
    }

    public function sms_notifier_settings_func()
    {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_attr__('You do not have sufficient permissions to access this page.', 'sms-notifier')
            );
        }
        $settingsGenerator = new SettingsGenerator();
        $settingsGenerator->show_fields();
    }

    public function sms_notifier_quick_sms_func()
    {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_attr__('You do not have sufficient permissions to access this page.', 'sms-notifier')
            );
        }
        $settingsGenerator = new SettingsGenerator();
        $settingsGenerator->show_quick_sms_view();
    }
}
