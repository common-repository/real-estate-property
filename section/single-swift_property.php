<?php
/**
 * The template for displaying all single property
 *
 */
get_header('property');

if (!file_exists(get_template_directory() . '/header-property.php')) {
    copy(SWIFT_PROPERTY__PLUGIN_DIR . 'section/header-property.php', get_template_directory() . '/header-property.php');
}

wp_enqueue_style('property-style', SWIFT_PROPERTY__PLUGIN_URL . 'css/single-property-css.css?time=' . time());
wp_enqueue_style('sp-swiper-css', SWIFT_PROPERTY__PLUGIN_URL . 'css/slider-pro.min.css');
wp_enqueue_style('swiftcloud-fontawesome', SWIFT_PROPERTY__PLUGIN_URL . 'css/font-awesome.min.css', '', '', '');
wp_enqueue_style('swiftcloud-plugin-tooltip', SWIFT_PROPERTY__PLUGIN_URL . 'css/swiftcloud-tooltip.css');
wp_enqueue_style('swiftcloud-mCustomScrollbar', SWIFT_PROPERTY__PLUGIN_URL . '/css/jquery.mCustomScrollbar.css', '', '');
wp_enqueue_style('swiftcloud-labelauty', SWIFT_PROPERTY__PLUGIN_URL . '/css/jquery-labelauty.css', '', '');
wp_enqueue_script('sp-swiper-js', SWIFT_PROPERTY__PLUGIN_URL . 'js/jquery.sliderPro.min.js', array('jquery'), '', true);
wp_enqueue_script('swift-form-jstz', SWIFT_PROPERTY__PLUGIN_URL . "js/jstz.min.js", '', '', true);
wp_enqueue_script('swift-theme-mCustomScrollbar', SWIFT_PROPERTY__PLUGIN_URL . '/js/jquery.mCustomScrollbar.js', array('jquery'), '', true);
wp_enqueue_script('swift-theme-timeago', SWIFT_PROPERTY__PLUGIN_URL . '/js/jquery.timeago.js', array('jquery'), '', true);
wp_enqueue_script('swift-theme-labelauty', SWIFT_PROPERTY__PLUGIN_URL . '/js/jquery-labelauty.js', array('jquery'), '', true);
wp_enqueue_script('swiftcloud-clipboard', SWIFT_PROPERTY__PLUGIN_URL . '/js/clipboard.min.js', array('jquery'), '', true);
wp_enqueue_script('swift-single-property', SWIFT_PROPERTY__PLUGIN_URL . '/js/single-property.js', array('jquery'), '', true);
wp_localize_script('swift-single-property', 'swiftproperty_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'home_url' => home_url(), 'plugin_url' => SWIFT_PROPERTY__PLUGIN_URL));
wp_enqueue_script('swift-qrcode', SWIFT_PROPERTY__PLUGIN_URL . '/js/qrcode.min.js', array('jquery'), '', true);

$sp_form_submission = 'SwiftCRM';
$sp_property_size_opt = $sp_lot_size_opt = esc_attr(get_option("sp_property_size"));
$sp_logo_url = esc_attr(get_option("sp_logo_url"));
$sp_gmap_api = esc_attr(get_option("sp_gmap_api"));
$sp_currency = esc_attr(get_option("sp_currency"));

if (!empty($sp_gmap_api)) {
    wp_enqueue_script('sp-gmap', "https://maps.googleapis.com/maps/api/js?key=" . $sp_gmap_api . "&libraries=places", '', '', '');
}

while (have_posts()) : the_post();
    $address = esc_html(get_post_meta(get_the_ID(), 'sp_street', true));
    $city = esc_html(get_post_meta(get_the_ID(), 'sp_city', true));
    $city = (!empty($city)) ? $city . ", " : "";

    $state = esc_html(get_post_meta(get_the_ID(), 'sp_state', true));
    $state = (!empty($state)) ? $state . ", " : "";

    $zip = esc_html(get_post_meta(get_the_ID(), 'sp_zip', true));
    $zip = (!empty($zip)) ? $zip : "";

    $price = esc_html(get_post_meta(get_the_ID(), 'sp_price', true));
    $beds = esc_html(get_post_meta(get_the_ID(), 'sp_beds', true));
    $baths = esc_html(get_post_meta(get_the_ID(), 'sp_baths', true));
    $sp_status = esc_html(get_post_meta(get_the_ID(), 'sp_status', true));
    $sp_promo_text = esc_html(get_post_meta(get_the_ID(), 'sp_promo_text', true));
    $sp_mls = esc_html(get_post_meta(get_the_ID(), 'sp_mls', true));

    $sp_property_size = esc_html(get_post_meta(get_the_ID(), 'sp_property_size', true));
    $sp_property_size = (!empty($sp_property_size)) ? number_format($sp_property_size, 0, '.', ',') . " " . $sp_property_size_opt : '';
    $sp_lot_size = esc_html(get_post_meta(get_the_ID(), 'sp_lot_size', true));
    $sp_lot_size = (!empty($sp_lot_size)) ? number_format($sp_lot_size, 0, '.', ',') : '';

    $sp_YT_url = esc_html(get_post_meta($post->ID, 'sp_YT_url', true));
    $sp_virtual_3d_url = esc_html(get_post_meta($post->ID, 'sp_virtual_3d_url', true));

    // Property gallery
    // include property featured image
    $gal_str = $gal_thumb_str = '';
    $print_main_img = $print_gal = $print_map = '';
    $slider_cnt = 0;

    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $featured_img_thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'swift_property_gallery_thumb');
    if (!empty($featured_img_url)) {
        $slider_cnt++;
        $gal_str = '<div class="sp-slide"><img class="sp-image" alt="featured image" src="' . esc_url($featured_img_url) . '" data-src="' . esc_url($featured_img_url) . '" data-retina="' . esc_url($featured_img_url) . '"/></div>';
        $gal_thumb_str = '<img class="sp-thumbnail" src="' . esc_url($featured_img_thumb_url) . '" alt="featured thumb image" />';
        $print_main_img = '<img src="' . esc_url($featured_img_url) . '" alt="featured image" />';
    }

    $cornerTag = '';
    if ($sp_status == 'Sold' || $sp_status == 'For Rent' || $sp_status == 'For Lease') {
        $cornerTag = '<div class="single-property-corner-tag sold">' . $sp_status . '</div>';
    } else if ($sp_status == 'Pending') {
        $cornerTag = '<div class="single-property-corner-tag pending">Pending</div>';
    }

    // include property gallery
    $sp_property_gal = get_post_meta(get_the_ID(), 'sp_property_images', true);
    $sp_images = unserialize($sp_property_gal);
    $print_gal_cnt = 1;

    if (!empty($sp_images)) {
        foreach ($sp_images as $sp_img) {
            if (empty($print_main_img)) {
                $print_main_img = '<img data-src="' . esc_url($sp_img) . '" src="' . SWIFT_PROPERTY__PLUGIN_URL . '/images/blank.gif" alt="image-' . esc_attr($print_gal_cnt) . '" />';
            } else {
                if ($print_gal_cnt <= 4) {
                    $print_gal .= '<td width="20%"><img src="' . esc_url($sp_img) . '" alt="image-' . esc_attr($print_gal_cnt) . '" /></td>';
                }
                $print_gal_cnt++;
            }

            $gal_str .= '<div class="sp-slide"><img class="sp-image" src="' . SWIFT_PROPERTY__PLUGIN_URL . '/images/blank.gif" data-src="' . esc_url($sp_img) . '" alt="image-' . $print_gal_cnt . '" data-src="' . esc_url($sp_img) . '" data-retina="' . esc_url($sp_img) . '"/></div>';
            $gal_thumb_str .= '<img class="sp-thumbnail" src="' . esc_url($sp_img) . '" alt="image-' . $print_gal_cnt . '" />';
            $slider_cnt++;
        }
    }

    if (!empty($sp_gmap_api) && (!empty($print_gal) || !empty($print_main_img))) {
        $print_map = '<img id="gmap_img" alt="Google Map of ' . esc_attr($address . ", " . $city . $state . $zip) . '" style="float: right; margin: 0 0 20px 20px; max-width: 200px; clear: both;">';
    }

    global $post;
    $author_id = $post->post_author;
    $agent_email = esc_attr(get_the_author_meta('user_email', $author_id));
    $sp_agent_phone = esc_attr(get_the_author_meta('sp_agent_phone', $author_id));
    $sp_agent_pic = esc_attr(get_the_author_meta('sp_agent_pic', $author_id));
    $sp_agent_form_id = esc_attr(get_the_author_meta('sp_agent_form_id', $author_id));
    $sp_agent_license_no = esc_attr(get_the_author_meta('sp_agent_license_no', $author_id));
    $license_no = (!empty($sp_agent_license_no)) ? "BRE# " . $sp_agent_license_no : '';
    ?>
    <section class="spfullBanner">
        <div class="spSiteBanner">
            <?php if ($slider_cnt > 0): ?>
                <div id="spSlider" class="slider-pro <?php echo ($slider_cnt <= 1) ? "sp_single_slide" : ""; ?>">
                    <div class="sp-slides">
                        <?php echo $gal_str; ?>
                    </div>
                    <div class="sp-thumbnails">
                        <?php echo $gal_thumb_str; ?>
                    </div>
                </div>
            <?php endif; ?>
            <header class="spHeader">
                <div class="logo">
                    <a href="<?php echo esc_url(home_url()); ?>"><?php echo (isset($sp_logo_url) && !empty($sp_logo_url)) ? '<img src="' . esc_url($sp_logo_url) . '" alt="logo" />' : bloginfo('title'); ?></a>
                </div>
                <div class="navigation">
                    <a href="#FrmGetInTouch" class="btn-Schedule"><i class="fa fa-calendar-alt"></i> Schedule Visit</a>
                </div>
            </header>
            <?php if (!empty($featured_img_url)) { ?>
                <div class="scrollNext"><a class="sp_anchor" href="#spPropertyDetails"><i class="fa fa-chevron-down"></i></a></div>
                <div class="slider-title"><h1><?php the_title(); ?></h1></div>
                <?php echo $cornerTag; ?>
            <?php } ?>
        </div>
    </section>
    <section class="spPropertyDetails <?php echo ($slider_cnt <= 0) ? "sp_no_featured_img" : ""; ?>" id="spPropertyDetails">
        <div class="propertyTop">
            <div class="layout">
                <div class="col-9">
                    <div class="property_title">
                        <h2><?php the_title(); ?></h2>
                        <p>
                            <?php echo esc_attr($address); ?>
                            <?php echo (!empty($city) || !empty($state) || !empty($zip)) ? '<span>|</span>' : ''; ?>
                            <?php echo esc_attr($city . $state . $zip); ?>
                            <a class="btnLinktoMap" href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr($address . ", " . $city . $state . $zip); ?>" target="_blank"><i class="fa fa-external-link-alt"></i></a>
                        </p>
                    </div>
                </div>
                <div class="col-3 propertySummary">
                    <div class="propertyRight">
                        <div class="groupPropertyDetailsRight">
                            <?php echo (!empty($price)) ? '<div class="propertyPrice">' . getSwiftPropertyCurrency($sp_currency) . number_format($price, 0, '.', ',') . '</div>' : ''; ?>

                            <?php if (!empty($beds) || !empty($baths)): ?>
                                <div class="propertyDetailsRow">
                                    <?php if (!empty($beds)): ?>
                                        <div class="fieldLeft"><i class="fa fa-bed"></i> <?php echo esc_attr($beds); ?> beds</div>
                                    <?php endif; ?>
                                    <?php if (!empty($baths)): ?>
                                        <div class="fieldLeft"><i class="fa fa-door-closed"></i> <?php echo esc_attr($baths); ?> baths</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($sp_mls)): ?>
                                <div class="propertyDetailsRow">
                                    <?php if (!empty($sp_mls)): ?>
                                        <div class="fieldFullCol">MLS #: <?php echo esc_attr($sp_mls); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($sp_property_size)): ?>
                                <div class="propertyDetailsRow">
                                    <?php if (!empty($sp_property_size)): ?>
                                        <div class="fieldFullCol"><i class="fa fa-expand"></i> <?php echo esc_attr($sp_property_size); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($sp_lot_size)): ?>
                                <div class="propertyDetailsRow">
                                    <?php if (!empty($sp_lot_size)): ?>
                                        <div class="fieldFullCol"><i class="fa fa-expand"></i> <?php echo esc_attr($sp_lot_size); ?> Lot size</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="ctaContainer">
                                <a href="javascript:;" onclick="printContent('spPropertyPrint')" class="btnPrintFlyer tooltip-bottom" data-tooltip="Print Flyer"><i class="fa fa-print"></i></a>
    <!--                                <a href="javascript:;" class="tooltip-bottom" data-tooltip="Send to Phone"><i class="fa fa-mobile-phone"></i></a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($sp_promo_text)): ?>
            <div class="spPromoTextContainer">
                <div class="layout">
                    <div class="col-9">
                        <p class="sp_promot_text"><?php echo esc_html($sp_promo_text); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="propertyContent" id="spPropertyOverview">
            <div class="layout">
                <div class="col-12">
                    <h3>Overview</h3>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <?php if (isset($sp_YT_url) && !empty($sp_YT_url)): ?>
            <div class="propertyYTContainer">
                <h2 class="propertyVirtual3DTitle">Video Tour</h2>
                <div class="sp-YT-video-background">
                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($sp_YT_url); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($sp_virtual_3d_url) && !empty($sp_virtual_3d_url)): ?>
            <div class="propertyVirtual3DContainer">
                <h2 class="propertyVirtual3DTitle">Virtual 3D Tour</h2>
                <div class="sp-YT-video-background">
                    <iframe width="640" height="480" frameborder="0" allowfullscreen="" allow="xr-spatial-tracking" src="https://my.matterport.com/show/?m=<?php echo esc_attr($sp_virtual_3d_url); ?>"></iframe>
                </div>
            </div>
        <?php endif; ?>

        <div class="propertyContent pb-0 border-bottom-0" id="spPropertyOverview">
            <div class="layout">
                <div class="col-12">
                    <?php if ($prop_tags = get_the_term_list(get_the_ID(), 'swift_property_tag', '<ul><li>', '</li><li>', '</li></ul>')): ?>
                        <div class="amenitiesContainer">
                            <fieldset>
                                <legend>Amenities</legend>
                                <?php echo ($prop_tags); ?>
                            </fieldset>
                        </div>
                    <?php endif; ?>
                    <?php if ($prop_cats = get_the_term_list(get_the_ID(), 'swift_property_category', '<ul><li>', '</li><li>', '</li></ul>')): ?>
                        <div class="amenitiesTags">
                            <h3>Tags</h3>
                            <?php echo ($prop_cats); ?>
                        </div>
                    <?php endif; ?>
                    <?php edit_post_link('Edit', '<p class="sp-edit-post tooltip-left" data-tooltip="Only you can see this because you\'re logged in.">', '</p>'); ?>
                </div>
            </div>

            <div class="propertyTop scheduleVisitContainer">
                <div class="layout">
                    <div class="col-3">
                        <div class="propertyPresented">
                            <h3>PRESENTED BY</h3>
                            <div class="propertyAgent">
                                <div class="agentImg">
                                    <?php if (!empty($sp_agent_pic)): ?>
                                        <img src="<?php echo esc_url($sp_agent_pic); ?>" alt="<?php the_author(); ?>" />
                                    <?php else: ?>
                                        <img src="<?php echo SWIFT_PROPERTY__PLUGIN_URL; ?>/images/swiftproperty_user_avatar.png" alt="<?php the_author(); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="agentInfo">
                                    <h5><?php the_author(); ?></h5>
                                    <p>
                                        <?php
                                        echo (!empty($sp_agent_phone)) ? $sp_agent_phone . '<br/>' : "";
                                        if (!empty($agent_email)) {
                                            $agent_email_parts = explode('@', $agent_email);
                                            echo '<a class="sp_agent_email" href="mailto:' . $agent_email . '">' . $agent_email . '</a>';
                                            echo '<a class="sp_agent_email_mobile" href="mailto:' . $agent_email . '"><i class="fa fa-envelope"></i></a><br/>';
                                        }
                                        echo (!empty($license_no)) ? $license_no : "";
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9 border-right-0">
                        <div class="getInTouch">
                            <h5>Schedule a Visit</h5>
                            <?php if ($sp_form_submission == 'SwiftCRM'): ?>
                                <?php if (!empty($sp_agent_form_id)): ?>
                                    <?php
                                    $sd_captcha_arr = array("sc1.jpg", "sc2.jpg", "sc3.jpg", "sc4.jpg");
                                    $rand_keys = array_rand($sd_captcha_arr, 1);
                                    ?>
                                    <form id="FrmGetInTouch" name="FrmGetInTouch" method="post" action="">
                                        <div class="formCol6">
                                            <input type="text" name="swift_name_both" id="full_name" placeholder="Full Name*" required="required">
                                            <input name="email" id="email2" type="email">
                                            <input name="email_offdomain" id="email_offdomain" required="required" type="email" placeholder="Email*">
                                            <input type="tel" name="phone_number" id="phone_number"  placeholder="Phone#">

                                            <div class="field" id="sp_captcha_code_container">
                                                <div class="sp_captcha_img">
                                                    <img src="<?php echo SWIFT_PROPERTY__PLUGIN_URL . '/images/' . $sd_captcha_arr[$rand_keys]; ?>" alt="captcha" />
                                                </div>
                                                <div class="sp_captcha_field">
                                                    <input type="text" name="sp_captcha_code" id="sp_captcha_code" placeholder="Please enter code" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="formCol6">
                                            <textarea name="sp_msg" placeholder="Your Messages">I'm interested in <?php echo $address . ", " . $city . $state . $zip; ?> & would like to see it</textarea>
                                            <div id="btnContainer" style="display: inline-block"></div>
                                        </div>

                                        <input type="hidden" name="ip_address" id="ip_address" value="<?php echo esc_attr($_SERVER['SERVER_ADDR']) ?>">
                                        <input type="hidden" name="browser" id="SC_browser" value="<?php echo esc_attr($_SERVER['HTTP_USER_AGENT']) ?>">
                                        <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                                        <input type="hidden" id="SC_fh_timezone" value="" name="timezone">
                                        <input type="hidden" id="SC_fh_language" value="" name="language">
                                        <input type="hidden" id="SC_fh_capturepage" value="" name="capturepage">
                                        <input type="hidden" value="<?php echo esc_attr($sp_agent_form_id); ?>" id="formid" name="formid">
                                        <input type="hidden" name="vTags" id="vTags" value="#real estate">
                                        <input type="hidden" name="vThanksRedirect" value="">
                                        <input type="hidden" id="sc_lead_referer" value="" name="sc_lead_referer"/>
                                        <input type="hidden" value="817" name="iSubscriber">
                                        <input type="hidden" id="sc_referer_qstring" value="" name="sc_referer_qstring"/>
                                        <input type="hidden" name="tagscore_buyer" value="<?php echo esc_attr($price); ?>" />
                                        <?php
                                        if (isset($_COOKIE['sc_lead_scoring']) && !empty($_COOKIE['sc_lead_scoring'])) {
                                            $sc_lead_scoring_cookie = unserialize(stripslashes($_COOKIE['sc_lead_scoring']));
                                            if (!empty($sc_lead_scoring_cookie)) {
                                                foreach ($sc_lead_scoring_cookie as $key => $val) {
                                                    echo '<input type="hidden" id="' . sanitize_text_field($key) . '" value="' . sanitize_text_field($val) . '" name="extra_' . sanitize_text_field($key) . '">';
                                                }
                                            }
                                        }
                                        ?>
                                        <script type="text/javascript">
                                            var button = document.createElement("button");
                                            button.innerHTML = 'Schedule a Visit';
                                            var body = document.getElementById("btnContainer");
                                            body.appendChild(button);
                                            button.id = "btn_schedule_visit";
                                            button.name = "btn_schedule_visit";
                                            button.className = "";
                                            button.value = 'Schedule a Visit';
                                            button.type = 'button';
                                        </script>
                                        <noscript>
                                        <p style='color:red;font-size:18px;'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                                        </noscript>
                                    </form>
                                <?php else: ?>
                                    <p class="sp-formid-error">Heads up! Your form will not display until you add a Form ID for Agent.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php
                                $sd_captcha_arr = array("sc1.jpg", "sc2.jpg", "sc3.jpg", "sc4.jpg");
                                $rand_keys = array_rand($sd_captcha_arr, 1);
                                ?>
                                <form id="FrmGetInTouch" name="FrmGetInTouch" method="post" action="">
                                    <div class="formCol6">
                                        <input type="hidden" value="<?php echo esc_attr($sp_agent_form_id); ?>" id="formid" name="formid">
                                        <input type="text" name="swift_name_both" id="full_name" placeholder="Full Name*" required="required">
                                        <input name="email" id="email2" type="email">
                                        <input type="tel" name="phone_number" id="phone_number"  placeholder="Phone#">
                                        <!--<input type="email" name="email" id="email" placeholder="Email*" required="required">-->
                                        <input name="email_offdomain" id="email_offdomain" required="required" type="email" placeholder="Email*">
                                        <div class="field" id="sp_captcha_code_container">
                                            <div class="sp_captcha_img">
                                                <img src="<?php echo SWIFT_PROPERTY__PLUGIN_URL . '/images/' . $sd_captcha_arr[$rand_keys]; ?>" alt="captcha" />
                                            </div>
                                            <div class="sp_captcha_field">
                                                <input type="text" name="sp_captcha_code" id="sp_captcha_code" placeholder="Please enter code" class="mt-0" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="formCol6">
                                        <textarea name="sp_msg" id="sp_msg" placeholder="Your Messages">I'm interested in <?php echo esc_attr($address . ", " . $city . $state . $zip); ?> & would like to see it</textarea>
                                        <div id="btnContainer" style="display: inline-block"></div>
                                    </div>

                                    <script type="text/javascript">
                                        var button = document.createElement("button");
                                        button.innerHTML = 'Schedule a Visit';
                                        var body = document.getElementById("btnContainer");
                                        body.appendChild(button);
                                        button.id = "btn_schedule_visit";
                                        button.name = "btn_schedule_visit";
                                        button.className = "";
                                        button.value = 'Schedule a Visit';
                                        button.type = 'button';
                                    </script>
                                    <noscript>
                                    <p style='color:red;font-size:18px;'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                                    </noscript>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $sp_property_gal = get_post_meta(get_the_ID(), 'sp_property_documents', true);
        $sp_pdfs = unserialize($sp_property_gal);
        if (isset($sp_pdfs) && !empty($sp_pdfs)) {
            ?>
            <div class="propertyDocumentContainer">
                <div class="layout">
                    <div class="col-12">
                        <?php
                        echo '<h2 class="propertyDocumentTitle">Documents</h2>';

                        echo '<ul>';
                        foreach ($sp_pdfs as $sp_pdf) {
                            echo '<li><a href="' . esc_url($sp_pdf['pdf_url']) . '" target="_blank"><i class="fa fa-file-pdf"></i> ' . esc_html($sp_pdf['pdf_title']) . '</a></li>';
                        }
                        echo '</ul>';
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>

    <section class="socialSharing">
        <div class="layout">
            <a class="tooltip-top" data-tooltip="Send to friend" href="mailto:?subject=You would like this&body=Hello%0A%0AYou%20will%20like%20this%3A%0A<?php echo get_permalink(get_the_ID()); ?>"><i class="fa fa-envelope"></i></a>
            <a class="spBtnCopy tooltip-top" data-tooltip="Copy this property URL" data-copytarget="#sp_property_url" href="javascript:void(0);"><i class="fa fa-copy" data-copytarget="#sp_property_url"></i></a>
            <a class="spBtnQRCode tooltip-top" data-tooltip="Scan URL" href="javascript:void(0);"><i class="fa fa-qrcode"></i></a>
            <input type="text" id="sp_property_url" value="<?php echo get_permalink(get_the_ID()); ?>" style="position: absolute;left: -2000px;" />
            <a class="tooltip-top" data-tooltip="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink(get_the_ID())); ?>"><i class="fab fa-facebook-f"></i></a>
            <a class="tooltip-top" data-tooltip="Share on Twitter" href="https://twitter.com/share?url=<?php echo get_permalink(get_the_ID()); ?>"><i class="fab fa-twitter"></i></a>

            <div class="qrcodeContainer display-none">
                <div id="sp_qrcode"></div>
            </div>
        </div>
    </section>
    <section class="map">
        <div class="layout">
            <div class="printFlyerRow">
                <div id='spPropertyPrint' style="display: none;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin: 15px 0px;">
                                    <tr>
                                        <td width="90%">
                                            <h1><?php the_title(); ?></h1>
                                            <p>
                                                <?php echo $address; ?>
                                                <?php echo (!empty($city) || !empty($state) || !empty($zip)) ? '<span>|</span>' : ''; ?>
                                                <?php echo $city . $state . $zip; ?>
                                            </p>
                                        </td>
                                        <td width="10%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding-bottom: 5px; font-size: 20px; font-weight: 600; color: #333;"><?php echo (!empty($price)) ? '$' . number_format($price, 0, '.', ',') : ''; ?></td>
                                                </tr>
                                                <?php if (!empty($beds)): ?>
                                                    <tr>
                                                        <td style="padding-bottom: 5px;"><i class="fa fa-bed"></i> <?php echo $beds; ?> Beds</td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($baths)): ?>
                                                    <tr>
                                                        <td><i class="fa fa-door-closed"></i> <?php echo $baths; ?> Baths</td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($sp_property_size)): ?>
                                                    <tr>
                                                        <td><i class="fa fa-expand"></i> <?php echo $sp_property_size; ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($sp_lot_size)): ?>
                                                    <tr>
                                                        <td><i class="fa fa-expand"></i> <?php echo $sp_lot_size; ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><?php echo $print_main_img; ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table width="100%" cellpadding="2" cellspacing="0" class="spPrintGal">
                                    <tr>
                                        <?php echo $print_gal; ?>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h3>Overview</h3>
                                <div id="sp_print_qrcode" style="float: right; margin: 0 0 20px 20px; clear: both;"></div>
                                <?php echo $print_map; ?>
                                <?php the_content(); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php if ($prop_tags = get_the_term_list(get_the_ID(), 'swift_property_tag', '<ul><li><i class="fa fa-check"></i>', '</li><li>', '</li></ul>')): ?>
                                    <h3>Amenities</h3>
                                    <?php echo $prop_tags; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php if ($prop_cats = get_the_term_list(get_the_ID(), 'swift_property_category', '<ul><li>', '</li><li>', '</li></ul>')): ?>
                                    <h3>Tags</h3>
                                    <?php echo $prop_cats; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 10px; background: #333; color: #fff;">&copy; <?php echo date('Y'); ?> <?php echo bloginfo('name'); ?></td>
                        </tr>
                    </table>
                </div>
                <a href="javascript:;" onclick="printContent('spPropertyPrint')" class="btnPrintFlyer"><i class="fa fa-print"></i> Print Flyer</a>
            </div>
        </div>
        <div class="layout">
            <div class="directionRow">
                <form action="https://maps.google.com/maps" method="get" target="_blank" class="DirectionsTous" _lpchecked="1">
                    <input type="text" name="saddr" placeholder="Enter Your Starting Address or Location here" />
                    <input name="daddr" value="<?php echo $address . $city . $state . $zip; ?>" type="hidden">
                    <button>Directions to Property</button>
                </form>
            </div>
        </div>
        <?php if (!empty($sp_gmap_api)) { ?>
            <div id="map_canvas" style="width:100%;"></div>
        <?php } ?>

    </section>
    <section class="ftTopBar">
        <div class="copyRight">&copy; <?php echo date('Y'); ?> <?php echo bloginfo('name'); ?></div>
        <div class="userFullLinks"><a href="https://SwiftCRM.Com/">Single Property Websites</a>::<a href="https://SwiftCRM.Com/">Wordpress Real Estate Plugin</a></div>
        <div class="providerRight"><img src="<?php echo SWIFT_PROPERTY__PLUGIN_URL; ?>images/equal-housing.png" alt="Equal Housing Provider" />Equal Housing Provider</div>
    </section>
    <!--Cookie policy-->
    <?php /*
      <div class="swiftCloudThemeChat">
      <div class="chatWebFromBox"></div>
      <div class="swiftCloudThemeChatConversion">
      <!-- ask a question -->
      <div class="askQuestionMainContainer pos-relative">
      <div class="chatClose">
      <a href="javascript:;" class="tooltip-left" data-tooltip="Close"><i class="fa fa-times"></i></a>
      </div>
      <div class="askQuestionContainer">
      <div class="swiftTeamChat">
      <div class="sTeamAvtar tooltip-right" data-tooltip="SwiftCloud Robot">
      <img src="<?php echo SWIFT_PROPERTY__PLUGIN_URL; ?>/images/BotSmall.gif"/>
      </div>
      <div class="sTeamMsg">
      <div class="msgContant">
      <p>Hi there! What questions can I answer about <strong><?php echo the_title(); ?></strong>.</p>
      </div>
      </div>
      </div>
      <div class="swiftChatReplayBox">
      <div class="msgSend">
      <button class="btn sendBtn tooltip-left" data-tooltip="Send"><i class="fa fa-paper-plane"></i></button>
      </div>
      <textarea class="form-control swiftChatAskQueReplay" placeholder="Send a message..."></textarea>
      </div>
      <input type="hidden" name="hiddenDocAction" id="hiddenDocAction" />
      <input type="hidden" name="hiddenSequence" id="hiddenSequence" value="1" />
      <input type="hidden" name="isAnswerRequired" id="isAnswerRequired" value="Yes" />
      <input type="hidden" name="fieldValidation" id="fieldValidation" value="" />
      </div>
      </div>
      </div>
      </div>
     */ ?>
