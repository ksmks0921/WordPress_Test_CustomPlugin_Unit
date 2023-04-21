jQuery(document).ready(function($) {
    $('#my-custom-plugin-button').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_custom_plugin_get_custom_posts'
            },
            success: function(response) {
                alert('Custom posts created!');
            }
        });
    });
});