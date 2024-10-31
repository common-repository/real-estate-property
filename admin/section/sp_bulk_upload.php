<?php
/*
 *      Bulk Property Upload
 */

// set featured image for property
function setSwiftPropertyFeaturedImage($file_url, $post_id) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $user_id = get_current_user_id();
    $upload_dir = wp_upload_dir();

    if (!$post_id && !$file_url) {
        return false;
    }

    //rename the file... alternatively, you could explode on "/" and keep the original file name
    $ext = array_pop(explode(".", $file_url));
    $new_filename = 'swift-property-featured-img-' . $post_id . "." . $ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

    $image_url = $file_url; // Define the image URL here
    $image_data = file_get_contents($image_url); // Get image data
    $unique_file_name = wp_unique_filename($upload_dir['path'], $new_filename); // Generate unique name
    $filename = basename($unique_file_name); // Create image file name
    // Check folder permission and define file location
    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    // Create the image  file on the server
    file_put_contents($file, $image_data);

    // Check image file type
    $wp_filetype = wp_check_filetype($filename, null);

    // Set attachment data
    $attachment = array(
        'post_author' => $user_id,
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_type' => 'attachment',
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);

    // And finally assign featured image to post
    set_post_thumbnail($post_id, $attach_id);

    return true;
}

// setSwiftPropertyGallery
function setSwiftPropertyGallery($file_url, $post_id) {
    $upload_dir = wp_upload_dir();
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    if (!$post_id && !empty($file_url)) {
        return false;
    }

    //rename the file... alternatively, you could explode on "/" and keep the original file name
    $files = @explode(",", $file_url);
    if (!empty($files)) {
        $gallery_arr = array();
        $user_id = get_current_user_id();

        foreach ($files as $file_url) {
            $file_url = trim($file_url);
            $ext = array_pop(explode(".", $file_url));
            $new_filename = 'swift-property-' . $post_id . "." . $ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

            $image_url = $file_url; // Define the image URL here
            $image_data = file_get_contents($image_url); // Get image data
            $unique_file_name = wp_unique_filename($upload_dir['path'], $new_filename); // Generate unique name
            $filename = basename($unique_file_name); // Create image file name
            // Check folder permission and define file location
            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
                $file_url = $upload_dir['url'] . '/' . $filename;
            } else {
                $file = $upload_dir['path'] . '/' . $filename;
                $file_url = $upload_dir['url'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents($file, $image_data);

            // Check image file type
            $wp_filetype = wp_check_filetype($filename, null);

            // Set attachment data
            $attachment = array(
                'post_author' => $user_id,
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'attachment',
            );

            // Create the attachment
            $attach_id = wp_insert_attachment($attachment, $file, $post_id);

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);

            // Assign metadata to attachment
            wp_update_attachment_metadata($attach_id, $attach_data);

            $gallery_arr[] = $file_url;
        }
        return serialize($gallery_arr);
    } else {
        return false;
    }
}

add_action("init", "swift_property_bulk_upload_post_init");

