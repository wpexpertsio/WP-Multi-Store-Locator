<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Stores_Backend')){
	class WPMSL_Stores_Backend{
		public function __construct(){
			// add submenu page
			add_action('admin_menu', array($this,'register_stores_submenu_page'));
			//add scripts to backend
			add_action('admin_enqueue_scripts', array($this,'store_locator_backend_script'));
			add_action('admin_notices', array($this,'sample_admin_notice__error' ));
			add_action('wp_dashboard_setup', array($this,'store_locator_custom_dashboard_widgets'));
			// multi maps
			add_action('add_meta_boxes', array($this, 'add_map_locator_meta'));
			add_action('save_post_maps',array($this, 'save_map_meta'));
		}
		public function register_stores_submenu_page() {
		    add_submenu_page('edit.php?post_type=store_locator', esc_html__('Settings','store_locator'), esc_html__('Settings','store_locator'), 'manage_options', 'store_locator_settings_page', array($this,'store_locator_settings_page_callback'));
		}
		public function store_locator_settings_page_callback() {
		    $store_locator_API_KEY  = get_option('store_locator_API_KEY');
		    $store_locator_street_API_KEY  = get_option('store_locator_street_API_KEY');
		    $map_options  = get_option('store_locator_map');
		    $grid_options = get_option('store_locator_grid');
		    $single_options = get_option('store_locator_single');
		    $placeholder_setting = get_option('placeholder_settings');
		    include STORE_LOCATOR_PLUGIN_PATH . 'inc/class-general-settings.php';
		}
		public function store_locator_backend_script() {
			global $pagenow; ?>
		    <script>
		        var stores_json_encoded;
		        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		        var wpmsl_url = '<?php echo STORE_LOCATOR_PLUGIN_URL; ?>';
		    </script>
		    <?php
		    $store_locator_API_KEY = get_option('store_locator_API_KEY');
		    $post_type = get_post_type( get_the_ID() );
		    if( $post_type  == 'store_locator' 
		        || @$_GET['page'] == 'store_locator_settings_page' 
		        || @$_GET['page'] == 'import-export-submenu-page-partner' 
		        || $post_type  == 'maps' 
		        || $pagenow=='edit-tags.php'
		        || $pagenow=='term.php'
		        ) { 
		        wp_enqueue_media();
		        wp_enqueue_script('store_locator_backend_map', "https://maps.googleapis.com/maps/api/js?key=".$store_locator_API_KEY."&libraries=places");
		        wp_enqueue_script('store_locator_backend_script',  STORE_LOCATOR_PLUGIN_URL . '/assets/js/backend_script.js', array('jquery'));
		        wp_enqueue_script('store_backend_select2', STORE_LOCATOR_PLUGIN_URL . '/assets/js/select2.js');
		        wp_enqueue_style('store_backend_select2_style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/select2.css');
		        wp_enqueue_script('ldm_script_time_js', STORE_LOCATOR_PLUGIN_URL . 'assets/js/jquery.timepicker.js');
		        wp_enqueue_style('ldm_script_time_css', STORE_LOCATOR_PLUGIN_URL . 'assets/css/jquery.timepicker.css');
		    }
		    wp_enqueue_style('wpmsl_backend', STORE_LOCATOR_PLUGIN_URL . 'assets/css/backend_styles.css');
		}
		public function sample_admin_notice__error($array) {
		    $class = 'notice notice-error';
		    $admin_notice_array = $this->admin_notice_array($array);
		    if(!empty($admin_notice_array)){
		        foreach($admin_notice_array as $key => $value){
		            if(
		                //condition one is site must be ssl.
		                !is_ssl() and $key == 'wp-locator-ssl-error'
		            ){
		                $message = esc_html__( $value, $key );
		                printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		            }
		        }
		    }
		    $store_locator_API_KEY = get_option('store_locator_API_KEY');
		    if(empty($store_locator_API_KEY)){
		        $message = __( 'Must Provide Google Map Api for Search Location via google map <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">Click here to Create Google Map Api Key.</a>', 'store_locator' );
		        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		    }
		    $nomaps=get_posts(array('post_type' => 'maps'));
		    if(empty($nomaps)){
		        $message = sprintf('<p>'.esc_html__( 'Thanks for updating to WP Multistore locator 2.0. You can now create multiple map instance with lots of new features. So what are you waiting for? %s', 'store_locator' ).'</p>','<p><a href="'.esc_url(admin_url('post-new.php?post_type=maps')).'" class="wpml maps notice">'.__("Create New Map","store_locator").'</a></p>');
		        printf( '<div class="%1$s">%2$s</div>', 'notice notice-success', $message );
		    }
		}
		public function admin_notice_array($array){
		    return array(
		        'wp-locator-ssl-error' => sprintf(esc_html__('Get Current location functionality is disabled For Store Locator Plugin. You must enable SSL for your domain in order to enable this functionality.')),
		    );
		}
		public function store_locator_custom_dashboard_widgets() {
		    global $wp_meta_boxes;
		    wp_add_dashboard_widget('store_locator_custom_dashboard_widget', esc_html__('Top Search for Stores','store_locator'), 
		    	array($this,'store_locator_custom_dashboard_widget_callback'));
		}
		public function store_locator_custom_dashboard_widget_callback() {
		    global $wpdb;
		    $posts_table = $wpdb->prefix . 'posts';
		    $transactions = $wpdb->get_results("SELECT ps.post_title as store, count(tr.post_id) as total_count FROM $posts_table ps LEFT JOIN store_locator_transactions tr ON tr.post_id=ps.ID WHERE ps.post_type='store_locator' AND ps.post_status='publish' GROUP BY ps.ID ORDER BY total_count DESC LIMIT 3");
		    if($transactions){
		        ?>
		        <table class="store_locator_data_dashboard">
		            <tr>
		                <th><?php echo esc_html__("Store Name", 'store_locator'); ?></th>
		                <th><?php echo esc_html__("Hits", 'store_locator'); ?></th>
		            </tr>
		            <?php foreach ($transactions as $store): ?>
		                <tr>
		                    <td><?php echo $store->store; ?></td>
		                    <td><?php echo $store->total_count; ?></td>
		                </tr>
		            <?php endforeach; ?>
		        </table>
		        <a href="<?php echo esc_url(admin_url('edit.php?post_type=store_locator&page=statistics_submenu_page')); ?>" ><?php echo esc_html__("See more ...","store_locator"); ?></a>
		        <?php
		    }else{
		        echo "<div class='store_locator_nodata_dashboard'>".esc_html__("No Data found yet.",'store_locator')."</div>";
		    }
		}
		public function add_map_locator_meta(){
            add_meta_box(
                    'maps-shortcode-metabox', 
                    esc_html__('Map Shortcode', 'store_locator'), 
                    array($this,'map_shortcode_matabox_callback'), 
                    'maps',
                    'side',
                    'high');
            add_meta_box(
                    'maps-layout-metabox', 
                    esc_html__('Select Map Layout', 'store_locator'), 
                    array($this,'map_layout_matabox_callback'), 
                    'maps',
                    'side',
                    'high');        
			add_meta_box('initial-settings', 
			        esc_html__('Initialize Map', 'store_locator'), 
			        array($this,'initial_map_settings_meta_box_callback'), 
			        'maps');
			add_meta_box('map-settings', 
			        esc_html__('Map Settings', 'store_locator'), 
			        array($this,'map_settings_meta_box_callback'), 
			        'maps');
			add_meta_box('grid-settings', 
			        esc_html__('Grid Settings', 'store_locator'), 
			        array($this,'grid_settings_meta_box_callback'), 
			        'maps');
			add_meta_box('placeholders-settings', 
			        esc_html__('Placeholders Settings', 'store_locator'), 
			        array($this,'placeholder_settings_meta_box_callback'), 
			        'maps');
           
		}
        public function map_shortcode_matabox_callback($post){
            global $post;
            ob_start();
            ?>
            <div class="map_shortcode_callback">
                <div class="map_shortcode_copy"  onclick="copytoclipboard()"><span class="dashicons dashicons-admin-page"></span></div>
                <?php  
				echo '<p> <input type="text" value="[wp_multi_store_locator_map id=' . esc_attr($post->ID) . ']" readonly="readonly" name="shortcode"> </p>';?>
            </div>
            <?php 
            echo ob_get_clean();
        }
        public function map_layout_matabox_callback($post){
            global $post;
            //$map_layout=get_post_meta($post->ID, 'map_layouts', true);
            $map_layouts['layout']='custom';
            ob_start(); ?>
            <div class="map_layout_callback">
                <label title="Select Map layout type" for="store_locator_map_layout"><?php echo esc_html__('Map Layout Type', 'store_locator'); ?>:</label>               
                <select name="map_layouts[layout]" id="store_locator_map_layout" >
                    <option value="custom"><?php esc_html_e('Custom','store_locator'); ?></option>
                    <option value="custom" disabled><?php esc_html_e('Full Screen (Pro)','store_locator'); ?></option>
                </select>
            </td></tr>
            </div>
            <?php 
            echo ob_get_clean();
        }
		public function save_map_meta($post_id){
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
				return;
			remove_action('save_post_maps',array($this, 'save_map_meta'));
			if(isset($_POST['map_landing_address'])){
			    $data=$_POST['map_landing_address'];
			    $data['global']=isset($_POST['map_landing_address']['global']) ? 'yes' : 'no';
				update_post_meta($post_id, 'map_landing_address', $data);
			}
			if(isset($_POST['store_locator_map'])){
			    $data=$_POST['store_locator_map'];
			    $data['global']=isset($_POST['store_locator_map']['global']) ? 'yes' : 'no';
				update_post_meta($post_id, 'store_locator_map', $data);
			}
			if(isset($_POST['store_locator_grid'])){
			    $data=$_POST['store_locator_grid'];
			    $data['global']=isset($_POST['store_locator_grid']['global']) ? 'yes' : 'no';
				update_post_meta($post_id, 'store_locator_grid', $data);
			}
			if(isset($_POST['placeholder_settings'])){
			    $data=$_POST['placeholder_settings'];
			    $data['global']=isset($_POST['placeholder_settings']['global']) ? 'yes' : 'no';
				update_post_meta($post_id, 'placeholder_settings', $data);
			}
			if(isset($_POST['map_layouts'])){
			    update_post_meta($post_id, 'map_layouts', $_POST['map_layouts']);
			}
			add_action('save_post_maps',array($this, 'save_map_meta'));
		}
		public function initial_map_settings_meta_box_callback(){
			global $post;
		 $store_locator_API_KEY  = get_option('store_locator_API_KEY');
         $map_landing_address  = get_post_meta($post->ID,'map_landing_address',true); ?>
        <!-- Single page settings -->
          <div class="global_settings_switcher">
          <label for="settings_switcher_wpmsl"><?php esc_html_e('Use global settings','store_locator') ?></label>
              <label class="switch"> 
              <input type="checkbox" name="map_landing_address[global]" value="1" class="settings_switcher_wpmsl" <?php echo (isset($map_landing_address['global']) &&  $map_landing_address['global']=='yes') ? 'checked' : ''; ?>>
              <span class="slider round"></span>
            </label>
          </div>
        <div class="inside store_locator_singel_page_settings" <?php echo (isset($map_landing_address['global']) &&  $map_landing_address['global']=='yes') ? 'style="display:none;"' : ''; ?>>           
            <div class="default_Address_landing">
                <h3><?php echo esc_html__("Map Landing Address", 'store_locator'); ?></h3>
                 <?php if(!empty($store_locator_API_KEY)): ?>
            <table class="widefat" style="border: 0px;">
        <tbody>
        <tr>
            <td><?php echo esc_html__("Address", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_address" class="regular-text" type="text" value="<?php echo isset($map_landing_address['address']) ? $map_landing_address['address'] : ''; ?>" name="map_landing_address[address] "/>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html__("Country", 'store_locator'); ?></td>
            <td>
                <select class="regular-text" name="map_landing_address[country]" id="store_locator_country">
                    <option value="" ></option>
                    <?php
                    global $wpdb;
                    $allCountries = $wpdb->get_results("SELECT * FROM store_locator_country");
                   $selectedCountry =  isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
                    foreach ($allCountries as $country) { ?>
                        <option value="<?php echo $country->name; ?>" <?php  echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
            <td><?php echo esc_html__("State", 'store_locator'); ?></td>
            <td>
                <select class="regular-text" name="map_landing_address[state]" id="store_locator_state">
                    <option value="" ></option>
                    <?php
                    global $wpdb;
                    $allStates = $wpdb->get_results("SELECT * FROM store_locator_state",ARRAY_A);
                    usort($allStates, function($a, $b){ return strcmp($a["name"], $b["name"]); });
                    $selectedState = isset($map_landing_address['state']) ? $map_landing_address['state'] : '';
                    foreach ($allStates as $state) {
                        ?>
                        <option value="<?php echo $state['name']; ?>" <?php echo ($selectedState == $state['name']) ? "selected" : ""; ?>><?php echo $state['name']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html__("City", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_city" type="text" class="regular-text" value="<?php echo isset($map_landing_address['city']) ? $map_landing_address['city'] : ''; ?>" name="map_landing_address[city]"/>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html__("Postal Code", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_zipcode" class="regular-text" type="text" value="<?php echo isset($map_landing_address['zipcode']) ? $map_landing_address['zipcode'] : ''; ?>" name="map_landing_address[zipcode]"/>
            </td>
        </tr>
        </tbody>
    </table>
		    <p><?php esc_html_e('Select default location for marker from bottom','store_locator'); ?></p>
		    <input type="hidden" value="<?php echo isset($map_landing_address['lat']) ? $map_landing_address['lat'] : ''; ?>" name="map_landing_address[lat]" id="store_locator_lat"/>
		    <input type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>" name="map_landing_address[lng]" id="store_locator_lng"/>
                <div id="map-container" style="position: relative;">
                <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
                <div id="map-canvas" style="height: 200px;width: 100%;"></div>
            </div>
              <script>
		        jQuery(document).ready(function (jQuery) {
		            initializeMapBackend();
		        });
		    </script>
              <?php else: ?>
                <?php esc_html_e('To set map landing address please add API key first.','store_locator'); ?>
            <?php endif; ?>
            </div>
        </div>
        <?php
		}
		public function map_settings_meta_box_callback(){
			global $post;
			 $map_options  = get_post_meta($post->ID,'store_locator_map',true);
        ?>
         <div class="global_settings_switcher">
          <label for="settings_switcher_wpmsl"><?php esc_html_e('Use global settings','store_locator') ?></label>
              <label class="switch"> 
              <input type="checkbox" class="settings_switcher_wpmsl" name="store_locator_map[global]" <?php echo (isset($map_options['global']) &&  $map_options['global']=='yes') ? 'checked' : ''; ?>>
              <span class="slider round"></span>
            </label>
          </div>
         <!-- Map settings -->
        <div class="inside store_locator_map_settings" <?php echo (isset($map_options['global']) &&  $map_options['global']=='yes') ? 'style="display:none;"' : ''; ?>>
            <table class="wpml-table">
            	<tbody>
                <tr><th>
                <label title="Enable the display of map on the frontend" for="store_locator_map_enable"><?php echo esc_html__("Show map on frontend", 'store_locator'); ?>?</label>
            </th><td>
                <input value="0" type="hidden" name="store_locator_map[enable]" >
                <input <?php echo (isset($map_options['enable']) && $map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_enable" name="store_locator_map[enable]" >
            </td></tr>
           
            <tr><th>
                <label title="Select Map Width pixels" for="store_locator_map_width"><?php echo esc_html__('Map Width', 'store_locator'); ?>:</label>
            </th><td>
                <input value="<?php echo isset($map_options['width']) ? $map_options['width'] : ''; ?>" type="text" id="store_locator_map_width" name="store_locator_map[width]" size="4">
                <select name="store_locator_map[widthunit]" id="store_locator_map_widthunit" >
                    <option <?php  (isset($map_options['widthunit']) && $map_options['widthunit'] == 'px') ?"selected=": ""; ?> selected value="px">PX</option>
                    <option <?php  echo (isset($map_options['widthunit']) && $map_options['widthunit'] == '%') ?"selected=": ""; ?>  value="%">%</option>
                    
                </select>
            </td></tr>

            <tr><th>
                <label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php echo esc_html__("Map Height ", 'store_locator'); ?>:</label>
            </th><td>
                <input value="<?php echo isset($map_options['height']) ? $map_options['height'] : ''; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
                    <option <?php  echo (isset($map_options['heightunit']) && $map_options['heightunit'] == 'px') ?"selected=": ""; ?>  value="px">PX</option>           
                </select>
            </td></tr>

            <tr><th>
                <label title="Select Map Type" for="store_locator_map_type"><?php echo esc_html__("Map Type", 'store_locator'); ?>:</label>
            </th><td>
                <select name="store_locator_map[type]" id="store_locator_map_type">
                    <option <?php echo (isset($map_options['type']) && $map_options['type'] == 'roadmap') ?"selected=": ""; ?> value="roadmap"><?php esc_html_e('Roadmap','store_locator');?></option>
                    <option <?php echo (isset($map_options['type']) && $map_options['type'] == 'hybrid') ?"selected=": ""; ?> value="hybrid"><?php esc_html_e('Hybrid','store_locator');?></option>
                    <option <?php echo (isset($map_options['type']) && $map_options['type'] == 'satellite') ?"selected=": ""; ?> value="satellite"><?php esc_html_e('Satellite','store_locator');?></option>
                    <option <?php echo (isset($map_options['type']) && $map_options['type'] == 'terrain') ?"selected=": ""; ?> value="terrain"><?php esc_html_e('Terrain','store_locator');?></option>
                </select>
           </td> </tr>

            <tr><th>
                <label title="Choose the unit of search km/mile" for="store_locator_map_unit"><?php echo esc_html__("Search Unit", 'store_locator'); ?>:</label>
            </th><td>
                <select name="store_locator_map[unit]" id="store_locator_map_unit">
                    <option <?php echo (isset($map_options['unit']) && $map_options['unit'] == 'km') ?"selected=": ""; ?> value="km"><?php esc_html_e('Km','store_locator'); ?></option>
                    <option <?php echo (isset($map_options['unit']) && $map_options['unit'] == 'mile') ?"selected=": ""; ?> value="mile"><?php esc_html_e('Mile','store_locator'); ?></option>
                </select>
            </td></tr>
            <tr><th>
                <label title="Choose search options here. the default one will be between square brakets" for="store_locator_map_radius"><?php echo esc_html__("Search radius options", 'store_locator'); ?>:</label>
            </th><td>
                <input value="<?php echo (isset($map_options['radius']) && $map_options['radius']) ? $map_options['radius'] : ''; ?>" type="text" id="store_locator_map_radius" name="store_locator_map[radius]" >
                <span class="store_locator_tip">e.g: 5,10,[25],50,100,200,500</span>
            </td></tr>           
            <tr><th>
                <label title="Scroll Map to screen after search" for="store_locator_map_scroll"><?php echo esc_html__("Scroll to map top after search", 'store_locator'); ?>?</label>
            </th><td>
                <input <?php echo (isset($map_options['mapscrollsearch']) && ($map_options['mapscrollsearch'])==1)?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[mapscrollsearch]" >
            </td></tr>
            <tr><th>
                <label title="Enable the user to change the map type from the frontend" for="store_locator_map_mapTypeControl"><?php echo esc_html__("Show map type control", 'store_locator'); ?>?</label>
            </th><td>
                <input value="0" type="hidden" name="store_locator_map[mapTypeControl]" >
                <input <?php echo (isset($map_options['mapTypeControl']) && $map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_mapTypeControl" name="store_locator_map[mapTypeControl]" >
            </td></tr>

            <tr><th>
                <label title="Enable/Disable zoom by scroll on map" for="store_locator_map_scroll"><?php echo esc_html__('Zoom by scroll on map', 'store_locator'); ?>?</label>
            </th><td>
                <input value="0" type="hidden" name="store_locator_map[scroll]" >
                <input <?php echo (isset($map_options['scroll']) && $map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[scroll]" >
            </td></tr>

            <tr><th>
                <label title="Display default Map Search" for="store_locator_default_search"><?php echo esc_html__('Show Map Search options', 'store_locator'); ?></label>
            </th><td>
                <input value="0" type="hidden" name="store_locator_map[default_search]" >
                <input <?php echo (isset($map_options['default_search']) && $map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_default_search" name="store_locator_map[default_search]" >
            </td></tr>
            <tr>
            	<th>
                <label title="Hide Field Options" for="store_locator_default_search"><?php echo esc_html__('Hide Fields for Search', 'store_locator'); ?></label>
            </th>
            <td>
                <ul class="hide_fields">
                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_get_my_location]" ><?php esc_html_e('Get My Location','store_locator');?></li>
                    <li>
					<input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_location]" ><?php esc_html_e('Location Field','store_locator');?>
                    </li>
                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_radius]" ><?php esc_html_e('Radius Field','store_locator');?></li>
                    <li><input <?php echo (isset($map_options['category']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[category]" ><?php esc_html_e('Category Field','store_locator');?></li>
                    <li><input <?php echo (isset($map_options['tag']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[tag]" ><?php esc_html_e('Tags Field','store_locator');?></li>
                </ul>
            </td>
            </tr>

            <tr><th>
                <label title="Map Search Open as Default" for="map_window_open"><?php echo esc_html__("Map Search Open as Default", 'store_locator'); ?></label>
            </th><td>
                <input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="store_locator_map[map_window_open]" >
            </td></tr>
            
            <tr><th>
                <label title="Switch To RTL" for="rtl_enabled"><?php echo esc_html__("Switch To RTL", 'store_locator'); ?></label>
            </th><td>
                <input <?php echo (isset($map_options['rtl_enabled'])) ?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="store_locator_map[rtl_enabled]" >
            </td></tr>
        </tbody>
    </table>
            <hr>
            <p><b><?php esc_html_e('Map Styles','store_locator');?></b></p>
            <div class="map_Styles_div">
            <p><?php $map_style=!empty($map_options['map_style']) ? $map_options['map_style'] : 1; ?>
                <label title="Standard Map" for="store_locator_map_style1">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/staticmap.png'); ?>" />
                    <?php echo esc_html__("Standard Map", 'store_locator'); ?>
                    <input value="1" type="radio" id="store_locator_map_style1" name="store_locator_map[map_style]" <?php checked( 1, $map_style); ?>>
                </label>
            </p>
            <p>
                <label title="Silver Map" for="store_locator_map_style2">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/silver.png'); ?>" />
                    <?php echo esc_html__("Silver Map", 'store_locator'); ?>
                    <input value="2" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" <?php checked(2, $map_style); ?> >
                </label>
            </p>
            <p>
                <label title="Retro Map" for="store_locator_map_style3">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/retro.png'); ?>" />
                    <?php echo esc_html__("Retro Map", 'store_locator'); ?>
                    <input value="3" type="radio" id="store_locator_map_style3" name="store_locator_map[map_style]" <?php checked(3, $map_style); ?>>
                </label>
            </p>
            <p>
                <label title="Dark Map" for="store_locator_map_style4">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/dark.png'); ?>" />
                    <?php echo esc_html__("Dark Map", 'store_locator'); ?>
                    <input value="4" type="radio" id="store_locator_map_style4" name="store_locator_map[map_style]" <?php checked(4, $map_style); ?>>
                </label>
            </p>

            <p>
                <label title="Night Map" for="store_locator_map_style5">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/night.png'); ?>" />
                    <?php echo esc_html__("Night Map", 'store_locator'); ?>
                    <input value="5" type="radio" id="store_locator_map_style5" name="store_locator_map[map_style]" <?php checked(5, $map_style); ?>>
                </label>
            </p>
            <p>
                <label title="Aubergine Map" for="store_locator_map_style6">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/aubergine.png'); ?>" />
                    <?php echo esc_html__("Aubergine Map", 'store_locator'); ?>
                    <input value="6" type="radio" id="store_locator_map_style6" name="store_locator_map[map_style]" <?php checked(6, $map_style); ?>>
                </label>
            </p>
            <p>
                <label title="Basic Map" for="store_locator_map_style7">
                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/basic.png'); ?>" />
                    <?php echo esc_html__("Basic Map", 'store_locator'); ?>
                    <input value="7" type="radio" id="store_locator_map_style7" name="store_locator_map[map_style]" <?php checked(7, $map_style); ?>>
                </label>
            </p>
        </div>
        <div style="clear: both;"></div>
        <hr>
            <p>
                <label title="Choose the color of user marker" for="store_locator_map_type"><strong><?php echo esc_html__("User Marker", 'store_locator'); ?>:</strong></label>
            </p><?php $marker1=!empty($map_options['marker1']) ? $map_options['marker1'] : 'red.png'; ?>
            <ul class="default_markers">
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'); ?>" />
                    <input type="radio" value="blue.png" name="store_locator_map[marker1]" <?php checked('blue.png', $marker1);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'); ?>" />
                    <input type="radio" value="red.png" name="store_locator_map[marker1]" <?php checked('red.png', $marker1);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'); ?>" />
                    <input type="radio" value="green.png" name="store_locator_map[marker1]" <?php checked('green.png', $marker1);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'); ?>" />
                    <input type="radio" value="orange.png" name="store_locator_map[marker1]" <?php checked('orange.png', $marker1);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'); ?>" />
                    <input type="radio" value="purple.png" name="store_locator_map[marker1]" <?php checked('purple.png', $marker1);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'); ?>" />
                    <input type="radio" value="yellow.png" name="store_locator_map[marker1]" <?php checked('yellow.png', $marker1);?>/>
                </li>

            </ul>
            <p>
              <?php esc_html_e('or add custom marker url','store_locator');?>
              <?php 
                    if(isset($map_options['marker1_custom']) && !empty($map_options['marker1_custom'])){
                        $marker1=$map_options['marker1_custom'];
                        $class='wpmsl_custom_marker';
                        $uploadRemove=esc_html__('Remove','store_locator');
                    }
                    else{
                        $marker1=STORE_LOCATOR_PLUGIN_URL . 'assets/img/upload.png';
                        $class='wpmsl_custom_marker_upload'; 
                        $uploadRemove =esc_html__('Upload','store_locator');
                    }

                ?>
               <div class="<?php echo $class; ?>">
                <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="store_locator_map[marker1_custom]" />
                  <p><?php echo $uploadRemove; ?></p>
              </div>
            </p>
             <hr>
            <p>
                <label title="Choose the color of store marker" for="store_locator_map_type"><strong><?php echo esc_html__("Store Marker", 'store_locator'); ?>:</strong></label>
            </p><?php $marker2=!empty($map_options['marker2']) ? $map_options['marker2'] : 'red.png'; ?>
            <ul class="default_markers">
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'); ?>" />
                    <input type="radio" value="blue.png" name="store_locator_map[marker2]" <?php checked('blue.png', $marker2);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'); ?>" />
                    <input type="radio" value="red.png" name="store_locator_map[marker2]" <?php checked('red.png', $marker2);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'); ?>" />
                    <input type="radio" value="green.png" name="store_locator_map[marker2]" <?php checked('green.png', $marker2);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'); ?>" />
                    <input type="radio" value="orange.png" name="store_locator_map[marker2]" <?php checked('orange.png', $marker2);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'); ?>" />
                    <input type="radio" value="purple.png" name="store_locator_map[marker2]" <?php checked('purple.png', $marker2);?>/>
                </li>
                <li>
                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'); ?>" />
                    <input type="radio" value="yellow.png" name="store_locator_map[marker2]" <?php checked('yellow.png', $marker2);?>/>
                </li>
            </ul>
            <p>
               <?php esc_html_e('or add custom marker url','store_locator');?>
                <?php 
                    if(isset($map_options['marker2_custom']) && !empty($map_options['marker2_custom'])){
                        $marker2=$map_options['marker2_custom'];
                        $class='wpmsl_custom_marker';
                        $uploadRemove=esc_html__('Remove','store_locator');
                    }
                    else{
                        $marker2=STORE_LOCATOR_PLUGIN_URL . 'assets/img/upload.png';
                        $class='wpmsl_custom_marker_upload'; 
                        $uploadRemove =esc_html__('Upload','store_locator');
                    }
                ?>
               <div class="<?php echo $class; ?>">
                <img src="<?php echo $marker2; ?>" width="50px" height="50px">
                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker2 : ''; ?>" name="store_locator_map[marker2_custom]" />
                  <p><?php echo $uploadRemove; ?></p>
              </div>
            </p>
            <p>
                <label title="Use category icons instead" for="store_locator_map_icons_cat"><?php echo esc_html__("Use category markers for store markers", 'store_locator'); ?></label>
                <input <?php echo (isset($map_options['category_icons_or_user']) && $map_options['category_icons_or_user'] == 1)?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_icons_cat" name="store_locator_map[category_icons_or_user]" >
                
            </p>
            <?php
            echo do_action('wpmsl_private_marker_settings');
            ?>
             <hr>
            <p>
                <label title="<?php echo esc_html__('You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content','store_locator'); ?>" for="store_locator_map_infowindow"> <strong><?php echo esc_html__("Info Window Content", 'store_locator'); ?>: </strong><p class="store_locator_tip">placeholders: {image} {address} {city} {state} {country} {zipcode} {name} {phone} {email} {website} {working_hours}</p></label>
                <textarea name="store_locator_map[infowindow]" class="widefat" rows="10" cols="70" id="store_locator_map_infowindow"><?php echo isset($map_options['infowindow']) ? $map_options['infowindow'] : ''; ?></textarea>
            </p>
            <p>
                <label title="<?php echo esc_html__('You can customize the look of the map by adding styles here','store_locator'); ?>" for="store_locator_map_style"><strong><?php echo esc_html__("Customised Map Style", 'store_locator'); ?>:</strong> <p class="store_locator_tip"><?php esc_html_e('You can get some styles from','store_locator');?><a target="_blanck" href="https://snazzymaps.com"> <?php esc_html_e('Snazzy Maps','store_locator');?></a></p> </label>
                <textarea name="store_locator_map[custom_style]" class="widefat" rows="10" cols="70" id="store_locator_map_style"><?php echo isset($map_options['custom_style']) ? $map_options['custom_style'] : ''; ?></textarea>
            </p>
        </div>
        <?php
		}
		public function grid_settings_meta_box_callback(){
			global $post;
			 $grid_options = get_post_meta($post->ID,'store_locator_grid',true); ?>
	         <div class="global_settings_switcher">
          <label for="settings_switcher_wpmsl"><?php esc_html_e('Use global settings','store_locator') ?></label>
              <label class="switch"> 
              <input type="checkbox" class="settings_switcher_wpmsl" name="store_locator_grid[global]" <?php echo (isset($grid_options['global']) &&  $grid_options['global']=='yes') ? 'checked' : ''; ?>>
              <span class="slider round"></span>
            </label>
          </div>
	        <div class="store_locator_grid_settings" <?php echo (isset($grid_options['global']) &&  $grid_options['global']=='yes') ? 'style="display:none;"' : ''; ?>>
	         <!-- Grid settings -->
                <table class="wpml-table">
                    <tbody>
                    <tr><th>                                
                    <label title="Show the results in grid in the frontend" for="store_locator_grid_enable"><?php echo esc_html__("Show grid on frontend", 'store_locator'); ?>?</label>
                    </th><td>
                        <input <?php echo (isset($grid_options['enable']) && $grid_options['enable']==1) ? 'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_enable" name="store_locator_grid[enable]" >
                    </td></tr>
	                <tr class="hide_if_fullscreen_map hide_if_regular_map"><th>
                        <label title="Map Result Show on" for="store_locator_map_type"><?php echo esc_html__("Map Result Show on", 'store_locator'); ?>:</label>
                         </th><td>
                        <ul class='listing_postions_grid_settings'>
                        	<?php $listing=empty($grid_options['listing_position']) ? 'left' : $grid_options['listing_position']; ?>
                            <li>
                                <label style="width: 26px;"><?php esc_html_e('On Map Left Side','store_locator');?>
                                <input <?php checked( 'left' ,$listing); ?> type="radio" value="left" name="store_locator_grid[listing_position]" /></label>
                            </li>
                            <li>
                                <label style="width: 26px;"><?php esc_html_e('On Map Right Side','store_locator');?>
                                <input <?php checked( 'right' ,$listing); ?> type="radio" value="right" name="store_locator_grid[listing_position]" /></label>
                            </li>
                            <li>
                                <label style="width: 26px;"><?php esc_html_e('Below Map','store_locator');?>
                                <input <?php checked( 'below_map' ,$listing); ?> type="radio" value="below_map" name="store_locator_grid[listing_position]" /></label>
                            </li>
                        </ul>
                    </td></tr>
                   <tr class="hide_if_regular_map hide_if_fullscreen_map"><th>
                    <label title="Map Search Options Window Show on" for="store_locator_map_type"><?php echo esc_html__("Map Search Options Window Show on", 'store_locator'); ?>:</label>
                         </th><td>
                    <ul class='listing_postions_grid_settings'>
                        <li><?php $listing=empty($grid_options['search_window_position']) ? 'wpml_search_right' : $grid_options['search_window_position']; ?>
                            <label style="width: 26px;"><?php esc_html_e('On Map Left Side','store_locator');?>
                            <input <?php checked( 'left' ,$listing); ?> type="radio" value="left" name="store_locator_grid[search_window_position]" /></label>
                        </li>
                        <li>
                            <label style="width: 26px;"><?php esc_html_e('On Map Right Side','store_locator');?>
                            <input <?php checked( 'wpml_search_right' ,$listing); ?> type="radio" value="wpml_search_right" name="store_locator_grid[search_window_position]" /></label>
                        </li>
                    </ul>
                   </td></tr>
               </tbody>
           </table>
            </div> 
	        <?php
		}
		public function placeholder_settings_meta_box_callback(){
			global $post;
			$placeholder_setting = get_post_meta($post->ID,'placeholder_settings',true);
			if(empty($placeholder_setting))
				$placeholder_setting=array();
        ?>
         <div class="global_settings_switcher">
          <label for="settings_switcher_wpmsl"><?php esc_html_e('Use global settings','store_locator') ?></label>
              <label class="switch"> 
              <input type="checkbox" class="settings_switcher_wpmsl" name="placeholder_settings[global]" <?php echo (isset($placeholder_setting['global']) &&  $placeholder_setting['global']=='yes') ? 'checked' : ''; ?>>
              <span class="slider round"></span>
            </label>
          </div>
        <div class="store_locator_map_settings" <?php echo (isset($placeholder_setting['global']) &&  $placeholder_setting['global']=='yes') ? 'style="display:none;"' : ''; ?>>
	        <table class="wpml-table"><tbody>
	        <tr><th>
	        	<?php esc_html_e('Get Location Text Button','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[get_location_btn_txt]" value="<?php echo isset($placeholder_setting['get_location_btn_txt']) ? $placeholder_setting['get_location_btn_txt'] : ''; ?>"/>
		    </td></tr>
			<tr><th>
	        	<?php esc_html_e('Get Direction Text ','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[getdirection_btn_txt]" value="<?php echo isset($placeholder_setting['getdirection_btn_txt']) ? $placeholder_setting['getdirection_btn_txt'] : ''; ?>"/>
		    </td></tr>
			<tr><th>
	        	<?php esc_html_e('Search Button Text','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[search_btn_txt]" value="<?php echo isset($placeholder_setting['search_btn_txt']) ? $placeholder_setting['search_btn_txt'] : ''; ?>"/>
		    </td></tr>
		     <tr><th>
	        <?php esc_html_e('Enter Location Text','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[enter_location_txt]" value="<?php echo !empty($placeholder_setting['enter_location_txt']) ? $placeholder_setting['enter_location_txt'] : ''; ?>" />
	        </td></tr>
	        <tr><th>
	        	<?php esc_html_e('Select Category','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[select_category_txt]" value="<?php echo !empty($placeholder_setting['select_category_txt']) ? esc_attr($placeholder_setting['select_category_txt']) : ''; ?>"/>
	        </td></tr>
	        <tr><th>
	        <?php esc_html_e('Select Tags','store_locator');?>
	        </th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[select_tags_txt]" value="<?php echo !empty($placeholder_setting['select_tags_txt']) ? esc_attr($placeholder_setting['select_tags_txt']) :''; ?>"/>
	        </td></tr>
	        <tr><th>
	        <?php esc_html_e('Search Options Button Text','store_locator');?>
	        	</th><td>
	        <input type="text" class="regular-text" name="placeholder_settings[search_options_btn]" value="<?php echo !empty($placeholder_setting['search_options_btn']) ? esc_attr($placeholder_setting['search_options_btn']) : ''; ?>"/>
	        </td></tr>
	         <tr><th>
	         	<?php esc_html_e('Location not found text','store_locator');?>
	         </th><td>	
	        <input type="text" class="regular-text" name="placeholder_settings[location_not_found]" value="<?php echo !empty($placeholder_setting['location_not_found']) ? esc_attr($placeholder_setting['location_not_found']) : ''; ?>"/>
	        </td></tr>
		    </tbody>
		</table>
		</div>
        <?php
		}
	}
	new WPMSL_Stores_Backend();
}