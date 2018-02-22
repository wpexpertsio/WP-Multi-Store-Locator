<?php
$counter = 0;
if ($stores) {
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $user_ID;
    global $wpdb;
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

		$options = get_option('store_locator_map',true);
        $radius_unit = $options['unit'];

        $infowindow_content = $options['infowindow'];
        $infowindow_source = $options['info_window_source'];

        $APKI_KEY = get_option('store_locator_street_API_KEY');

        $img = '';
        $get_store_img = wp_get_attachment_url( get_post_thumbnail_id($store->ID) );
        if(!empty($get_store_img))
            $img = '<img src="'.$get_store_img.'" class="store-img"/>';

		$linkable_title = '<a href="'.get_permalink( $store->ID ).'" target="_blank">' . get_the_title($store->ID) . '</a>';
        $infowindow_content = str_replace('{image}',$img,$infowindow_content);
        $infowindow_content = str_replace('{name}',$linkable_title,$infowindow_content);
        $infowindow_content = str_replace('{address}',get_post_meta($store->ID,'store_locator_address',true),$infowindow_content);
        $infowindow_content = str_replace('{city}',get_post_meta($store->ID,'store_locator_city',true),$infowindow_content);
        $infowindow_content = str_replace('{state}',get_post_meta($store->ID,'store_locator_state',true),$infowindow_content);
        $infowindow_content = str_replace('{country}',get_post_meta($store->ID,'store_locator_country',true),$infowindow_content);
        $infowindow_content = str_replace('{zipcode}',get_post_meta($store->ID,'store_locator_zipcode',true),$infowindow_content);
        $infowindow_content = str_replace('{phone}',get_post_meta($store->ID,'store_locator_phone',true),$infowindow_content);
		$visit_website = __('Visit Website','wpmsl');
        $infowindow_content = str_replace('{website}','<a href="'.get_post_meta($store->ID,'store_locator_website',true).'">'.$visit_website.'</a>',$infowindow_content);

        $infowindow_content .= '<div class="wpsl-distance">'.number_format($store->distance, 2) . ' '.$radius_unit.'</div>';

        $pano_loader = '';
        if($infowindow_source == 'none')
            $pano_loader = 'pano-hide';

        $infowindow = '<div class="store-infowindow">';
        $infowindow .= apply_filters('wpmsl_infowindow_content',$infowindow_content,$store);
        $infowindow .= '</div>';

		$markers_location = array('lat' => $store->lat, 'lng' => $store->lng, 'infowindow' => $infowindow,'private'=>$private_clinic);
		$markers_location = apply_filters('wpmsl_markers_location',$markers_location,$infowindow,$store);
		$locations['locations'][] = $markers_location;

        //insert transactions to DB
        $sql = "INSERT INTO store_locator_transactions (`post_id`, `user_id`, `date`) VALUES ('".$store->ID."', '".$user_ID."', '".date('Y-m-d H:i:s')."')";
        dbDelta($sql);
        $counter++;
    }
} else {
    $locations = array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
}
?>

<!-- Show Map -->
<?php
$width = '74%';
if( empty( $grid_options['enable'] ) ) {
	$width = '100%';
}

if ($map_options['enable']): $map_options['enable'];?>

	<div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $grid_options['listing_position']?>"></div>

    <script>
        var locations = <?php echo json_encode($locations); ?>;
        store_locator_map_initialize(locations);
    </script>
<?php

	do_action('store_locations',$locations);

 endif; ?>

