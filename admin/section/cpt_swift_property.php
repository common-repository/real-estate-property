<?php
/*
 *      CPT: swift_property
 */

add_action('init', 'cpt_swift_property');
if (!function_exists('cpt_swift_property')) {

    function cpt_swift_property() {
        add_image_size('swift_property_gallery_thumb', 120, 100, true);

        $icon_url = plugins_url('../images/swiftcloud.png', __FILE__);
        $labels = array(
            'name' => _x('Swift Property', 'post type general name', 'swift-property'),
            'singular_name' => _x('Swift Property', 'post type singular name', 'swift-property'),
            'menu_name' => _x('Swift Property', 'admin menu', 'swift-property'),
            'add_new' => _x('Add New', '', 'swift-property'),
            'add_new_item' => __('Add New', 'swift-property'),
            'new_item' => __('New Property', 'swift-property'),
            'edit_item' => __('Edit Property', 'swift-property'),
            'view_item' => __('View Property', 'swift-property'),
            'all_items' => __('All Properties', 'swift-property'),
            'search_items' => __('Search Property', 'swift-property'),
            'not_found' => __('No property found.', 'swift-property'),
            'not_found_in_trash' => __('No property found in trash.', 'swift-property')
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => true,
            'menu_icon' => __($icon_url, 'swift-property'),
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'author'),
            'taxonomies' => array('swift_property_category'),
            'rewrite' => array('slug' => 'swift_property')
        );
        register_post_type('swift_property', $args);

        /* -------------------------------------
         *      Add new taxonomy
         */
        $cat_labels = array(
            'name' => _x('Swift Property Categories', 'taxonomy general name'),
            'singular_name' => _x('Swift Property Category', 'taxonomy singular name'),
            'add_new_item' => __('Add New Category'),
            'new_item_name' => __('New Category Name'),
            'menu_name' => __('Categories'),
            'search_items' => __('Search Category'),
            'not_found' => __('No Category found.'),
        );

        $cat_args = array(
            'hierarchical' => true,
            'labels' => $cat_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'swift_property_category'),
        );

        register_taxonomy('swift_property_category', 'swift_property', $cat_args);


        /**
         *      tags
         */
        $tags_labels = array(
            'name' => _x('Swift Property Tags', 'taxonomy general name'),
            'singular_name' => _x('Swift Property Tag', 'taxonomy singular name'),
            'search_items' => __('Search Tags'),
            'popular_items' => __('Popular Tags'),
            'all_items' => __('All Tags'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Tag'),
            'update_item' => __('Update Tag'),
            'add_new_item' => __('Add New Tag'),
            'new_item_name' => __('New Tag Name'),
            'separate_items_with_commas' => __('Separate tags with commas'),
            'add_or_remove_items' => __('Add or remove tags'),
            'choose_from_most_used' => __('Choose from the most used tags'),
            'menu_name' => __('Tags'),
        );

        register_taxonomy('swift_property_tag', 'swift_property', array(
            'hierarchical' => false,
            'labels' => $tags_labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'swift_property_tag'),
        ));

        // insert default tags
        $default_cat = array(
            "BY LOCATION" => array(
                "child" => array(
                    'Los Angeles',
                    'New York',
                    'London'
                )
            ),
            "BY FEATURES" => array(
                "child" => array(
                    'Pool',
                    'Fireplace',
                    'Garage (Attached)'
                )
            ),
            "BY STYLE" => array(
                "child" => array(
                    'Mid-Century',
                    'Tudor'
                )
            ),
            "Single Family Home",
            "Condominium",
            "Land",
            "Duplex",
            "3 Unit",
            "4 Unit",
            "Commercial",
            "Lease",
            "Other"
        );
        foreach ($default_cat as $d_cat_key => $d_cat_val) {
            // insert parent category
            if (isset($d_cat_val['child'])) {
                $parent_cat = $d_cat_key;
            } else {
                $parent_cat = $d_cat_val;
            }
            $term_id = wp_insert_term($parent_cat, "swift_property_category", array('parent' => 0));

            if (!is_wp_error($term_id) && !empty($term_id['term_id']) && isset($d_cat_val['child']) && !empty($d_cat_val['child'])) {
                foreach ($d_cat_val['child'] as $child_key => $child_val) {
                    // insert child category
                    if (isset($child_val['subchild'])) {
                        $child_cat = $child_key;
                    } else {
                        $child_cat = $child_val;
                    }
                    $child_term_id = wp_insert_term($child_cat, "swift_property_category", array('parent' => $term_id['term_id']));


                    if (!is_wp_error($child_term_id) && !empty($child_term_id['term_id']) && isset($child_val['subchild']) && !empty($child_val['subchild'])) {
                        foreach ($child_val['subchild'] as $subchild) {
                            // insert subchild category
                            $subchild_term_id = wp_insert_term($subchild, "swift_property_category", array('parent' => $child_term_id['term_id']));
                        }
                    }
                }
            }
        }//foreach
    }

}


