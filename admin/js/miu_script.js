jQuery(document).ready(function () {
    // remove featured image from gallery list
    jQuery(document).on("click", "#remove-post-thumbnail", function () {
        jQuery("ol.sp_property_gal li.no_sort").remove();
    });

    jQuery(document).ajaxComplete(function (event, xhr, settings) {
        // insert featured image
        if (typeof settings.data === 'string' && !(/thumbnail_id=-1/.test(settings.data)) && /action=get-post-thumbnail-html/.test(settings.data) && xhr.responseJSON && typeof xhr.responseJSON.data === 'string') {
            if (jQuery("ol.sp_property_gal li.no_sort").length > 0) {
                jQuery("ol.sp_property_gal li.no_sort").remove();
            }
            var metabox_content = '';
            var itemsCount = jQuery.now();
            var img = jQuery('#set-post-thumbnail').find('img').attr('src');
            metabox_content += '<li class="no_sort" id=row-' + itemsCount + '>';
            metabox_content += '<div class="sp_img_sorter"><i class="fas fa-arrows-alt"></i></div>';
            metabox_content += '<img class="sp_property_img" src="' + img + '" alt="img-' + itemsCount + '" />';
            metabox_content += '<input id="Image_button-' + itemsCount + '" class="button button-primary btn_sp_prop_gal" data-img="' + itemsCount + '" type="button" value="Upload Image" />';
            metabox_content += '<input class="miu-remove button sp-featured-image" type=\'button\' value=\'Remove\' id=\'remove-' + itemsCount + '\' /> <strong>Featured Image</strong>';
            metabox_content += '</li>';

            if (jQuery('#sp_property_images ol.sp_property_gal li').length > 0) {
                jQuery('#sp_property_images ol.sp_property_gal li:first').before(metabox_content);
            } else {
                jQuery('#sp_property_images ol.sp_property_gal').append(metabox_content);
            }
        }

        // remove featured image
        if (typeof settings.data === 'string' && (/thumbnail_id=-1/.test(settings.data)) && /action=get-post-thumbnail-html/.test(settings.data) && xhr.responseJSON && typeof xhr.responseJSON.data === 'string') {
            if (jQuery("ol.sp_property_gal li.no_sort").length > 0) {
                jQuery("ol.sp_property_gal li.no_sort").remove();
            }
        }
    });

    jQuery(document).on("click", '.miu-remove', function (e) {
        e.preventDefault();
        var id = jQuery(this).attr("id");
        var btn = id.split("-");
        var img_id = btn[1];
        jQuery("#row-" + img_id).remove();

        if (jQuery(this).hasClass('sp-featured-image')) {
            jQuery("#remove-post-thumbnail").trigger('click');
        }
    });

    var custom_uploader;
    var row_id;
    jQuery(document).on('click', '.btn_sp_prop_gal', function (e) {
        var _this = jQuery(this);
        row_id = jQuery(this).attr('data-img');
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
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('.sp_prop_gal_img_path_' + row_id).val(attachment.url);
            jQuery(document).find('#row-' + row_id).find('.sp_property_img').attr('src', attachment.url);

            if (jQuery('#row-' + row_id).hasClass('no_sort')) {
                wp.media.featuredImage.set(attachment.id);
            }
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

    var formfield;
    var img_id;
    jQuery(document).on("click", '.Image_button', function (e) {
        e.preventDefault();
        var id = jQuery(this).attr("id");
        var btn = id.split("-");
        img_id = btn[1];

        jQuery('html').addClass('Image');
        formfield = jQuery('#img-' + img_id).attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });

//    window.original_send_to_editor = window.send_to_editor;
//    window.send_to_editor = function (html) {
//        if (formfield) {
//            fileurl = jQuery('img', html).attr('src');
//            jQuery('#img-' + img_id).val(fileurl);
//            tb_remove();
//            jQuery('html').removeClass('Image');
//        } else {
//            window.original_send_to_editor(html);
//        }
//    };


    // *************** PDF ***************
    jQuery(document).on("click", '.sp-pdf-remove', function (e) {
        e.preventDefault();
        var id = jQuery(this).attr("data-id");
        jQuery("#row-pdf-" + id).remove();
    });

    var custom_pdf_uploader;
    var row_pdf_id;
    jQuery(document).on('click', '.btn_sp_pdf_gal', function (e) {
        var _this = jQuery(this);
        row_pdf_id = jQuery(this).attr('data-pdf');
        e.preventDefault();

        //If the uploader object has already been created, reopen the dialog
        if (custom_pdf_uploader) {
            custom_pdf_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_pdf_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose PDF',
            library: {
                type: 'application/pdf'
            },
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_pdf_uploader.on('select', function () {
            attachment = custom_pdf_uploader.state().get('selection').first().toJSON();
            jQuery('.sp_prop_gal_pdf_path_' + row_pdf_id).val(attachment.url);
        });
        //Open the uploader dialog
        custom_pdf_uploader.open();
    });

    var formfield_pdf;
    var pdf_id;
    jQuery(document).on("click", '.pdf_button', function (e) {
        e.preventDefault();
        var id = jQuery(this).attr("id");
        var btn = id.split("-");
        pdf_id = btn[1];

        jQuery('html').addClass('Image');
        formfield_pdf = jQuery('#img-' + pdf_id).attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });

    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function (html) {
        if (formfield_pdf) {
            fileurl = jQuery('img', html).attr('src');
            jQuery('#pdf-' + pdf_id).val(fileurl);
            tb_remove();
            jQuery('html').removeClass('Image');
        } else {
            window.original_send_to_editor(html);
        }
    };

    jQuery('body').on('change', '#miu_file', function () {
        var fd = new FormData();
        var files_data = jQuery('.upload-form .files-data'); // The <input type="file" /> field

        // Loop through each data and create an array file[] containing our files data.
        jQuery.each(jQuery(files_data), function (i, obj) {
            jQuery.each(obj.files, function (j, file) {
                fd.append('files[' + j + ']', file);
            });
        });

        // our AJAX identifier
        fd.append('action', 'swift_property_cvf_upload_files');

        jQuery.ajax({
//            xhr: function () {
//                var xhr = new window.XMLHttpRequest();
//                xhr.upload.addEventListener("progress", function (evt) {
//                    if (evt.lengthComputable) {
//                        var percentComplete = evt.loaded / evt.total;
//                        percentComplete = parseInt(percentComplete * 100);
//                        jQuery('.progress-bar').width(percentComplete + '%');
//                        jQuery('.progress-bar').html(percentComplete + '%');
//                    }
//                }, false);
//                return xhr;
//            },
            type: 'POST',
            url: sp_multi_image_obj.ajax_url,
            data: fd,
            contentType: false,
            processData: false,
            beforeSend: function () {
//                jQuery(".progress-bar").width('0%');
                jQuery('.upload-form .spinner').addClass('is-active');
            },
            success: function (response) {
                jQuery('#sp_property_images ol.sp_property_gal').append(response); // Append Server Response
                jQuery('.upload-form .spinner').removeClass('is-active');
            }
        });
    });

    jQuery('body').on('click', '.upload-form .btn-upload', function (e) {
        e.preventDefault;

        var fd = new FormData();
        var files_data = jQuery('.upload-form .files-data'); // The <input type="file" /> field

        var input_file = jQuery("#miu_file");
        var files = input_file.prop("files");
        var names = jQuery.map(files, function (val) {
            return val.name;
        });
        jQuery.each(names, function (j, file) {
            fd.append('files[' + j + ']', file);
        });

        // Loop through each data and create an array file[] containing our files data.
        jQuery.each(jQuery(files_data), function (i, obj) {
            jQuery.each(obj.files, function (j, file) {
                fd.append('files[' + j + ']', file);
            });
        });

        // our AJAX identifier
        fd.append('action', 'swift_property_cvf_upload_files');

        jQuery.ajax({
            type: 'POST',
            url: sp_multi_image_obj.ajax_url,
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                jQuery('#sp_property_images ol.sp_property_gal').append(response); // Append Server Response
            }
        });
    });
});

function addRow(image_url) {
    if (typeof (image_url) === 'undefined')
        image_url = sp_multi_image_obj.plug_url + "admin/images/blank-img.png";
    var tmp = jQuery.now();
    var emptyRowTemplate = '<li id="row-' + tmp + '">';
    emptyRowTemplate += '<div class="sp_img_sorter"><i class="fas fa-arrows-alt"></i></div>';
    if (image_url) {
        emptyRowTemplate += '<img class="sp_property_img" src="' + image_url + '" alt="">';
    }
    emptyRowTemplate += '<input id="img-' + tmp + '" type="text" class="sp_prop_gal_img_url sp_prop_gal_img_path_' + tmp + '" name="sp_property_images[' + tmp + ']" value="' + image_url + '" />';
    emptyRowTemplate += '<input id="Image_button-' + tmp + '" class="button button-primary btn_sp_prop_gal" data-img="' + tmp + '" type="button" value="Upload Image" />';
    emptyRowTemplate += '<input class="miu-remove button" type="button" value="Remove" id="remove-' + tmp + '" />';

    emptyRowTemplate += '</li>';
    jQuery('#sp_property_images ol.sp_property_gal').append(emptyRowTemplate);
}

function addPDFRow() {
    var tmp = jQuery.now();
    var emptyRowTemplate = '<li id="row-pdf-' + tmp + '">';
    emptyRowTemplate += '<div class="sp_pdf_sorter"><i class="fas fa-arrows-alt"></i></div>';
    emptyRowTemplate += '<input id="pdf-title-' + tmp + '" type="text" class="sp_prop_gal_pdf_title" name="sp_property_documents_title[' + tmp + ']" value="" placeholder="Document Title" />';
    emptyRowTemplate += '<input id="pdf-' + tmp + '" type="text" class="sp_prop_gal_pdf_url sp_prop_gal_pdf_path_' + tmp + '" name="sp_property_documents[' + tmp + ']" value="" />';
    emptyRowTemplate += '<input id="pdf_button-' + tmp + '" class="button button-primary btn_sp_pdf_gal" data-pdf="' + tmp + '" type="button" value="Upload PDF" />';
    emptyRowTemplate += '<input class="sp-pdf-remove button" type="button" value="Remove" data-id="' + tmp + '" id="pdf-remove-' + tmp + '" />';
    emptyRowTemplate += '</li>';
    jQuery('#sp_property_documents ol.sp_property_pdf').append(emptyRowTemplate);
}