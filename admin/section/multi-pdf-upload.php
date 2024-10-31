<?php

function swift_property_call_Multi_PDF_Uploader() {
    new swift_property_Multi_PDF_Uploader();
}

//add_action( 'admin_init', 'do_something_152677' );
if (is_admin()) {
    add_action('load-post.php', 'swift_property_call_Multi_PDF_Uploader');
    add_action('load-post-new.php', 'swift_property_call_Multi_PDF_Uploader');
}

/**
 * swift_property_Multi_PDF_Uploader
 */
class swift_property_Multi_PDF_Uploader {

    var $post_types = array();

    /**
     * Initialize swift_property_Multi_PDF_Uploader
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
                    'multi_pdf_upload_meta_box'
                    , __('Property Documents', 'swift-property')
                    , array($this, 'render_meta_box_content')
                    , $post_type
                    , 'advanced'
                    , 'high'
            );
        }
    }

    /**
     * Save the pdfs when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save($post_id) { /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

        // Check if our nonce is set.
        if (!isset($_POST['inner_custom_pdf_nonce']))
            return $post_id;

        $nonce = sanitize_text_field($_POST['inner_custom_pdf_nonce']);

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'inner_custom_pdf'))
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
        $posted_pdfs = sanitize_text_or_array_field($_POST['sp_property_documents']);
        $posted_pdfs_title = sanitize_text_or_array_field($_POST['sp_property_documents_title']);
        $pdfs = array();
        if (!empty($posted_pdfs)) {
            foreach ($posted_pdfs as $i => $pdf_url) {
                if (!empty($pdf_url)) {
                    $pdfs[$i] = array(
                        'pdf_url' => esc_url_raw($pdf_url),
                        'pdf_title' => sanitize_text_field($posted_pdfs_title[$i])
                    );
                }
            }
        }

        // Update the pdfs meta field.
        update_post_meta($post_id, 'sp_property_documents', serialize($pdfs));
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field('inner_custom_pdf', 'inner_custom_pdf_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta($post->ID, 'sp_property_documents', true);
        $metabox_content = '<div id="sp_property_documents">';
        $pdfs = unserialize($value);

        $itemsCount = 0;
        $metabox_content .= '<ol class="sp_property_pdf vertical">';
        if (!empty($pdfs)) {
            foreach ($pdfs as $pdf) {
                $metabox_content .= '<li id=row-pdf-' . $itemsCount . '>';
                $metabox_content .= '<div class="sp_pdf_sorter"><i class="fas fa-arrows-alt"></i></div>';
                $metabox_content .= '<input id="pdf-title-' . $itemsCount . '" type="text" class="sp_prop_gal_pdf_title" name="sp_property_documents_title[' . $itemsCount . ']" value="' . esc_attr($pdf['pdf_title']) .'" placeholder="Document Title" />';
                $metabox_content .= '<input id="pdf-' . $itemsCount . '" type="text" class="sp_prop_gal_pdf_url sp_prop_gal_pdf_path_' . $itemsCount . '" name="sp_property_documents[' . $itemsCount . ']" value="' . esc_url($pdf['pdf_url']) . '" />';
                $metabox_content .= '<input id="pdf_button-' . $itemsCount . '" class="button button-primary btn_sp_pdf_gal" data-pdf="' . $itemsCount . '" type="button" value="Upload PDF" />';
                $metabox_content .= '<input class="sp-pdf-remove button" type=\'button\' value=\'Remove\' data-id=\'' . $itemsCount . '\' id=\'pdf-remove-' . $itemsCount . '\' />';
                $metabox_content .= '</li>';
                $itemsCount++;
            }
        }
        $metabox_content .= '</ol>';
        $metabox_content .= '</div><input type="button" onClick="addPDFRow()" value="Add PDF" class="button" />';
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
