<?php

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Settings {
    const OPTION_NAME = 'modular_pricing_settings';

    public static function init() {
        add_action('admin_init', array(__CLASS__, 'register_settings'));
    }

    public static function register_settings() {
        register_setting('modular_pricing_group', self::OPTION_NAME);
    }

    public static function get_option_name() {
        return self::OPTION_NAME;
    }

    public static function get_settings() {
        return get_option(self::OPTION_NAME, self::get_default_settings());
    }

    public static function get_default_settings() {
        return array(
            'model_a_name' => 'Fix-Abo',
            'model_b_name' => 'Flex-Abo',
            'model_a_half_1' => 30,
            'model_a_half_2' => 28,
            'model_a_half_3' => 26,
            'model_a_half_4' => 24,
            'model_a_half_5' => 22,
            'model_a_full_1' => 50,
            'model_a_full_2' => 48,
            'model_a_full_3' => 46,
            'model_a_full_4' => 44,
            'model_a_full_5' => 42,
            'model_b_half_1' => 35,
            'model_b_half_2' => 33,
            'model_b_half_3' => 31,
            'model_b_half_4' => 29,
            'model_b_half_5' => 27,
            'model_b_full_1' => 55,
            'model_b_full_2' => 53,
            'model_b_full_3' => 51,
            'model_b_full_4' => 49,
            'model_b_full_5' => 47,
            'currency' => '€',
            'form_display_mode' => 'accordion',
            'notification_email' => get_option('admin_email'),
            'recaptcha_enabled' => 0,
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => '',
            'primary_color' => '#4a90e2',
            'primary_hover_color' => '#357abd'
        );
    }
}