<?php endwhile; ?>
<script type="text/javascript">
    function printContent(div_id) {
<?php if (!empty($sp_gmap_api)) { ?>
            var staticMapUrl = "https://maps.googleapis.com/maps/api/staticmap?key=<?php echo $sp_gmap_api; ?>";
            staticMapUrl += "&center=<?php echo $address . ", " . $city . $state . $zip; ?>";
            staticMapUrl += "&size=600x400";
            staticMapUrl += "&zoom=16&scale=2";
            staticMapUrl += "&maptype=roadmap";
            staticMapUrl += "&markers=color:red|<?php echo $address . ", " . $city . $state . $zip; ?>";
            var imgMap = document.getElementById("gmap_img");
            imgMap.src = staticMapUrl;
            imgMap.style.display = "block";
<?php } ?>

        var sp_print_qrcode = new QRCode("sp_print_qrcode", {
            text: "<?php echo get_permalink() ?>",
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
        });

        setTimeout(function () {
            var mywindow = window.open('', 'PRINT', 'height=600,width=800');
            mywindow.document.write('<html><head>');
            mywindow.document.write('<meta charset="UTF-8">');
            mywindow.document.write('<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">');
            mywindow.document.write('<meta name="viewport" content="width=device-width, initial-scale=1">');
            mywindow.document.write('<style>@media print {body {-webkit-print-color-adjust: exact;}.aligncenter {clear: both;display: block;}.alignleft {float: left;}.alignright {float: right;}}body{margin: 0px;padding: 0px;font-family: "helvetica";font-weight: 400;}.layout{margin: 0 auto;width: 100%;max-width: 1000px;}h1{font-size: 30px;line-height: 36px;font-weight: 600;color: #333;margin: 0px 0px 10px 0px;}h3{font-size: 20px;line-height: 24px;font-weight: 600;color: #333;margin: 20px 0px 10px 0px;}p{margin: 0px;padding: 0px;font-size: 16px;line-height: 18px;font-weight: 500;color: #888;}tr td{vertical-align: top;color: #444;font-weight: 400;font-size: 14px;}tr td img{max-width: 100%;height: auto;}.spPrintGal tr td img{max-width: 100%;max-height: 100px;} tr td ul{margin: 0px;padding: 0px 0px 20px;}tr td ul li{margin: 0px 20px 10px 0px;padding: 0px;list-style: none;display: inline-block;}tr td ul li i{margin-right: 5px;}ul li {display: list-item;border: 0;margin: 0;padding: 0;}</style>');
            mywindow.document.write('</head><body >');
            mywindow.document.write(document.getElementById(div_id).innerHTML);
            mywindow.document.write('</body></html>');
            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10*/
            mywindow.print();
            mywindow.close();
            return true;
        }, 1500);
    }
