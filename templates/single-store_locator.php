<?php get_header(); ?>


        <?php
        $store_meta = get_post_meta(get_the_ID());
        $single_options = get_option('store_locator_single');
        $map_options = get_option('store_locator_map');
        $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . $map_options['marker1'];
        $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . $map_options['marker2'];
        $working_hours = "<table class='store_locator_grid_results store_locator_working_hours'><tr><td colspan='3'>" . __("Working Hours", "store_locator") . "</td></tr>";
        $store_metaDays = $store_meta['store_locator_days'][0];
        $store_metaDays = unserialize($store_metaDays);
        $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        foreach ($days as $day) {
            $working_hours .= "<tr class='".(($store_metaDays[$day]['status'] == "1") ?"store-locator-open":"store-locator-closed") ."'><td>" . $day . "</td><td>" . (($store_metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $store_metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $store_metaDays[$day]['end'] . "</span></td></tr>";
        }
        $working_hours .= "</table>";

        $infowindow = str_replace(array("{address}", "{city}", "{state}", "{country}", "{zipcode}", "{name}", "{phone}", "{website}", "{working_hours}"), array($store_meta['store_locator_address'][0], $store_meta['store_locator_city'][0], $store_meta['store_locator_state'][0], $store_meta['store_locator_country'][0], $store_meta['store_locator_zipcode'][0], $store_meta['store_locator_name'][0], $store_meta['store_locator_phone'][0], $store_meta['store_locator_website'][0], $working_hours), $map_options['infowindow']);

        ?>
<div class="entry-content">
        <div class="store_locator_container <?php echo ($single_options['image'] && has_post_thumbnail())?'store_locator_has_coverImage':'store_locator_no_coverImage'; ?>">
            <?php if ($single_options['image'] && has_post_thumbnail()): ?>
                <div class="store_locator_header">
                    <?php if ( has_post_thumbnail() ) {
                        the_post_thumbnail();
                     }  ?>
                </div>
            <?php endif; ?>
            <div class="store_locator_body">
                <?php if ($single_options['map']): ?>
                    <div class="store_locator_map">
                        <div id="store_locatore_map" style="height: 400px;width: 400px;"></div>
                        <script>
                            var store_locator_map_options = <?php echo json_encode($map_options); ?>;
                            jQuery(document).ready(function (jQuery) {
                                    initializeMapFrontend(<?php echo $store_meta['store_locator_lat'][0]; ?>, <?php echo $store_meta['store_locator_lng'][0]; ?>);
                            });
                        </script>
                    </div>
                <?php endif; ?>
                <?php if ($single_options['items']): ?>
                    <div class="store_locator_content">
                        <ul>
                            <?php foreach ($single_options['items'] as $item): ?>
                            <li class="<?php echo ($item == "working_hours")?"store_locator_working_hours":""; ?>">
                                    <?php
                                    switch ($item) {
                                        case 'name':
                                            echo "<h1>" . $store_meta['store_locator_name'][0] . "</h1>";
                                            break;
                                        case 'website':
                                            echo "<span>". __("Website: ", "store_locator") ."</span>". $store_meta['store_locator_website'][0];
                                            break;
                                        case 'description':
                                            echo nl2br($store_meta['store_locator_description'][0]);
                                            break;
                                        case 'phone':
                                            echo "<span>". __("Phone: ", "store_locator")."</span>" . $store_meta['store_locator_phone'][0];
                                            break;
                                        case 'fax':
                                            echo "<span>". __("Fax: ", "store_locator") ."</span>". $store_meta['store_locator_fax'][0];
                                            break;
                                        case 'full_address':
                                            echo "<span>". __("Address: ", "store_locator") ."</span>". $store_meta['store_locator_address'][0] . " " . $store_meta['store_locator_city'][0] . " " . $store_meta['store_locator_state'][0] . " " . $store_meta['store_locator_country'][0] . " " . $store_meta['store_locator_zipcode'][0];
                                            break;
                                        case 'working_hours':
                                            $working_hours = "<table class='store_locator_grid_results store_locator_working_hours'>";
                                            $store_metaDays = $store_meta['store_locator_days'][0];
                                            $store_metaDays = unserialize($store_metaDays);
                                            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                                            foreach ($days as $day) {
                                                $working_hours .= "<tr class='".(($store_metaDays[$day]['status'] == "1") ?"store-locator-open":"store-locator-closed") ."'><td>" . $day . "</td><td>" . (($store_metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $store_metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $store_metaDays[$day]['end'] . "</span></td></tr>";
                                            }
                                            $working_hours .= "</table>";
                                            echo $working_hours;
                                            break;
                                        case 'managers':
                                            $sales = unserialize($store_meta['store_locator_sales'][0]);
                                            if ($sales) {
                                                echo "<span class='store_locator_managers_titles' >". __("Managers: ", "store_locator")."</span><div class='store_locator_managers_wrapper'>";
                                                foreach ($sales as $manager) {
                                                    echo "<span>" . get_post($manager)->post_title . "</span>";
                                                }
                                                echo '</div>';
                                            }
                                            break;
                                        default :
                                            break;
                                    }
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </div>
   

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<script>
    function  initializeMapFrontend(store_lat, store_lng) {
        // Detect user location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                    
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
                
        // Display markers on map
        var store_locator_map = new google.maps.Map(document.getElementById('store_locatore_map'), {
            center: new google.maps.LatLng(store_lat, store_lng),
            mapTypeControl: Number( store_locator_map_options.mapTypeControl ),
            scrollwheel: Number( store_locator_map_options.scroll ),
            streetViewControl: Number( store_locator_map_options.streetViewControl ),
            mapTypeId: google.maps.MapTypeId[ store_locator_map_options.type.toUpperCase() ]
        });

        // check if there is style
        if ( store_locator_map_options.style ) {
            store_locator_map.set( 'styles', JSON.parse(store_locator_map_options.style) );
        }

        var bounds = new google.maps.LatLngBounds();
        var infowindow = new google.maps.InfoWindow();

        // user location display
        var marker1 = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng),
            map: store_locator_map,
            icon: store_locator_map_options.marker1
        });
        
        // Store location display
        var marker2 = new google.maps.Marker({
            position: new google.maps.LatLng(store_lat, store_lng),
            map: store_locator_map,
            icon: store_locator_map_options.marker2
        });
        
        //extend the bounds to include each marker's position
        bounds.extend(marker1.position);
        bounds.extend(marker2.position);
        
        google.maps.event.addListener(marker1, 'click', function () {
                infowindow.setContent("<?php echo __("Your location", "store_locator"); ?>");
                infowindow.open(store_locator_map, marker1);
        });
        
        google.maps.event.addListener(marker2, 'click', function () {
                infowindow.setContent("<?php echo $infowindow; ?>");
                infowindow.open(store_locator_map, marker2);
        });
        
        store_locator_map.fitBounds(bounds);
        
        // Draw the route
        directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
        var directionsService = new google.maps.DirectionsService();
        
        var request = {
            origin: new google.maps.LatLng(lat, lng),
            destination: new google.maps.LatLng(store_lat, store_lng),
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(store_locator_map);
            } 
        });
            }, function () {
            
                });
            } else {

            }
    }
</script>
