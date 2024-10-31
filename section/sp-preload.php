<?php

/*
 *      Load data with plugin active
 */

function swift_property_pre_load_data() {
    update_option('sp_event_flag', 1);
    update_option('sp_property_slug', 'homes');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'swift_property_log';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		date_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name varchar(255) DEFAULT '' NOT NULL,
		email varchar(255) DEFAULT '' NOT NULL,
		status TINYINT DEFAULT '0' NOT NULL,
                form_data TEXT, 
		UNIQUE KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

/*
 *      Load data after user permisssion
 */

function swift_property_initial_data() {
    global $wpdb;

    /**
     *   Auto generate pages
     */
    $page_id = 0;
    $page_id_array = array();

    $property_listing = wp_kses_post('[swift-properties]');
    $swift_property_rss_feed_content = wp_kses_post('This page is being used for RSS Feed');

    $pages_array = array(
        "properties" => array("title" => sanitize_text_field("Properties"), "content" => $property_listing, "slug" => "properties", "option" => "", "template" => ""),
        "property-feed" => array("title" => sanitize_text_field("Property Feed"), "content" => $swift_property_rss_feed_content, "slug" => "property-feed", "option" => "swift_property_feed_page_id", "template" => "rss-property-feed.php"),
    );

    foreach ($pages_array as $key => $page) {
        $page_data = array(
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_title' => $page['title'],
            'post_name' => $page['slug'],
            'post_content' => $page['content'],
            'comment_status' => 'closed'
        );

        $page_id = wp_insert_post($page_data);
        $page_id_array[] = $page_id;

        /* Set default template */
        if (isset($page['template']) && !empty($page['template'])) {
            update_post_meta($page_id, "_wp_page_template", sanitize_text_field($page['template']));
        }

        if (isset($page['option']) && !empty($page['option'])) {
            update_option($page['option'], sanitize_text_field($page_id));
        }
    }
    $sp_pages_ids = @implode(",", $page_id_array);
    if (!empty($sp_pages_ids)) {
        update_option('swift_property_pages', sanitize_text_field($sp_pages_ids));
    }
}