<?php

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Ajax {
    public function __construct() {
        add_action('wp_ajax_save_pricing_configuration', array($this, 'save_pricing_configuration'));
        add_action('wp_ajax_nopriv_save_pricing_configuration', array($this, 'save_pricing_configuration'));
        add_action('wp_ajax_update_pricing_status', array($this, 'update_pricing_status'));
        add_action('wp_ajax_bulk_delete_pricing_configs', array($this, 'bulk_delete_pricing_configs'));
    }

    public function save_pricing_configuration() {
        $settings = Modular_Pricing_Settings::get_settings();

        if (!empty($settings['recaptcha_enabled']) && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key'])) {
            $recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : '';

            if (empty($recaptcha_response) || !$this->verify_recaptcha($recaptcha_response, $settings['recaptcha_secret_key'])) {
                wp_send_json_error(array('message' => 'reCAPTCHA verification failed. Please try again.'));
                return;
            }
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';

        $customer_name = sanitize_text_field(wp_unslash($_POST['customer_name']));
        $customer_email = sanitize_email(wp_unslash($_POST['customer_email']));
        $customer_phone = sanitize_text_field(wp_unslash($_POST['customer_phone']));
        $dog_name = sanitize_text_field(wp_unslash($_POST['dog_name']));
        $subscription_model = sanitize_text_field(wp_unslash($_POST['subscription_model']));
        $duration = sanitize_text_field(wp_unslash($_POST['duration']));
        $selected_days = sanitize_text_field(wp_unslash($_POST['selected_days']));
        $monthly_price = sanitize_text_field(wp_unslash($_POST['monthly_price']));
        $notes = sanitize_textarea_field(wp_unslash($_POST['notes']));

        $wpdb->insert(
            $table_name,
            array(
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'dog_name' => $dog_name,
                'subscription_model' => $subscription_model,
                'duration' => $duration,
                'selected_days' => $selected_days,
                'monthly_price' => $monthly_price,
                'notes' => $notes,
                'status' => 'Neu',
                'created_at' => current_time('mysql')
            )
        );

        $this->send_notification_email($settings, array(
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'dog_name' => $dog_name,
            'subscription_model' => $subscription_model,
            'duration' => $duration,
            'selected_days' => $selected_days,
            'monthly_price' => $monthly_price,
            'notes' => $notes,
        ));

        wp_send_json_success(array('message' => 'Configuration saved successfully!'));
    }

    private function verify_recaptcha($response, $secret_key) {
        if (empty($secret_key)) {
            return true;
        }

        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        $verify_response = wp_remote_post($verify_url, array(
            'body' => array(
                'secret' => $secret_key,
                'response' => $response
            )
        ));

        if (is_wp_error($verify_response)) {
            return false;
        }

        $response_body = wp_remote_retrieve_body($verify_response);
        $result = json_decode($response_body);

        return isset($result->success) && true === $result->success;
    }

    private function send_notification_email($settings, $data) {
        $notification_email = isset($settings['notification_email']) ? $settings['notification_email'] : '';

        if (empty($notification_email)) {
            return;
        }

        $subject = 'Neue Betreuungsanfrage von ' . $data['customer_name'];
        $received_date = current_time('d.m.Y');
        $received_time = current_time('H:i');

        $message_lines = array(
            'Neue Betreuungsanfrage',
            '',
            'Kontaktdaten',
            'Name: ' . $data['customer_name'],
            'E-Mail: ' . $data['customer_email']
        );

        if (!empty($data['customer_phone'])) {
            $message_lines[] = 'Telefon: ' . $data['customer_phone'];
        }

        $message_lines[] = 'Hundename: ' . $data['dog_name'];
        $message_lines[] = '';
        $message_lines[] = 'Betreuungsdetails';
        $message_lines[] = 'Abo-Modell: ' . $data['subscription_model'];
        $message_lines[] = 'Betreuungsdauer: ' . ($data['duration'] === 'half' ? 'Halbtags' : 'Ganztags');
        $message_lines[] = 'Wochentage: ' . $data['selected_days'];
        $message_lines[] = 'Monatlicher Preis: ' . $data['monthly_price'];

        if (!empty($data['notes'])) {
            $message_lines[] = '';
            $message_lines[] = 'Anmerkungen';
            $message_lines[] = $data['notes'];
        }

        $message_lines[] = '';
        $message_lines[] = 'Eingegangen am ' . $received_date . ' um ' . $received_time . ' Uhr';

        $message_plain = implode("\n", $message_lines);

        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $site_name . ' <' . $admin_email . '>'
        );

        wp_mail($notification_email, $subject, $message_plain, $headers);
    }

    public function update_pricing_status() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'update_pricing_status')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
            return;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'));
            return;
        }

        $config_id = isset($_POST['config_id']) ? intval($_POST['config_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';

        if ($config_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid configuration ID.'));
            return;
        }

        $allowed_statuses = array('Neu', 'Kontaktiert', 'nicht erreicht', 'Nicht interessiert', 'Vertrag geschlossen', 'Gekündigt');
        if (!in_array($status, $allowed_statuses, true)) {
            wp_send_json_error(array('message' => 'Invalid status.'));
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';

        $result = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $config_id),
            array('%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to update status.'));
            return;
        }

        wp_send_json_success(array('message' => 'Status updated successfully.'));
    }

    public function bulk_delete_pricing_configs() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'bulk_delete_pricing_configs')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
            return;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'));
            return;
        }

        $config_ids = isset($_POST['config_ids']) ? array_map('intval', $_POST['config_ids']) : array();

        if (empty($config_ids)) {
            wp_send_json_error(array('message' => 'No items selected.'));
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';

        // Sanitize IDs and create placeholders
        $config_ids = array_filter($config_ids, function($id) {
            return $id > 0;
        });

        if (empty($config_ids)) {
            wp_send_json_error(array('message' => 'Invalid configuration IDs.'));
            return;
        }

        $placeholders = implode(',', array_fill(0, count($config_ids), '%d'));
        $query = $wpdb->prepare(
            "DELETE FROM $table_name WHERE id IN ($placeholders)",
            $config_ids
        );

        $result = $wpdb->query($query);

        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to delete items.'));
            return;
        }

        wp_send_json_success(array('message' => sprintf('%d item(s) deleted successfully.', $result)));
    }
}

