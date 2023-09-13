<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Stores_Frontend')){
	class WPMSL_Stores_Frontend{
		public function __construct(){
			//add scripts to frontend
			add_action('wp_enqueue_scripts', array($this,'store_frontend_script'),200);
			// single map shortcode
			if(!is_admin())
			add_shortcode('store_locator_show',array($this, 'store_locator_show_func'));
			// Do Search Ajax
			add_action('wp_ajax_make_search_request', array($this, 'make_search_request'));
			add_action('wp_ajax_nopriv_make_search_request', array($this, 'make_search_request'));
		}
		public function store_frontend_script(){
			?>
		    <script>
		        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		    </script>
		    <?php
			
			global $post;

			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wp_multi_store_locator_map') ) {
				$setting_options = get_option('store_locator_map');
				// FOR UPD POWER BUILDER TO AVOID CONFLICTION
				wp_dequeue_script( 'google-maps-api' ); 
				// GOOGLE MAP SCRIPT TO RENDER MAP
				 wp_enqueue_script('store_frontend_map', "https://maps.googleapis.com/maps/api/js?key=".get_option('store_locator_API_KEY')."&libraries=places");
				if( isset( $setting_options['rtl_enabled'] ) ) {
					if( $setting_options['rtl_enabled'] == '1' ) {
						wp_enqueue_style('store_frontend-style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/rtl-style.css');
					} else {
						wp_enqueue_style('store_frontend-style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/style.css');
					}
				} else {
					wp_enqueue_style('store_frontend-style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/style.css');
				}

				wp_enqueue_script('store_frontend_select2', STORE_LOCATOR_PLUGIN_URL . '/assets/js/select2.js', array('jquery'));
				wp_enqueue_style('store_frontend_select2_style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/select2.css');
			}
		}
		public function store_locator_show_func($atts) {
			global $post;
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wp_multi_store_locator_map') ) {
				wp_enqueue_script('store_frontend_map');
			}
		    ob_start();
		    $map_options = get_option('store_locator_map');
		    $grid_options = get_option('store_locator_grid');
		    $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
		    $tag = isset($map_options['tag']) ? $map_options['tag'] : '';
		    $category = isset($map_options['category']) ? $map_options['category'] : '';
		    $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker1']) && !empty($map_options['marker1'])) ? $map_options['marker1'] : "blue.png");
		    $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . ((isset($map_options['marker2']) && !empty($map_options['marker2'])) ? $map_options['marker2'] : "red.png");
		    if(!empty($map_options['marker1_custom'])) {
		        $map_options['marker1'] = $map_options['marker1_custom'];
		    }
		    if(!empty($map_options['marker2_custom'])) {
		        $map_options['marker2'] = $map_options['marker2_custom'];
		    }
		    $default_radius = 50;
		    if(!empty($map_options['radius'])){
		    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
		    $default_radius = trim($find[2]);
		    }
		    // Attributes
		    $map_landing_address=get_option('map_landing_address');
		    $atts = shortcode_atts(
		        array(
		            'location' => isset($map_landing_address['address']) ? $map_landing_address['address'] : 'United States',
		            'radius' => $default_radius,
		            'city' => isset($map_landing_address['city']) ? $map_landing_address['city'] : '',
		            'state' => isset($map_landing_address['country']) ? $map_landing_address['country'] : '',
		        ),
		        $atts
		    );
		    $placeholder_setting = get_option('placeholder_settings');
		     if (is_ssl()) {
		        $btn = $placeholder_setting['get_location_btn_txt'];
		        if(empty($btn)){
		            $btn=esc_html__('Get my location','store_locator');
		        } 
		        $display = 'style="display:block;"';
		    } else {
		        $btn=esc_html__('Get my location ssl must be activated','store_locator');
		        $display = 'style="display:none;"';
		    }
		    $state = (!empty($atts['state'])) ? ', ' . $atts['state']  : '';
		    $address = $atts['location'] .' '. $atts['city'] . $state;
		    ?>
		    <script>
		        var store_locator_map_options  =  <?php echo json_encode($map_options); ?>;
		        var store_locator_grid_options =  <?php echo json_encode($grid_options); ?>;
		        var placeholder_location =  '<?php echo @json_encode($placeholder_setting['location_not_found']); ?>';
		        setTimeout(function() {
		            wpmsl_update_map('<?php echo $address ?>','<?php echo $atts['radius']?>');
		            jQuery('#store_locatore_search_input').val('<?php echo $address?>');
		            jQuery('#store_locatore_search_radius option[value="<?php echo $atts['radius']?>"]').prop('selected', true);
		            if (jQuery.fn.niceSelect) {  
		                jQuery('#store_locatore_search_radius').niceSelect('update'); 
		            }
		        },2000);
		    </script>
		    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/frontend_script.js'; ?>'></script>
		    <?php
		    $map_height = 'height:774px;';
		    if(isset($map_options['height']) && !empty($map_options['height'])) {
		        $map_height = 'height:' . $map_options['height'].$map_options['heightunit'].';'; 
		    }
		    $map_width = 'width:100%;';
		    if(isset($map_options['width']) && !empty($map_options['width'])) {
		        $map_width = 'width:' . $map_options['width'].$map_options['widthunit'].';'; 
		    } ?>
		    <div class="row ob_stor-relocator compatibility" id="store-locator-id" style="<?php echo $map_height . $map_width?>">
		           <?php $map_landing_address=get_option('map_landing_address') ?>
		                <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lat'] : ''; ?>">
		                <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>">
		        <?php
		        echo do_action('wpmsl_before_map');?>
		        <div class="loader"><div>
		            <?php $placeholder_settings = get_option('placeholder_settings');  ?>
		                <?php if(!empty($map_options['default_search'])){ ?>
		                    <div class="col-left leftsidebar slide-left <?php echo isset($grid_options['listing_position']) ? $grid_options['listing_position'].'-skip ' : 'below_map-skip ';  echo isset($grid_options['search_window_position']) ? $grid_options['search_window_position'] : 'wpml_above_map'; ?>">
		                        <?php
		                        $map_window_open = '';
		                        if(isset($map_options['map_window_open'])) {
		                            $map_window_open = $map_options['map_window_open'];
		                            if(!empty($map_window_open))
		                                $map_window_open = 'show_store_locator';
		                        }
		                        ?>
		                        <div class="search-options-btn"><?php echo (isset($placeholder_settings['search_options_btn']) && !empty($placeholder_settings['search_options_btn'])) ? $placeholder_settings['search_options_btn'] : esc_html_e('Search Options','store_locator'); ?></div>
		            <div class="store-search-fields <?php echo $map_window_open?>">
		                <form id="store_locator_search_form" >
		                    <?php if(!empty($display)): ?>
		                    <div class="store_locator_field">
		                <input id="store_locatore_get_btn" class="<?php echo $map_options['search_field_get_my_location']?>"  type="button" value="<?php  echo __($btn, 'store_locator'); ?>"  <?php echo $display; ?>  />
		                    </div>
		                <?php endif; ?>
		                    <div class="store_locator_field <?php echo isset($map_options['search_field_location']) ? $map_options['search_field_location'] : ''; ?>">
		                <input id="store_locatore_search_input"  class="wpsl_search_input " name="store_locatore_search_input" type="text" placeholder="<?php echo ($placeholder_settings['enter_location_txt'] == ''? esc_html_e('Location / Zip Code','store_locator') :$placeholder_settings['enter_location_txt']); ?>">
		                    </div>
		                <?php if($radius): ?>
		                    <div class="store_locator_field <?php echo isset($map_options['search_field_radius']) ? $map_options['search_field_radius'] : ''; ?>">
		                    <select id="store_locatore_search_radius" name="store_locatore_search_radius" class="wpsl_search_radius ">
		                        <?php foreach ($radius as $option): 
		                            $default = (preg_match("/\[[^\]]*\]/", $option))?true:false;
		                            $option = str_replace(array('[',']'), array('',''), $option);
		                            ?>
		                            <option value="<?php echo $option; ?>" <?php echo ($default)?"selected":"" ?> ><?php echo $option." ".$map_options['unit'] ; ?></option>
		                        <?php endforeach; ?>
		                    </select>
		                </div>
		                <?php endif; ?>
		                <?php
		                $terms = get_terms( 'store_locator_category', array('hide_empty' => 0));
		                ?>
		                <div class="store_locator_field <?php echo isset($map_options['category']) ? $map_options['category'] : ''; ?>">
		                    <select name="store_locator_category" id="wpsl_store_locator_category" class="wpsl_locator_category ">
		                        <option value=""> <?php echo (!isset($placeholder_settings['select_category_txt'])) ? esc_html_e('Select Category','store_locator') :$placeholder_settings['select_category_txt']; ?> </option>
		                        <?php foreach ( $terms as $term ) : ?>
		                            <option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?> </option>
		                        <?php endforeach; ?>
		                    </select>
		                </div>
		                <?php $terms = get_terms( 'store_locator_tag', array('hide_empty' => 0)); ?>
		                <div class="store_locator_field <?php echo isset($map_options['tag']) ? $map_options['tag'] : ''; ?>">
		                    <select placeholder="<?php echo (isset($placeholder_settings['select_tags_txt']))? $placeholder_settings['select_tags_txt'] : esc_html_e('Select Tags','store_locator') ; ?>" name="store_locator_tag[]" class="wpsl_locator_category " id="store_locator_tag" multiple="multiple">
		                        <?php foreach ( $terms as $term ) : ?>
		                            <option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?></option>
		                        <?php endforeach; ?>
		                    </select>
		                </div>
		                <div class="store_locator_field">               
		                <input id="store_locatore_search_btn" type="submit" value="<?php echo (!empty($placeholder_settings['search_btn_txt'])) ? $placeholder_settings['search_btn_txt'] : esc_html_e("Search", 'store_locator'); ?>" />
		            </div>
		            </form>
		        </div>
		      </div>
		                <?php }?>
		                <div class="col-right right-sidebar">
		                    <div id="map-container" style="position: relative;width: 100%;right: 0%;" class="<?php echo @$grid_options['listing_position']?>">
		                        <div id="map_loader" style="display:none;z-index: 9;height: <?php echo $map_options['height'].$map_options['heightunit']; ?>;width: <?php echo $map_options['width'].'%'; ?>;position: absolute;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
		                        <div id="store_locatore_search_results"></div>
		                    </div>
		                    <?php 
		                    if( !empty($grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']!='below_map') {                       
		                    ?>
		                        <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
		                        </div>
		                    <?php } ?>      
		                </div>
		            </div>
		            <script>
		                // adding class in content div
		                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		                jQuery( ".loader" ).append( '<img class="load-img" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader.gif'); ?>" width="350" height="350" >' );
		                jQuery( ".ob_stor-relocator" ).append( '<div class="overlay-store"></div>' );
		                jQuery(document).ready(function () {
		                    jQuery( ".closesidebar" ).click(function() {
		                        jQuery( '.leftsidebar' ).toggleClass( "slide-left" );
		                        jQuery( this ).toggleClass( "arrow_right" );
		                    });
		                });
		            </script>
		        </div></div>
		          <?php 
                if( !empty( $grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']=='below_map') {                      
                ?>
                    <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
                    </div>
                <?php } ?>   
		    <?php
		    do_action('wpmsl_end_shortcode', esc_attr($address), esc_attr($atts['radius']));
		    return ob_get_clean();
		}
		public function make_search_request() {
		    global $wpdb;
		    $map_options  = get_option('store_locator_map');
		    $grid_options = get_option('store_locator_grid');
		    $center_lat   = $_POST['lat'];
		    $center_lng   = $_POST['lng'];
		    $radius       = (isset($_POST["store_locatore_search_radius"]))?("HAVING distance < ".$_POST["store_locatore_search_radius"]):"";
		    $unit         = ( $map_options['unit'] == 'km' ) ? 6371 : 3959;
		    $stores       = array();
		    $total =  (isset($grid_options['total_markers']) && !empty($grid_options['total_markers']) && $grid_options['total_markers']!='-1') ? absint($grid_options['total_markers']) : '100000';
		// Check if we need to filter the results by category.
		    $tag_filter = '';
		    $cat_filter = '';
		    if ((isset($_POST['store_locator_category']) && $_POST['store_locator_category']) && (!isset($_POST['store_locator_tag']) || !$_POST['store_locator_tag'])) {
		        if(is_array($_POST['store_locator_category'])){
		            $filter_category_ids = $_POST['store_locator_category'];
		        }else{
		            $filter_category_ids = array($_POST['store_locator_category']);
		        }
		        $cat_filter = " INNER JOIN $wpdb->term_relationships AS term_rel ON posts.ID = term_rel.object_id
		                        INNER JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
		                        AND term_tax.taxonomy = 'store_locator_category'
		                        AND term_tax.term_id IN (" . implode(',', $filter_category_ids) . ") ";
		    }
		    if ((isset($_POST['store_locator_tag']) && $_POST['store_locator_tag']) && (!isset($_POST['store_locator_category']) || !$_POST['store_locator_category'])) {
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

		        $query_byCat="
		                SELECT DISTINCT psts.ID
		                FROM $wpdb->posts AS psts, $wpdb->term_relationships AS psts_rel, $wpdb->terms AS psts_ter
		                WHERE psts.ID = psts_rel.object_id
		                AND psts_ter.term_id = psts_rel.term_taxonomy_id
		                AND psts_ter.term_id IN (".implode(',', $filter_category_ids).")";

		        $tag_filter = " AND posts.ID IN (".$query_byTag.") ";
		        $cat_filter = " AND posts.ID IN (".$query_byCat.") ";
		    }

		    // Set Your Custom Post Type with lat & lng meta field
		    $store_locator_array = array('post_type' => 'store_locator','lat'=> 'store_locator_lat','lng'=> 'store_locator_lng');
		    $store_locator = apply_filters('wpmsl_query_array',$store_locator_array);
		    $strore_post_type = $store_locator['post_type'];
		    $strore_lat = $store_locator['lat'];
		    $strore_lng = $store_locator['lng'];
		    $stores_query = $wpdb->get_results("SELECT post_lat.meta_value AS lat,
		                           post_lng.meta_value AS lng,
		                           posts.ID,
		                           ( $unit * acos( cos( radians( $center_lat ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( $center_lng ) ) + sin( radians( $center_lat ) ) * sin( radians( post_lat.meta_value ) ) ) )
		                      AS distance
		                      FROM $wpdb->posts AS posts
		                      INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = '$strore_lat'
		                      INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = '$strore_lng'
		                      $cat_filter
		                      $tag_filter
		                      WHERE posts.post_type = '$strore_post_type'
		                      AND posts.post_status = 'publish' GROUP BY posts.ID $radius ORDER BY distance LIMIT 0,$total"

		    );
		    $stores = apply_filters('wpmsl_store_query',$stores_query,$strore_post_type,$strore_lat,$strore_lng,$center_lat,$center_lng,$unit,$radius);
			$this->show_stores($stores,$map_options,$center_lat,$center_lng,$grid_options);
		    wp_die();
		}
		public function show_stores($stores,$map_options ,$center_lat  , $center_lng ,$grid_options){
		$counter = 0;
		if ($stores) {
		    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		    global $user_ID;
		    global $wpdb;
		    $single_options = get_option('store_locator_single');
		   $placeholder=get_option('placeholder_settings',true); 
		    $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
			foreach ($stores as $store) {
					if(defined('ICL_LANGUAGE_CODE')){
						$my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $store->ID) ;                
						if(!empty($my_post_language_details['language_code']) 
							and 
							$my_post_language_details['language_code'] == ICL_LANGUAGE_CODE){
							$filter_stores[] = $store;
						}               
					}
		    }
		    if(!empty($filter_stores) and is_array($filter_stores)){
		        $stores = $filter_stores;
		    } 
		    foreach ($stores as $store) {
		        $meta = get_post_meta($store->ID);
		        $working_hours = "<table class='store_locator_working_hours'><tr><td colspan='3'>" . __("Working Hours", "store_locator") . "</td></tr>";
		        $metaDays = $meta['store_locator_days'][0];
		        $metaDays = unserialize($metaDays);     
		        $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		        foreach ($days as $day) {
		            $working_hours .= "<tr class='".(($metaDays[$day]['status'] == "1") ?'store-locator-open':'store-locator-closed') ."'><td>" . $day . "</td><td>" . (($metaDays[$day]['status'] == "1") ? __("Open", "store_locator") : __("Closed", "store_locator")) . "</td><td><span class='store_locator_start'>" . $metaDays[$day]['start'] . "</span><span class='store_locator_end'>" . $metaDays[$day]['end'] . "</span></td></tr>";
		        }
		        $working_hours.="</table>";
				if (has_post_thumbnail( $store->ID ) ){ 
				 $images = wp_get_attachment_image_src( get_post_thumbnail_id( $store->ID ), 'single-post-thumbnail' );
				 $image='<div class="img-content" ><img  style="width:150px;" src="'.$images[0].'" /></div>';
				} else {
					 $image='';
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
				if( $single_options['page'] )
					$store_title = '<a href="'.get_permalink( $store->ID ).'" target="_blank">' . get_the_title($store->ID) . '</a>';
				else
					$store_title =  get_the_title($store->ID);
		        $infowindow_content = str_replace('{image}',$img,$infowindow_content);
		        $infowindow_content = str_replace('{name}',$store_title,$infowindow_content);
		        $infowindow_content = str_replace('{address}',get_post_meta($store->ID,'store_locator_address',true),$infowindow_content);
		        $infowindow_content = str_replace('{city}',get_post_meta($store->ID,'store_locator_city',true),$infowindow_content);
		        $infowindow_content = str_replace('{state}',get_post_meta($store->ID,'store_locator_state',true),$infowindow_content);
		        $infowindow_content = str_replace('{country}',get_post_meta($store->ID,'store_locator_country',true),$infowindow_content);
		        $infowindow_content = str_replace('{zipcode}',get_post_meta($store->ID,'store_locator_zipcode',true),$infowindow_content);
		        $infowindow_content = str_replace('{phone}',get_post_meta($store->ID,'store_locator_phone',true),$infowindow_content);
		        $store_locator_website = get_post_meta($store->ID,'store_locator_website',true);
				if(!empty($store_locator_website)){
		        $infowindow_content = str_replace('{website}','<a href="'.get_post_meta($store->ID,'store_locator_website',true).'">'.((isset($placeholder['visit_website']) && !empty($placeholder['visit_website'])) ? $placeholder['visit_website'] : __("Visit Website","store_locator")).'</a>',$infowindow_content);
		        }
		        else{
		             $infowindow_content = str_replace('{website}','',$infowindow_content);
		        }
		        $infowindow_content .= '<div class="wpsl-distance">'.number_format($store->distance, 2) . ' '.$radius_unit.'</div>';
		        $pano_loader = '';
		        if($infowindow_source == 'none')
		            $pano_loader = 'pano-hide';
		        $infowindow = '<div class="store-infowindow">';
		        $infowindow .= apply_filters('wpmsl_infowindow_content',$infowindow_content,$store);
		        $infowindow .= '</div>';
				$markers_location = array('lat' => $store->lat, 'lng' => $store->lng, 'infowindow' => $infowindow);
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
		$width = '100%';
		if( empty( $grid_options['enable'] ) ) {
			$width = '100%';
		}
		if ($map_options['enable']): $map_options['enable'];?>
			<div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $map_options['listing_position']?>"></div>
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
		    <?php if(empty($grid_options['view']) && $grid_options['view'] == 'table'): ?>
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
		                    echo '<tr class="store_locator_tr" style="' . (($grid_options['total_markers'] > 0 && $index > $grid_options['total_markers']) ? 'display: none;' : '') . ' ">';
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
		                                $days = array(
		                                        "Monday",
		                                        "Tuesday",
		                                        "Wednesday",
		                                        "Thursday",
		                                        "Friday",
		                                        "Saturday",
		                                        "Sunday");
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
		            <?php $placeholder=get_option('placeholder_settings',true); ?>
		            <div class="wpsl-list-title"><?php echo !empty($placeholder['store_list']) ? esc_html($placeholder['store_list']) : esc_html__('Locations','store_locator'); ?></div>
		                <?php
		                if($stores){
		                 if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		                    require_once( GFCommon::get_base_path() . '/form_display.php' );
		                 }
		                $index = 1;
						$counter = 1;
						$listing_counter = 1;
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
							if( $single_options['page'] )
								$store_title = '<a href="'.get_permalink( $store->ID ).'" target="_blank">' . get_the_title($store->ID) . '</a>';
							else
								$store_title =  get_the_title($store->ID);
		                    echo '<input type="hidden" id="pano-address-'.$store->ID.'" class="pano-address" value="'.$address.'" />';
		                    $list_content = '<div class="wpsl-name">' . $store_title . '</div>';
		                    $list_content .= '<div class="wpsl-distance">'.number_format($store->distance, 2) . ' '.$radius_unit.'</div>';
		                    $list_content .= '<div class="wpsl-address">'. $address . '</div>';
		                    $list_content .= '<div class="wpsl-city">'.get_post_meta($store->ID,'store_locator_city',true). ', ' . get_post_meta($store->ID,'store_locator_state',true) . ' ' . get_post_meta($store->ID,'store_locator_zipcode',true) .'</div>';
							$store_locator_phone = get_post_meta($store->ID,'store_locator_phone',true);
							if(!empty($store_locator_phone)){
								$list_content .= '<div class="wpsl-phone"> <a href="tel:'.get_post_meta($store->ID,'store_locator_phone',true).'">'.get_post_meta($store->ID,'store_locator_phone',true).'</a> </div>';
							}
							echo $list_data = apply_filters('wpmsl_list_item',$list_content,$store,$radius_unit);
		                    do_action('wpmsl_listing_list_item',$store);
							$weblink = get_post_meta($store->ID,'store_locator_website',true);
							if(!empty($weblink)){
							   ?><div class="wpsl-wesite-link"><a href="<?php echo $weblink;?>" target="_blank"><?php echo (isset($placeholder['visit_website']) && !empty($placeholder['visit_website'])) ? $placeholder['visit_website'] : __("Visit Website","store_locator"); ?></a></div>
		                    <?php
		                        }
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
								  (!empty($placeholder_settings['getdirection_btn_txt'])) ? _e($placeholder_settings['getdirection_btn_txt'],'store_locator') : _e('Get Direction','store_locator'); 
		                        echo "<div jstcache='600' class='section-hero-header-directions-icon' style='background-image:url(".STORE_LOCATOR_PLUGIN_URL."assets/img/directions-1x-20150909.png);background-size: 14px;width: 14px;height: 14px;background-repeat: no-repeat;float: right;'></div>
		                        </div>";
							echo '</div>';
		                    $index++;
							do_action('wpmls_after_list_item',$store);
		                echo '</div>';
		                }
		                }else{
		                    echo "<tr><td style='text-align: center;' colspan='".count($columns)."'><div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found',__('No Store found','store_locator')) ."</p></div>" ."</td></tr>";
		                }
		                ?>

		        </div>
		        <?php endif; ?>
		        <?php $elements = $grid_options['columns']; ?>
		        <?php if (!$grid_options['scroll'] && !empty($elements[0])): ?>
		            <div id="store_locator_load_more" style="<?php echo (count($stores) > $grid_options['total_markers']) ? 'display: block;' : 'display: none;'; ?>"> Load more ...</div>
		            <script>
		                jQuery('#map_loader').hide();
		                jQuery('#store_locator_load_more').click(function () {
		                    if(jQuery('.store_locator_grid_results tr.store_locator_tr:hidden').length <= <?php echo $grid_options['total_markers']; ?>){
		                        jQuery('#store_locator_load_more').hide();
		                    }
		                    jQuery('.store_locator_grid_results tr.store_locator_tr:hidden:lt(<?php echo $grid_options['total_markers']; ?>)').show("slow");
		                    if(jQuery('.store-locator-item:hidden').length <= <?php echo $grid_options['total_markers']; ?>){
		                        jQuery('#store_locator_load_more').hide();
		                    }
		                    jQuery('.store-locator-item:hidden:lt(<?php echo $grid_options['total_markers']; ?>)').show("slow");
		                });
		            </script>
		        <?php endif; ?>    
		    </div>
		    <?php if ($grid_options['scroll']): ?>
		        <script>
		            jQuery('#map_loader').hide();
		            <?php if (count($stores) > $grid_options['total_markers']):?>        
		                jQuery('.store_locator_table-container').scroll(function() {
		                    if(jQuery('.store_locator_table-container').scrollTop() == jQuery('.store_locator_grid_results').height() - jQuery('.store_locator_table-container').height()) {
		                        jQuery('.store_locator_grid_results tr.store_locator_tr:hidden:lt(<?php echo $grid_options['total_markers']; ?>)').show("slow");
		                    }
		                });
		                jQuery('.store_locator_table-container').height(jQuery('.store_locator_table-container').height()-5);
		            <?php endif; ?>    
		        </script>
		    <?php endif; ?>    
		<?php endif; ?>
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

		</style>

		<?php 
		} 
	}
	new WPMSL_Stores_Frontend();
}
