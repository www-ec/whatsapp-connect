(function($) {
    $(document).ready(function() {
        console.log('WPWC Admin JS Loaded'); // Debug

        // Add new agent
        $('.wpwc-add-agent').on('click', function(e) {
            e.preventDefault();
            console.log('Add agent clicked'); // Debug
            var $agents = $('#wpwc-agents');
            var index = $agents.find('.wpwc-agent').length;
            $agents.append(
                '<div class="wpwc-agent">' +
                '<input type="text" name="wpwc_agents[' + index + '][department]" placeholder="Nombre del departamento (ej. Ventas)" class="regular-text" />' +
                '<input type="text" name="wpwc_agents[' + index + '][number]" placeholder="ej. +1234567890" class="regular-text" />' +
                '<button type="button" class="button wpwc-remove-agent">Eliminar</button>' +
                '</div>'
            );
            console.log('Agent added: index ' + index);
        });

        // Remove agent
        $(document).on('click', '.wpwc-remove-agent', function(e) {
            e.preventDefault();
            console.log('Remove agent clicked'); // Debug
            $(this).closest('.wpwc-agent').remove();
            console.log('Agent removed');
        });
    });
})(jQuery);