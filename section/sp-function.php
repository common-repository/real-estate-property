<?php

/*
 *      Custom excerpt function
 */

if (!function_exists('swift_property_get_excerpt')) {

    function swift_property_get_excerpt($excerpt_length = 55, $id = false, $echo = false, $excerpt_more = true) {
        return swift_property_excerpt($excerpt_length, $id, $echo, $excerpt_more);
    }

}

if (!function_exists('swift_property_excerpt')) {

    function swift_property_excerpt($excerpt_length = 55, $id = false, $echo = false, $excerpt_more = true) {

        $text = '';

        if ($id) {
            $the_post = get_post($my_id = $id);
            $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
        } else {
            global $post;
            $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }

        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);

        if ($excerpt_more)
            $excerpt_more = ' ' . '<a href=' . get_permalink($id) . ' class="scal-readmore">...more...</a>';

        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        if ($echo)
            echo apply_filters('the_content', $text);
        else
            return $text;
    }

}

if (!function_exists('swift_property_pagination')) {

    function swift_property_pagination($pages = '', $range = 2, $echo = true) {
        $showitems = ($range * 2) + 1;

        global $paged;
        if (empty($paged))
            $paged = 1;

        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {
            $sp_pg = "<div class='swift_pagination'>";
            if ($paged > 2 && $paged > $range + 1 && $showitems < $pages)
                $sp_pg .= "<a href='" . get_pagenum_link(1) . "'>&laquo;</a>";
            if ($paged > 1 && $showitems < $pages)
                $sp_pg .= "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a>";

            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    $sp_pg .= ($paged == $i) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class='inactive' >" . $i . "</a>";
                }
            }

            if ($paged < $pages && $showitems < $pages)
                $sp_pg .= "<a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a>";
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages)
                $sp_pg .= "<a href='" . get_pagenum_link($pages) . "'>&raquo;</a>";
            $sp_pg .= "</div>\n";

            if ($echo)
                echo $sp_pg;
            else
                return $sp_pg;
        }
    }

}

function swift_property_archive_query($query) {
    $swiftproperty_review_per_page = (get_option("swiftproperty_review_per_page")) ? get_option("swiftproperty_review_per_page") : 10;
    if ($query->is_post_type_archive('swift_property') && $query->is_main_query()) {
        $query->set('posts_per_page', $swiftproperty_review_per_page);
    }
}

add_action('pre_get_posts', 'swift_property_archive_query');

function getSwiftPropertyBlock($post_id, $echo = false) {
    $price = esc_html(get_post_meta($post_id, 'sp_price', true));
    $beds = esc_html(get_post_meta($post_id, 'sp_beds', true));
    $baths = esc_html(get_post_meta($post_id, 'sp_baths', true));
    $sp_status = esc_html(get_post_meta($post_id, 'sp_status', true));
    $sp_promo_text = esc_html(get_post_meta($post_id, 'sp_promo_text', true));
    $sp_currency = esc_html(get_option("sp_currency"));

    if (has_post_thumbnail($post_id)) {
        $url = esc_url(get_the_post_thumbnail_url($post_id, 'full'));
    }

    $cornerTag = '';
    if ($sp_status == 'Sold' || $sp_status == 'For Rent' || $sp_status == 'For Lease') {
        $cornerTag = '<div class="sp-corner-tag sold">'.$sp_status.'</div>';
    } else if ($sp_status == 'Pending') {
        $cornerTag = '<div class="sp-corner-tag pending">Pending</div>';
    }
//    $prop_tags = get_the_term_list($post_id, 'swift_property_tag', '<ul><li>', '</li><li>', '</li></ul>');


    $op = '';
    $op .= '<div class="col-lg-12 spPropertyList">';
    $op .= '<div class="row no-gutters">';
    if (has_post_thumbnail($post_id)) {
        $op .= '<div class="col-lg-4 col-md-6 col-sm-12 spProImg">';
        $op .= $cornerTag;
        $op .= '<a href="' . get_the_permalink($post_id) . '" class="spPropertyImg"><img src="' . $url . '" alt="' . get_the_title($post_id) . '" class="img-fluid" /></a>';
        $op .= '</div>';
    }
    $op .= '<div class="' . ((has_post_thumbnail($post_id)) ? 'col-lg-8 col-md-6 col-sm-12' : 'col-lg-12 col-md-12 col-sm-12') . ' spPropertySortDesc">';
    $op .= '<a href="' . get_the_permalink($post_id) . '" class="spPropertyTitle">' . get_the_title($post_id) . '</a>';
    $op .= (!empty($sp_promo_text)) ? '<p class="sp_promot_text">' . $sp_promo_text . '</p>' : "";
    $op .= '<p><a href="' . get_the_permalink($post_id) . '" class="spPropertyDesc">' . swift_property_get_excerpt(35, $post_id, false, true) . '</a></p>';
    $op .= '<div class="spPropertyKeys">';
    $op .= (!empty($price)) ? '<span class="propertyPrice">' . getSwiftPropertyCurrency($sp_currency) . number_format($price, 0, '.', ',') . '</span>' : '';
    $op .= (!empty($beds)) ? '<span class="propertyItems"><i class="fa fa-bed"></i> ' . $beds . ' Beds</span>' : '';
    $op .= (!empty($baths)) ? '<span class="propertyItems"><i class="fa fa-door-closed"></i> ' . $baths . ' Baths</span>' : '';
    $op .= '</div>';
//    $op .= (!empty($prop_tags)) ? '<div class="spPropertyTags">' . $prop_tags . '</div>' : '';
    $op .= '</div>';
    $op .= '</div>';
    $op .= '</div>';

    if ($echo)
        echo $op;
    else
        return $op;
}

function getSwiftPropertyCurrency($curr) {
    $curr_sym = "$";
    switch ($curr) {
        case "USD" : $curr_sym = "$";
            break;
        case "GBP" : $curr_sym = "&pound;";
            break;
        case "EUR" : $curr_sym = "&euro;";
            break;
    }
    return $curr_sym;
}

function swift_property_save_local_capture() {
    $result['type'] = "fail";
    if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == 'sp_save_local_capture') {
        global $wpdb;
        $table_name = $wpdb->prefix . "swift_property_log";

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        parse_str(sanitize_text_field($_POST['form_data']), $form_data);
        $form_data = maybe_serialize($form_data);

        $wpdb->insert(
                $table_name, array(
            'name' => $name,
            'email' => $email,
            'form_data' => $form_data,
            'date_time' => date('Y-m-d h:i:s'),
            'status' => 0
                ), array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
                )
        );
        $result['type'] = "success";
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    wp_die();
}

add_action('wp_ajax_sp_save_local_capture', 'swift_property_save_local_capture');
add_action('wp_ajax_nopriv_sp_save_local_capture', 'swift_property_save_local_capture');
