<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPWC_Tracking {
    public function __construct() {
        add_action('admin_post_wpwc_export_csv', [$this, 'export_csv']);
    }

    public function log_click($agent_index = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpwc_clicks';
        $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $click_time = current_time('mysql');

        error_log('WPWC: Attempting to log click - Table: ' . $table_name);
        error_log('WPWC: Data - IP: ' . $ip_address . ', Time: ' . $click_time . ', Agent Index: ' . ($agent_index ?? 'null'));

        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'agent_index'");
        $has_agent_index = !empty($columns);

        $data = [
            'click_time' => $click_time,
            'ip_address' => $ip_address
        ];
        $format = ['%s', '%s'];

        if ($has_agent_index && $agent_index !== null) {
            $data['agent_index'] = $agent_index;
            $format[] = '%d';
        }

        $result = $wpdb->insert($table_name, $data, $format);

        if ($result === false) {
            error_log('WPWC: Failed to insert click - Error: ' . $wpdb->last_error);
            error_log('WPWC: Query: ' . $wpdb->last_query);
        } else {
            error_log('WPWC: Click inserted successfully - Rows affected: ' . $result);
        }

        $this->purge_old_data();
        return $result;
    }

    public function get_clicks($report_type = 'daily') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpwc_clicks';
        $group_by = $report_type === 'daily' ? 'DATE(click_time)' : 'DATE_FORMAT(click_time, "%Y-%m")';
        $date_format = $report_type === 'daily' ? '%Y-%m-%d' : '%Y-%m';

        $query = $wpdb->prepare(
            "SELECT DATE_FORMAT(click_time, %s) as date, ip_address, COUNT(*) as click_count
            FROM $table_name
            GROUP BY $group_by, ip_address
            ORDER BY click_time DESC",
            $date_format
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    public function get_clicks_by_agent() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpwc_clicks';
        $agents = get_option('wpwc_agents', []);

        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'agent_index'");
        if (empty($columns)) {
            error_log('WPWC: agent_index column does not exist, skipping clicks by agent');
            return [];
        }

        $query = "SELECT agent_index, COUNT(*) as click_count
                  FROM $table_name
                  WHERE agent_index IS NOT NULL
                  GROUP BY agent_index
                  ORDER BY click_count DESC";

        $results = $wpdb->get_results($query, ARRAY_A);
        $clicks_by_agent = [];

        foreach ($results as $result) {
            $agent_index = $result['agent_index'];
            if (isset($agents[$agent_index])) {
                $clicks_by_agent[] = [
                    'department' => $agents[$agent_index]['department'] ?: 'Departamento ' . ($agent_index + 1),
                    'number' => $agents[$agent_index]['number'],
                    'click_count' => $result['click_count']
                ];
            }
        }

        return $clicks_by_agent;
    }

    public function purge_old_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpwc_clicks';

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE click_time < %s",
                date('Y-m-d H:i:s', strtotime('-30 days'))
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table_name WHERE click_time < %s",
                date('Y-m-d H:i:s', strtotime('-12 months'))
            )
        );
    }

    public function export_csv() {
        check_admin_referer('wpwc_export_nonce', 'wpwc_export_nonce');
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción.', 'wpwc'));
        }

        $report_type = isset($_POST['report_type']) ? sanitize_text_field($_POST['report_type']) : 'daily';
        $clicks = $this->get_clicks($report_type);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="wpwc-clics-' . $report_type . '-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Fecha', 'Dirección IP', 'Clics']);

        foreach ($clicks as $click) {
            fputcsv($output, [$click['date'], $click['ip_address'], $click['click_count']]);
        }

        fclose($output);
        exit;
    }
}