<?php
/*
 * Description: Archive listing for swift property
 */
get_header();
wp_enqueue_style('property-listing-style', SWIFT_PROPERTY__PLUGIN_URL . 'css/sp_listing.css');
wp_enqueue_style('sc-bootstrap', SWIFT_PROPERTY__PLUGIN_URL . 'css/bootstrap-grid.min.css');
?>
<section class="spPropertyListingRow">
    <div class="layout">
        <div class="bootstrap-wrapper">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <div class="row no-gutters">
                        <?php
                        while (have_posts()) : the_post();
                            getSwiftPropertyBlock(get_the_ID(), true);
                        endwhile;
                        swift_property_pagination(wp_count_posts('swift_property')->publish, 3, true);
                        ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 sp-sidebar">
                    <?php if (is_active_sidebar('swift-property-sidebar')) : ?>
                        <div class="swift-property-widget swift-property-widget-space">
                            <?php dynamic_sidebar('swift-property-sidebar'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var nav = jQuery("body").find("nav").css("position");
        var header = jQuery("body").find("header").css("position");
        var padding = '';

        if (header === 'fixed') {
            padding = jQuery("body").find("header").height();
        } else if (nav === 'fixed') {
            padding = jQuery("body").find("nav").height();
        }
        if (padding !== '') {
            jQuery(".spPropertyListingRow").css('padding-top', padding);
        }
    });
</script>
<?php
get_footer();
