<?php

function swift_property_call_Multi_Image_Uploader() {
    new swift_property_Multi_Image_Uploader();
}

//add_action( 'admin_init', 'do_something_152677' );
if (is_admin()) {
    add_action('load-post.php', 'swift_property_call_Multi_Image_Uploader');
    add_action('load-post-new.php', 'swift_property_call_Multi_Image_Uploader');
}

/**
 * swift_property_Multi_Image_Uploader
 */
class swift_property_Multi_Image_Uploader {

    var $post_types = array();

    /**
     * Initialize swift_property_Multi_Image_Uploader
     */
    public function __construct() {
        $this->post_types = array('swift_property');     //limit meta box to certain post types
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Adds the meta box container.
     */
    public function add_meta_box($post_type) {
        if (in_array($post_type, $this->post_types)) {
            add_meta_box(
                    'multi_image_upload_meta_box'
                    , __('Property Gallery', 'swift-property')
                    , array($this, 'render_meta_box_content')
                    , $post_type
                    , 'advanced'
                    , 'high'
            );
        }
    }

    /**
     * Save the images when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save($post_id) { /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

        // Check if our nonce is set.
        if (!isset($_POST['inner_custom_box_nonce']))
            return $post_id;

        $nonce = sanitize_text_field($_POST['inner_custom_box_nonce']);

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'inner_custom_box'))
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */
        // Validate user input.
        $posted_images = sanitize_text_or_array_field($_POST['sp_property_images']);
        $images = array();
        if (!empty($posted_images)) {
            foreach ($posted_images as $image_url) {
                if (!empty($image_url))
                    $images[] = esc_url_raw($image_url);
            }
        }

        // Update the images meta field.
        update_post_meta($post_id, 'sp_property_images', serialize($images));
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field('inner_custom_box', 'inner_custom_box_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta($post->ID, 'sp_property_images', true);

        $metabox_content = '<div id="sp_property_images">';
        $images = unserialize($value);

        $itemsCount = 0;
        $metabox_content .= '<ol class="sp_property_gal vertical">';

        // featured image
        $featured_img_url = get_the_post_thumbnail_url($post->ID, 'full');
        if (!empty($featured_img_url)) {
            $metabox_content .= '<li class="no_sort" id=row-' . $itemsCount . '>';
            $metabox_content .= '<div class="sp_img_sorter"><i class="fas fa-arrows-alt"></i></div>';
            $metabox_content .= '<img class="sp_property_img" src="' . esc_url($featured_img_url) . '" alt="img-' . $itemsCount . '" />';
            $metabox_content .= '<input id="Image_button-' . $itemsCount . '" class="button button-primary btn_sp_prop_gal" data-img="' . $itemsCount . '" type="button" value="Upload Image" />';
            $metabox_content .= '<input class="miu-remove button sp-featured-image" type=\'button\' value=\'Remove\' id=\'remove-' . $itemsCount . '\' /> <strong>Featured Image</strong>';
            $metabox_content .= '</li>';
            $itemsCount++;
        }


        if (!empty($images)) {
            foreach ($images as $image) {
                $metabox_content .= '<li id=row-' . $itemsCount . '>';
                $metabox_content .= '<div class="sp_img_sorter"><i class="fas fa-arrows-alt"></i></div>';
                if ($image) {
                    $metabox_content .= '<img class="sp_property_img" src="' . esc_url($image) . '" alt="img-' . $itemsCount . '" />';
                }
                $metabox_content .= '<input id="img-' . $itemsCount . '" type="text" class="sp_prop_gal_img_url sp_prop_gal_img_path_' . $itemsCount . '" name="sp_property_images[' . $itemsCount . ']" value="' . esc_url($image) . '" />';
                $metabox_content .= '<input id="Image_button-' . $itemsCount . '" class="button button-primary btn_sp_prop_gal" data-img="' . $itemsCount . '" type="button" value="Upload Image" />';
                $metabox_content .= '<input class="miu-remove button" type=\'button\' value=\'Remove\' id=\'remove-' . $itemsCount . '\' />';
                $metabox_content .= '</li>';
                $itemsCount++;
            }
        }
        $metabox_content .= '</ol>';
        $metabox_content .= '</div>';
        // <input type="button" onClick="addRow()" value="Add Image" class="button" />
        $metabox_content .= '<div class="upload-form"><input type="file" id="miu_file" name="files[]" accept="image/*" class="files-data form-control" multiple /><label for="miu_file">Choose file(s)</label><span class="spinner"></span></div>';
        echo $metabox_content;

        $script = "<script>itemsCount=" . $itemsCount . ";</script>";
        echo $script;
    }

    function enqueue_scripts($hook) {
        if ('post.php' != $hook && 'post-edit.php' != $hook && 'post-new.php' != $hook)
            return;
//        wp_enqueue_script('banner_uploader', get_template_directory_uri() . "/js/banner_uploader.js", array('jquery'));
    }

}

add_action('wp_ajax_swift_property_cvf_upload_files', 'swift_property_cvf_upload_files');
add_action('wp_ajax_nopriv_swift_property_cvf_upload_files', 'swift_property_cvf_upload_files'); // Allow front-end submission

function swift_property_cvf_upload_files() {
    $parent_post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;  // The parent ID of our attachments
    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg"); // Supported file types
    $max_file_size = getMaximumFileUploadSize(); // in kb
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;
    $img_gal = '';

    // Image upload handler
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        foreach ($_FILES['files']['name'] as $f => $name) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            // Generate a randon code for each file name
            $new_filename = swift_property_generate_random_code(20) . '.' . $extension;

            if ($_FILES['files']['error'][$f] == 4) {
                continue;
            }

            if ($_FILES['files']['error'][$f] == 0) {
                // Check if image size is larger than the allowed file size
                if ($_FILES['files']['size'][$f] > $max_file_size) {
                    $upload_message[] = "Max upload per batch is " . min(ini_get('post_max_size'), ini_get('upload_max_filesize')) . ", so if you have trouble, try 2 images, then 3, then 4, etc. per batch; this is based on your server's limitation, not this plugin.";
                    continue;

                    // Check if the file being uploaded is in the allowed file types
                } elseif (!in_array(strtolower($extension), $valid_formats)) {
                    $upload_message[] = "$name is not a valid format";
                    continue;
                } else {
                    // If no errors, upload the file...
                    if (move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path . $new_filename)) {

                        $count++;
                        $filename = $path . $new_filename;
                        $filetype = wp_check_filetype(basename($filename), null);
                        $wp_upload_dir = wp_upload_dir();
                        $attachment = array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        // Insert attachment to the database
                        $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);

                        require_once( ABSPATH . 'wp-admin/includes/image.php' );

                        // Generate meta data
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $tmp = md5(microtime());
                        $image_url = $wp_upload_dir['url'] . '/' . basename($filename);
                        $img_gal .= '<li id="row-' . $tmp . '">';
                        $img_gal .= '<div class="sp_img_sorter"><i class="fas fa-arrows-alt"></i></div>';
                        $img_gal .= '<img class="sp_property_img" src="' . esc_url($image_url) . '" alt="">';
                        $img_gal .= '<input id="img-' . $tmp . '" type="text" class="sp_prop_gal_img_url sp_prop_gal_img_path_' . $tmp . '" name="sp_property_images[' . $tmp . ']" value="' . esc_url($image_url) . '" />';
                        $img_gal .= '<input id="Image_button-' . $tmp . '" class="button button-primary btn_sp_prop_gal" data-img="' . $tmp . '" type="button" value="Upload Image" />';
                        $img_gal .= '<input class="miu-remove button" type="button" value="Remove" id="remove-' . $tmp . '" />';
                        $img_gal .= '</li>';
                    }
                }
            }
        }
    }
    // Loop through each error then output it to the screen
    if (isset($upload_message)) :
        foreach ($upload_message as $msg) {
            printf(__('<p class="bg-danger">%s</p>', 'swift-property'), $msg);
        }
    endif;

    // If no error, show success message
    if ($count != 0) {
        echo $img_gal;
    }

    exit();
}

// Random code generator used for file names.
function swift_property_generate_random_code($length = 10) {

    $string = '';
    $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}
