<?php

if (!defined('ABSPATH')) {
    exit;
}

class Modular_Pricing_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pricing_configurations';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone varchar(50) DEFAULT '',
            dog_name varchar(255) NOT NULL,
            subscription_model varchar(50) NOT NULL,
            duration varchar(10) NOT NULL,
            selected_days text NOT NULL,
            monthly_price varchar(50) NOT NULL,
            notes text DEFAULT '',
            status varchar(50) DEFAULT 'Neu',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        
        // Add status column if it doesn't exist (for existing installations)
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'status'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN status varchar(50) DEFAULT 'Neu'");
        }
    }
}