// Single property template
add_filter('single_template', 'swift_property_plugin_templates_callback');
if (!function_exists('swift_property_plugin_templates_callback')) {

    function swift_property_plugin_templates_callback($template) {
        $post_types = array('swift_property');
        if (is_singular($post_types)) {
            if (file_exists(get_stylesheet_directory() . '/single-swift_property.php')) {
                $template = get_stylesheet_directory() . "/single-swift_property.php";
            } else {
                $template = SWIFT_PROPERTY__PLUGIN_DIR . "section/single-swift_property.php";
            }
        }
        return $template;
    }

}

// property archive template
add_filter('archive_template', 'swift_property_set_archive_template_callback');
if (!function_exists('swift_property_set_archive_template_callback')) {

    function swift_property_set_archive_template_callback($archive_template) {
        global $post;
        if (get_post_type() == 'swift_property' && is_archive('swift_property')) {
            if (file_exists(get_stylesheet_directory() . '/archive-swift_property.php')) {
                $archive_template = get_stylesheet_directory() . '/archive-swift_property.php';
            } else {
                $archive_template = SWIFT_PROPERTY__PLUGIN_DIR . '/section/archive-swift_property.php';
            }
        }
        return $archive_template;
    }

}


/*
 *  Custom field ::
 *      - Event start
 *      - Event Duration
 */
add_action('add_meta_boxes', 'swift_property_metaboxes');
if (!function_exists('swift_property_metaboxes')) {

    function swift_property_metaboxes() {
        add_meta_box('swift_property_metas', 'Property Information', 'swift_property_metas', 'swift_property', 'normal', 'default');
    }

}

if (!function_exists('swift_property_metas')) {

    function swift_property_metas($post) {
        $sp_price = esc_attr(get_post_meta($post->ID, 'sp_price', true));
        $sp_beds = esc_attr(get_post_meta($post->ID, 'sp_beds', true));
        $sp_baths = esc_attr(get_post_meta($post->ID, 'sp_baths', true));
        $sp_property_size = esc_attr(get_post_meta($post->ID, 'sp_property_size', true));
        $sp_lot_size = esc_attr(get_post_meta($post->ID, 'sp_lot_size', true));
        $sp_street = esc_attr(get_post_meta($post->ID, 'sp_street', true));
        $sp_city = esc_attr(get_post_meta($post->ID, 'sp_city', true));
        $sp_state = esc_attr(get_post_meta($post->ID, 'sp_state', true));
        $sp_zip = esc_attr(get_post_meta($post->ID, 'sp_zip', true));
        $sp_status = esc_attr(get_post_meta($post->ID, 'sp_status', true));
        $sp_promo_text = esc_attr(get_post_meta($post->ID, 'sp_promo_text', true));
        $sp_mls = esc_attr(get_post_meta($post->ID, 'sp_mls', true));
        $sp_YT_url = esc_attr(get_post_meta($post->ID, 'sp_YT_url', true));
        $sp_virtual_3d_url = esc_attr(get_post_meta($post->ID, 'sp_virtual_3d_url', true));

        $sp_status_arr = array('Active', 'Pending', 'Sold', 'Back on Market', 'Accepting Backups', 'For Rent', 'For Lease', 'Rent-to-Own', 'Leased');
        ?>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td valign="top">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <label for="sp_price">Price: </label><br />
                                <input type="number" name="sp_price" id="sp_price" class="regular-text" value="<?php echo $sp_price; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_beds">No. of Beds: </label><br />
                                <input type="number" name="sp_beds" id="sp_beds" class="regular-text" value="<?php echo $sp_beds; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_baths">No. of Baths: </label><br />
                                <input type="number" name="sp_baths" id="sp_baths" class="regular-text" value="<?php echo $sp_baths; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_property_size">Property Size: </label><br />
                                <input type="number" name="sp_property_size" id="sp_property_size" class="regular-text" value="<?php echo $sp_property_size; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_status">Property Status: </label><br />
                                <select name="sp_status" id="sp_status" class="regular-text">
                                    <option value="">Select Status</option>
                                    <?php
                                    foreach ($sp_status_arr as $sp_stat) {
                                        echo '<option ' . selected($sp_status, $sp_stat) . ' value="' . $sp_stat . '">' . $sp_stat . '</option>';
                                    }
                                    ?>
                                </select><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_mls">MLS #: </label><br />
                                <input type="text" name="sp_mls" id="sp_mls" class="regular-text" value="<?php echo $sp_mls; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_YT_url">YouTube Video URL: </label><br />
                                <input type="text" name="sp_YT_url" id="sp_YT_url" class="regular-text" value="<?php echo $sp_YT_url; ?>" /><br />
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <label for="sp_street">Street Address: </label><br />
                                <input type="text" name="sp_street" id="sp_street" class="regular-text" value="<?php echo $sp_street; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_city">City: </label><br />
                                <input type="text" name="sp_city" id="sp_city" class="regular-text" value="<?php echo $sp_city; ?>" /><br /><br />

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_state">State: </label><br />
                                <input type="text" name="sp_state" id="sp_state" class="regular-text" value="<?php echo $sp_state; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_lot_size">Lot Size: </label><br />
                                <input type="number" name="sp_lot_size" id="sp_lot_size" class="regular-text" value="<?php echo $sp_lot_size; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_zip">Zip: </label><br />
                                <input type="text" name="sp_zip" id="sp_zip" class="regular-text" value="<?php echo $sp_zip; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_promo_text">Promo text (Optional): </label><br />
                                <input type="text" name="sp_promo_text" id="sp_promo_text" class="regular-text" value="<?php echo $sp_promo_text; ?>" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="sp_virtual_3d_url">Virtual 3D Tour URL (Optional): </label><br />
                                <input type="text" name="sp_virtual_3d_url" id="sp_virtual_3d_url" class="regular-text" value="<?php echo $sp_virtual_3d_url; ?>" /><br /><br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php
    }

}

