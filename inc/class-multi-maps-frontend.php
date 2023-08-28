<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Multi_Maps_Frontend')){
	class WPMSL_Multi_Maps_Frontend extends WPMSL_Maps_Frontend_Controller{
		public function __construct(){
			if(!is_admin())
			add_shortcode('wp_multi_store_locator_map',array($this, 'wp_multi_store_locator_map_callback'));

			add_action('wp_ajax_make_search_request_maps',array($this, 'make_search_request_maps_callback'));
			add_action('wp_ajax_nopriv_make_search_request_maps',array($this, 'make_search_request_maps_callback'));
			
			add_action('wp_ajax_make_search_request_maps_regular',array($this, 'make_search_request_maps_regular_callback'));
			add_action('wp_ajax_nopriv_make_search_request_maps_regular',array($this, 'make_search_request_maps_regular_callback'));
			
			add_action('wp_ajax_make_search_request_custom_maps',array($this, 'make_search_request_custom_maps_callback'));
			add_action('wp_ajax_nopriv_make_search_request_custom_maps',array($this, 'make_search_request_custom_maps_callback'));

			add_filter('template_include', array($this,'load_map_iframe_template'),10,1);

			add_action( 'trashed_post',array($this, 'refresh_stores_on_delete_restore') );
			add_action( 'untrashed_post',array($this, 'refresh_stores_on_delete_restore') );
			
			add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_script'));
		}

		function refresh_stores_on_delete_restore( $post_id ) {
            if ( 'store_locator' == get_post_type( $post_id )){
				$this->refresh_stores();
			}
         }
         


		public function wp_multi_store_locator_map_callback($atts){
			 $map = shortcode_atts( array(
		        'id' => '',
		    ), $atts );
			 $map_id=$map['id'];
			$layout=get_post_meta($map_id,'map_layouts',true);
			if(isset($layout['layout'])){
				wp_enqueue_style( 'dashicons' );
    			switch ($layout['layout']) {
    				case 'custom':
    					return $this->custom_multiple_map($map_id);
    				break;
    				default:
    					return $this->custom_multiple_map($map_id);
    				break;
    			}
			}
			else{
			     // echo do_shortcode('[store_locator_show map_id='.$map_id.']');
				 echo do_shortcode(esc_attr('[store_locator_show map_id='.$map_id.']'));
			}
		}
		
		public function get_client_ip() {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP']))
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_X_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            else if(isset($_SERVER['HTTP_FORWARDED']))
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            else if(isset($_SERVER['REMOTE_ADDR']))
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            else
                $ipaddress = 'UNKNOWN';
            return $ipaddress;
        }
		public function custom_multiple_map($map_id){
			//wp_enqueue_script('store_frontend_map');
		    ob_start();
		    $map_landing_address=get_post_meta($map_id,'map_landing_address',true);
	        if(isset($map_landing_address['global']) && $map_landing_address['global']=='yes'){
	            $map_landing_address=get_option('map_landing_address',true);
	        }
	        $placeholder_setting = get_post_meta($map_id,'placeholder_settings',true);
	        if(isset($placeholder_setting['global']) && $placeholder_setting['global']=='yes'){
	            $placeholder_setting = get_option('placeholder_settings',true);
	        }
	        $map_options = get_post_meta($map_id,'store_locator_map',true);
	        if(isset($map_options['global']) && $map_options['global']=='yes'){
	            $map_options = get_option('store_locator_map',true);
	        }
	        $grid_options = get_post_meta($map_id,'store_locator_grid',true);
	        if(isset($grid_options['global']) && $grid_options['global']=='yes'){
	            $grid_options = get_option('store_locator_grid',true);
	        }
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
		    if(isset($map_options['radius'])){
			    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
			    $default_radius = trim($find[2]);
		    }
		    // Attributes
		    $atts['location'] = isset($map_landing_address['address']) ? $map_landing_address['address'] : 'United States';
		    $atts['radius'] = $default_radius;
		    $atts['city'] = isset($map_landing_address['city']) ? $map_landing_address['city'] : '';
		    $atts['state'] = isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
		     if (is_ssl()) {
		        $btn = !empty($placeholder_setting['get_location_btn_txt']) ? $placeholder_setting['get_location_btn_txt'] : esc_html__('Get my location','store_locator');
		        $display = 'style="display:block;"';
		    } else {
		        $btn=esc_html__('Get my location ssl must be activated','store_locator');
		        $display = 'style="display:none;"';
		    }
		    $state = (!empty($atts['state'])) ? ', ' . $atts['state']  : '';
		    $address = $atts['location'] .' '. $atts['city'] . $state;
		    ?>
		    <div class="custom">
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
		        },1000);
		    </script>
		    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/custom_script.js'; ?>'></script>
		    <?php
		    $map_height = 'height:774px;';
		    if(isset($map_options['height']) && !empty($map_options['height'])) {
		        $map_height = 'height:' . $map_options['height'].$map_options['heightunit'].';'; 
		    }
		    $map_width = 'width:100%;';
		    if(isset($map_options['width']) && !empty($map_options['width'])) {
		        $map_width = 'width:' . $map_options['width'].$map_options['widthunit'].';'; 
		    }
		    ?>
		    <div class="row ob_stor-relocator" id="store-locator-id" style="<?php echo $map_height . $map_width?>">
		           <?php $map_landing_address=get_option('map_landing_address') ?>
		           		<input id="store_locatore_map_id" name="store_locatore_map_id" type="hidden" value="<?php echo esc_attr($map_id); ?>">
		           		<input id="store_locatore_direction_Addr" name="store_locatore_direction_Addr" type="hidden" value="<?php echo esc_attr($address); ?>">
		                <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lat'] : ''; ?>">
		                <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>">
		        <?php
		        echo do_action('wpmsl_before_map');?>
		        <div class="loader"><div>
		            <?php $placeholder_settings = get_option('placeholder_settings');  ?>
		                <?php if(!empty($map_options['default_search'])){ ?>
		                    <?php  $this->search_window($map_id, $map_options,$grid_options, $placeholder_settings); ?>  
		                <?php }?>
		                <div class="col-right right-sidebar">
						<?php $fullwidth_custom_map = '';
							if( !empty($grid_options['enable'] )) {
								$fullwidth_custom_map = ' class="fullwidth-custom-map" ';
							} 
						
						?>
		                    <div id="map-container" style="position: relative;width: 100%;right: 0%;" class="<?php echo @$grid_options['listing_position']?>">
		                    <div id="store_locatore_search_results" <?php echo $fullwidth_custom_map; ?>></div>
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
		          <?php if( !empty( $grid_options['enable'] ) && isset($grid_options['listing_position']) && $grid_options['listing_position']=='below_map') { ?>
                    <div class="map-listings <?php echo $grid_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
                    </div>
                <?php } ?>   
            </div>
		    <?php
		    do_action('wpmsl_end_shortcode', esc_html($address), esc_attr($atts['radius']));
		    $content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		public function make_search_request_custom_maps_callback() {
            global $wpdb;
            $map_id = isset($_POST['map_id']) ? absint($_POST['map_id']) : '';
            if(empty($map_id))
                wp_send_json_error( esc_html('Oops! something went wrong','store_locator'));
            $radius= (isset($_POST["store_locatore_search_radius"])) ? absint($_POST["store_locatore_search_radius"]) : "50";
            $placeholder_setting = get_post_meta($map_id,'placeholder_settings',true);
            if(isset($placeholder_setting['global']) && $placeholder_setting['global']=='yes'){
                $placeholder_setting = get_option('placeholder_settings',true);
            }
            $map_options = get_post_meta($map_id,'store_locator_map',true);
            if(isset($map_options['global']) && $map_options['global']=='yes'){
                 $map_options = get_option('store_locator_map',true);
            }
            $grid_options = get_post_meta($map_id,'store_locator_grid',true);
            if(isset($grid_options['global']) && $grid_options['global']=='yes'){
                 $grid_options = get_option('store_locator_grid',true);
            } 
            $term_ids=array();
            if ( $terms = get_the_terms($map_id, 'store_locator_category' ) ) {
                $term_ids = wp_list_pluck( $terms, 'term_id' );
            } 
            $cat_posted= !empty($_POST['store_locator_category']) ? absint($_POST['store_locator_category']) : '';
            if (($catkey = array_search($cat_posted, $term_ids)) !== false) {
               $term_ids=array($cat_posted);
            }
            $unit         = ( $map_options['unit'] == 'km' ) ? 'km' : 'mile';
            $center_lat   = isset($_POST['lat']) ? $_POST['lat'] : '';
            $center_lng   = isset($_POST['lng']) ? $_POST['lng'] : '';
            $stores=get_option('wp_multi_store_locator_stores');

            $tag_ids = array();
            if(isset($_POST['store_locator_tag']) && !empty($_POST['store_locator_tag'])){
                $tag_ids=$_POST['store_locator_tag'];
            }
            if(empty($stores))
                $stores=$this->refresh_stores();
            $locations=array();
            if(!empty($stores)){
                $counter = 0;
                $map_list_items='';
                $single_options = get_option('store_locator_single');
                $locations['center'] = array('lat' => $center_lat, 'lng' => $center_lng);
                if(defined('ICL_LANGUAGE_CODE')){
                    foreach ($stores as $store) {
                        $my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $store['ID']) ;
                        if(!empty($my_post_language_details['language_code']) 
                            && 
                            $my_post_language_details['language_code'] == ICL_LANGUAGE_CODE){
                            $filter_stores[] = $store;
                        }
                    }
                }
                if(!empty($filter_stores) and is_array($filter_stores)){
                    $stores = $filter_stores;
                } 
                foreach ($stores as $store_id => $store) {
                    $cats=explode(',', $store['categories']);
                    if(empty(array_intersect($cats,$term_ids))){
                        continue; 
                    }
                    if(!empty($tag_ids)){
                    $tags=$store['tags'];
                    if(empty(array_intersect($tags,$tag_ids))){
                        continue; 
                    }
                    }
                    $meta = $store['post_meta'];
                    $distance=$this->distance($center_lat,$center_lng,$meta['store_locator_lat'],$meta['store_locator_lng'],$unit );
                    if($radius<absint($distance)){
                        continue;
                    }
                    $infowindow_content = $this->info_window_content($map_id,$store_id,$map_options['infowindow']);
                    $infowindow_source = $options['info_window_source'];
                    $infowindow = '<div class="store-infowindow">';
                    $infowindow .= apply_filters('wpmsl_infowindow_content',$infowindow_content,$store);
                    $infowindow .= '</div>';
                    $markers_location = array('lat' => $meta['store_locator_lat'], 'lng' => $meta['store_locator_lng'], 'infowindow' => $infowindow);
                    $markers_location = apply_filters('wpmsl_markers_location',$markers_location,$infowindow,$store);
                    $locations['locations'][] = $markers_location;
                    $map_list_items.=$this->custom_map_list_items($store, $counter,$map_options,$distance,$placeholder_setting);
                    $counter++;
                    
                }
            }
            if(empty($locations)){
                $locations = array('center' => array('lat' => $center_lat, 'lng' => $center_lng), 'locations' => array());
            } ?>
                <!-- Show Map -->
        <?php
        $width = '100%';
        if ($map_options['enable']): $map_options['enable']; ?>
            <div id="store_locatore_search_map" style="height: <?php echo $map_options['height'] . $map_options['heightunit']; ?>;width: <?php echo $width?>;position:absolute" class="<?php echo $map_options['listing_position']?>">
            </div>
            <script>
                setTimeout(function(){
                var locations = <?php echo json_encode($locations); ?>;
                store_locator_map_initialize(locations);
            },300);
            </script>
        <?php
            do_action('store_locations',$locations);
         endif; ?>
        <!-- Show Grid -->
        <?php if ($grid_options['enable']): ?>
                <div class="store-locator-item-container">
		            <div class="wpsl-list-title"><?php echo (!empty($placeholder_setting['store_list'])) ? esc_html($placeholder_setting['store_list']) : esc_html__('Locations','store_locator'); ?></div>
                <?php
                    if($stores){
                        ob_start();
                        echo $map_list_items;
                        echo ob_get_clean();
                    }else{
                        echo "<div class='store-locator-not-found'><i class='fa fa-map-marker' aria-hidden='true'></i><p>". apply_filters('wpmsl_no_stores_found',__('No Store found','store_locator')) ."</p></div>";
                    } 
                    ?>
                </div>
        <?php endif; ?>
        <?php
        die();
        }
		public function custom_map_list_items($store,$counter,$map_options,$distance,$placeholder_setting){
			$meta=$store['post_meta'];
            $accordion = '';
            $dir_type='gmap';
            if($map_options['show_accordion'])
                $accordion = 'accordion-show';
			$list_item='';
			$list_item.='<div class="store-locator-item '.$accordion.'" data-store-id="'.$store['ID'].'" data-marker="'. ($counter) .'" id="list-item-'. ($counter) .'" >';
			ob_start();
			do_action('wpmls_before_list_item',$store,$counter);
			$list_item.=ob_get_clean();
			$list_item.='<div class="circle-count">';
			$list_item.=apply_filters('wpmsl_list_counter',$counter+1,$store);
			$list_item.='</div>';
			$radius_unit = $map_options['unit'];
            $address = $meta['store_locator_address'];
            $list_item.='<div class="store-list-details">';
            $list_item.='<div class="store-list-address">';
			if( $single_options['page'] )
				$store_title = '<a href="'.esc_url($store['guid']).'" target="_blank">' . $store['post_title'] . '</a>';
			else
				$store_title =  $store['post_title'];
            $list_item.='<input type="hidden" id="pano-address-'.$store['ID'].'" class="pano-address" value="'.esc_attr($address).'" />';
            $list_content = '<div class="wpsl-name">' . $store_title . '</div>';
            if(has_post_thumbnail($store['ID']))
            {
            	$list_content .= '<div class="wpsl-image">'.get_the_post_thumbnail($store['ID'], 'post-thumbnail', '' ).'</div>';
            }
            $list_content .= '<div class="wpsl-distance">'.number_format($distance, 2) . ' '.$radius_unit.'</div>';
            $list_content .= '<div class="wpsl-address">'. $address . '</div>';
            $list_content .= '<div class="wpsl-city">'.$meta['store_locator_city']. ', ' .$meta['store_locator_state']. ' ' . $meta['store_locator_zipcode'] .'</div>';
			$store_locator_phone = $meta['store_locator_phone'];
			if(!empty($store_locator_phone)){
				$list_content .= '<div class="wpsl-phone"> <a href="tel:'.esc_attr($meta['store_locator_phone']).'">'.$meta['store_locator_phone'].'</a> </div>';
			}
			$list_data = apply_filters('wpmsl_list_item',$list_content,$store,$radius_unit);
			$list_item.=$list_data;
			$weblink = $meta['store_locator_website'];
			$meta['placeholder_setting'] = $placeholder_setting;
			if(!empty($weblink)){
				$list_item.='<a href="'.esc_url($weblink).'" target="_blank">'.((isset($placeholder_setting['visit_website']) && !empty($placeholder_setting['visit_website'])) ? $placeholder_setting['visit_website'] : __("Visit Website","store_locator")).'</a> ';
            }
            $list_item.=$this->get_direction($meta, $dir_type);
			$list_item.='</div>';
			ob_start();
			do_action('wpmls_after_list_item',$store);
			$list_item.=ob_get_clean();
        	$list_item.='</div></div>';
			return $list_item;
		}
		public function load_map_iframe_template($template){
			$post_id = get_the_ID();
			$post = get_post($post_id);
			
			$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
			if ( $post->post_type == 'store_locator' && strpos($url_path, 'wp-multi-store-locator')!==false) {
		        global $wp_query;
		        $wp_query->is_404=false;
		        status_header(200);
		        $template=STORE_LOCATOR_PLUGIN_PATH.'templates/wp-multi-store-locator.php';
			}
			return $template;
		}
	}
	new WPMSL_Multi_Maps_Frontend();
}