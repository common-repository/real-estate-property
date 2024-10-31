<?php
/*
 *      Setting page
 */

add_action("init", "swift_property_settings_post_init");

function swift_property_settings_post_init() {
    if (isset($_POST['sp_save_property_settings']) && wp_verify_nonce($_POST['sp_save_property_settings'], 'sp_save_property_settings')) {
        $sp_property_slug = sanitize_text_field($_POST['sp_property_slug']);
        $update1 = update_option('sp_property_slug', $sp_property_slug);

        $sp_property_category_slug = sanitize_text_field($_POST['sp_property_category_slug']);
        $update8 = update_option('sp_property_category_slug', $sp_property_category_slug);

        $sp_logo = sanitize_text_field($_POST['sp_logo_url']);
        $update2 = update_option('sp_logo_url', $sp_logo);

        $sp_property_size = sanitize_text_field($_POST['sp_property_size']);
        $update3 = update_option('sp_property_size', $sp_property_size);

        $sp_lot_size = sanitize_text_field($_POST['sp_lot_size']);
        $update4 = update_option('sp_lot_size', $sp_lot_size);

        $sp_gmap_api = sanitize_text_field($_POST['sp_gmap_api']);
        $update5 = update_option('sp_gmap_api', $sp_gmap_api);

        $sp_currency = sanitize_text_field($_POST['sp_currency']);
        $update6 = update_option('sp_currency', $sp_currency);

        $sp_form_submission = sanitize_text_field($_POST['sp_form_submission']);
        update_option('sp_form_submission', $sp_form_submission);

        if ($update1 || $update2 || $update3 || $update4 || $update5 || $update6 || $update8) {
            wp_safe_redirect(admin_url("admin.php?page=swift_property_control_panel&update=1&tab=sp-general-settings"));
            die;
        }
    }
}

if (!function_exists('swift_property_settings_callback')) {

    function swift_property_settings_callback() {
        ?>
        <div class="wrap">
            <h3>Settings</h3><hr>
            <?php if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) { ?>
                <div id="message" class="notice notice-success is-dismissible below-h2">
                    <p>Setting updated successfully.</p>
                </div>
            <?php } ?>
            <div class="inner_content">
                <h2 class="nav-tab-wrapper" id="sp-setting-tabs">
                    <a class="nav-tab custom-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "sp-general-settings") ? 'nav-tab-active' : ''; ?>" id="sp-general-settings-tab" href="#sp-general-settings">General Settings</a>
                    <a class="nav-tab custom-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sp-help-support-settings") ? 'nav-tab-active' : ''; ?>" id="sp-help-support-tab" href="#sp-help-support-settings">Help & Support</a>
                </h2>

                <div class="tabwrapper">
                    <div id="sp-general-settings" class="panel <?php echo (!isset($_GET['tab']) || $_GET['tab'] == "sp-general-settings") ? 'active' : ''; ?>">
                        <?php include 'sp_general_settings.php'; ?>
                    </div>
                    <div id="sp-help-support-settings" class="panel <?php echo (isset($_GET['tab']) && $_GET['tab'] == "sp-help-support-settings") ? 'active' : ''; ?>">
                        <?php include 'sp_help_support_settings.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}