/**
 *      Save meta
 */
add_action('save_post', 'swift_property_save_meta');

if (!function_exists('swift_property_save_meta')) {

    function swift_property_save_meta($post_id) {
        $sp_price = (isset($_POST["sp_price"]) && !empty($_POST["sp_price"])) ? sanitize_text_field($_POST['sp_price']) : "";
        update_post_meta($post_id, 'sp_price', $sp_price);

        $sp_beds = (isset($_POST["sp_beds"]) && !empty($_POST["sp_beds"])) ? sanitize_text_field($_POST['sp_beds']) : "";
        update_post_meta($post_id, 'sp_beds', $sp_beds);
        
        $sp_baths = (isset($_POST["sp_baths"]) && !empty($_POST["sp_baths"])) ? sanitize_text_field($_POST['sp_baths']) : "";
        update_post_meta($post_id, 'sp_baths', $sp_baths);

        $sp_property_size = (isset($_POST["sp_property_size"]) && !empty($_POST["sp_property_size"])) ? sanitize_text_field($_POST['sp_property_size']) : "";
        update_post_meta($post_id, 'sp_property_size', $sp_property_size);
        
        $sp_lot_size = (isset($_POST["sp_lot_size"]) && !empty($_POST["sp_lot_size"])) ? sanitize_text_field($_POST['sp_lot_size']) : "";
        update_post_meta($post_id, 'sp_lot_size', $sp_lot_size);

        $sp_street = (isset($_POST["sp_street"]) && !empty($_POST["sp_street"])) ? sanitize_text_field($_POST['sp_street']) : "";
        update_post_meta($post_id, 'sp_street', $sp_street);

        $event_recurring = (isset($_POST["sp_city"]) && !empty($_POST["sp_city"])) ? sanitize_text_field($_POST['sp_city']) : "";
        update_post_meta($post_id, 'sp_city', $event_recurring);

        $sp_state = (isset($_POST["sp_state"]) && !empty($_POST["sp_state"])) ? sanitize_text_field($_POST['sp_state']) : "";
        update_post_meta($post_id, 'sp_state', $sp_state);

        $sp_zip = (isset($_POST["sp_zip"]) && !empty($_POST["sp_zip"])) ? sanitize_text_field($_POST['sp_zip']) : "";
        update_post_meta($post_id, 'sp_zip', $sp_zip);

        $sp_status = (isset($_POST["sp_status"]) && !empty($_POST["sp_status"])) ? sanitize_text_field($_POST['sp_status']) : "";
        update_post_meta($post_id, 'sp_status', $sp_status);

        $sp_promo_text = (isset($_POST["sp_promo_text"]) && !empty($_POST["sp_promo_text"])) ? sanitize_text_field($_POST['sp_promo_text']) : "";
        update_post_meta($post_id, 'sp_promo_text', $sp_promo_text);

        $sp_mls = (isset($_POST["sp_mls"]) && !empty($_POST["sp_mls"])) ? sanitize_text_field($_POST['sp_mls']) : "";
        update_post_meta($post_id, 'sp_mls', $sp_mls);
        
        $sp_YT_url = (isset($_POST["sp_YT_url"]) && !empty($_POST["sp_YT_url"])) ? sanitize_text_field($_POST['sp_YT_url']) : "";
        update_post_meta($post_id, 'sp_YT_url', $sp_YT_url);
        
        $sp_virtual_3d_url = (isset($_POST["sp_virtual_3d_url"]) && !empty($_POST["sp_virtual_3d_url"])) ? sanitize_text_field($_POST['sp_virtual_3d_url']) : "";
        update_post_meta($post_id, 'sp_virtual_3d_url', $sp_virtual_3d_url);
    }

}



