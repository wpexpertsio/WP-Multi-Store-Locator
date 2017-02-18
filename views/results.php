<?php
if (!defined('ABSPATH')) {    
exit; // Exit if accessed directly 
}

	if(wp_verify_nonce( $_POST['main_nonce_search'], 'main_nonce_searchact' )){
		
	
	global $wpdb;
    $map_options  = get_option('store_locator_map');
    $grid_options = get_option('store_locator_grid');
    $center_lat   = floatval($_POST['lat']);
    $center_lng   = floatval($_POST['lng']);
    $radius       = (isset($_POST["store_locatore_search_radius"]))?("HAVING distance < ".floatval($_POST["store_locatore_search_radius"])):"";
    $unit         = ( $map_options['unit'] == 'km' ) ? 6371 : 3959;
    $stores       = array();



// Check if we need to filter the results by category.
    $tag_filter = '';
    $cat_filter = '';
	
	
	
    if (isset($_POST['store_locator_category']) and !empty($_POST['store_locator_category'])) {
		$filter_category_ids = intval($_POST['store_locator_category']);
        $cat_filter = " INNER JOIN $wpdb->term_relationships AS term_rel ON posts.ID = term_rel.object_id
                        INNER JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
                        AND term_tax.taxonomy = 'store_locator_category'
                        AND term_tax.term_id IN (" . $filter_category_ids . ") ";
    } 
    if (isset($_POST['store_locator_tag']) and !empty($_POST['store_locator_tag'])) {
        if(is_array($_POST['store_locator_tag'])){
             $filter_tag_ids = $_POST['store_locator_tag'];
        }else{
            $filter_tag_ids = array($_POST['store_locator_tag']);
        }
        $tag_filter = " INNER JOIN $wpdb->term_relationships AS term_rel ON posts.ID = term_rel.object_id
                        INNER JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
                        AND term_tax.taxonomy = 'store_locator_tag'
                        AND term_tax.term_id IN (" . implode(',', $filter_tag_ids) . ") ";
    } 
    
      if ((isset($_POST['store_locator_tag']) && $_POST['store_locator_tag']) && (isset($_POST['store_locator_category']) && $_POST['store_locator_category'])) {
        if(is_array($_POST['store_locator_tag'])){
             $filter_tag_ids = $_POST['store_locator_tag'];
        }else{
            $filter_tag_ids = array($_POST['store_locator_tag']);
        }
        if(is_array($_POST['store_locator_category'])){
            $filter_category_ids = $_POST['store_locator_category'];
        }else{
            $filter_category_ids = array($_POST['store_locator_category']);
        }
          $query_byTag="
                SELECT DISTINCT psts.ID
                FROM $wpdb->posts AS psts, $wpdb->term_relationships AS psts_rel, $wpdb->terms AS psts_ter
                WHERE psts.ID = psts_rel.object_id
                AND psts_ter.term_id = psts_rel.term_taxonomy_id
                AND psts_ter.term_id IN (".implode(',', $filter_tag_ids).")";
//    print_r($wpdb->get_results($query_byTag));
        $query_byCat="
                SELECT DISTINCT psts.ID
                FROM $wpdb->posts AS psts, $wpdb->term_relationships AS psts_rel, $wpdb->terms AS psts_ter
                WHERE psts.ID = psts_rel.object_id
                AND psts_ter.term_id = psts_rel.term_taxonomy_id
                AND psts_ter.term_id IN (".implode(',', $filter_category_ids).")";
//    print_r($wpdb->get_results($query_byCat));
    $tag_filter = " AND posts.ID IN (".$query_byTag.") ";
    $cat_filter = " AND posts.ID IN (".$query_byCat.") ";
      } 

    $stores = $wpdb->get_results("SELECT post_lat.meta_value AS lat,
                           post_lng.meta_value AS lng,
                           posts.ID, 
                           ( $unit * acos( cos( radians( $center_lat ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( $center_lng ) ) + sin( radians( $center_lat ) ) * sin( radians( post_lat.meta_value ) ) ) ) 
                      AS distance
                      FROM $wpdb->posts AS posts
                      INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'store_locator_lat'
                      INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'store_locator_lng'
                      $cat_filter
                      $tag_filter
                      WHERE posts.post_type = 'store_locator' 
                      AND posts.post_status = 'publish' GROUP BY posts.ID $radius ORDER BY distance"
    );
}
//    print_r($stores);
// echo dirname( __FILE__ );



if ($stores) {

    global $user_ID;
   
    $single_options = get_option('store_locator_single');
    $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
    foreach ($stores as $store) {
        $meta = get_post_meta($store->ID);
        $working_hours = "<table class='store_locator_working_hours'><tr><td colspan='3'>" . __("Working Hours", "store_locator") . "</td></tr>";
        $metaDays = $meta['store_locator_days'][0];
        $metaDays = unserialize($metaDays);
        $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        foreach ($days as $day) {
            $working_hours .= "<tr class='".(($metaDays[$day]['status'] == "1") ?'store-locator-open':'store-locator-closed') ."'><td>" . $day . "</td><td>" . (($metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $metaDays[$day]['end'] . "</span></td></tr>";
        }
        $working_hours .= "</table>";
		if (has_post_thumbnail( $store->ID ) ){ ?>
		<?php $images = wp_get_attachment_image_src( get_post_thumbnail_id( $store->ID ), 'single-post-thumbnail' ); 
		
		 $image = '<div class="img-content" ><img  style="width:150px;" src="'.$images[0].'" /></div>';
		 
		} else {
			 $image = '';
		}
        $infowindow = str_replace(
		array("{address}", "{image}","{city}", "{state}", "{country}", "{zipcode}", "{name}", "{phone}", "{website}", "{working_hours}"), 
		array($meta['store_locator_address'][0], $image, $meta['store_locator_city'][0], $meta['store_locator_state'][0], $meta['store_locator_country'][0], $meta['store_locator_zipcode'][0], $meta['store_locator_name'][0], $meta['store_locator_phone'][0], $meta['store_locator_website'][0], $working_hours), $map_options['infowindow']
		);
        
		$locations['locations'][] = array('lat' => $store->lat, 'lng' => $store->lng, 'infowindow' => $infowindow);
        
        //insert transactions to DB
        // $sql = "INSERT INTO store_locator_transactions (`post_id`, `user_id`, `date`) VALUES ('".$store->ID."', '".$user_ID."', '".date('Y-m-d H:i:s')."')";
        // dbDelta($sql);
    }
} else {
    $locations = array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
}
?>


<!-- Show Map -->   
<?php 

 $map_options  = get_option('store_locator_map');
if ($map_options['enable']):
	update_option('store_locator_map_data_location_data',$locations);
?>
    <div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $map_options['width'] . $map_options['widthunit']; ?>;"></div>
    
<?php endif; ?>



<!-- Show Grid -->   
<?php if ($grid_options['enable'] && isset($grid_options['columns']) && $grid_options['columns']): ?>
    <br>
    <?php if(empty($grid_options['view']) || $grid_options['view'] == 'table'): ?>
    <div class="store_locator_table-container" style="overflow: auto;">
        <table class="store_locator_grid_results" style="margin: 0px;">
            <thead>
                <tr>
                    <?php foreach ($grid_options['columns'] as $column): ?>
                        <th><?php echo esc_html($column); ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                    <tbody>
                <?php
                if($stores){
                     if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                        require_once( GFCommon::get_base_path() . '/form_display.php' );
                     }
                $index = 1;
                foreach ($stores as $store) {
                    $meta = get_post_meta($store->ID);
                    echo '<tr class="store_locator_tr" style="' . (($grid_options['number'] > 0 && $index > $grid_options['number']) ? 'display: none;' : '') . ' ">';
                    foreach ($grid_options['columns'] as $column) {
                        echo '<td>';
                        switch ($column) {
                            case 'name':
                                if($single_options['page']){
                                    echo "<a href='". get_post_permalink($store->ID) ."'>".esc_html($meta['store_locator_name'][0])."</a>";
                                }else{
                                    echo ($meta['store_locator_name'][0])?$meta['store_locator_name'][0]:'-';
                                }
                                break;
                            case 'address':
                                echo ($meta['store_locator_address'][0])?$meta['store_locator_address'][0]:'-';
                                break;
                            case 'city':
                                echo ($meta['store_locator_city'][0])?$meta['store_locator_city'][0]:'-';
                                break;
                            case 'state':
                                echo ($meta['store_locator_state'][0])?$meta['store_locator_state'][0]:'-';
                                break;
                            case 'country':
                                echo ($meta['store_locator_country'][0])?$meta['store_locator_country'][0]:'-';
                                break;
                            case 'zipcode':
                                // echo ($meta['store_locator_zipecode'][0])?$meta['store_locator_zipecode'][0]:'-';
                                break;
                            case 'website':
                                echo ($meta['store_locator_website'][0])?$meta['store_locator_website'][0]:'-';
                                break;
                            case 'phone':
                                echo ($meta['store_locator_phone'][0])?$meta['store_locator_phone'][0]:'-';
                                break;
                            case 'fax':
                                echo ($meta['store_locator_fax'][0])?$meta['store_locator_fax'][0]:'-';
                                break;
                            case 'full_address':
                                echo $meta['store_locator_address'][0] . " " . $meta['store_locator_city'][0] . " " . $meta['store_locator_state'][0] . " " . $meta['store_locator_country'][0] . " " . $meta['store_locator_zipcode'][0];
                                break;
                            case 'working_hours':
                                $working_hours = "<table class='store_locator_working_hours'>";
                                $metaDays = $meta['store_locator_days'][0];
                                $metaDays = unserialize($metaDays);
                                $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                                foreach ($days as $day) {
                                    $working_hours .= "<tr class='".(($metaDays[$day]['status'] == "1") ?'store-locator-open':'store-locator-closed') ."'><td>" . $day . "</td><td>" . (($metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $metaDays[$day]['end'] . "</span></td></tr>";
                                }
                                $working_hours .= "</table>";
                                echo $working_hours;
                                break;
                            case 'managers':
                                $sales = unserialize($meta['store_locator_sales'][0]);
                                if ($sales) {
                                    foreach ($sales as $manager) {
                                        echo get_post($manager)->post_title . "<br>";
                                    }
                                }else{
                                    echo '-';
                                }
                                break;
                            case 'gravity_form':
                                if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                $form_id = $meta['store_locator_gform'][0];
                                // get form array
                                $form = RGFormsModel::get_form_meta( $form_id );
                                // form is valid
                                if(empty($form)) {
                                    echo '-';                                
                                    break;
                                }
                                // print form scripts
                                GFFormDisplay::print_form_scripts($form, true);
                                ?>
                                <!-- Trigger the modal with a button -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#store-locator-modal-<?php echo $store->ID; ?>"><?php _e('Show Form', 'store_locator'); ?></button>

                                <!-- Modal -->
                                <div id="store-locator-modal-<?php echo $store->ID; ?>" class="modal fade store_locator_gf_form" role="dialog" style="display: none !important;">
                                  <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>
                                      <div class="modal-body">
                                        <?php 
                                            $request_uri = $_SERVER['REQUEST_URI'];
                                            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REFERER'];
                                            gravity_form($form_id, false, false, false, array( 'store_id' => $store->ID, 'test' => 2 ), true, 12); 
                                            $_SERVER['REQUEST_URI'] = $request_uri;
                                        ?>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php 
                                }else{
                                    echo '-';
                                }
                                break;
                            default :
                                break;
                        }
                        echo '</td>';
                    }
                    echo '</tr>';
                    $index++;
                }
                }else{
                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'>". __('No Stores founds.', 'store_locator') ."</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
        <?php else: ?>
        <div class="store-locator-item-container">
            
                <?php
                if($stores){
                 if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                    require_once( GFCommon::get_base_path() . '/form_display.php' );
                 }
                $index = 1;
                foreach ($stores as $store) {
                echo '<div class="store-locator-item" style="' . (($grid_options['number'] > 0 && $index > $grid_options['number']) ? 'display: none;' : '') . ' ">';
                    $meta = get_post_meta($store->ID);
                    foreach ($grid_options['columns'] as $column) {
                    echo '<div>';
                        switch ($column) {
                            case 'name':
                                if($single_options['page']){
                                    echo
                                    "<a href='". get_post_permalink($store->ID) ."'><h1 class='store-locator-name'>".$meta['store_locator_name'][0]."</h1> </a><p class='data-distance-".str_replace('.','',$meta['store_locator_lat'][0])."-".str_replace('.','',$meta['store_locator_lng'][0])."' ></p><input type='hidden' class='data-direction-infowindow-".str_replace('.','',$meta['store_locator_lat'][0])."-".str_replace('.','',$meta['store_locator_lng'][0])."'  /> <a class='store-direction' data-direction='".$meta['store_locator_lat'][0].",".$meta['store_locator_lng'][0]."' style='cursor:pointer;' > <div jstcache='599' class='section-hero-header-directions-base ripple-container' style='padding:18px'> <div jstcache='600' class='section-hero-header-directions-icon' style='background-image:url(".plugins_url( 'assets/img/directions-1x-20150909.png', dirname(__FILE__) ).");background-size:20px;width:20px;height:20px'></div> </div> Get Direction  </a>";
                                }else{
                                    echo 
                                    "<h1 class='store-locator-name'>". (($meta['store_locator_name'][0])?$meta['store_locator_name'][0]:'-') ."</h1> <p class='data-distance-".str_replace('.','',$meta['store_locator_lat'][0])."-".str_replace('.','',$meta['store_locator_lng'][0])."' ></p><input type='hidden' class='data-direction-infowindow-".str_replace('.','',$meta['store_locator_lat'][0])."-".str_replace('.','',$meta['store_locator_lng'][0])."'  /> <a class='store-direction' data-direction='".$meta['store_locator_lat'][0].",".$meta['store_locator_lng'][0]."' style='cursor:pointer;' > <div jstcache='599' class='section-hero-header-directions-base ripple-container' style='padding:18px'> <div jstcache='600' class='section-hero-header-directions-icon' style='background-image:url(".plugins_url( 'assets/img/directions-1x-20150909.png', dirname(__FILE__) ).");background-size:20px;width:20px;height:20px'></div> </div> Get Direction  </a>";
                                }
                                break;
                            case 'address':
                                echo "<span>" . __("Address", "store_locator") . "</span>";
                                echo ($meta['store_locator_address'][0])?$meta['store_locator_address'][0]:'-';
                                break;
                            case 'city':
                                echo "<span>" . __("City", "store_locator") . "</span>";
                                echo ($meta['store_locator_city'][0])?$meta['store_locator_city'][0]:'-';
                                break;
                            case 'state':
                                echo "<span>" . __("State", "store_locator") . "</span>";
                                echo ($meta['store_locator_state'][0])?$meta['store_locator_state'][0]:'-';
                                break;
                            case 'country':
                                echo "<span>" . __("Country", "store_locator") . "</span>";
                                echo ($meta['store_locator_country'][0])?$meta['store_locator_country'][0]:'-';
                                break;
                            case 'zipcode':
                                // echo "<span>" . __("Zipcode", "store_locator") . "</span>";
                                // echo ($meta['store_locator_zipecode'][0])?$meta['store_locator_zipecode'][0]:'-';
                                break;
                            case 'website':
                                echo "<span>" . __("Website", "store_locator") . "</span>";
                                echo ($meta['store_locator_website'][0])?$meta['store_locator_website'][0]:'-';
                                break;
                            case 'phone':
                                echo "<span>" . __("Phone", "store_locator") . "</span>";
                                echo ($meta['store_locator_phone'][0])?$meta['store_locator_phone'][0]:'-';
                                break;
                            case 'fax':
                                echo "<span>" . __("Fax", "store_locator") . "</span>";
                                echo ($meta['store_locator_fax'][0])?$meta['store_locator_fax'][0]:'-';
                                break;
                            case 'full_address':
                                echo "<span>" . __("Address", "store_locator") . "</span>";
                                echo $meta['store_locator_address'][0] . " " . $meta['store_locator_city'][0] . " " . $meta['store_locator_state'][0] . " " . $meta['store_locator_country'][0] . " " . $meta['store_locator_zipcode'][0];
                                break;
                            case 'working_hours':
                                $working_hours = "<table class='store_locator_working_hours'>";
                                $metaDays = $meta['store_locator_days'][0];
                                $metaDays = unserialize($metaDays);
                                $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                                foreach ($days as $day) {
                                    $working_hours .= "<tr class='".(($metaDays[$day]['status'] == "1") ?'store-locator-open':'store-locator-closed') ."'><td>" . $day . "</td><td>" . (($metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $metaDays[$day]['end'] . "</span></td></tr>";
                                }
                                $working_hours .= "</table>";
                                echo $working_hours;
                                break;
                            case 'managers':
                                echo "<span>" . __("Managers", "store_locator") . "</span><div class='store_locator_managers_list'>";
                                $sales = unserialize($meta['store_locator_sales'][0]);
                                if ($sales) {
                                    foreach ($sales as $manager) {
                                        echo get_post($manager)->post_title . "<br>";
                                    }
                                }else{
                                    echo '-';
                                }
                                echo "</div>";
                                break;
                            case 'gravity_form':
                                if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                $form_id = $meta['store_locator_gform'][0];
                                // get form array
                                $form = RGFormsModel::get_form_meta( $form_id );
                                // form is valid
                                if(empty($form)) {
                                    echo '-';                                
                                    break;
                                }
                                // print form scripts
                                GFFormDisplay::print_form_scripts($form, true);
                                ?>
                                <!-- Trigger the modal with a button -->
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#store-locator-modal-<?php echo $store->ID; ?>"><?php _e('Show Form', 'store_locator'); ?></button>

                                <!-- Modal -->
                                <div id="store-locator-modal-<?php echo $store->ID; ?>" class="modal fade store_locator_gf_form" role="dialog" style="display: none !important;">
                                  <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>
                                      <div class="modal-body">
                                        <?php 
                                            $request_uri = $_SERVER['REQUEST_URI'];
                                            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REFERER'];
                                            gravity_form($form_id, false, false, false, array( 'store_id' => $store->ID, 'test' => 2 ), true, 12); 
                                            $_SERVER['REQUEST_URI'] = $request_uri;
                                        ?>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php 
                                }else{
                                    echo '-';
                                }
                                break;
                            default :
                                break;
                        }
                    echo '</div>';
                    }
                    $index++;
                echo '</div>';
                }
                }else{
                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'>". __('No Stores founds.', 'store_locator') ."</td></tr>";
                }
                ?>
        </div>
        <?php endif; ?>
        <?php $elements = $grid_options['columns']; ?>
         
    </div>
      
<?php endif; ?>

     