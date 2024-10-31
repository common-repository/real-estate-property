jQuery(document).ready(function($) {
    /* upload affiliate image  */
    var custom_uploader;
    var thisthis;


    $('.sp_upload_agent_pic').click(function(e) {

        thisthis = $(this);
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $(thisthis).parent().find('input[type="text"]').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();

    });
});