<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php _e('Gestión de Agentes', 'wpwc'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wpwc_agents_group');
        do_settings_sections('wpwc_agents_group');
        ?>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Agentes', 'wpwc'); ?></label></th>
                <td>
                    <div id="wpwc-agents">
                        <?php
                        $agents = get_option('wpwc_agents', []);
                        if (empty($agents)) {
                            $agents = [['number' => '', 'department' => '', 'schedules' => []]];
                        }
                        foreach ($agents as $index => $agent) {
                            $schedules = isset($agent['schedules']) ? $agent['schedules'] : [];
                            ?>
                            <div class="wpwc-agent wpwc-agent-card">
                                <div class="wpwc-agent-header">
                                    <h3><?php _e('Agente', 'wpwc'); ?> <?php echo $index + 1; ?></h3>
                                    <button type="button" class="button wpwc-remove-agent"><?php _e('Eliminar Agente', 'wpwc'); ?></button>
                                </div>
                                <div class="wpwc-agent-body">
                                    <div class="wpwc-agent-field">
                                        <label for="wpwc_agents_<?php echo $index; ?>_department"><?php _e('Departamento', 'wpwc'); ?>:</label>
                                        <input type="text" id="wpwc_agents_<?php echo $index; ?>_department" name="wpwc_agents[<?php echo $index; ?>][department]" value="<?php echo esc_attr($agent['department'] ?? ''); ?>" placeholder="<?php _e('Nombre del departamento (ej. Ventas)', 'wpwc'); ?>" class="regular-text">
                                    </div>
                                    <div class="wpwc-agent-field">
                                        <label for="wpwc_agents_<?php echo $index; ?>_number"><?php _e('Número de WhatsApp', 'wpwc'); ?>:</label>
                                        <input type="text" id="wpwc_agents_<?php echo $index; ?>_number" name="wpwc_agents[<?php echo $index; ?>][number]" value="<?php echo esc_attr($agent['number'] ?? ''); ?>" placeholder="<?php _e('ej. +1234567890', 'wpwc'); ?>" class="regular-text">
                                    </div>
                                    <div class="wpwc-agent-field">
                                        <label><?php _e('Horarios de Disponibilidad', 'wpwc'); ?>:</label>
                                        <div class="wpwc-schedule-container">
                                            <?php
                                            $days_of_week = ['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo'];
                                            foreach ($days_of_week as $day_key => $day_label) {
                                                $schedule = isset($schedules[$day_key]) ? $schedules[$day_key] : ['no_support' => 0, 'enabled' => 0, 'ranges' => [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '17:00']]];
                                                $no_support = isset($schedule['no_support']) ? (int) $schedule['no_support'] : 0;
                                                $enabled = isset($schedule['enabled']) ? (int) $schedule['enabled'] : 0;
                                                $ranges = isset($schedule['ranges']) ? $schedule['ranges'] : [['start' => '09:00', 'end' => '13:00'], ['start' => '14:00', 'end' => '17:00']];
                                                ?>
                                                <div class="wpwc-schedule">
                                                    <div class="wpwc-schedule-row">
                                                        <div class="wpwc-day-column">
                                                            <span class="wpwc-day-label"><?php echo esc_html($day_label); ?></span>
                                                        </div>
                                                        <div class="wpwc-toggles-column">
                                                            <label class="wpwc-toggle-label">
                                                                <input type="checkbox" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][no_support]" value="1" <?php checked($no_support); ?> class="wpwc-no-support-toggle">
                                                                <span><?php _e('Sin soporte', 'wpwc'); ?></span>
                                                            </label>
                                                            <label class="wpwc-toggle-label">
                                                                <input type="checkbox" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][enabled]" value="1" <?php checked($enabled); ?> class="wpwc-schedule-toggle" <?php echo $no_support ? 'disabled' : ''; ?>>
                                                                <span><?php _e('Habilitar horarios', 'wpwc'); ?></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="wpwc-schedule-details" style="display: <?php echo $enabled && !$no_support ? 'block' : 'none'; ?>;">
                                                        <div class="wpwc-range-row">
                                                            <label class="wpwc-range-label"><?php _e('Rango 1', 'wpwc'); ?>:</label>
                                                            <div class="wpwc-time-range">
                                                                <input type="time" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][ranges][0][start]" value="<?php echo esc_attr($ranges[0]['start']); ?>" class="wpwc-time-input">
                                                                <span class="wpwc-time-separator">-</span>
                                                                <input type="time" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][ranges][0][end]" value="<?php echo esc_attr($ranges[0]['end']); ?>" class="wpwc-time-input">
                                                            </div>
                                                        </div>
                                                        <div class="wpwc-range-row">
                                                            <label class="wpwc-range-label"><?php _e('Rango 2', 'wpwc'); ?>:</label>
                                                            <div class="wpwc-time-range">
                                                                <input type="time" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][ranges][1][start]" value="<?php echo esc_attr($ranges[1]['start']); ?>" class="wpwc-time-input">
                                                                <span class="wpwc-time-separator">-</span>
                                                                <input type="time" name="wpwc_agents[<?php echo $index; ?>][schedules][<?php echo $day_key; ?>][ranges][1][end]" value="<?php echo esc_attr($ranges[1]['end']); ?>" class="wpwc-time-input">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <button type="button" class="button button-primary wpwc-add-agent" id="wpwc-add-agent-btn"><?php _e('Añadir Nuevo Agente', 'wpwc'); ?></button>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Guardar Cambios', 'wpwc')); ?>
    </form>
