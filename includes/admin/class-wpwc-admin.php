<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPWC_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'initialize_default_settings']);
    }

    public function add_menu() {
        add_menu_page(
            __('WhatsApp Connect', 'wpwc'),
            __('WhatsApp Connect', 'wpwc'),
            'manage_options',
            'wpwc-settings',
            [$this, 'render_settings_page'],
            'dashicons-whatsapp',
            80
        );
        add_submenu_page(
            'wpwc-settings',
            __('Ajustes', 'wpwc'),
            __('Ajustes', 'wpwc'),
            'manage_options',
            'wpwc-settings',
            [$this, 'render_settings_page']
        );
        add_submenu_page(
            'wpwc-settings',
            __('Agentes', 'wpwc'),
            __('Agentes', 'wpwc'),
            'manage_options',
            'wpwc-agents',
            [$this, 'render_agents_page']
        );
        add_submenu_page(
            'wpwc-settings',
            __('Reportes de Clics', 'wpwc'),
            __('Reportes', 'wpwc'),
            'manage_options',
            'wpwc-reports',
            [$this, 'render_reports_page']
        );
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'toplevel_page_wpwc-settings' || $hook === 'whatsapp-connect_page_wpwc-agents' || $hook === 'whatsapp-connect_page_wpwc-reports') {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpwc-admin', WPWC_URL . 'assets/js/wpwc-admin.js', ['jquery'], WPWC_VERSION, true);

            $style_handle = 'wpwc-admin';
            $style_src = WPWC_URL . 'assets/css/wpwc-admin.css';
            if (!empty($style_handle) && !empty($style_src)) {
                wp_enqueue_style($style_handle, $style_src, [], WPWC_VERSION);
            } else {
                error_log('WPWC: Failed to enqueue admin style - Handle or source is empty');
            }

            if ($hook === 'whatsapp-connect_page_wpwc-reports') {
                wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.4', true);
            }
        }
    }

    public function initialize_default_settings() {
        // Inicializar valores por defecto si no existen
        $defaults = [
            'wpwc_enabled' => 1,
            'wpwc_show_mobile' => 1,
            'wpwc_default_message' => '¡Hola, necesito ayuda!',
            'wpwc_selector_title' => '¡Hola! Elige un departamento para chatear',
            'wpwc_selector_subtitle' => 'El equipo responde en pocos minutos.',
            'wpwc_button_color' => '#25D366',
            'wpwc_selector_bg_color' => '#FFFFFF',
            'wpwc_button_position' => 'bottom-right',
            'wpwc_icon_size' => 'medium',
            'wpwc_visibility_mode' => 'everywhere',
            'wpwc_woocommerce_enabled' => 0,
            'wpwc_woocommerce_pages' => 'all',
            'wpwc_woocommerce_message' => 'Hola, estoy interesado en [producto]'
        ];

        foreach ($defaults as $option => $value) {
            if (false === get_option($option)) {
                update_option($option, $value);
                error_log("WPWC: Initialized default setting $option to " . print_r($value, true));
            }
        }

        // Forzar la inicialización si los valores están vacíos o no definidos
        if (empty(get_option('wpwc_enabled'))) {
            update_option('wpwc_enabled', 1);
            error_log("WPWC: Forced initialization of wpwc_enabled to 1");
        }
        if (empty(get_option('wpwc_show_mobile'))) {
            update_option('wpwc_show_mobile', 1);
            error_log("WPWC: Forced initialization of wpwc_show_mobile to 1");
        }
        if (empty(get_option('wpwc_default_message'))) {
            update_option('wpwc_default_message', '¡Hola, necesito ayuda!');
            error_log("WPWC: Forced initialization of wpwc_default_message");
        }
        if (empty(get_option('wpwc_selector_title'))) {
            update_option('wpwc_selector_title', '¡Hola! Elige un departamento para chatear');
            error_log("WPWC: Forced initialization of wpwc_selector_title");
        }
        if (empty(get_option('wpwc_selector_subtitle'))) {
            update_option('wpwc_selector_subtitle', 'El equipo responde en pocos minutos.');
            error_log("WPWC: Forced initialization of wpwc_selector_subtitle");
        }
    }

    public function register_settings() {
        // Registro de ajustes (settings)
        register_setting('wpwc_settings_group', 'wpwc_enabled', [
            'sanitize_callback' => 'intval',
            'default' => 1
        ]);
        register_setting('wpwc_settings_group', 'wpwc_show_mobile', [
            'sanitize_callback' => 'intval',
            'default' => 1
        ]);
        register_setting('wpwc_settings_group', 'wpwc_default_message', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '¡Hola, necesito ayuda!'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_selector_title', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '¡Hola! Elige un departamento para chatear'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_selector_subtitle', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'El equipo responde en pocos minutos.'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_button_color', [
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#25D366'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_selector_bg_color', [
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#FFFFFF'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_button_position', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'bottom-right'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_icon_size', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'medium'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_visibility_mode', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'everywhere'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_visibility_pages', [
            'sanitize_callback' => [$this, 'sanitize_visibility_pages']
        ]);
        register_setting('wpwc_settings_group', 'wpwc_woocommerce_enabled', [
            'sanitize_callback' => 'intval',
            'default' => 0
        ]);
        register_setting('wpwc_settings_group', 'wpwc_woocommerce_pages', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'all'
        ]);
        register_setting('wpwc_settings_group', 'wpwc_woocommerce_message', [
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Hola, estoy interesado en [producto]'
        ]);

        // Registro de agentes (agents)
        register_setting('wpwc_agents_group', 'wpwc_agents', [
            'sanitize_callback' => [$this, 'sanitize_agents']
        ]);

        // Añadir depuración para verificar el guardado
        add_action('update_option', function($option_name, $old_value, $new_value) {
            if (strpos($option_name, 'wpwc_') === 0) {
                error_log("WPWC: Updated option $option_name from " . print_r($old_value, true) . " to " . print_r($new_value, true));
            }
        }, 10, 3);
    }

    public function sanitize_agents($agents) {
        $sanitized = [];
        if (is_array($agents) && !empty($agents)) {
            foreach ($agents as $index => $agent) {
                if (!empty($agent['number']) && !empty($agent['department'])) {
                    $schedules = isset($agent['schedules']) && is_array($agent['schedules']) ? $agent['schedules'] : [];
                    $sanitized_schedules = [];
                    foreach ($schedules as $day => $schedule) {
                        $no_support = isset($schedule['no_support']) ? (int) $schedule['no_support'] : 0;
                        $enabled = isset($schedule['enabled']) ? (int) $schedule['enabled'] : 0;
                        $ranges = isset($schedule['ranges']) && is_array($schedule['ranges']) ? $schedule['ranges'] : [];
                        $sanitized_ranges = [];
                        foreach ($ranges as $range) {
                            if (isset($range['start']) && isset($range['end'])) {
                                $start = sanitize_text_field($range['start']);
                                $end = sanitize_text_field($range['end']);
                                if (preg_match('/^\d{2}:\d{2}$/', $start) && preg_match('/^\d{2}:\d{2}$/', $end)) {
                                    $sanitized_ranges[] = ['start' => $start, 'end' => $end];
                                }
                            }
                        }
                        $sanitized_schedules[$day] = [
                            'no_support' => $no_support,
                            'enabled' => $enabled,
                            'ranges' => $sanitized_ranges
                        ];
                    }
                    $sanitized[$index] = [
                        'number' => preg_replace('/[^0-9]/', '', $agent['number']),
                        'department' => sanitize_text_field($agent['department']),
                        'schedules' => $sanitized_schedules
                    ];
                }
            }
        }
        error_log('WPWC: Sanitized agents - ' . print_r($sanitized, true));
        return $sanitized;
    }

    public function sanitize_visibility_pages($pages) {
        if (!is_array($pages)) {
            return [];
        }
        return array_map('intval', $pages);
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para acceder a esta página.', 'wpwc'));
        }
        require_once WPWC_PATH . 'includes/admin/templates/settings-page.php';
    }

    public function render_agents_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para acceder a esta página.', 'wpwc'));
        }
        require_once WPWC_PATH . 'includes/admin/templates/agents-page.php';
    }

    public function render_reports_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para acceder a esta página.', 'wpwc'));
        }
        require_once WPWC_PATH . 'includes/admin/templates/report-page.php';
    }
}