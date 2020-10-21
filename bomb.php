<?php
/*
* Plugin Name: Bomb - SMS Notifier
* Plugin URI: https://redq.io
* Description: WooCommerce order notification plugin.
* Version: 1.0.0
* Author: RedQ, Inc
* Author URI: https://redq.io
* Requires at least: 4.7
* Tested up to: 5.5.1
*
* Text Domain: sms-notifier
* Domain Path: /languages/
*
* Copyright: © 2012-2020 RedQ,Inc.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
*/


/**
 * Class SmsNotifier
 */
class SmsNotifier
{

	/**
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * @create instance on self
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	public function __construct()
	{
		if (!defined('SMS_NOTIFIER_REQUIRED_PHP_VERSION')) {
			define('SMS_NOTIFIER_REQUIRED_PHP_VERSION', 5.6);
		}
		if (!defined('SMS_NOTIFIER_REQUIRED_WP_VERSION')) {
			define('SMS_NOTIFIER_REQUIRED_WP_VERSION', 4.5);
		}
		add_action('admin_init', array($this, 'check_version'));
		if (!self::compatible_version()) {
			return;
		}
		$this->sms_notifier_load_all_classes();
		$this->sms_notifier_app_bootstrap();
		add_action('plugins_loaded', array($this, 'sms_notifier_language_textdomain'), 1);
	}

	public function sms_notifier_load_all_classes()
	{
		include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
	}

	/**
	 *  App Bootstrap
	 *  Fire all class
	 */
	public function sms_notifier_app_bootstrap()
	{
		/**
		 * Define plugin constant
		 */
		define('SMS_NOTIFIER_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
		define('SMS_NOTIFIER_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
		define('SMS_NOTIFIER_FILE', __FILE__);
		define('SMS_NOTIFIER_CSS', SMS_NOTIFIER_URL . '/assets/css/');
		define('SMS_NOTIFIER_JS', SMS_NOTIFIER_URL . '/assets/js/');
		new SmsNotifier\Admin\Settings();
		new SmsNotifier\Admin\SmsGatewayInit();
		new SmsNotifier\Admin\AsyncHandler();
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function template_path()
	{
		return apply_filters('sms_notifier_template_path', 'sms-notifier/');
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_path()
	{
		return untrailingslashit(plugin_dir_path(__FILE__));
	}

	/**
	 * Get the plugin textdomain for multilingual.
	 * @return null
	 */
	public function sms_notifier_language_textdomain()
	{
		load_plugin_textdomain('sms-notifier', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	static function compatible_version()
	{
		if (phpversion() < SMS_NOTIFIER_REQUIRED_PHP_VERSION || $GLOBALS['wp_version'] < SMS_NOTIFIER_REQUIRED_WP_VERSION) return false;
		return true;
	}

	// The backup sanity check, in case the plugin is activated in a weird way,
	// or the versions change after activation.
	public function check_version()
	{
		if (!self::compatible_version()) {
			if (is_plugin_active(plugin_basename(__FILE__))) {
				deactivate_plugins(plugin_basename(__FILE__));
				add_action('admin_notices', array($this, 'disabled_notice'));
				if (isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
			}
		}
	}

	public function disabled_notice()
	{
		if (phpversion() < SMS_NOTIFIER_REQUIRED_PHP_VERSION) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e('SMS Notifier requires PHP ' . SMS_NOTIFIER_REQUIRED_PHP_VERSION . ' or higher!', 'sms-notifier'); ?></p>
			</div>
		<?php
		}
		if ($GLOBALS['wp_version'] < SMS_NOTIFIER_REQUIRED_WP_VERSION) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e('SMS Notifier requires Wordpress ' . SMS_NOTIFIER_REQUIRED_WP_VERSION . ' or higher!', 'sms-notifier'); ?></p>
			</div>
<?php
		}
	}
}

/**
 * @return null|SmsNotifier
 */
function SmsNotifier()
{

	return SmsNotifier::instance();
}

$GLOBALS['sms_notifier'] = SmsNotifier();
