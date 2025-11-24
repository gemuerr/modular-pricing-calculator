<?php
/**
 * Plugin Name: Modular Pricing Calculator
 * Plugin URI: https://example.com
 * Description: Configure modular pricing based on subscription model, day duration, and number of days with accordion toggle, variable pricing, and reCAPTCHA protection
 * Version: 0.14.0
 * Author: Johannes Gemürr and Claude. Mainly Claude.
 * Author URI: https://www.hinterlandforefront.de
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: modular-pricing-calculator
 * Changelog: See CHANGELOG.md
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('MODULAR_PRICING_PLUGIN_VERSION')) {
    define('MODULAR_PRICING_PLUGIN_VERSION', '0.14.0');
}

if (!defined('MODULAR_PRICING_PLUGIN_DIR')) {
    define('MODULAR_PRICING_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

require_once MODULAR_PRICING_PLUGIN_DIR . 'includes/class-modular-pricing-settings.php';
require_once MODULAR_PRICING_PLUGIN_DIR . 'includes/class-modular-pricing-admin.php';
require_once MODULAR_PRICING_PLUGIN_DIR . 'includes/class-modular-pricing-frontend.php';
require_once MODULAR_PRICING_PLUGIN_DIR . 'includes/class-modular-pricing-ajax.php';
require_once MODULAR_PRICING_PLUGIN_DIR . 'includes/class-modular-pricing-activator.php';

function modular_pricing_calculator_bootstrap() {
    Modular_Pricing_Settings::init();
    new Modular_Pricing_Admin();
    new Modular_Pricing_Frontend();
    new Modular_Pricing_Ajax();
}
modular_pricing_calculator_bootstrap();

register_activation_hook(__FILE__, array('Modular_Pricing_Activator', 'activate'));