</div>
<script>
    jQuery(document).ready(function($) {
        // Variable para controlar si se está procesando un clic
        let isAddingAgent = false;

        function addAgent() {
            if (isAddingAgent) {
                console.log('WPWC: Agent addition already in progress, ignoring duplicate request');
                return; // Evitar múltiples ejecuciones
            }
            isAddingAgent = true;

            // Obtener el índice del último agente
            var index = $('#wpwc-agents .wpwc-agent').length;
            var agentHtml = `
                <div class="wpwc-agent wpwc-agent-card">
                    <div class="wpwc-agent-header">
                        <h3><?php _e('Agente', 'wpwc'); ?> ${index + 1}</h3>
                        <button type="button" class="button wpwc-remove-agent"><?php _e('Eliminar Agente', 'wpwc'); ?></button>
                    </div>
                    <div class="wpwc-agent-body">
                        <div class="wpwc-agent-field">
                            <label for="wpwc_agents_${index}_department"><?php _e('Departamento', 'wpwc'); ?>:</label>
                            <input type="text" id="wpwc_agents_${index}_department" name="wpwc_agents[${index}][department]" placeholder="<?php _e('Nombre del departamento (ej. Ventas)', 'wpwc'); ?>" class="regular-text">
                        </div>
                        <div class="wpwc-agent-field">
                            <label for="wpwc_agents_${index}_number"><?php _e('Número de WhatsApp', 'wpwc'); ?>:</label>
                            <input type="text" id="wpwc_agents_${index}_number" name="wpwc_agents[${index}][number]" placeholder="<?php _e('ej. +1234567890', 'wpwc'); ?>" class="regular-text">
                        </div>
                        <div class="wpwc-agent-field">
                            <label><?php _e('Horarios de Disponibilidad', 'wpwc'); ?>:</label>
                            <div class="wpwc-schedule-container">
                                <?php foreach ($days_of_week as $day_key => $day_label) { ?>
                                    <div class="wpwc-schedule">
                                        <div class="wpwc-schedule-row">
                                            <div class="wpwc-day-column">
                                                <span class="wpwc-day-label"><?php echo esc_html($day_label); ?></span>
                                            </div>
                                            <div class="wpwc-toggles-column">
                                                <label class="wpwc-toggle-label">
                                                    <input type="checkbox" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][no_support]" value="1" class="wpwc-no-support-toggle">
                                                    <span><?php _e('Sin soporte', 'wpwc'); ?></span>
                                                </label>
                                                <label class="wpwc-toggle-label">
                                                    <input type="checkbox" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][enabled]" value="1" class="wpwc-schedule-toggle">
                                                    <span><?php _e('Habilitar horarios', 'wpwc'); ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="wpwc-schedule-details" style="display: none;">
                                            <div class="wpwc-range-row">
                                                <label class="wpwc-range-label"><?php _e('Rango 1', 'wpwc'); ?>:</label>
                                                <div class="wpwc-time-range">
                                                    <input type="time" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][ranges][0][start]" value="09:00" class="wpwc-time-input">
                                                    <span class="wpwc-time-separator">-</span>
                                                    <input type="time" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][ranges][0][end]" value="13:00" class="wpwc-time-input">
                                                </div>
                                            </div>
                                            <div class="wpwc-range-row">
                                                <label class="wpwc-range-label"><?php _e('Rango 2', 'wpwc'); ?>:</label>
                                                <div class="wpwc-time-range">
                                                    <input type="time" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][ranges][1][start]" value="14:00" class="wpwc-time-input">
                                                    <span class="wpwc-time-separator">-</span>
                                                    <input type="time" name="wpwc_agents[${index}][schedules][<?php echo esc_attr($day_key); ?>][ranges][1][end]" value="17:00" class="wpwc-time-input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('#wpwc-agents').append(agentHtml);

            console.log('WPWC: Added agent with index ' + index);

            // Resetear la bandera después de un pequeño retraso
            setTimeout(() => {
                isAddingAgent = false;
            }, 500);
        }

        // Asegurarse de que el evento solo se asocie una vez
        const $addAgentBtn = $('#wpwc-add-agent-btn');
        $addAgentBtn.off('click'); // Limpiar cualquier evento previo
        $addAgentBtn.on('click', function(e) {
            e.preventDefault(); // Prevenir comportamiento predeterminado
            e.stopImmediatePropagation(); // Detener propagación de eventos
            addAgent();
        });

        $(document).on('click', '.wpwc-remove-agent', function() {
            $(this).closest('.wpwc-agent').remove();
            // Renumerar agentes después de eliminar
            $('.wpwc-agent').each(function(i) {
                $(this).find('.wpwc-agent-header h3').text('<?php _e('Agente', 'wpwc'); ?> ' + (i + 1));
            });
        });

        $(document).on('change', '.wpwc-no-support-toggle', function() {
            var $schedule = $(this).closest('.wpwc-schedule');
            var $scheduleToggle = $schedule.find('.wpwc-schedule-toggle');
            var $details = $schedule.find('.wpwc-schedule-details');
            if ($(this).is(':checked')) {
                $scheduleToggle.prop('checked', false).prop('disabled', true);
                $details.hide();
            } else {
                $scheduleToggle.prop('disabled', false);
            }
        });

        $(document).on('change', '.wpwc-schedule-toggle', function() {
            var $schedule = $(this).closest('.wpwc-schedule');
            var $details = $schedule.find('.wpwc-schedule-details');
            var $noSupportToggle = $schedule.find('.wpwc-no-support-toggle');
            if ($(this).is(':checked') && !$noSupportToggle.is(':checked')) {
                $details.show();
            } else {
                $details.hide();
            }
        });

        $('form').on('submit', function(e) {
            $('.wpwc-agent').each(function() {
                var $agent = $(this);
                var department = $agent.find('input[name$="[department]"]').val();
                var number = $agent.find('input[name$="[number]"]').val();
                
                if (!department || !number) {
                    alert('Error: Todos los agentes deben tener un departamento y un número de WhatsApp.');
                    e.preventDefault();
                    return false;
                }

                var $ranges = $agent.find('.wpwc-schedule-details');
                $ranges.each(function() {
                    if ($(this).is(':visible')) {
                        var $times = $(this).find('input[type="time"]');
                        var times = [];
                        for (var i = 0; i < $times.length; i += 2) {
                            var start = $times.eq(i).val();
                            var end = $times.eq(i + 1).val();
                            if (start && end) {
                                times.push({ start: start, end: end });
                            }
                        }
                        times.sort((a, b) => a.start.localeCompare(b.start));
                        for (var i = 0; i < times.length - 1; i++) {
                            if (times[i].end > times[i + 1].start) {
                                alert('Error: Los rangos de horario no deben superponerse.');
                                e.preventDefault();
                                return false;
                            }
                        }
                    }
                });
            });
        });
    });
</script>