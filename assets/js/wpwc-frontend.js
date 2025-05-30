jQuery(document).ready(function($) {
    var settings = wpwc_settings;
    var $button = $('.wpwc-whatsapp-button');
    var $selector = $('.wpwc-agent-selector');
    var $container = $('.wpwc-whatsapp-container');
    var clickTimeouts = {}; // Objeto para almacenar tiempos de espera por agente

    if (!$button.length || !$container.length) {
        console.error('WPWC: Button or container not found');
        return;
    }

    if (settings.agents.length === 0) {
        $selector.addClass('wpwc-unavailable-message').show();
        return;
    }

    $button.on('click', function(e) {
        if ($(this).hasClass('wpwc-direct-link')) {
            e.preventDefault(); // Prevenir comportamiento predeterminado
            var agentIndex = $(this).data('agent-index');
            handleClick(agentIndex);
            return;
        }
        e.preventDefault();
        $selector.toggle();
    });

    $(document).on('click', function(e) {
        if (!$button.is(e.target) && !$button.has(e.target).length && !$selector.is(e.target) && !$selector.has(e.target).length) {
            $selector.hide();
        }
    });

    $selector.on('click', 'li', function(e) {
        e.preventDefault(); // Prevenir comportamiento predeterminado
        var agentIndex = $(this).data('agent-index');
        handleClick(agentIndex);
    });

    function handleClick(agentIndex) {
        var agent = settings.agents[agentIndex];
        if (!agent) {
            console.error('WPWC: Agent not found for index:', agentIndex);
            return;
        }

        // Generar un ID único para el clic
        var clickId = 'click_' + settings.click_timestamp + '_' + agentIndex + '_' + Math.random().toString(36).substr(2, 9);

        // Verificar si hay un clic reciente para este agente
        if (clickTimeouts[agentIndex]) {
            console.log('WPWC: Click ignored due to recent click for agent:', agentIndex);
            return;
        }

        // Establecer un tiempo de espera para evitar clics duplicados
        clickTimeouts[agentIndex] = setTimeout(function() {
            delete clickTimeouts[agentIndex];
        }, 2000); // 2 segundos de enfriamiento

        var number = agent.number.replace(/[^0-9]/g, '');
        var message = settings.default_message;

        if (settings.woocommerce_enabled && typeof wc_product !== 'undefined') {
            message = settings.woocommerce_message.replace('[producto]', wc_product.name);
        }

        var url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(message);

        trackClick(agentIndex, clickId, function() {
            window.open(url, '_blank');
        });
    }

    function trackClick(agentIndex, clickId, callback) {
        console.log('WPWC: Attempting to track click for agent index:', agentIndex, 'Click ID:', clickId);

        var data = {
            action: 'wpwc_track_click',
            nonce: settings.nonce,
            agent_index: agentIndex,
            click_id: clickId
        };

        $.ajax({
            url: settings.ajax_url,
            method: 'POST',
            data: data,
            dataType: 'json',
            cache: false, // Deshabilitar caché
            success: function(response) {
                console.log('WPWC: Click tracked successfully:', response);
                if (callback) {
                    callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('WPWC: Error tracking click:', status, error);
                console.error('WPWC: Response:', xhr.responseText);
                if (callback) {
                    callback();
                }
            }
        });
    }
});