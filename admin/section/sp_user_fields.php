<?php
add_action('show_user_profile', 'add_swift_property_agent_fields');
add_action('edit_user_profile', 'add_swift_property_agent_fields');
add_action('user_new_form', 'add_swift_property_agent_fields');

function add_swift_property_agent_fields($user) {
    $sp_agent_phone = esc_html(get_the_author_meta('sp_agent_phone', $user->ID));
    $sp_agent_pic = esc_html(get_the_author_meta('sp_agent_pic', $user->ID));
    $sp_agent_form_id = esc_html(get_the_author_meta('sp_agent_form_id', $user->ID));
    $sp_agent_license_no = esc_html(get_the_author_meta('sp_agent_license_no', $user->ID));
    ?>
    <h3>Swift Property Agent Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="sp_agent_phone">Agent Phone</label></th>
            <td><input type="text" id="sp_agent_phone" name="sp_agent_phone" value="<?php echo $sp_agent_phone; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="sp_agent_pic">Profile Picture</label></th>
            <td>
                <input class="image_field" type="text" size="36" id="sp_agent_pic" name="sp_agent_pic" value="<?php echo $sp_agent_pic; ?>" />
                <input class="button primary sp_upload_agent_pic" type="button" value="Upload Image" />
                <br />Enter a URL or upload an image
            </td>
        </tr>
        <tr>
            <th><label for="sp_agent_form_id">Agent Form ID</label></th>
            <td><input type="text" id="sp_agent_form_id" name="sp_agent_form_id" value="<?php echo $sp_agent_form_id; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="sp_agent_license_no">License #</label></th>
            <td><input type="text" id="sp_agent_license_no" name="sp_agent_license_no" value="<?php echo $sp_agent_license_no; ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_swift_property_agent_fields');
add_action('edit_user_profile_update', 'save_swift_property_agent_fields');
add_action('user_register', 'save_swift_property_agent_fields');


function save_swift_property_agent_fields($user_id) {
    update_user_meta($user_id, 'sp_agent_phone', sanitize_text_field($_POST['sp_agent_phone']));
    update_user_meta($user_id, 'sp_agent_pic', sanitize_text_field($_POST['sp_agent_pic']));
    update_user_meta($user_id, 'sp_agent_form_id', sanitize_text_field($_POST['sp_agent_form_id']));
    update_user_meta($user_id, 'sp_agent_license_no', sanitize_text_field($_POST['sp_agent_license_no']));
}
