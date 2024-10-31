<?php
/*
 *  Admin modul of SwiftProperty
 */


/** On plugin activation notice * */
if (version_compare($GLOBALS['wp_version'], SWIFT_PROPERTY__MINIMUM_WP_VERSION, '>=')) {
    add_action('admin_notices', 'swift_property_admin_notice');
}
if (!function_exists('swift_property_admin_notice')) {

    function swift_property_admin_notice() {
        if (!get_option('swift_property_page_notice') && !get_option('swift_property_pages')) {
            ?>
            <div class="notice notice-success is-dismissible sc-admin-notice" id="sp-admin-notice">
                <p><b>Swift Property Plugin</b></p>
                <form method="post">
                    <p class="sc-notice-msg"><?php _e('Want to auto-create the following page(s) to quickly get you set up? ', 'swift-property'); ?></p>
                    <ul>
                        <li>Property Listing</li>
                    </ul>
                    <?php wp_nonce_field('swift_property_autogen_pages', 'swift_property_autogen_pages'); ?>
                    <button type="submit" value="yes" name="swift_property_autogen_yes" class="button button-green"><span class="dashicons dashicons-yes"></span> Yes</button>  <button type="submit" name="sp_autogen_no" value="no" class="button button-default button-red"><i class="fa fa-ban"></i> No</button>
                </form>
            </div>
            <?php
        }
    }

}

/**
 *      Admin menu
 */
add_action('admin_menu', 'swift_property_control_panel');
if (!function_exists('swift_property_control_panel')) {

    function swift_property_control_panel() {
        $icon_url = plugins_url('/images/swiftcloud.png', __FILE__);
        $parent_menu_slug = 'swift_property_control_panel';
        $menu_capability = 'manage_options';

        add_menu_page('Swift Property', 'Swift Property', $menu_capability, $parent_menu_slug, 'swift_property_settings_callback', $icon_url, 26);
        add_submenu_page($parent_menu_slug, "Settings", "Settings", $menu_capability, $parent_menu_slug, '');

        //cpt menu
        add_submenu_page($parent_menu_slug, "All Properties", "All Properties", $menu_capability, "edit.php?post_type=swift_property", null);
        add_submenu_page($parent_menu_slug, "Add New Property", "Add New Property", $menu_capability, "post-new.php?post_type=swift_property", null);
        add_submenu_page($parent_menu_slug, "Categories", "Categories", $menu_capability, "edit-tags.php?taxonomy=swift_property_category&post_type=swift_property", null);
        add_submenu_page($parent_menu_slug, "Tags", "Tags", $menu_capability, "edit-tags.php?taxonomy=swift_property_tag&post_type=swift_property", null);
        add_submenu_page($parent_menu_slug, "Updates & Tips", "Updates & Tips", 'manage_options', 'swift_property_dashboard', 'swift_property_dashboard_callback');

        //log page
        $page_hook_suffix = add_submenu_page($parent_menu_slug, 'Form Submission', 'Form Submission', 'manage_options', 'swift_property_admin_display_log', 'swift_property_admin_display_log');
        add_submenu_page("", "Log Detail", "Log Detail", 'manage_options', 'swift_property_admin_display_log_details', 'swift_property_admin_display_log_details');
    }

}

/**
 *      Set current menu selected
 */
add_filter('parent_file', 'swift_property_set_current_menu');
if (!function_exists('swift_property_set_current_menu')) {

    function swift_property_set_current_menu($parent_file) {
        global $submenu_file, $current_screen, $pagenow;

        if ($current_screen->post_type == 'swift_property') {
            if ($pagenow == 'post.php') {
                $submenu_file = "edit.php?post_type=" . $current_screen->post_type;
            }
            if ($pagenow == 'edit-tags.php') {
                if ($current_screen->taxonomy == 'swift_property_category') {
                    $submenu_file = "edit-tags.php?taxonomy=swift_property_category&post_type=" . $current_screen->post_type;
                } else if ($current_screen->taxonomy == 'swift_property_tag') {
                    $submenu_file = "edit-tags.php?taxonomy=swift_property_tag&post_type=" . $current_screen->post_type;
                }
            }
            $parent_file = 'swift_property_control_panel';
        }
        return $parent_file;
    }

}


