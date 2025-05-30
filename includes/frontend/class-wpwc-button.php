<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPWC_Button {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_button']);
        add_action('wp_ajax_wpwc_track_click', [$this, 'track_click']);
        add_action('wp_ajax_nopriv_wpwc_track_click', [$this, 'track_click']);
    }

    public function enqueue_assets() {
        if (get_option('wpwc_enabled', 1) && ($this->is_mobile_allowed() || !wp_is_mobile()) && $this->should_display_button()) {
            $style_handle = 'wpwc-button';
            $style_src = WPWC_URL . 'assets/css/wpwc-button.css';
            if (!empty($style_handle) && !empty($style_src)) {
                wp_enqueue_style($style_handle, $style_src, [], WPWC_VERSION);
            } else {
                error_log('WPWC: Failed to enqueue style - Handle or source is empty');
            }

            $custom_css = $this->get_custom_styles();
            wp_add_inline_style($style_handle, $custom_css);

            wp_enqueue_script('wpwc-frontend', WPWC_URL . 'assets/js/wpwc-frontend.js', ['jquery'], WPWC_VERSION, true);
            wp_localize_script('wpwc-frontend', 'wpwc_settings', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpwc_click_nonce'),
                'agents' => $this->get_available_agents(),
                'default_message' => esc_js(get_option('wpwc_default_message', '¡Hola, necesito ayuda!')),
                'selector_title' => esc_js(get_option('wpwc_selector_title', '¡Hola! Elige un departamento para chatear')),
                'selector_subtitle' => esc_js(get_option('wpwc_selector_subtitle', 'El equipo responde en pocos minutos.')),
                'woocommerce_enabled' => get_option('wpwc_woocommerce_enabled', 0),
                'woocommerce_message' => esc_js(get_option('wpwc_woocommerce_message', 'Hola, estoy interesado en [producto]')),
                'click_timestamp' => time() // Añadimos un timestamp para evitar caché
            ]);
        }
    }

    private function get_custom_styles() {
        $button_color = get_option('wpwc_button_color', '#25D366');
        $selector_bg_color = get_option('wpwc_selector_bg_color', '#FFFFFF');
        $button_position = get_option('wpwc_button_position', 'bottom-right');
        $icon_size = get_option('wpwc_icon_size', 'medium');

        $icon_sizes = [
            'small' => '40px',
            'medium' => '60px',
            'large' => '80px'
        ];
        $icon_size_px = isset($icon_sizes[$icon_size]) ? $icon_sizes[$icon_size] : '60px';

        $position_styles = '';
        if ($button_position === 'bottom-left') {
            $position_styles = 'bottom: 20px; left: 20px; right: auto;';
            $selector_position = 'bottom: 80px; left: 0; right: auto;';
        } else {
            $position_styles = 'bottom: 20px; right: 20px; left: auto;';
            $selector_position = 'bottom: 80px; right: 0; left: auto;';
        }

        $selector_bottom = $icon_size === 'small' ? '60px' : ($icon_size === 'large' ? '100px' : '80px');
        $mobile_icon_size = $icon_size === 'small' ? '30px' : ($icon_size === 'large' ? '70px' : '50px');
        $mobile_selector_bottom = $icon_size === 'small' ? '50px' : ($icon_size === 'large' ? '90px' : '70px');

        $custom_css = "
            .wpwc-whatsapp-container {
                {$position_styles}
            }
            .wpwc-whatsapp-button,
            .wpwc-whatsapp-button.wpwc-direct-link {
                background-color: {$button_color};
                border-radius: 50%;
                width: {$icon_size_px} !important;
                height: {$icon_size_px} !important;
            }
            .wpwc-whatsapp-button img,
            .wpwc-whatsapp-button.wpwc-direct-link img {
                width: {$icon_size_px} !important;
                height: {$icon_size_px} !important;
            }
            .wpwc-agent-selector {
                background-color: {$selector_bg_color};
                {$selector_position}
                bottom: {$selector_bottom};
            }
            @media (max-width: 768px) {
                .wpwc-whatsapp-button,
                .wpwc-whatsapp-button.wpwc-direct-link {
                    width: {$mobile_icon_size} !important;
                    height: {$mobile_icon_size} !important;
                }
                .wpwc-whatsapp-button img,
                .wpwc-whatsapp-button.wpwc-direct-link img {
                    width: {$mobile_icon_size} !important;
                    height: {$mobile_icon_size} !important;
                }
                .wpwc-agent-selector {
                    bottom: {$mobile_selector_bottom};
                }
            }
        ";

        return $custom_css;
    }

    private function is_department_available($agent) {
        $schedules = isset($agent['schedules']) ? $agent['schedules'] : [];

        $current_day = strtolower(date('l')); // Día actual en inglés (ej. "thursday")
        $current_time = current_time('H:i'); // Hora actual en formato HH:MM (ej. "14:53")

        if (!isset($schedules[$current_day])) {
            return true; // Si no hay horarios definidos para este día, está disponible por defecto
        }

        $schedule = $schedules[$current_day];
        $no_support = isset($schedule['no_support']) ? (int) $schedule['no_support'] : 0;
        $enabled = isset($schedule['enabled']) ? (int) $schedule['enabled'] : 0;

        if ($no_support) {
            return false; // No está disponible si el día está marcado como "sin soporte"
        }

        if (!$enabled) {
            return true; // Si los horarios no están habilitados, está disponible todo el día
        }

        $ranges = isset($schedule['ranges']) ? $schedule['ranges'] : [];
        if (empty($ranges)) {
            return true; // Si no hay rangos definidos, está disponible
        }

        foreach ($ranges as $range) {
            $start = $range['start'];
            $end = $range['end'];
            if ($current_time >= $start && $current_time <= $end) {
                return true; // Está disponible si la hora actual cae dentro de un rango
            }
        }

        return false; // No está disponible si la hora actual no cae dentro de ningún rango
    }

    private function get_next_available_time($agent) {
        $schedules = isset($agent['schedules']) ? $agent['schedules'] : [];
        $current_day = strtolower(date('l'));
        $current_time = current_time('H:i');
        $days_of_week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $days_of_week_spanish = ['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo'];

        $current_day_index = array_search($current_day, $days_of_week);
        $days_to_check = array_merge(
            array_slice($days_of_week, $current_day_index),
            array_slice($days_of_week, 0, $current_day_index)
        );

        foreach ($days_to_check as $day) {
            if (!isset($schedules[$day])) {
                return 'siempre disponible';
            }

            $schedule = $schedules[$day];
            $no_support = isset($schedule['no_support']) ? (int) $schedule['no_support'] : 0;
            $enabled = isset($schedule['enabled']) ? (int) $schedule['enabled'] : 0;

            if ($no_support) {
                continue; // Saltar días marcados como "sin soporte"
            }

            if (!$enabled) {
                $day_label = ucfirst($days_of_week_spanish[$day]);
                return "el {$day_label} todo el día";
            }

            $ranges = isset($schedule['ranges']) ? $schedule['ranges'] : [];
            $is_today = ($day === $current_day);

            foreach ($ranges as $range) {
                if ($is_today && $current_time < $range['start']) {
                    return "hoy a las {$range['start']}";
                } elseif (!$is_today) {
                    $day_label = ucfirst($days_of_week_spanish[$day]);
                    return "el {$day_label} a las {$range['start']}";
                }
            }
        }

        return 'próximamente';
    }

    private function get_available_agents() {
        $agents = get_option('wpwc_agents', []);
        $available_agents = [];

        foreach ($agents as $index => $agent) {
            if ($this->is_department_available($agent)) {
                $available_agents[$index] = $agent;
            }
        }

        return $available_agents;
    }

    private function should_display_button() {
        $visibility_mode = get_option('wpwc_visibility_mode', 'everywhere');
        $visibility_pages = get_option('wpwc_visibility_pages', []);
        $woocommerce_enabled = get_option('wpwc_woocommerce_enabled', 0);
        $woocommerce_pages = get_option('wpwc_woocommerce_pages', 'all');

        if ($visibility_mode !== 'everywhere') {
            if (!is_page()) {
                return true;
            }

            $current_page_id = get_queried_object_id();
            if ($visibility_mode === 'exclude') {
                return !in_array($current_page_id, $visibility_pages);
            }
            if ($visibility_mode === 'only') {
                return in_array($current_page_id, $visibility_pages);
            }
        }

        if ($woocommerce_enabled && class_exists('WooCommerce')) {
            if ($woocommerce_pages === 'product' && !is_product()) {
                return false;
            }
            if ($woocommerce_pages === 'shop' && !is_shop()) {
                return false;
            }
        }

        return true;
    }

    public function render_button() {
        if (!get_option('wpwc_enabled', 1) || (wp_is_mobile() && !$this->is_mobile_allowed()) || !$this->should_display_button()) {
            return;
        }

        $agents = get_option('wpwc_agents', []);
        $available_agents = $this->get_available_agents();

        if (empty($agents)) {
            echo '<div class="wpwc-error" style="color: red; position: fixed; bottom: 20px; right: 20px;">' . __('Por favor, configura un número de WhatsApp en los ajustes.', 'wpwc') . '</div>';
            return;
        }

        if (empty($available_agents)) {
            $unavailable_message = '<div class="wpwc-agent-selector wpwc-unavailable-message">';
            $unavailable_message .= '<p>' . esc_html__('No estamos disponibles ahora. Horarios próximos:', 'wpwc') . '</p>';
            $unavailable_message .= '<ul>';
            foreach ($agents as $index => $agent) {
                $next_time = $this->get_next_available_time($agent);
                $unavailable_message .= '<li>' . esc_html($agent['department']) . ': ' . esc_html($next_time) . '</li>';
            }
            $unavailable_message .= '</ul>';
            $unavailable_message .= '</div>';
            echo '<div class="wpwc-whatsapp-container">' . $unavailable_message . '</div>';
            return;
        }

        if (count($available_agents) === 1) {
            $agent = reset($available_agents);
            $index = key($available_agents);
            $number = esc_attr($agent['number']);
            $default_message = esc_attr(get_option('wpwc_default_message', '¡Hola, necesito ayuda!'));
            $whatsapp_url = sprintf('https://wa.me/%s?text=%s', preg_replace('/[^0-9]/', '', $number), urlencode($default_message));
            ?>
            <div class="wpwc-whatsapp-container">
                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="wpwc-whatsapp-button wpwc-direct-link" data-agent-index="<?php echo esc_attr($index); ?>">
                    <img src="<?php echo WPWC_URL . 'assets/images/whatsapp-icon.png'; ?>" alt="WhatsApp">
                </a>
            </div>
            <?php
        } else {
            ?>
            <div class="wpwc-whatsapp-container">
                <div class="wpwc-whatsapp-button">
                    <img src="<?php echo WPWC_URL . 'assets/images/whatsapp-icon.png'; ?>" alt="WhatsApp">
                </div>
                <div class="wpwc-agent-selector">
                    <div class="wpwc-selector-header">
                        <h3 class="wpwc-title"><?php echo esc_html(get_option('wpwc_selector_title', '¡Hola! Elige un departamento para chatear')); ?></h3>
                        <p class="wpwc-subtitle"><?php echo esc_html(get_option('wpwc_selector_subtitle', 'El equipo responde en pocos minutos.')); ?></p>
                    </div>
                    <ul class="wpwc-department-list">
                        <?php foreach ($available_agents as $index => $agent) : ?>
                            <li data-agent-index="<?php echo esc_attr($index); ?>">
                                <span><?php echo esc_html($agent['department'] ?: 'Departamento ' . ($index + 1)); ?></span>
                                <img src="<?php echo WPWC_URL . 'assets/images/whatsapp-icon.png'; ?>" alt="WhatsApp" class="wpwc-department-icon">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php
        }
    }

    public function track_click() {
        error_log('WPWC: track_click called at ' . current_time('mysql'));

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpwc_click_nonce')) {
            error_log('WPWC: Nonce verification failed - Received nonce: ' . ($_POST['nonce'] ?? 'none'));
            wp_send_json_error(['message' => 'Error de seguridad: nonce inválido']);
            wp_die();
        }

        if (!isset($_POST['click_id'])) {
            error_log('WPWC: click_id not provided');
            wp_send_json_error(['message' => 'Click ID no proporcionado']);
            wp_die();
        }

        $click_id = sanitize_text_field($_POST['click_id']);
        $agent_index = isset($_POST['agent_index']) ? intval($_POST['agent_index']) : null;
        error_log('WPWC: Agent index received - ' . ($agent_index !== null ? $agent_index : 'null') . ', Click ID: ' . $click_id);

        // Verificar si el clic ya fue registrado
        $transient_key = 'wpwc_click_' . $click_id;
        if (get_transient($transient_key)) {
            error_log('WPWC: Duplicate click detected for Click ID: ' . $click_id);
            wp_send_json_success(['message' => 'Clic ya registrado']);
            wp_die();
        }

        $tracking = new WPWC_Tracking();
        $result = $tracking->log_click($agent_index);

        if ($result === false) {
            error_log('WPWC: Failed to log click - Possible DB error');
            wp_send_json_error(['message' => 'No se pudo registrar el clic']);
        } else {
            // Registrar el clic para evitar duplicados
            set_transient($transient_key, true, HOUR_IN_SECONDS);
            error_log('WPWC: Click logged successfully - Rows affected: ' . $result . ', Click ID: ' . $click_id);
            wp_send_json_success(['message' => 'Clic registrado']);
        }

        wp_die();
    }

    private function is_mobile_allowed() {
        return get_option('wpwc_show_mobile', 1);
    }
}