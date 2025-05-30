<?php
if (!defined('ABSPATH')) {
    exit;
}

// Instanciar la clase WPWC_Tracking para obtener los clics
$tracking = new WPWC_Tracking();

// Determinar el tipo de vista (diaria o mensual) desde el formulario
$view_type = isset($_GET['view_type']) ? sanitize_text_field($_GET['view_type']) : 'daily';

// Obtener los clics y los clics por agente
$clicks = $tracking->get_clicks($view_type);
$clicks_by_agent = $tracking->get_clicks_by_agent();

// Depuración: Registrar los datos obtenidos
error_log('WPWC: View Type - ' . $view_type);
error_log('WPWC: Clicks Data - ' . print_r($clicks, true));
error_log('WPWC: Clicks by Agent Data - ' . print_r($clicks_by_agent, true));

// Preparar datos para el gráfico de fechas (sumar clics por período, ignorando IP)
$clicks_by_period = [];
if (!empty($clicks) && is_array($clicks)) {
    foreach ($clicks as $click) {
        if (isset($click['date']) && isset($click['click_count'])) {
            $period = $click['date']; // La fecha ya está formateada por WPWC_Tracking (YYYY-MM-DD o YYYY-MM)
            if (!isset($clicks_by_period[$period])) {
                $clicks_by_period[$period] = 0;
            }
            $clicks_by_period[$period] += (int) $click['click_count'];
        }
    }
}

// Ordenar los períodos
ksort($clicks_by_period);

// Preparar etiquetas y datos para el gráfico de fechas
$chart_labels = array_keys($clicks_by_period);
$chart_data = array_values($clicks_by_period);

// Preparar datos para el gráfico de agentes
$agent_labels = [];
$agent_data = [];
if (!empty($clicks_by_agent) && is_array($clicks_by_agent)) {
    foreach ($clicks_by_agent as $agent) {
        if (isset($agent['department']) && isset($agent['click_count'])) {
            $agent_labels[] = $agent['department'];
            $agent_data[] = $agent['click_count'];
        }
    }
}

// Preparar datos detallados por período e IP para la tabla
$clicks_by_period_and_ip = [];
if (!empty($clicks) && is_array($clicks)) {
    foreach ($clicks as $click) {
        if (isset($click['date']) && isset($click['click_count']) && isset($click['ip_address'])) {
            $period = $click['date'];
            $ip = $click['ip_address'];
            if (!isset($clicks_by_period_and_ip[$period][$ip])) {
                $clicks_by_period_and_ip[$period][$ip] = 0;
            }
            $clicks_by_period_and_ip[$period][$ip] += (int) $click['click_count'];
        }
    }
}
?>

<div class="wrap">
    <h1><?php _e('Reporte de Clics', 'wpwc'); ?></h1>

    <!-- Formulario para seleccionar vista diaria o mensual -->
    <form method="get" action="">
        <input type="hidden" name="page" value="wpwc-reports">
        <label for="view_type"><?php _e('Vista:', 'wpwc'); ?></label>
        <select id="view_type" name="view_type" onchange="this.form.submit()">
            <option value="daily" <?php selected($view_type, 'daily'); ?>><?php _e('Diaria', 'wpwc'); ?></option>
            <option value="monthly" <?php selected($view_type, 'monthly'); ?>><?php _e('Mensual', 'wpwc'); ?></option>
        </select>
    </form>

    <!-- Mensaje si no hay datos -->
    <?php if (empty($chart_labels)) : ?>
        <div class="notice notice-warning">
            <p><?php _e('No se encontraron datos de clics para mostrar. Intenta generar clics haciendo clic en el botón de WhatsApp en el frontend.', 'wpwc'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Gráfico de clics por fecha -->
    <h2><?php _e('Estadísticas de Clics por Fecha', 'wpwc'); ?></h2>
    <div class="wpwc-report">
        <canvas id="wpwcClicksChart" width="400" height="200"></canvas>
    </div>

    <!-- Tabla detallada por período e IP -->
    <h2><?php _e('Detalles por Período e IP', 'wpwc'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Período', 'wpwc'); ?></th>
                <th><?php _e('Dirección IP', 'wpwc'); ?></th>
                <th><?php _e('Número de Clics', 'wpwc'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($clicks_by_period_and_ip)) {
                foreach ($clicks_by_period_and_ip as $period => $ips) {
                    foreach ($ips as $ip => $count) {
                        ?>
                        <tr>
                            <td><?php echo esc_html($period); ?></td>
                            <td><?php echo esc_html($ip); ?></td>
                            <td><?php echo esc_html($count); ?></td>
                        </tr>
                        <?php
                    }
                }
            } else {
                ?>
                <tr>
                    <td colspan="3"><?php _e('No hay datos disponibles.', 'wpwc'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <!-- Gráfico de clics por agente -->
    <h2><?php _e('Estadísticas de Clics por Agente', 'wpwc'); ?></h2>
    <div class="wpwc-report">
        <canvas id="wpwcAgentChart" width="400" height="200"></canvas>
    </div>

    <!-- Tabla detallada por agente -->
    <h2><?php _e('Detalles por Agente', 'wpwc'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Agente/Departamento', 'wpwc'); ?></th>
                <th><?php _e('Número de Clics', 'wpwc'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($clicks_by_agent) && is_array($clicks_by_agent)) {
                foreach ($clicks_by_agent as $agent) {
                    ?>
                    <tr>
                        <td><?php echo esc_html($agent['department']); ?></td>
                        <td><?php echo esc_html($agent['click_count']); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="2"><?php _e('No hay datos disponibles.', 'wpwc'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        // Gráfico de clics por fecha
        var ctx = document.getElementById('wpwcClicksChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: '<?php _e('Clics por Período', 'wpwc'); ?>',
                    data: <?php echo json_encode($chart_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '<?php echo $view_type === 'daily' ? __('Fecha', 'wpwc') : __('Mes', 'wpwc'); ?>'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '<?php _e('Número de Clics', 'wpwc'); ?>'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Gráfico de clics por agente
        var ctxAgent = document.getElementById('wpwcAgentChart').getContext('2d');
        var agentChart = new Chart(ctxAgent, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($agent_labels); ?>,
                datasets: [{
                    label: '<?php _e('Clics por Agente', 'wpwc'); ?>',
                    data: <?php echo json_encode($agent_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Gráfico de barras horizontal
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '<?php _e('Número de Clics', 'wpwc'); ?>'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '<?php _e('Agente/Departamento', 'wpwc'); ?>'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
</script>