/**
 *         Add sidebar
 */
add_action('widgets_init', 'swift_property_widget_init');
if (!function_exists('swift_property_widget_init')) {

    function swift_property_widget_init() {
        register_sidebar(array(
            'name' => __('Property Sidebar', 'swift-property'),
            'id' => 'swift-property-sidebar',
            'description' => __('Add widgets here to appear in property sidebar', 'swift-property'),
            'before_widget' => '<div class="pr-widget-border pr-m-b-15">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="pr-widget-title">',
            'after_title' => '</h3>',
        ));
    }

}

if (!function_exists('sanitize_text_or_array_field')) {

    function sanitize_text_or_array_field($array_or_string) {
        if (is_string($array_or_string)) {
            $array_or_string = sanitize_text_field($array_or_string);
        } elseif (is_array($array_or_string)) {
            foreach ($array_or_string as $key => &$value) {
                if (is_array($value)) {
                    $value = sanitize_text_or_array_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }
            }
        }

        return $array_or_string;
    }

}

function change_swift_property_post_types_slug($args, $post_type) {
    $sp_property_slug = get_option('sp_property_slug');
    if ('swift_property' === $post_type && !empty($sp_property_slug)) {
        $args['rewrite']['slug'] = $sp_property_slug;
    }
    return $args;
}

add_filter('register_post_type_args', 'change_swift_property_post_types_slug', 10, 2);

function change_swift_property_category_slug($args, $taxonomy) {
    $sp_property_category_slug = get_option("sp_property_category_slug");
    if ('swift_property_category' === $taxonomy && !empty($sp_property_category_slug)) {
        $args['rewrite']['slug'] = $sp_property_category_slug;
    }
    return $args;
}

add_filter('register_taxonomy_args', 'change_swift_property_category_slug', 10, 2);


/**
 *         Add sidebar
 */
add_action('widgets_init', 'swift_property_reg_footer_widget');
if (!function_exists('swift_property_reg_footer_widget')) {

    function swift_property_reg_footer_widget() {
        register_sidebar(array(
            'name' => __('Swift Property Footer Widget', 'swift-property'),
            'id' => 'sp_property_footer',
            'description' => __('Add widgets here to appear in Property page Footer area', 'swift-property'),
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => '',
        ));
    }

}

// Add the custom columns to the swift_property type:
add_filter( 'manage_swift_property_posts_columns', 'set_custom_edit_swift_property_columns' );
add_action( 'manage_swift_property_posts_custom_column' , 'custom_swift_property_column', 10, 2 );

function set_custom_edit_swift_property_columns($columns) {
    $columns['sp_beds'] = __('# of Beds', 'swift-property');
    $columns['sp_baths'] = __('# of Baths', 'swift-property');
    $columns['sp_property_size'] = __('Property Size', 'swift-property');
    $columns['sp_price'] = __('Price', 'swift-property');

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_swift_property_custom_column', 'custom_swift_property_column', 10, 2);

function custom_swift_property_column($column, $post_id) {
    switch ($column) {
        case 'sp_beds' :
            echo esc_attr(get_post_meta($post_id, 'sp_beds', true));
            break;
        case 'sp_baths' :
            echo esc_attr(get_post_meta($post_id, 'sp_baths', true));
            break;
        case 'sp_property_size' :
            echo esc_attr(get_post_meta($post_id, 'sp_property_size', true));
            break;
        case 'sp_price' :
            echo esc_attr(get_post_meta($post_id, 'sp_price', true));
            break;
    }
}