function swift_property_bulk_upload_post_init() {
    if (isset($_POST['sp_upload_property_settings']) && wp_verify_nonce($_POST['sp_upload_property_settings'], 'sp_upload_property_settings')) {

        $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if (!empty($_FILES['sp_property_csv']['name']) && in_array($_FILES['sp_property_csv']['type'], $csvMimes)) {
            if (is_uploaded_file($_FILES['sp_property_csv']['tmp_name'])) {
                global $wpdb;

                //open uploaded csv file with read only mode
                $csvFile = fopen($_FILES['sp_property_csv']['tmp_name'], 'r');

                //parse data from csv file line by line
                $cnt = 0;
                $upload_dir = wp_upload_dir();
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    if ($cnt == 0) {
                        $cnt++;
                        continue;
                    }

                    $result = array_filter($line, 'strlen');
                    $result = wp_slash($result);
                    wp_reset_postdata();
                    $user_id = get_current_user_id();

                    // Insert the post into the database
                    $post_id = wp_insert_post(array(
                        "post_title" => sanitize_title($result[0]),
                        "post_content" => sanitize_text_field($result[1]),
                        "post_type" => 'swift_property',
                        "post_status" => "publish",
                        'post_author' => $user_id,
                    ));

                    // Set attachment meta
                    setSwiftPropertyFeaturedImage($result[16], $post_id);

                    // property gallery
                    $gal_arr = setSwiftPropertyGallery($result[17], $post_id);

                    // set post metadata
                    $updated = update_post_meta($post_id, 'sp_price', (isset($result[2]) && !empty($result[2]) ? preg_replace('/[^0-9]/', '', sanitize_text_field($result[2])) : ""));
                    $updated = update_post_meta($post_id, 'sp_beds', (isset($result[3]) && !empty($result[3]) ? sanitize_text_field($result[3]) : ""));
                    $updated = update_post_meta($post_id, 'sp_baths', (isset($result[4]) && !empty($result[4]) ? sanitize_text_field($result[4]) : ""));
                    $updated = update_post_meta($post_id, 'sp_property_size', (isset($result[5]) && !empty($result[5]) ? sanitize_text_field($result[5]) : ""));
                    $updated = update_post_meta($post_id, 'sp_lot_size', (isset($result[6]) && !empty($result[6]) ? sanitize_text_field($result[6]) : ""));
                    $updated = update_post_meta($post_id, 'sp_street', (isset($result[7]) && !empty($result[7]) ? sanitize_text_field($result[7]) : ""));
                    $updated = update_post_meta($post_id, 'sp_city', (isset($result[8]) && !empty($result[8]) ? sanitize_text_field($result[8]) : ""));
                    $updated = update_post_meta($post_id, 'sp_state', (isset($result[9]) && !empty($result[9]) ? sanitize_text_field($result[9]) : ""));
                    $updated = update_post_meta($post_id, 'sp_zip', (isset($result[10]) && !empty($result[10]) ? sanitize_text_field($result[10]) : ""));
                    $updated = update_post_meta($post_id, 'sp_mls', (isset($result[11]) && !empty($result[11]) ? sanitize_text_field($result[11]) : ""));
                    $updated = update_post_meta($post_id, 'sp_promo_text', (isset($result[12]) && !empty($result[12]) ? sanitize_text_field($result[12]) : ""));
                    $updated = update_post_meta($post_id, 'sp_YT_url', (isset($result[13]) && !empty($result[13]) ? sanitize_text_field($result[13]) : ""));
                    $updated = update_post_meta($post_id, 'sp_virtual_3d_url', (isset($result[14]) && !empty($result[14]) ? sanitize_text_field($result[14]) : ""));
                    $updated = update_post_meta($post_id, 'sp_status', (isset($result[15]) && !empty($result[15]) ? sanitize_text_field($result[15]) : "Active"));
                    $updated = update_post_meta($post_id, 'sp_property_images', $gal_arr);

                    // set category
                    if (isset($result[18]) && !empty($result[18])) {
                        $sp_cats = @explode(", ", $result[18]);
                        if (!empty($sp_cats)) {
                            foreach ($sp_cats as $sp_cat) {
                                $sp_cat = sanitize_text_field($sp_cat);
                                $terms = term_exists($sp_cat, 'swift_property_category');
                                if ($terms) {
                                    wp_set_post_terms($post_id, $terms['term_id'], 'swift_property_category', true);
                                } else {
                                    $wp_term_id = wp_insert_term($sp_cat, 'swift_property_category');
                                    wp_set_post_terms($post_id, $wp_term_id, 'swift_property_category', true);
                                }
                            }
                        }
                    }

                    // set category
                    if (isset($result[19]) && !empty($result[19])) {
                        $sp_cats = @explode(", ", $result[19]);
                        if (!empty($sp_cats)) {
                            wp_set_post_terms($post_id, sanitize_text_field($sp_cats), 'swift_property_tag', true);
                        }
                    }
                }
                fclose($csvFile);

                if ($post_id) {
                    wp_redirect(admin_url("admin.php?page=sp_bulk_upload&update=1"));
                    exit();
                }
            } else {
                wp_redirect(admin_url() . "admin.php?page=sp_bulk_upload&update=98");
                exit();
            }
        } else {
            wp_redirect(admin_url() . "admin.php?page=sp_bulk_upload&update=98");
            exit();
        }
    }
}

if (!function_exists('swift_property_bulk_upload_callback')) {

    function swift_property_bulk_upload_callback() {
        ?>
        <div class="wrap">
            <h3>Bulk Upload</h3><hr>
            <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
                <div id="message" class="notice notice-success is-dismissible below-h2">
                    <p>Properties imported successfully.</p>
                </div>
            <?php } ?>
            <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 98) { ?>
                <div id="message" class="error error-message is-dismissible below-h2">
                    <p>Please select CSV file only.</p>
                </div>
            <?php } ?>
            <div class="inner_content">
                <form name="FrmKeyphrase" id="FrmKeyphrase" method="post" enctype="multipart/form-data">
                    <table class="form-table">
                        <tr>
                            <th><label for="sp_property_csv">Select CSV file</label><br><small>Please choose only .csv file</small></th>
                            <td>
                                <input type="file" name="sp_property_csv" id="sp_property_csv" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th><small><a href="<?php echo SWIFT_PROPERTY__PLUGIN_URL."swift_property_sample2.csv"; ?>">Click Here to download sample file.</a></small></th>
                            <td>
                                <?php wp_nonce_field('sp_upload_property_settings', 'sp_upload_property_settings') ?>
                                <input type="submit" class="button button-primary" value="Upload" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <?php
    }

}    