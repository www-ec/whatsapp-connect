jQuery(document).ready(function($) {
    $('.wpwc-whatsapp-button').on('click', function(e) {
        var nonce = $(this).data('nonce');
        $.ajax({
            url: wpwc_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'wpwc_track_click',
                nonce: nonce
            },
            success: function(response) {
                console.log('Click tracked');
            }
        });
    });
});