jQuery(document).ready(function ($) {
    /* tooltip */
    //jQuery(".ttip").tooltip();

    /* plugin activation notice dismiss.*/
    jQuery("#scal-admin-notice .notice-dismiss").on('click', function () {
        var data = {
            'action': 'sp_dismiss_notice'
        };
        jQuery.post(sp_admin_ajax_obj.ajax_url, data, function (response) {

        });
    });

    jQuery("#sp_event_recurring").on("change", function () {
        if (jQuery(this).val() == "Recurring") {
            jQuery(".rucurring_container").slideDown();
        } else {
            jQuery(".rucurring_container").slideUp();
        }
    });

    jQuery("#sp_event_repeat").on("change", function () {
        if (jQuery(this).val() == "1" || jQuery(this).val() == "5" || jQuery(this).val() == "6" || jQuery(this).val() == "7") {
            jQuery(".sp_event_repeat_every_container").slideDown();
            jQuery("#repeat_every_label").text($(this).find(':selected').attr('title'));
        } else {
            jQuery(".sp_event_repeat_every_container").slideUp();
        }

        if (jQuery(this).val() == "6") {
            jQuery(".sp_event_repeat_every_week_container").slideDown();
        } else {
            jQuery(".sp_event_repeat_every_week_container").slideUp();
        }
    });

    if (jQuery("ol.sp_property_gal").length > 0) {
        jQuery("ol.sp_property_gal").sortable({
            handle: 'i.fa-arrows',
            items: '> li:not(.no_sort)',
            onDragStart: function ($item, container, _super) {
                // Duplicate items of the no drop area
                if (!container.options.drop)
                    $item.clone().insertAfter($item);
                _super($item, container);
            }
        });
    }
    
    if (jQuery("ol.sp_property_pdf").length > 0) {
        jQuery("ol.sp_property_pdf").sortable({
            handle: 'i.fa-arrows',
            onDragStart: function ($item, container, _super) {
                // Duplicate items of the no drop area
                if (!container.options.drop)
                    $item.clone().insertAfter($item);
                _super($item, container);
            }
        });
    }
});