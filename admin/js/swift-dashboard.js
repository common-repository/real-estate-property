/*------------ swift form -----------------*/
var $compain_var = getUrlVars()['utm_source'];
/*Set cookie if compaign vars exists*/
if ($compain_var === undefined) {
    //do nothing
} else {
    setSwiftCookie('compain_var', window.location.href);
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
    });
    return vars;
}
/*Cookie functions*/
function setSwiftCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getSwiftCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}
jQuery(document).ready(function($) {
    var sp_pluginPrefix = 'sp_';
    /* swift form */
    if (jQuery('.SC_fh_timezone').length > 0) {
        jQuery('#SC_fh_timezone').val(jstz.determine().name());
    }
    if (jQuery('.SC_fh_capturepage').length > 0) {
        jQuery('.SC_fh_capturepage').val(window.location.origin + window.location.pathname);
    }
    if (jQuery('.SC_fh_language').length > 0) {
        jQuery('.SC_fh_language').val(window.navigator.userLanguage || window.navigator.language);
    }
    jQuery("#referer").val(document.URL);
    /*check if cookie exists then add the values in variable*/
    if (getSwiftCookie('compain_var')) {
        jQuery('.trackingvars').val(getSwiftCookie('compain_var'));
    }

    /*---- Dashboard ----*/
    // subscribe form submit
    jQuery(".dashboard-subscribe").on("click", function(e) {
        var error = '';
        jQuery(".ssign-error").remove();
        if (jQuery.trim(jQuery("#email").val()) === '') {
            jQuery("#frm_sp_dashboard_subscribe").after('<span class="ssign-error" style="color:#fff;margin-bottom:10px;">Email is required.</span>');
            error++;
        } else if (!ValidateEmail(jQuery.trim(jQuery("#email").val()))) {
            jQuery("#frm_sp_dashboard_subscribe").after('<span class="ssign-error" style="color:#fff;margin-bottom:10px;">Invalid email.</span>');
            error++;
        }

        if (error > 0) {
            e.preventDefault();
        } else {
            jQuery(this).attr("disabled", "disabled");
            jQuery(this).html('');
            jQuery(this).html('<i class="ssing-loader fa fa-spinner fa-pulse fa-lg fa-fw"></i>');
            var data = {
                'action': sp_pluginPrefix + 'dashboard_subscribe',
                'data': jQuery("#frm_sp_dashboard_subscribe").serialize(),
                'swiftdashboard_subs_form': jQuery('#swiftdashboard_subs_form').val()
            };
            jQuery.post(ajaxurl, data, function(response) {
                if (response == 1) {
                    jQuery(".dashboard-subscribe-block").fadeOut();
                    jQuery("#sp_subscribe").after('<div id="message" class="notice notice-success is-dismissible"><p>Thanks! Welcome to the club.</p></div>');
                }
            });
        }
    });

    //subscribe box close
    jQuery(".dashboard-close-subscribe-block").on("click", function() {
        var data = {
            'action': sp_pluginPrefix + 'dashboard_close_subscribe',
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (response == 1) {
                jQuery(".dashboard-subscribe-toggle").fadeIn();
                jQuery(".dashboard-subscribe-block").fadeOut();
            }
        });
    });

    //subscribe checkbox
    jQuery("#swift_dashboard_subscribe").on("click", function() {
        jQuery(this).attr("disabled", "disabled");
        jQuery('.dashboard-subscribe-toggle spinner').css('visibility', 'visible');
        var check = jQuery("#swift_dashboard_subscribe:checked").val();
        if (check == 1) {
            var data = {
                'action': sp_pluginPrefix + 'dashboard_subscribe_checkbox',
            };
            jQuery.post(ajaxurl, data, function(response) {
                if (response == 1) {
                    jQuery(".swift_dashboard_subscribe").removeAttr("checked");
                    jQuery(".swift_dashboard_subscribe").removeAttr("disabled");
                    jQuery(".dashboard-subscribe-toggle").fadeOut();
                    jQuery(".dashboard-subscribe-block").fadeIn();
                }
            });
        }
    });


});
//Email validation
function ValidateEmail(mail)
{
    if (/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/.test(mail))
    {
        return (true);
    }
    return (false);
}