/*
 *      Enqueue scripts and styles
 */
add_action('admin_enqueue_scripts', 'swift_property_admin_enqueue');
if (!function_exists('swift_property_admin_enqueue')) {

    function swift_property_admin_enqueue($hook) {
        global $pagenow;

        wp_enqueue_style('swift-property-admin-style', plugins_url('/css/sp_admin.css', __FILE__), '', '', '');
        wp_enqueue_script('swift-property-admin-custom', plugins_url('/js/sp_admin.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('swift-property-admin-custom', 'sp_admin_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));

        wp_enqueue_style('swiftcloud-toggle-style', plugins_url('/css/sp_rcswitcher.css', __FILE__), '', '', '');
        wp_enqueue_script('swiftloud-toggle', plugins_url('/js/sp_rcswitcher.js', __FILE__), array('jquery'), '', true);

        wp_enqueue_script('swift-property-multi-image-upload', plugins_url('/js/miu_script.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('swift-property-multi-image-upload', 'sp_multi_image_obj', array('plug_url' => SWIFT_PROPERTY__PLUGIN_URL, 'ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_script('swift-property-tab-script', plugins_url('/js/sp_tab.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_style('swiftcloud-fontawesome', SWIFT_PROPERTY__PLUGIN_URL . 'css/font-awesome.min.css', '', '', '');

        if ($pagenow == 'user-edit.php' || $pagenow == 'profile.php') {
            wp_enqueue_media();
            wp_register_script('swift-property-upload-img', plugins_url('/js/sp_upload-image.js', __FILE__), array('jquery'));
            wp_enqueue_script('swift-property-upload-img');
        }
    }

}

include_once 'section/cpt_swift_property.php';
include_once 'section/multi-image-upload.php';
include_once 'section/multi-pdf-upload.php';
include_once 'section/swift_dashboard.php';
include_once 'section/sp_settings.php';
include_once 'section/sp_user_fields.php';
include_once 'section/sp_bulk_upload.php';
include_once 'section/sp_local_capture.php';

/*
 *      Init
 */
add_action("init", "swift_property_admin_forms_submit");

function swift_property_admin_forms_submit() {
    /* on plugin active auto generate pages and options */
    if (isset($_POST['swift_property_autogen_pages']) && wp_verify_nonce($_POST['swift_property_autogen_pages'], 'swift_property_autogen_pages')) {
        if (isset($_POST['swift_property_autogen_yes']) && $_POST['swift_property_autogen_yes'] == 'yes') {
            swift_property_initial_data();
        }
        update_option('swift_property_page_notice', true);
    }
}

/* Dismiss notice callback */
add_action('wp_ajax_sp_dismiss_notice', 'swift_property_dismiss_notice_callback');
add_action('wp_ajax_nopriv_sp_dismiss_notice', 'swift_property_dismiss_notice_callback');

function swift_property_dismiss_notice_callback() {
    update_option('swift_property_page_notice', true);
    wp_die();
}

function swift_property_post_types_admin_order($wp_query) {
    if (is_admin()) {
        $post_type = $wp_query->query['post_type'];
        if ($post_type == 'swift_property') {
            $wp_query->set('orderby', 'date');
            $wp_query->set('order', 'DESC');
        }
    }
}

add_filter('pre_get_posts', 'swift_property_post_types_admin_order');

/**
 * This function returns the maximum files size that can be uploaded in PHP
 * @returns int File size in bytes
 */
function getMaximumFileUploadSize() {
    return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
}

/**
 * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
 * 
 * @param string $sSize
 * @return integer The value in bytes
 */
function convertPHPSizeToBytes($sSize) {
    //
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
        return (int) $sSize;
    }
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
        // Fallthrough intended
        case 'T':
            $iValue *= 1024;
        // Fallthrough intended
        case 'G':
            $iValue *= 1024;
        // Fallthrough intended
        case 'M':
            $iValue *= 1024;
        // Fallthrough intended
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int) $iValue;
}
