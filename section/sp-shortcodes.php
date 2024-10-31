<?php

/**
 *      Shortcode : [swift-properties no_of_property="5" category="all"]
 *      - Display upcoming events listing form event cpt.
 *          - no_of_property : optional; N number of events; Default: 5;
 *          - category : optional; pass category slug, Can pass multiple comma separated category slug; Default: All categories;
 */
add_shortcode('swift-properties', 'swift_property_listing_callback');
if (!function_exists('swift_property_listing_callback')) {

    function swift_property_listing_callback($atts) {
        $sp_gmap_api = get_option("sp_gmap_api");
        wp_enqueue_style('property-listing-style', SWIFT_PROPERTY__PLUGIN_URL . 'css/sp_listing.css');
        wp_enqueue_style('sc-bootstrap', SWIFT_PROPERTY__PLUGIN_URL . 'css/bootstrap-grid.min.css');
        wp_enqueue_style('swiftcloud-fontawesome', SWIFT_PROPERTY__PLUGIN_URL . 'css/font-awesome.min.css', '', '', '');
        wp_enqueue_script('sp-gmap', "https://maps.googleapis.com/maps/api/js?key=" . $sp_gmap_api . "&libraries=places", '', '', '');

        $op = '';
        $prop_address = array();
        $prop_title = array();
        $a = shortcode_atts(
                array(
            'no_of_property' => '',
            'category' => '',
                ), $atts);
        extract($a);


        $no_of_property = sanitize_text_field($no_of_property);
        $category = sanitize_text_field($category);

        $no_of_property = !empty($no_of_property) ? $no_of_property : "5";
        $cat_array = !empty($category) ? explode(",", $category) : '';

        global $paged;
        if (empty($paged))
            $paged = 1;

        $events_args = array(
            'post_type' => 'swift_property',
            'post_status' => 'publish',
            'posts_per_page' => $no_of_property,
            'paged' => $paged
        );

        /*
         *      Category
         */
        if (!empty($cat_array)) {
            $events_args['tax_query'] = array(
                array(
                    'taxonomy' => 'swift_property_category',
                    'field' => 'slug',
                    'terms' => $cat_array
                )
            );
        }

        // sorting
        $sort_by = (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) ? sanitize_text_field($_GET['sort_by']) : false;
        $sort_by_new = $sort_by_bed = $sort_by_size = $sort_by_price = '';
        switch ($sort_by) {
            case 'bedrooms':
                $events_args['meta_key'] = 'sp_beds';
                $events_args['orderby'] = 'meta_value_num';
                $sort_by_bed = 'active';
                break;
            case 'lot':
                $events_args['meta_key'] = 'sp_property_size';
                $events_args['orderby'] = 'meta_value_num';
                $sort_by_size = 'active';
                break;
            case 'price':
                $events_args['meta_key'] = 'sp_price';
                $events_args['orderby'] = 'meta_value_num';
                $events_args['order'] = 'ASC';
                $sort_by_price = 'active';
                break;
            default:
                $events_args['orderby'] = 'publish_date';
                $events_args['order'] = 'DESC';
                $sort_by_new = 'active';
                break;
        }

        $sp_posts = new WP_Query($events_args);

        $op .= '<section class="spPropertyListingRow">
                <div class="layout">
                    <div class="bootstrap-wrapper">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="sp_listing_tabs_view">
                                    <!-- Nav tabs -->
                                    <div class="sp_listing_tabs_nav">
                                      <div class="sp_listing_listTab active"><a href="javascript:;" data-tab="property-list"><i class="fa fa-th-list"></i> List</a></div>
                                      <div class="sp_listing_listTab"><a href="javascript:;" data-tab="map-list"><i class="fa fa-map-marker"></i> Map</a></div>
                                      <a href="#" class="advanceSearchBtn tooltip-bottom" data-tooltip="Coming Soon"><i class="fa fa-search"></i></a>
                                      <div class="sortBtn">
                                        <a href="#"><i class="fa fa-unsorted"></i> Sort by</a>
                                        <ul>
                                            <li>
                                                <a class="' . $sort_by_new . '" href="' . add_query_arg('sort_by', 'newest', get_permalink(get_the_ID())) . '">Newest</a>
                                            </li>
                                            <li>
                                                <a class="' . $sort_by_bed . '" href="' . add_query_arg('sort_by', 'bedrooms', get_permalink(get_the_ID())) . '">Bedrooms (Most)</a>
                                            </li>
                                            <li>
                                                <a class="' . $sort_by_size . '" href="' . add_query_arg('sort_by', 'lot', get_permalink(get_the_ID())) . '">Lot Size (Highest)</a>
                                            </li>
                                            <li>
                                                <a class="' . $sort_by_price . '" href="' . add_query_arg('sort_by', 'price', get_permalink(get_the_ID())) . '">Price (Lowest)</a>
                                            </li>
                                        </ul>
                                      </div>
                                    </div>
                                    <!-- Tab panes -->
                                    <div class="content-tab">
                                      <div class="pane-tab" id="property-list">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row no-gutters">';
        if ($sp_posts->have_posts()):
            $sp_property_size_opt = esc_html(get_option("sp_property_size"));
            $sp_lot_size_opt = esc_html(get_option("sp_lot_size"));

            while ($sp_posts->have_posts()) : $sp_posts->the_post();
                $address = esc_html(get_post_meta(get_the_ID(), 'sp_street', true));
                $city = esc_html(get_post_meta(get_the_ID(), 'sp_city', true));
                $city = (!empty($city)) ? $city . ", " : "";

                $state = esc_html(get_post_meta(get_the_ID(), 'sp_state', true));
                $state = (!empty($state)) ? $state . ", " : "";

                $zip = esc_html(get_post_meta(get_the_ID(), 'sp_zip', true));
                $zip = (!empty($zip)) ? $zip : "";

                $prop_address[] = $address . ", " . $city . $state . $zip;
                $prop_title[] = get_the_title(get_the_ID());

                $op .= getSwiftPropertyBlock(get_the_ID());
            endwhile;
            $op .= swift_property_pagination($sp_posts->max_num_pages, 3, false);

            

        else:
            $op .= "<h3>No property found...</h3>";
        endif;
        $op .= '                </div>
                                            </div>
                                        </div>
                                      </div>';

        // map view
        $op .= '<div class="pane-tab" id="map-list" style="display: none;">';
        if ($sp_posts->have_posts()):
            $op .= '<div id="sp_properties_map" style="width:100%; height: 500px;"></div>';
            $op .= '<script>
                    var delay, geocoder, map, bounds, infowindow, latlng;
                    function sp_initialize() {
                        delay = 100;
                        infowindow = new google.maps.InfoWindow();
                        latlng = new google.maps.LatLng(36.778259, -119.417931);
                        var mapOptions = {
                            zoom: 5,
                            center: latlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }
                        geocoder = new google.maps.Geocoder();
                        map = new google.maps.Map(document.getElementById("sp_properties_map"), mapOptions);
                        bounds = new google.maps.LatLngBounds();
                        
                        setTimeout(function () {
                            theNext();
                        }, 1500);
                    }
                    
                    var locations = ' . json_encode($prop_address) . '
                    var prop_title = ' . json_encode($prop_title) . '
                    function geocodeAddress(address, prop_title, next) {
                        geocoder.geocode({address: address}, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                var p = results[0].geometry.location;
                                var lat = p.lat();
                                var lng = p.lng();
                                createMarker("<h3 style=\"margin-top:0\">"+prop_title+ "</h3>" +address, lat, lng);
                            } else {
                                if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                                    nextAddress--;
                                    delay++;
                                }
                            }
                            next();
                        });
                    }
                    function createMarker(add, lat, lng) {
                        var contentString = add;
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, lng),
                            map: map,
                        });

                        google.maps.event.addListener(marker, "click", function () {
                            infowindow.setContent(contentString);
                            infowindow.open(map, marker);
                        });
                        bounds.extend(marker.position);
                    }


                    var nextAddress = 0;
                    function theNext() {
                        if (nextAddress < locations.length) {
                            setTimeout("geocodeAddress(\'" + locations[nextAddress] + "\', \'" + prop_title[nextAddress] + "\', theNext)", delay);
                            nextAddress++;
                        } else {
                            map.fitBounds(bounds);
                        }
                    }
                    </script>';
        endif;
        $op .= '</div>
                                    </div>
                                 </div>
                            </div>
                        </div>                        
                    </div>
                </section>'; //event wrap

        return $op;
    }

}