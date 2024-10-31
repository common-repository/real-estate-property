<?php
/*
 *      Swift Review Dashboard
 */
if (!function_exists('swift_property_dashboard_callback')) {

    function swift_property_dashboard_callback() {

        include_once( ABSPATH . WPINC . '/feed.php' );
        wp_enqueue_script('swift-form-jstz', SWIFT_PROPERTY__PLUGIN_URL . 'admin/js/jstz.min.js', '', '', true);
        wp_enqueue_style('swift-dashboard', SWIFT_PROPERTY__PLUGIN_URL . 'admin/css/swift-dashboard.css', '', '', '');
        wp_enqueue_script(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard-script', SWIFT_PROPERTY__PLUGIN_URL . 'admin/js/swift-dashboard.js', array('jquery'), '', true);

        $subscribed = get_option(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe');
        $subs_checkbox_flag = isset($_COOKIE[SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_unsubscribe']) && $_COOKIE[SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_unsubscribe'] == 1 ? "block" : "none";
        $subs_box_flag = (empty($subscribed) && $subs_checkbox_flag == 'none') ? 'block' : 'none';
        ?>

        <div class="wrap dashboard-wrap">
            <h2 class="dashboard-title">Updates & Tips</h2>
            <div class="dashboard-subscribe-toggle" style="display:<?php echo $subs_checkbox_flag; ?>;">
                <label for="swift_dashboard_subscribe">Want to subscribe? </label><input type="checkbox" value="1" name="swift_dashboard_subscribe" id="swift_dashboard_subscribe" class="swift_dashboard_subscribe" />
            </div>
            <div class="clear"></div>
            <hr/>
            <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
                <div id="message" class="notice notice-success is-dismissible below-h2">
                    <p>Setting updated successfully.</p>
                </div>
            <?php } ?>
            <!--subscribe box-->
            <div class="dashboard-subscribe-block" id="sp_subscribe" style="display: <?php echo $subs_box_flag; ?>;">
                <div class="dashboard-close-subscribe-block"><span class="dashicons dashicons-no-alt"></span></div>
                <p>Get Update News, Free Gifts, Best Practices, Tips, Tricks & More Privacy respected; emails never sold or shared. Emails sent approximately 1x/month.</p>
                <form method="post" id="frm_sp_dashboard_subscribe">
                    <input type="email" name="email" id="email" class="regular-text" required="required" placeholder="Enter email" />
                    <input type="hidden" name="ip_address" id="ip_address" value="<?php echo esc_attr($_SERVER['SERVER_ADDR']) ?>">
                    <input type="hidden" name="browser" id="SC_browser" value="<?php echo esc_attr($_SERVER['HTTP_USER_AGENT']) ?>">
                    <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                    <input type="hidden" name="timezone" value="" id="SC_fh_timezone" class="SC_fh_timezone">
                    <input type="hidden" name="language" id="SC_fh_language" class="SC_fh_language" value="" >
                    <input type="hidden" name="capturepage" id="SC_fh_capturepage" class="SC_fh_capturepage" value="">
                    <input type="hidden" name="formid" value="648" id="formid" />
                    <input type="hidden" name="vTags" id="vTags" value="#swiftdashboard">
                    <input type="hidden" name="vThanksRedirect" value="<?php echo admin_url("admin.php?" . esc_attr($_SERVER['QUERY_STRING'])); ?>">
                    <input type="hidden" name="sc_lead_referer" id="sc_lead_referer" value=""/>
                    <input type="hidden" name="iSubscriber" value="817" >
                    <input type="hidden" name="sc_referer_qstring" value="" id="sc_referer_qstring" />
                    <?php wp_nonce_field('swiftdashboard_subs_form', 'swiftdashboard_subs_form'); ?>
                    <button type="button" class="button button-orange dashboard-subscribe"><span class="dashicons dashicons-yes"></span> Hook Me Up & Keep Me Posted</button>
                </form>
            </div>

            <div class="inner_content">
                <div class="dashboard-row">
                    <div class="dashboard-col-8 col-left">
                        <div class="col-dashboard-block">
                            <div class="col-dashboard-block-title"><h3>Updates, News & Best Practices</h3></div>
                            <div class="col-dashboard-block-content">
                                <?php
                                $rss = fetch_feed('https://SwiftCRM.Com/support/tag/calendar/feed'); //Change here
                                $maxitems = 0;

                                if (!is_wp_error($rss)) : // Checks that the object is created correctly
                                    // Figure out how many total items there are, but limit it to 5.
                                    $maxitems = $rss->get_item_quantity(10);

                                    // Build an array of all the items, starting with element 0 (first element).
                                    $updates = $rss->get_items(0, $maxitems);
                                endif;
                                ?>
                                <?php if ($maxitems == 0) : ?>
                                    <div class="col-dashboard-item">
                                        <p><?php _e('No items', 'swift-calendar'); ?></p>
                                    </div>
                                <?php else : ?>
                                    <?php // Loop through each feed item and display each item as a hyperlink. ?>
                                    <?php foreach ($updates as $item) : ?>
                                        <div class="col-dashboard-item">
                                            <div class="col-dashboard-item-content">
                                                <div class="col-dashboard-item-img">
                                                    <?php
                                                    $feed_thumb_src = '';
                                                    $doc = new DOMDocument();
                                                    @$doc->loadHTML($item->get_content());
                                                    $tags = $doc->getElementsByTagName('img');

                                                    foreach ($tags as $tag) {
                                                        $feed_thumb_src = $tag->getAttribute('src');
                                                    }
                                                    $feed_thumb_src = ($tags->length > 0) ? $feed_thumb_src : plugins_url('../images/blank-img.png', __FILE__);
                                                    ?>
                                                    <a target="_blank" href="<?php echo esc_url($item->get_permalink()); ?>" title="<?php printf(__('Posted %s', 'my-text-domain'), $item->get_date('j F Y | g:i a')); ?>">
                                                        <div class="col-dashboard-round-img" style="background-image: url('<?php echo esc_url($feed_thumb_src); ?>')"></div>
                                                    </a>
                                                </div>
                                                <div class="col-dashboard-item-title"><a target="_blank" href="<?php echo esc_url($item->get_permalink()); ?>" title="<?php printf(__('Posted %s', 'my-text-domain'), $item->get_date('j F Y | g:i a')); ?>"><?php echo esc_html($item->get_title()); ?></a></div>
                                                <?php echo ($item->get_content()); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-col-4 col-right">
                        <div class="col-right-wrap">
                            <!--<div class="col-right-title"><h3>Swift Recommendation</h3></div>-->
                            <div class="col-right-content">
                                <?php
                                $rss_recomm = fetch_feed('https://SwiftCRM.Com/support/tag/offers/feed');
                                $maxitems_recomm = 0;

                                if (!is_wp_error($rss_recomm)) : // Checks that the object is created correctly
                                    // Figure out how many total items there are, but limit it to 5.
                                    $maxitems_recomm = $rss_recomm->get_item_quantity(5);

                                    // Build an array of all the items, starting with element 0 (first element).
                                    $recommendations = $rss_recomm->get_items(0, $maxitems_recomm);

                                endif;
                                ?>
                                <?php if ($maxitems_recomm == 0) : ?>

                                <?php else : ?>
                                    <?php // Loop through each feed item and display each item as a hyperlink. ?>
                                    <?php foreach ($recommendations as $recomm) : ?>
                                        <div class="col-right-item">
                                            <div class="col-right-item-content">
                                                <?php
                                                $recomm_thumb_src = '';
                                                $recomm_doc = new DOMDocument();
                                                @$recomm_doc->loadHTML($recomm->get_content());
                                                $recomm_tags = $recomm_doc->getElementsByTagName('img');

                                                foreach ($recomm_tags as $recomm_tag) {
                                                    $recomm_thumb_src = $recomm_tag->getAttribute('src');
                                                }
                                                $recomm_thumb_src = ($recomm_tags->length > 0) ? $recomm_thumb_src : plugins_url('../images/blank-img.png', __FILE__);
                                                ?>
                                                <div class="col-right-item-img">
                                                    <a target="_blank" href="<?php echo esc_url($recomm->get_permalink()); ?>" title="<?php printf(__('Posted %s', 'my-text-domain'), $recomm->get_date('j F Y | g:i a')); ?>">
                                                        <div class="col-right-dashboard-round-img" style="background-image: url('<?php echo esc_url($recomm_thumb_src); ?>')"></div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-right-item-title"><a target="_blank" href="<?php echo esc_url($recomm->get_permalink()); ?>" title="<?php printf(__('Posted %s', 'my-text-domain'), $recomm->get_date('j F Y | g:i a')); ?>"><?php echo esc_html($recomm->get_title()); ?></a></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}


/*
 *      Close subscribe box and set cookie
 *      cookie value : 0 || 1
 */
add_action('wp_ajax_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox_callback');
add_action('wp_ajax_nopriv_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox_callback');
if (!function_exists(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox_callback')) {

    function sp_dashboard_subscribe_checkbox_callback() {
        if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_checkbox') {
            setcookie(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_unsubscribe', "", time() - 1, "/", '');
            echo "1";
        }
        wp_die();
    }

}

add_action('wp_ajax_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe_callback');
add_action('wp_ajax_nopriv_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe_callback');
if (!function_exists(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe_callback')) {

    function sp_dashboard_close_subscribe_callback() {
        if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_close_subscribe') {
            setcookie(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_unsubscribe', "1", time() + (10 * 365 * 24 * 60 * 60), "/", '');
            echo "1";
        }
        wp_die();
    }

}

add_action('wp_ajax_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_callback');
add_action('wp_ajax_nopriv_' . SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe', SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_callback');

if (!function_exists(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe_callback')) {

    function sp_dashboard_subscribe_callback() {
        check_ajax_referer('swiftdashboard_subs_form', 'swiftdashboard_subs_form');
        if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe') {
            parse_str(sanitize_text_field($_POST['data']), $subscribe_form_data);
            $subscribe_form_data['referer'] = home_url();

            update_option(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_subscribe', true);
            setcookie(SWIFT_PROPERTY__PLUGIN_PREFIX . 'dashboard_unsubscribe', "", time() - 1, "/", '');
            
            $args = array(
                'body' => $subscribe_form_data,
                'timeout' => '5',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'cookies' => array(),
            );
            wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);
            echo "1";
        }
        wp_die();
    }

}