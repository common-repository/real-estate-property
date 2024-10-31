<?php

/*
 *  Plugin Name:        Wordpress Real Estate Plugin by SwiftCRM.com
 *  Plugin URL:         https://swiftcrm.com/software/real-estate-crm
 *  Description:        Wordpress Real Estate Plugin by SwiftCRM.com
 *  Version:            1.1
 *  Requires at least:  5.7
 *  Requires PHP:       7.4
 *  Author:             SwiftCloud for Real Estate
 *  Author URI:         https://swiftcrm.com/software/real-estate-crm
 *  Text Domain:        swift-property
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    _e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'swift-property');
    exit;
}

define('SWIFT_PROPERTY_VERSION', '1.1');
define('SWIFT_PROPERTY__MINIMUM_WP_VERSION', '5.7');
define('SWIFT_PROPERTY__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SWIFT_PROPERTY__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWIFT_PROPERTY__PLUGIN_PREFIX', 'sp_');

register_activation_hook(__FILE__, 'swift_property_install');
if (!function_exists('swift_property_install')) {

    function swift_property_install() {
        if (version_compare($GLOBALS['wp_version'], SWIFT_PROPERTY__MINIMUM_WP_VERSION, '<')) {
            add_action('admin_notices', 'swift_property_version_admin_notice');

            function swift_property_version_admin_notice() {
                echo '<div class="notice notice-error is-dismissible sc-admin-notice"><p>' . sprintf(esc_html__('Swift Property %s requires WordPress %s or higher.', 'swift-property'), SWIFT_PROPERTY_VERSION, SWIFT_PROPERTY__MINIMUM_WP_VERSION) . '</p></div>';
            }

            add_action('admin_init', 'swift_property_deactivate_self');

            function swift_property_deactivate_self() {
                if (isset($_GET["activate"]))
                    unset($_GET["activate"]);
                deactivate_plugins(plugin_basename(__FILE__));
            }

            return;
        }
        update_option('swift_property_db_version', SWIFT_PROPERTY_VERSION);

        swift_property_pre_load_data();

        if (!wp_next_scheduled('swift_property_api_post')) {
            wp_schedule_event(time(), 'hourly', 'swift_property_api_post');
        }
    }

}

add_action('plugins_loaded', 'swift_property_update_check');

if (!function_exists('swift_property_update_check')) {

    function swift_property_update_check() {
        if (get_option("swift_property_db_version") != SWIFT_PROPERTY_VERSION) {
            swift_property_install();
        }
    }

}

//Load admin modules
require_once 'admin/swift_property_admin.php';
require_once 'section/sp-preload.php';

/**
 *      Deactivation plugin
 */
register_deactivation_hook(__FILE__, 'swift_property_deactive_plugin');
if (!function_exists('swift_property_deactive_plugin')) {

    function swift_property_deactive_plugin() {
        
    }

}


register_uninstall_hook(__FILE__, 'swift_property_uninstall_callback');
if (!function_exists('swift_property_uninstall_callback')) {

    function swift_property_uninstall_callback() {
        global $wpdb;

        wp_clear_scheduled_hook('swift_property_api_post');
        delete_option("swift_property_db_version");
        delete_option("swift_property_page_notice");

        // delete pages
        $pages = get_option('swift_property_pages');
        if ($pages) {
            $pages = explode(",", $pages);
            foreach ($pages as $pid) {
                wp_delete_post($pid, true);
            }
        }
        delete_option("swift_property_pages");

        /*
         * Delete cpt swift_property and swift_property_category
         */

        /* taxonomy */
        foreach (array('swift_property_category') as $taxonomy) {
            $wpdb->delete(
                    $wpdb->term_taxonomy, array('taxonomy' => $taxonomy)
            );
        }

        /* Delete reviews posts */
        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ('swift_property');");
        $wpdb->query("DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;");

        /* Deregister swift reviews cpt */
        if (function_exists('unregister_post_type')) {
            unregister_post_type('swift_property');
        }
    }

}

/**
 *      Enqueue style and scripts
 */
add_action('wp_enqueue_scripts', 'swift_property_enqueue_scripts_styles');

if (!function_exists('swift_property_enqueue_scripts_styles')) {

    function swift_property_enqueue_scripts_styles() {
        wp_enqueue_script('swift-property-custom-js', plugins_url('/js/sp_custom.js', __FILE__), array('jquery'), '', true);
    }

}


include 'swift-property-pagetemplater.php';
include 'section/sp-shortcodes.php';
include 'section/sp-function.php';

// Add event custom post type to feed
function swift_property_feed_request($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = array('post', 'press_release', 'swift_property', 'event_marketing', 'swift_jobs', 'vhcard');
    return $qv;
}

add_filter('request', 'swift_property_feed_request');


add_action('swift_property_api_post', 'do_swift_property_api_post');

function do_swift_property_api_post() {
    global $wpdb;
    $table_name = $wpdb->prefix . "swift_property_log";
    $fLog = $wpdb->get_results("SELECT * FROM $table_name WHERE status=0 ORDER BY `id` ASC LIMIT 1");
    if (isset($fLog[0]) && !empty($fLog[0])) {
        if (!empty($fLog[0]->form_data)) {
            $fData = @unserialize($fLog[0]->form_data);
            if (isset($fData) && !empty($fData)) {
                $fData['referer'] = home_url();
                $args = array(
                    'body' => $fData,
                    'timeout' => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'cookies' => array(),
                );
                wp_remote_post('https://portal.swiftcrm.com/f/fhx.php', $args);

                $wpdb->update($table_name, array('status' => 1), array('id' => $fLog[0]->id), array('%d'), array('%d'));
                echo "1";
            }
        }
    }
}
