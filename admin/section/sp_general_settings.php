<?php
/*
 *      Swift Property General settings tab
 */
$sp_license_flag = (get_option("sp_license") == "pro") ? 'checked="checked"' : '';
$sp_license_toggle = (get_option("sp_license") == "pro") ? '' : 'pro-license-email';
$sp_license_email_required = (get_option("sp_license") == "pro") ? 'required="required"' : '';
$sp_property_slug = esc_html(get_option("sp_property_slug"));
$sp_property_category_slug = esc_html(get_option("sp_property_category_slug"));
$sp_logo_url = esc_url(get_option("sp_logo_url"));
$sp_property_size = esc_html(get_option("sp_property_size"));
$sp_lot_size = esc_html(get_option("sp_lot_size"));
$sp_gmap_api = esc_html(get_option("sp_gmap_api"));
$sp_currency = esc_html(get_option("sp_currency"));
$sp_form_submission = esc_html(get_option("sp_form_submission"));

wp_enqueue_media();
wp_enqueue_script('sp-upload-media', plugins_url('../js/sp_admin_media_upload.js', __FILE__), array('jquery'), '', true);
?>
<form name="FrmSwiftPropertySettings" id="FrmSwiftPropertySettings" method="post">
    <table class="form-table">
        <tr>
            <th><label for="sp_property_slug">SEO Slug</label></th>
            <td><?php echo home_url('/'); ?><input type="text" id="sp_property_slug" name="sp_property_slug" value="<?php echo $sp_property_slug; ?>" placeholder="reviews" />/CPT-title-slug-here</td>
        </tr>
        <tr>
            <th><label for="sp_property_category_slug">SEO Slug for Category</label></th>
            <td><?php echo home_url('/'); ?><input type="text" id="sp_property_category_slug" name="sp_property_category_slug" value="<?php echo $sp_property_category_slug; ?>" placeholder="homes_for_sale" />/CPT-category-slug-here</td>
        </tr>
        <tr>
            <th><label for="sp_logo_url">Enter a URL or Upload logo <span class="dashicons dashicons-editor-help ttip" title="Upload image 250x60px dimensions in JPEG, PNG or GIF format. This will be used for Microformat data."></span></label></th>
            <td>
                <input id="sp_logo_url" type="text" size="36" name="sp_logo_url" class="regular-text" value="<?php echo esc_url($sp_logo_url); ?>" placeholder="URL" />
                <input id="sp_upload_image_button" class="button button-primary" type="button" value="Upload Image" />
            </td>
        </tr>
        <tr>
            <th><label for="sp_property_size">Show Property Size as</label></th>
            <td>
                <select name="sp_property_size" id="sp_property_size" class="regular-text">
                    <option value="Square Feet" <?php selected($sp_property_size, 'Square Feet') ?>>Square Feet</option>
                    <option value="Square Meters" <?php selected($sp_property_size, 'Square Meters') ?>>Square Meters</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sp_lot_size">Show Lot Size as</label></th>
            <td>
                <select name="sp_lot_size" id="sp_lot_size" class="regular-text">
                    <option value="Square Feet" <?php selected($sp_lot_size, 'Square Feet') ?>>Square Feet</option>
                    <option value="Acres" <?php selected($sp_lot_size, 'Acres') ?>>Acres</option>
                    <option value="Square Meters" <?php selected($sp_lot_size, 'Square Meters') ?>>Square Meters</option>
                    <option value="Hectares" <?php selected($sp_lot_size, 'Hectares') ?>>Hectares</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sp_currency">Select Currency</label></th>
            <td>
                <select name="sp_currency" id="sp_currency" class="regular-text">
                    <option value="USD" <?php selected($sp_currency, 'USD') ?>>USD</option>
                    <option value="GBP" <?php selected($sp_currency, 'GBP') ?>>GBP</option>
                    <option value="EUR" <?php selected($sp_currency, 'EUR') ?>>EUR</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sp_gmap_api">Enter Google Map API key</label></th>
            <td>
                <input type="text" id="sp_gmap_api" name="sp_gmap_api" value="<?php echo $sp_gmap_api; ?>" placeholder="Google Map API key" class="regular-text" />
                <a href="https://developers.google.com/maps/documentation/embed/get-api-key" target="_blank">Get Google Map API key</a>
                Maps JavaScript API
                Geocoding API
                Maps Static API
            </td>
        </tr>
        <?php if (get_option("sp_license") == "pro"): ?>
            <tr>
                <th><label for="sp_form_submission">Form Submission:</label></th>
                <td>
                    <select name="sp_form_submission" id="sp_form_submission" class="regular-text">
                        <option value="SwiftCRM" <?php selected($sp_form_submission, 'SwiftCRM') ?>>SwiftCRM</option>
                    </select>
                </td>
            </tr>            
        <?php endif; ?>
        <tr>
            <th colspan="2">
                <?php wp_nonce_field('sp_save_property_settings', 'sp_save_property_settings'); ?>
                <button type="submit" class="button-primary" id="sp-settings-btn" value="sp_settings" name="sp_settings">Save Settings</button>
            </th>
        </tr>
    </table>
</form>