</script>

<?php if (!empty($sp_gmap_api)) { ?>
    <script type="text/javascript">
        var geocoder;
        var map;
        var address = "<?php echo $address . ", " . $city . $state . $zip; ?>";

        function initialize() {
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(34.052235, -118.243683);
            var myOptions = {
                zoom: 18,
                center: latlng,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                navigationControl: true,
                //                mapTypeId: google.maps.MapTypeId.SATELLITE
            };
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            if (geocoder) {
                geocoder.geocode({
                    'address': address
                }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                            map.setCenter(results[0].geometry.location);

                            var contentString = '<h2 id="firstHeading" class="firstHeading"><?php echo get_the_title(); ?></h2> <?php echo $address . ', ' . $city . $state . $zip; ?>';
                            var infowindow = new google.maps.InfoWindow({
                                content: contentString,
                                size: new google.maps.Size(150, 50)
                            });

                            var marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map,
                                title: address
                            });
                            google.maps.event.addListener(marker, 'click', function () {
                                infowindow.open(map, marker);
                            });
                        }
                    }
                });
            }
        }

        window.addEventListener('load', function () {
            setTimeout(function () {
                initialize();

                if (jQuery('#sp_qrcode').length > 0) {
                    var qrcode = new QRCode("sp_qrcode", {
                        text: "<?php echo get_permalink() ?>",
                        width: 200,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                    });
                }
            }, 1500);
        });
    </script>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>