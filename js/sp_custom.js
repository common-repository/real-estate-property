jQuery(document).ready(function ($) {
    //swift from hidden variables
    if (jQuery('#SC_fh_timezone').length > 0) {
        jQuery('#SC_fh_timezone').val(jstz.determine().name());
    }
    if (jQuery('#SC_fh_capturepage').length > 0) {
        jQuery('#SC_fh_capturepage').val(window.location.origin + window.location.pathname);
    }
    if (jQuery('#SC_fh_language').length > 0) {
        jQuery('#SC_fh_language').val(window.navigator.userLanguage || window.navigator.language);
    }

    $("a.sp_anchor").on('click', function (event) {
        // Make sure this.hash has a value before overriding default behavior
        if (this.hash !== "") {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Store hash
            var hash = this.hash;

            // Using jQuery's animate() method to add smooth page scroll
            // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
            $('html, body').animate({
                scrollTop: $(hash).offset().top - 100
            }, 800);
        } // End if
    });

    if (jQuery('#spSlider').length > 0 && jQuery('#spSlider .sp-slide').length > 0) {
        jQuery('#spSlider').sliderPro({
            width: '100%',
            height: '700',
            responsive: true,
            fade: true,
            arrows: true,
            buttons: false,
            fullScreen: false,
            smallSize: 1000,
            mediumSize: 1000,
            largeSize: 3000,
            thumbnailArrows: true,
            autoplay: true,
            aspectRatio: -1,
            touchSwipe: false,
            breakpoints: {
                800: {
                    thumbnailsPosition: 'bottom',
                    thumbnailWidth: 270,
                    thumbnailHeight: 100,
                    aspectRatio: -1,
                    touchSwipe: true,
                },
                640: {
                    orientation: 'horizontal',
                    thumbnailsPosition: 'bottom',
                    thumbnailWidth: 120,
                    thumbnailHeight: 50,
                    aspectRatio: 1,
                    touchSwipe: true,
                }
            }
        });
    } else if (jQuery('.sp_no_featured_img').length > 0) {
        setTimeout(function () {
            jQuery('.sp_no_featured_img').css('margin-top', jQuery('.spfullBanner .spHeader').outerHeight());
        }, 500);
    }

    function SPLocalCapture(res) {
        var name = jQuery.trim(jQuery("#FrmGetInTouch #full_name").val());
        var email = jQuery.trim(jQuery("#FrmGetInTouch #email_offdomain").val());
        var email2 = jQuery.trim(jQuery("#FrmGetInTouch #email2").val());
        var err = false;

        jQuery(".sp-error").remove();

        // for honeypot
        if (email2.length > 0) {
            err = true;
            return false;
        }

        if (name.length <= 0) {
            jQuery("#FrmGetInTouch #full_name").after('<span class="sp-error">Name is required.</span>');
            err = true;
        }

        if (email.length <= 0) {
            jQuery("#FrmGetInTouch #email_offdomain").after('<span class="sp-error">Email is required.</span>');
            err = true;
        } else if (!SP_ValidateEmail(email)) {
            jQuery("#FrmGetInTouch #email_offdomain").after('<span class="sp-error">Invalid email address.</span>');
            err = true;
        }

        var sp_captcha_code = jQuery.trim(jQuery("#sp_captcha_code").val());
        if (sp_captcha_code.length <= 0) {
            jQuery("#sp_captcha_code_container .sp_captcha_field").after('<span class="sp-error">Please enter code.</span>');
            err = true;
        } else if (sp_captcha_code.toLowerCase() != 'swiftcloud') {
            jQuery("#sp_captcha_code_container .sp_captcha_field").after('<span class="sp-error">Please enter correct code.</span>');
            err = true;
        }

        if (!err && jQuery('#SC_browser').val() !== "WP Fastest Cache Preload Bot") {
            jQuery('#FrmGetInTouch #email2').attr('name', 'BlockThisSender');
            jQuery('#FrmGetInTouch #email_offdomain').attr('name', 'email');

            var data = {
                action: 'sp_save_local_capture',
                name: name,
                email: email,
                form_data: $('#FrmGetInTouch').serialize()
            };
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: swiftproperty_ajax_object.ajax_url,
                data: data,
                beforeSend: function (xhr) {
                    if (res) {
                        $('#btn_schedule_visit').html('<i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>').attr('disabled', 'disabled');
                    }
                },
                success: function (response) {
                    if (response) {
                        if (response.type == "success") {
                            $('#btn_schedule_visit').after('<span class="success"> Your request has been received.</span>');
                            $('#full_name, #email2, #phone_number, #email_offdomain, #sp_msg').val('');
                        } else {
                            $('#btn_schedule_visit').after('<span class="error"> Error! while submitting your request.</span>');
                        }
                        $('#btn_schedule_visit').html('Schedule a Visit').removeAttr('disabled');
                    }
                }
            });
        } else {
            return false;
        }
    }
    jQuery("#btn_schedule_visit").click(function () {
        SPLocalCapture(true);
    });

    // set position for right sidebar 
    if (jQuery(".propertyRight").length > 0 && jQuery(window).width() >= 1024) {
        setTimeout(function () {
            var spPropertyDetails = jQuery("#spPropertyDetails").offset().top;
            var spPromoTextContainer = (jQuery("#spPropertyOverview").length > 0) ? jQuery('#spPropertyOverview').offset().top : 0;
            var diff = (spPropertyDetails - spPromoTextContainer);
//            jQuery('.propertyRight').css('margin-top', diff);
        }, 1500);

    }

    $('.sp_listing_listTab a').click(function () {
        var tab_id = $(this).attr('data-tab');
        $(this).parents('.sp_listing_tabs_nav').find('.sp_listing_listTab').removeClass('active');
        $(this).parent('.sp_listing_listTab').addClass('active');
        $(this).parents('.sp_listing_tabs_view').find('.content-tab').find('.pane-tab').hide();
        $("#" + tab_id).show();

        if (tab_id == 'map-list') {
            sp_initialize();
        }
    });