<!-- Show Grid -->
<?php if ($grid_options['enable'] && isset($grid_options['columns']) && $grid_options['columns']): ?>
    <br>
    <?php if(empty($grid_options['view']) || $grid_options['view'] == 'table'): ?>
    <div class="store_locator_table-container" style="overflow: auto;">
        <table class="store_locator_grid_results" style="margin: 0px;">
            <thead>
                <tr>
                    <?php foreach ($grid_options['columns'] as $column): ?>
                        <th><?php echo $column; ?></th>
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
                                    echo "<a href='". get_post_permalink($store->ID) ."'>".$meta['store_locator_name'][0]."</a>";
                                }else{
                                    echo ($meta['store_locator_name'][0])?$meta['store_locator_name'][0]:'-';
                                }
                                break;
                            case 'address':
                                echo ($meta['store_locator_address'][0])?$meta['store_locator_address'][0]:'-';
								echo 'abtest1';
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
                                echo ($meta['store_locator_zipecode'][0])?$meta['store_locator_zipecode'][0]:'-';
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
								echo 'abtest2';
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
                    echo "<tr><td class='store-locator-not-found' style='text-align: center;' colspan='".count($columns)."'><span><i class='fa fa-map-marker' aria-hidden='true'></i></span><p>". apply_filters('wpmsl_no_stores_found','No Store found') ."</p></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
        <?php else: ?>
        <div class="store-locator-item-container">
            <div class="wpsl-list-title"><?php _e('Store List','wpmsl')?></div>
                <?php
                if($stores){
                 if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                    require_once( GFCommon::get_base_path() . '/form_display.php' );
                 }
                $index = 1;
				$counter = 1;
				$listing_counter = 1;
				// $stores = apply_filters('wpmls_store_info',$stores);
                $map_options = get_option('store_locator_map');
                $accordion = '';
                if($map_options['show_accordion'])
                    $accordion = 'accordion-show';
                foreach ($stores as $store) {

					echo '<div class="store-locator-item '.$accordion.'" data-store-id="'.$store->ID.'" data-marker="'. ($counter-1) .'" id="list-item-'. ($counter-1) .'" >';

					do_action('wpmls_before_list_item',$store,$listing_counter);
					$listing_counter++;

					echo '<div class="circle-count">';
						echo apply_filters('wpmsl_list_counter',$counter++,$store);
					echo '</div>';

					$radius_unit = get_option('store_locator_map',true);
					$radius_unit = $radius_unit['unit'];
                    $address = get_post_meta($store->ID,'store_locator_address',true);

                    echo '<div class="store-list-details">';
                   
                    echo '<div class="store-list-address">';
                    if($single_options['page']){
					$linkable_title = '<a href="'.get_permalink( $store->ID ).'" target="_blank">' . get_the_title($store->ID) . '</a>';
                    }
                    else{
                        $linkable_title =  get_the_title($store->ID);
                    }
                    echo '<input type="hidden" id="pano-address-'.$store->ID.'" class="pano-address" value="'.$address.'" />';
                    $list_content = '<div class="wpsl-name">' . $linkable_title . '</div>';
                    $list_content .= '<div class="wpsl-distance">'.number_format($store->distance, 2) . ' '.$radius_unit.'</div>';
                    $list_content .= '<div class="wpsl-address">'. $address . '</div>';
                    $list_content .= '<div class="wpsl-city">'.get_post_meta($store->ID,'store_locator_city',true). ', ' . get_post_meta($store->ID,'store_locator_state',true) . ' ' . get_post_meta($store->ID,'store_locator_zipcode',true) .'</div>';


					echo $list_data = apply_filters('wpmsl_list_item',$list_content,$store,$radius_unit);

                    do_action('wpmsl_listing_list_item',$store);


					$weblink = get_post_meta($store->ID,'store_locator_website',true);
					if(!empty($weblink))
					   ?><div class="wpsl-wesite-link"><a href="<?php echo $weblink;?>" target="_blank"><?php _e('Visit Website','wpmsl')?></a></div>
<?php
                    $direction_icon = plugins_url( 'assets/img/directions-1x-20150909.png', dirname(__FILE__) );
                    $direction_icon = str_replace(' ','%20',$direction_icon);

                    echo '<div class="store_days_time">';
                    $store_locator_days = get_post_meta($store->ID,'store_locator_days',true);
                    foreach( $store_locator_days as $key => $value ) {
                            if( !empty($value['start']) ) {
                                echo '<p class="days"><b>'.$key.'</b></p>';
                                foreach( $value as $k => $v ) {
                                    if($k == 'start')
                                        echo '<p class="time"><i class="fa fa-clock-o" aria-hidden="true"></i> ' . $v;
                                    else if($k == 'end')
                                    echo ' - ' . $v . '</p>';
                                }
                            }
                    }
                    echo '</div>';

					 echo '</div>';
                      echo "<div class='store-direction' data-direction='".$store->lat.",".$store->lng."' style='cursor:pointer;'>";
                    _e('Get Direction','wpmsl');
                    echo "<div jstcache='600' class='section-hero-header-directions-icon' style='background-image:url(".STORE_LOCATOR_PLUGIN_URL."assets/img/directions-1x-20150909.png);background-size: 14px;width: 14px;height: 14px;background-repeat: no-repeat;float: right;'></div>
                        </div>";
					echo '</div>';
                    $index++;
                    
                    
					do_action('wpmls_after_list_item',$store);
					
                echo '</div>';
                }
                }else{
                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'><div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found','No Store found') ."</p></div>" ."</td></tr>";
                }
				
                ?>
        </div>
        <?php endif; ?>
        <?php $elements = $grid_options['columns']; ?>
        <?php if (!$grid_options['scroll'] && !empty($elements[0])): ?>
            <div id="store_locator_load_more" style="<?php echo (count($stores) > $grid_options['number']) ? 'display: block;' : 'display: none;'; ?>"><?php _e('Load more ...','wpmsl');?></div>
            <script>
                jQuery('#map_loader').hide();
                jQuery('#store_locator_load_more').click(function () {
                    if(jQuery('.store_locator_grid_results tr.store_locator_tr:hidden').length <= <?php echo $grid_options['number']; ?>){
                        jQuery('#store_locator_load_more').hide();
                    }
                    jQuery('.store_locator_grid_results tr.store_locator_tr:hidden:lt(<?php echo $grid_options['number']; ?>)').show("slow");
                    if(jQuery('.store-locator-item:hidden').length <= <?php echo $grid_options['number']; ?>){
                        jQuery('#store_locator_load_more').hide();
                    }
                    jQuery('.store-locator-item:hidden:lt(<?php echo $grid_options['number']; ?>)').show("slow");
                });
            </script>
        <?php endif; ?>    
    </div>
    <?php if ($grid_options['scroll']): ?>
        <script>
            jQuery('#map_loader').hide();
            <?php if (count($stores) > $grid_options['number']):?>        
                jQuery('.store_locator_table-container').scroll(function() {
                    if(jQuery('.store_locator_table-container').scrollTop() == jQuery('.store_locator_grid_results').height() - jQuery('.store_locator_table-container').height()) {
                        jQuery('.store_locator_grid_results tr.store_locator_tr:hidden:lt(<?php echo $grid_options['number']; ?>)').show("slow");
                    }
                });
                jQuery('.store_locator_table-container').height(jQuery('.store_locator_table-container').height()-5);
            <?php endif; ?>    
        </script>
    <?php endif; ?>    
<?php endif; ?>



<!--[if !IE]><!-->
<style>
/*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
@media
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {

        /* Force table to not be like tables anymore */
        table.store_locator_grid_results, .store_locator_grid_results thead, .store_locator_grid_results tbody, .store_locator_grid_results th, .store_locator_grid_results td, .store_locator_grid_results tr {
                display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        .store_locator_grid_results thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
        }

        .store_locator_grid_results tr { border: 1px solid #ccc; }

        .store_locator_grid_results td {
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
        }

        .store_locator_grid_results td:before {
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
        }

        /*
        Label the data
        */
        <?php $index = 1; ?>
        <?php foreach ($grid_options['columns'] as $column): ?>
            .store_locator_grid_results td:nth-of-type(<?php echo $index; ?>):before { content: "<?php echo ucfirst($column); ?>"; }
            <?php $index++; ?>
        <?php endforeach; ?>
}
</style>
<!--<![endif]-->