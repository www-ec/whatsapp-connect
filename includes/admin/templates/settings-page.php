<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php _e('Ajustes de WhatsApp Connect', 'wpwc'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wpwc_settings_group');
        do_settings_sections('wpwc_settings_group');
        ?>
        <table class="form-table">
            <tr>
                <th><label for="wpwc_enabled"><?php _e('Activar Plugin', 'wpwc'); ?></label></th>
                <td>
                    <input type="checkbox" id="wpwc_enabled" name="wpwc_enabled" value="1" <?php checked(get_option('wpwc_enabled', 1), 1); ?>>
                    <p class="description"><?php _e('Habilita o deshabilita el plugin.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_show_mobile"><?php _e('Mostrar en Móviles', 'wpwc'); ?></label></th>
                <td>
                    <input type="checkbox" id="wpwc_show_mobile" name="wpwc_show_mobile" value="1" <?php checked(get_option('wpwc_show_mobile', 1), 1); ?>>
                    <p class="description"><?php _e('Muestra el botón en dispositivos móviles.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_default_message"><?php _e('Mensaje Predeterminado', 'wpwc'); ?></label></th>
                <td>
                    <?php $default_message = get_option('wpwc_default_message', '¡Hola, necesito ayuda!'); ?>
                    <input type="text" id="wpwc_default_message" name="wpwc_default_message" value="<?php echo esc_attr($default_message); ?>" class="regular-text">
                    <p class="description"><?php _e('Mensaje predeterminado que se enviará a WhatsApp.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_selector_title"><?php _e('Título del Selector', 'wpwc'); ?></label></th>
                <td>
                    <?php $selector_title = get_option('wpwc_selector_title', '¡Hola! Elige un departamento para chatear'); ?>
                    <input type="text" id="wpwc_selector_title" name="wpwc_selector_title" value="<?php echo esc_attr($selector_title); ?>" class="regular-text">
                    <p class="description"><?php _e('Título que aparece en el selector de agentes.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_selector_subtitle"><?php _e('Subtítulo del Selector', 'wpwc'); ?></label></th>
                <td>
                    <?php $selector_subtitle = get_option('wpwc_selector_subtitle', 'El equipo responde en pocos minutos.'); ?>
                    <input type="text" id="wpwc_selector_subtitle" name="wpwc_selector_subtitle" value="<?php echo esc_attr($selector_subtitle); ?>" class="regular-text">
                    <p class="description"><?php _e('Subtítulo que aparece en el selector de agentes.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_button_color"><?php _e('Color del Botón', 'wpwc'); ?></label></th>
                <td>
                    <input type="text" id="wpwc_button_color" name="wpwc_button_color" value="<?php echo esc_attr(get_option('wpwc_button_color', '#25D366')); ?>" class="regular-text wpwc-color-picker">
                    <p class="description"><?php _e('Selecciona el color de fondo del botón de WhatsApp (por defecto: verde WhatsApp).', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_selector_bg_color"><?php _e('Color de Fondo del Selector', 'wpwc'); ?></label></th>
                <td>
                    <?php $selector_bg_color = get_option('wpwc_selector_bg_color'); ?>
                    <?php $selector_bg_color = !empty($selector_bg_color) ? $selector_bg_color : '#FFFFFF'; ?>
                    <input type="text" id="wpwc_selector_bg_color" name="wpwc_selector_bg_color" value="<?php echo esc_attr($selector_bg_color); ?>" class="regular-text wpwc-color-picker">
                    <p class="description"><?php _e('Selecciona el color de fondo del selector de departamentos (por defecto: blanco).', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_button_position"><?php _e('Posición del Botón', 'wpwc'); ?></label></th>
                <td>
                    <select id="wpwc_button_position" name="wpwc_button_position">
                        <option value="bottom-right" <?php selected(get_option('wpwc_button_position', 'bottom-right'), 'bottom-right'); ?>><?php _e('Abajo-Derecha', 'wpwc'); ?></option>
                        <option value="bottom-left" <?php selected(get_option('wpwc_button_position', 'bottom-right'), 'bottom-left'); ?>><?php _e('Abajo-Izquierda', 'wpwc'); ?></option>
                    </select>
                    <p class="description"><?php _e('Selecciona la posición del botón flotante en la pantalla.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_icon_size"><?php _e('Tamaño del Ícono', 'wpwc'); ?></label></th>
                <td>
                    <select id="wpwc_icon_size" name="wpwc_icon_size">
                        <option value="small" <?php selected(get_option('wpwc_icon_size', 'medium'), 'small'); ?>><?php _e('Pequeño (40px)', 'wpwc'); ?></option>
                        <option value="medium" <?php selected(get_option('wpwc_icon_size', 'medium'), 'medium'); ?>><?php _e('Mediano (60px)', 'wpwc'); ?></option>
                        <option value="large" <?php selected(get_option('wpwc_icon_size', 'medium'), 'large'); ?>><?php _e('Grande (80px)', 'wpwc'); ?></option>
                    </select>
                    <p class="description"><?php _e('Selecciona el tamaño del ícono del botón.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_visibility_mode"><?php _e('Visibilidad del Botón', 'wpwc'); ?></label></th>
                <td>
                    <select id="wpwc_visibility_mode" name="wpwc_visibility_mode">
                        <option value="everywhere" <?php selected(get_option('wpwc_visibility_mode', 'everywhere'), 'everywhere'); ?>><?php _e('Mostrar en todo el sitio', 'wpwc'); ?></option>
                        <option value="exclude" <?php selected(get_option('wpwc_visibility_mode', 'everywhere'), 'exclude'); ?>><?php _e('Excluir en ciertas páginas', 'wpwc'); ?></option>
                        <option value="only" <?php selected(get_option('wpwc_visibility_mode', 'everywhere'), 'only'); ?>><?php _e('Mostrar solo en ciertas páginas', 'wpwc'); ?></option>
                    </select>
                    <p class="description"><?php _e('Selecciona dónde deseas que se muestre el botón de WhatsApp.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_visibility_pages"><?php _e('Páginas Afectadas', 'wpwc'); ?></label></th>
                <td>
                    <select id="wpwc_visibility_pages" name="wpwc_visibility_pages[]" multiple size="5" style="width: 300px;">
                        <?php
                        $pages = get_pages();
                        $selected_pages = get_option('wpwc_visibility_pages', []);
                        foreach ($pages as $page) {
                            $selected = in_array($page->ID, $selected_pages) ? 'selected' : '';
                            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e('Selecciona las páginas donde se aplicará la regla de visibilidad (mantén presionado Ctrl/Cmd para seleccionar múltiples páginas).', 'wpwc'); ?></p>
                </td>
            </tr>
            <?php if (class_exists('WooCommerce')) : ?>
            <tr>
                <th><label for="wpwc_woocommerce_enabled"><?php _e('Habilitar Integración con WooCommerce', 'wpwc'); ?></label></th>
                <td>
                    <input type="checkbox" id="wpwc_woocommerce_enabled" name="wpwc_woocommerce_enabled" value="1" <?php checked(get_option('wpwc_woocommerce_enabled', 0)); ?>>
                    <p class="description"><?php _e('Habilita opciones específicas para WooCommerce.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_woocommerce_pages"><?php _e('Mostrar en Páginas de WooCommerce', 'wpwc'); ?></label></th>
                <td>
                    <select id="wpwc_woocommerce_pages" name="wpwc_woocommerce_pages">
                        <option value="all" <?php selected(get_option('wpwc_woocommerce_pages', 'all'), 'all'); ?>><?php _e('Todas las páginas de WooCommerce', 'wpwc'); ?></option>
                        <option value="product" <?php selected(get_option('wpwc_woocommerce_pages', 'all'), 'product'); ?>><?php _e('Solo páginas de producto', 'wpwc'); ?></option>
                        <option value="shop" <?php selected(get_option('wpwc_woocommerce_pages', 'all'), 'shop'); ?>><?php _e('Solo página de tienda', 'wpwc'); ?></option>
                    </select>
                    <p class="description"><?php _e('Selecciona dónde mostrar el botón en páginas de WooCommerce.', 'wpwc'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="wpwc_woocommerce_message"><?php _e('Mensaje Personalizado para WooCommerce', 'wpwc'); ?></label></th>
                <td>
                    <input type="text" id="wpwc_woocommerce_message" name="wpwc_woocommerce_message" value="<?php echo esc_attr(get_option('wpwc_woocommerce_message', 'Hola, estoy interesado en [producto]')); ?>" class="regular-text">
                    <p class="description"><?php _e('Usa [producto] para incluir el nombre del producto en el mensaje.', 'wpwc'); ?></p>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <?php submit_button(__('Guardar Cambios', 'wpwc')); ?>
    </form>
</div>
<script>
    jQuery(document).ready(function($) {
        $('.wpwc-color-picker').wpColorPicker();
    });
</script>