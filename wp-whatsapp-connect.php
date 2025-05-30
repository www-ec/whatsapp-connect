<?php
/**
 * Plugin Name: WP WhatsApp Connect
 * Plugin URI: https://www.websystems.com.ec
 * Description: Plugin de botón de WhatsApp con seguimiento de clics y soporte para múltiples agentes.
 * Version: 1.0.5
 * Author: santy77_ec
 * Author URI: https://www.websystems.com.ec
 * License: GPL-2.0+
 * Text Domain: wpwc
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('WPWC_VERSION', '1.0.5');
define('WPWC_PATH', plugin_dir_path(__FILE__));
define('WPWC_URL', plugin_dir_url(__FILE__));
define('WPWC_BASENAME', plugin_basename(__FILE__));

// Load required files
require_once WPWC_PATH . 'includes/utils/class-wpwc-security.php';
require_once WPWC_PATH . 'includes/frontend/class-wpwc-button.php';
require_once WPWC_PATH . 'includes/admin/class-wpwc-admin.php';
require_once WPWC_PATH . 'includes/utils/class-wpwc-tracking.php';

// Initialize plugin
class WP_WhatsApp_Connect {
    public function __construct() {
        // Initialize modules
        new WPWC_Security();
        new WPWC_Button();
        new WPWC_Admin();
        new WPWC_Tracking();

        // Create/update database table on activation
        register_activation_hook(__FILE__, [$this, 'activate']);
        // Check for database updates on plugin load
        add_action('plugins_loaded', [$this, 'check_database_version']);
    }

    public function activate() {
        $this->create_or_update_table();
        // Update the database version
        update_option('wpwc_db_version', WPWC_VERSION);
    }

    public function check_database_version() {
        $current_db_version = get_option('wpwc_db_version', '0');
        if (version_compare($current_db_version, WPWC_VERSION, '<')) {
            $this->create_or_update_table();
            update_option('wpwc_db_version', WPWC_VERSION);
        }
    }

    private function create_or_update_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpwc_clicks';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            click_time DATETIME NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            agent_index INT DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Debug: Log table creation/update
        error_log('WPWC: Table creation/update executed for ' . $table_name);

        // Check if agent_index column exists, if not, add it
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'agent_index'");
        if (empty($columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN agent_index INT DEFAULT NULL AFTER ip_address");
            error_log('WPWC: Added agent_index column to ' . $table_name);
        }

        // Set default options
        if (!get_option('wpwc_enabled')) {
            update_option('wpwc_enabled', 1);
        }
        if (!get_option('wpwc_show_mobile')) {
            update_option('wpwc_show_mobile', 1);
        }
    }
}

// Instantiate plugin
new WP_WhatsApp_Connect();