//    jQuery('.sp_listing_tabs_view .sp_listing_tabs_nav .sp_listing_listTab:first-child').addClass('active');
//    jQuery('.sp_listing_tabs_view .content-tab .pane-tab').hide();
//    jQuery('.sp_listing_tabs_view .content-tab .pane-tab:first-child').show();
//
//    // Click function
//    jQuery('.sp_listing_tabs_view .sp_listing_tabs_nav .sp_listing_listTab').click(function () {
//        jQuery('.sp_listing_tabs_view .sp_listing_tabs_nav .sp_listing_listTab').removeClass('active');
//        jQuery(this).addClass('active');
//        jQuery('.sp_listing_tabs_view .content-tab .pane-tab').toggle();
//
////        var activeTab = jQuery(this).find('a').attr('href');
////        jQuery(activeTab).fadeIn();
//        return false;
//    });

    jQuery('.spBtnQRCode').click(function () {
        jQuery('.qrcodeContainer').slideDown().removeClass('display-none');
    });
});

/*
 *  affiliate promoter;
 *  copy to clipboard
 */
(function () {
    'use strict';
    document.body.addEventListener('click', copy, true);
    function copy(e) {
        var
                t = e.target,
                c = t.dataset.copytarget,
                inp = (c ? document.querySelector(c) : null);
        if (inp && inp.select) {
            inp.select();
            try {
                document.execCommand('copy');
                inp.blur();
                jQuery(t).parent().attr('data-tooltip', 'Property URL Copied');
            } catch (err) {
                alert('please press Ctrl/Cmd+C to copy');
            }
        }
    }
})();

//Email validation
function SP_ValidateEmail(mail)
{
    if (/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/.test(mail))
    {
        return (true);
    }
    return (false);
}

