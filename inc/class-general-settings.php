<div class="store_locator_settings_div">
    <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="updated below-h2"><p><?php esc_html_e("Settings updated.", 'store_locator'); ?></p></div>
    <?php endif; ?>

</div>
<?php 
if(!class_exists('WP_Multi_Store_Locator_Settings')){
class WP_Multi_Store_Locator_Settings {
    public function __construct(){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $tabs = array(
        'basic-settings'=> esc_html__( 'Initialize', 'store_locator' ), 
        'map-settings'  => esc_html__( 'Map Settings', 'store_locator' ),
        'dynamic-text'  => esc_html__('Placeholder Settings','store_locator'),
        'grid-settings' => esc_html__('Grid Settings','store_locator'),
        'single-page-settings' => esc_html__('Single Page Settings','store_locator'),
        );
        $this->init_tabs(apply_filters('wpml_setting_tabs',$tabs));
        $this->current_tab(apply_filters('wpml_current_tab',$current));
    }
    public function init_tabs($tabs=array()){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="edit.php?post_type=store_locator&page=store_locator_settings_page&tab=' . esc_attr($tab).'">'.esc_html($name).'</a>';
        }
        $html .= '</h2>';
        echo $html;

    }
    public function current_tab($current='basic-settings'){
        switch ($current) {
        case 'basic-settings':
            $this->initialize_settings();
            break;
        case 'map-settings':
            $this->map_settings();
            break;
        case 'dynamic-text':
            $this->dynamic_text_settings();
            break;
        case 'grid-settings':
            $this->grid_settings();
            break; 
        case 'single-page-settings':
            $this->single_page_settings();
            break;    
        default:
            $this->initialize_settings();
            break;
        }
    }
    public function initialize_settings(){
        $_POST = array_map( 'stripslashes_deep', $_POST );
        if (isset($_POST['api-settings'])) {
            update_option('store_locator_API_KEY', $_POST['store_locator_API_KEY']);
          //  update_option('store_locator_street_API_KEY', $_POST['store_locator_street_API_KEY']);
            if(isset($_POST['map_landing_address']))
                update_option('map_landing_address', $_POST['map_landing_address']);
        }
        $store_locator_API_KEY  = get_option('store_locator_API_KEY');
        $map_landing_address  = get_option('map_landing_address');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Google Maps Api", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">
                        <table class="widefat" style="border: 0px;">
                        <tr>
                            <th><label  for="store_locator_API_KEY"><?php esc_html_e("Google Maps API KEY", 'store_locator'); ?>:</label></th>
                            <td><input value="<?php print_r($store_locator_API_KEY); ?>" type="text" id="store_locator_API_KEY" name="store_locator_API_KEY"  class="regular-text"></td>
                        </tr>
                        <?php if(!empty($store_locator_API_KEY)): ?>
                        <tbody>
                        <tr>
                            <td colspan="2"><h3><?php esc_html_e("Map Landing Address", 'store_locator'); ?></h3></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Address", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_address" class="regular-text" type="text" value="<?php echo isset($map_landing_address['address']) ? $map_landing_address['address'] : ''; ?>" name="map_landing_address[address] "/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Country", 'store_locator'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[country]" id="store_locator_country">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allCountries = $wpdb->get_results("SELECT * FROM store_locator_country");
                                   $selectedCountry =  isset($map_landing_address['country']) ? $map_landing_address['country'] : '';
                                    foreach ($allCountries as $country) {
                                        ?>
                                        <option value="<?php echo $country->name; ?>" <?php  echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
                            <td><?php esc_html_e("State", 'store_locator'); ?></td>
                            <td>
                                <select class="regular-text" name="map_landing_address[state]" id="store_locator_state">
                                    <option value="" ></option>
                                    <?php
                                    global $wpdb;
                                    $allStates = $wpdb->get_results("SELECT * FROM store_locator_state");
                                    $selectedState = isset($map_landing_address['state']) ? $map_landing_address['state'] : '';
                                    foreach ($allStates as $state) {
                                        ?>
                                        <option value="<?php echo $state->name; ?>" <?php echo ($selectedState == $state->name) ? "selected" : ""; ?>><?php echo $state->name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("City", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_city" type="text" class="regular-text" value="<?php echo isset($map_landing_address['city']) ? $map_landing_address['city'] : ''; ?>" name="map_landing_address[city]"/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e("Postal Code", 'store_locator'); ?></td>
                            <td>
                                <input id="store_locator_zipcode" class="regular-text" type="text" value="<?php echo isset($map_landing_address['zipcode']) ? $map_landing_address['zipcode'] : ''; ?>" name="map_landing_address[zipcode]"/>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="2">
                            <p><?php esc_html_e('Select default location for marker from bottom','store_locator'); ?></p>
                            <input type="hidden" value="<?php echo isset($map_landing_address['lat']) ? $map_landing_address['lat'] : ''; ?>" name="map_landing_address[lat]" id="store_locator_lat"/>
                            <input type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>" name="map_landing_address[lng]" id="store_locator_lng"/>
                            <div id="map-container" style="position: relative;">
                                <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;">
                                    <div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div>
                                </div>
                                <div id="map-canvas" style="height: 200px;width: 100%;"></div>
                            </div>
                            <script>
                                jQuery(document).ready(function (jQuery) {
                                      initializeMapBackend();
                                });
                            </script>
                            </td></tr></tbody>
                              <?php else: ?>
                              <tr><td colspan="2"><?php esc_html_e('To set map landing address please add API key first.','store_locator'); ?></td></tr>
                            <?php endif; ?>
                        
                            <tr><td colspan="2">
                                <input type="submit" class="button-primary" name="api-settings" value="<?php esc_html_e("Save Changes", 'store_locator'); ?>">
                            </td></tr>
                            </table>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
        <?php
    }
    public function map_settings(){
        if (isset($_POST['map-settings'])) {
            $_POST['store_locator_map']['custom_style']=isset($_POST['store_locator_map']['custom_style']) ? stripslashes($_POST['store_locator_map']['custom_style']) : '';
            update_option('store_locator_map', $_POST['store_locator_map']);
        }
        $map_options  = get_option('store_locator_map'); ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Map settings -->
                <form method="POST">
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Map Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_map_settings">
                            <table>
                                <tbody>
                            <tr>
                                <td><label title="Enable the display of map on the frontend" for="store_locator_map_enable"><?php esc_html_e("Show map on frontend", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[enable]" >
                                <input <?php echo ($map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_enable" name="store_locator_map[enable]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Width pixels" for="store_locator_map_width"><?php esc_html_e('Map Width', 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['width']; ?>" type="text" id="store_locator_map_width" name="store_locator_map[width]" size="4">
                                <select name="store_locator_map[widthunit]" id="store_locator_map_widthunit" >
                                    <option <?php  ($map_options['widthunit'] == 'px') ?"selected": ""; ?> selected value="px">PX</option>
                                    <option <?php  echo ($map_options['widthunit'] == '%') ?"selected": ""; ?>  value="%">%</option>
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php esc_html_e("Map Height ", 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['height']; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
                                    <option <?php  echo ($map_options['heightunit'] == 'px') ?"selected": ""; ?>  value="px">PX</option>           
                                    <?php /* <option <?php  ($map_options['heightunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */?>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Select Map Type','store_locator'); ?>" for="store_locator_map_type"><?php esc_html_e("Map Type", 'store_locator'); ?>:</label></td>
                                <td><select name="store_locator_map[type]" id="store_locator_map_type">
                                    <option <?php echo ($map_options['type'] == 'roadmap') ?"selected": ""; ?> value="roadmap"><?php esc_html_e('Roadmap','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'hybrid') ?"selected": ""; ?> value="hybrid"><?php esc_html_e('Hybrid','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'satellite') ?"selected": ""; ?> value="satellite"><?php esc_html_e('Satellite','store_locator');?></option>
                                    <option <?php echo ($map_options['type'] == 'terrain') ?"selected": ""; ?> value="terrain"><?php esc_html_e('Terrain','store_locator');?></option>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Choose the unit of search km/mile','store_locator'); ?>" for="store_locator_map_unit"><?php esc_html_e("Search Unit", 'store_locator'); ?>:</label></td>
                                <td><select name="store_locator_map[unit]" id="store_locator_map_unit">
                                    <option <?php echo ($map_options['unit'] == 'km') ? "selected": ""; ?> value="km">Km</option>
                                    <option <?php echo ($map_options['unit'] == 'mile') ? "selected": ""; ?> value="mile">Mile</option>
                                </select></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Choose search options here. the default one will be between square brakets','store_locator'); ?>" for="store_locator_map_radius"><?php esc_html_e("Search radius options", 'store_locator'); ?>:</label></td>
                                <td><input value="<?php echo $map_options['radius']; ?>" type="text" id="store_locator_map_radius" name="store_locator_map[radius]" >
                                <div class="store_locator_tip">e.g: 5,10,[25],50,100,200,500</div></td>
                            </tr>
                            <tr>
                                <td><label title="Show street control on the map in frontend" for="store_locator_map_streetViewControl"><?php esc_html_e("Show street view control", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[streetViewControl]" >
                                <input <?php echo ($map_options['streetViewControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_streetViewControl" name="store_locator_map[streetViewControl]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Scroll Map to screen after search" for="store_locator_map_scroll_to_top"><?php esc_html_e("Scroll to map top after search", 'store_locator'); ?>?</label></td>
                                <td><input <?php echo (isset($map_options['mapscrollsearch']) && ($map_options['mapscrollsearch'])==1)?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll_to_top" name="store_locator_map[mapscrollsearch]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable the user to change the map type from the frontend" for="store_locator_map_mapTypeControl"><?php esc_html_e("Show map type control", 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[mapTypeControl]" >
                                <input <?php echo ($map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_mapTypeControl" name="store_locator_map[mapTypeControl]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Enable/Disable zoom by scroll on map" for="store_locator_map_scroll"><?php esc_html_e('Zoom by scroll on map', 'store_locator'); ?>?</label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[scroll]" >
                                <input <?php echo ($map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[scroll]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Display default Map Search" for="store_locator_default_search"><?php esc_html_e('Show Map Search options', 'store_locator'); ?></label></td>
                                <td><input value="0" type="hidden" name="store_locator_map[default_search]" >
                                <input <?php echo ($map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_default_search" name="store_locator_map[default_search]" ></td>
                            </tr>
                            <tr>
                                <td><label title="Hide Field Options" for="store_locator_default_search"><?php esc_html_e('Hide Fields for Search', 'store_locator'); ?></label></td>
                                <td><ul class="hide_fields">
                                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_get_my_location]" ><?php esc_html_e('Get My Location','store_locator');?></li>
                                    <li>
            <input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_location]" ><?php esc_html_e('Location Field','store_locator');?>
                                    </li>
                                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_radius]" ><?php esc_html_e('Radius Field','wpmsl');?></li>
                                    <li><input <?php echo (isset($map_options['category']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[category]" ><?php esc_html_e('Category Field','store_locator');?></li>
                                    <li><input <?php echo (isset($map_options['tag']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[tag]" ><?php esc_html_e('Tags Field','store_locator');?></li>
                                </ul></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Map Search Open as Default','store_locator'); ?>" for="map_window_open"><?php esc_html_e("Map Search Open as Default", 'store_locator'); ?></label></td>
                                <td><input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="store_locator_map[map_window_open]" ></td>
                            </tr>
                            <tr>
                                <td><label title="<?php esc_html_e('Switch To RTL','store_locator'); ?>" for="rtl_enabled"><?php esc_html_e("Switch To RTL", 'store_locator'); ?></label></td>
                                <td><input <?php echo (isset($map_options['rtl_enabled']))?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="store_locator_map[rtl_enabled]" ></td>
                            </tr>
                            <tr><td colspan="2"><b><?php esc_html_e('Map Styles','store_locator');?></b></td></tr>
                            <tr><td colspan="2">
                            <div class="map_Styles_div">
                            <p>
                                <label title="<?php esc_html_e('Standard Map','store_locator'); ?>" for="store_locator_map_style1">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/staticmap.png'); ?>" />
                                    <?php esc_html_e("Standard Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 1) ? 'checked':''; ?> value="1" type="radio" id="store_locator_map_style1" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Silver Map','store_locator'); ?>" for="store_locator_map_style2">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/silver.png'); ?>" />
                                    <?php esc_html_e("Silver Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 2)?'checked':''; ?> value="2" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Retro Map','store_locator'); ?>" for="store_locator_map_style3">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/retro.png'); ?>" />
                                    <?php esc_html_e("Retro Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 3)?'checked':''; ?> value="3" type="radio" id="store_locator_map_style3" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Dark Map','store_locator'); ?>" for="store_locator_map_style4">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/dark.png'); ?>" />
                                    <?php esc_html_e("Dark Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 4)?'checked':''; ?> value="4" type="radio" id="store_locator_map_style4" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Night Map','store_locator'); ?>" for="store_locator_map_style5">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/night.png'); ?>" />
                                    <?php esc_html_e("Night Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 5)?'checked':''; ?> value="5" type="radio" id="store_locator_map_style5" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Aubergine Map','store_locator'); ?>" for="store_locator_map_style6">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/aubergine.png'); ?>" />
                                    <?php esc_html_e("Aubergine Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 6)?'checked':''; ?> value="6" type="radio" id="store_locator_map_style6" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                            <p>
                                <label title="<?php esc_html_e('Basic Map','store_locator'); ?>" for="store_locator_map_style7">
                                    <img src="<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/basic.png'); ?>" />
                                    <?php esc_html_e("Basic Map", 'store_locator'); ?>
                                    <input <?php echo (isset($map_options['map_style']) && $map_options['map_style'] == 7)?'checked':''; ?> value="7" type="radio" id="store_locator_map_style7" name="store_locator_map[map_style]" >
                                </label>
                            </p>
                        </div></td>
                        </tr>
                            <tr>
                            <td>
                                <div style="clear: both;"></div>
                                <label title="Choose the color of user marker" for="store_locator_map_type"><?php esc_html_e("User Marker", 'store_locator'); ?>:</label>
                            </td>
                            <td><ul class="default_markers">
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'); ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker1]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                              <td><?php esc_html_e('or add custom marker url','store_locator');?></td>
                              <?php 
                                    if(isset($map_options['marker1_custom']) && !empty($map_options['marker1_custom'])){
                                        $marker1=$map_options['marker1_custom'];
                                        $class='wpmsl_custom_marker';
                                        $uploadRemove=__('Remove','store_locator');
                                    }
                                    else{
                                        $marker1=STORE_LOCATOR_PLUGIN_URL . 'assets/img/upload.png';
                                        $class='wpmsl_custom_marker_upload'; 
                                        $uploadRemove =__('Upload','store_locator');
                                    }

                                ?>
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="store_locator_map[marker1_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div></td>
                            </tr>
                            <tr>
                            <td>
                                <label title="Choose the color of store marker" for="store_locator_map_type"><?php esc_html_e("Store Marker", 'store_locator'); ?>:</label>
                            </td>
                            <td>
                            <ul class="default_markers">
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/blue.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'blue.png')? 'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/red.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/green.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/orange.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/purple.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo esc_url(STORE_LOCATOR_PLUGIN_URL.'assets/img/yellow.png'); ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker2]" />
                                </li>
                            </ul></td>
                            </tr>
                            <tr>
                               <td><?php esc_html_e('or add custom marker url','store_locator');?></td>
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
                               <td><div class="<?php echo $class; ?>">
                                <img src="<?php echo $marker2; ?>" width="50px" height="50px">
                                  <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker2 : ''; ?>" name="store_locator_map[marker2_custom]" />
                                  <p><?php echo $uploadRemove; ?></p>
                              </div>
                              </td>
                            </tr>
                            <?php echo do_action('wpmsl_private_marker_settings'); ?>
                            <tr>
                                <td colspan="2"><label title="<?php esc_html_e('You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content','store_locator'); ?>" for="store_locator_map_infowindow"><b><?php esc_html_e("Info Window Content", 'store_locator'); ?></b>: </label><p class="store_locator_tip">placeholders: {image} {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</p>
                                <textarea name="store_locator_map[infowindow]" rows="10" cols="70" id="store_locator_map_infowindow" class="widefat"><?php echo $map_options['infowindow']; ?></textarea></td>
                            </tr>
                             <tr>
                                <td colspan="2"><label title="<?php esc_html_e('You can customize the look of the map by adding styles here','store_locator'); ?>" for="store_locator_map_style"><b><?php esc_html_e("Customised Map Style", 'store_locator'); ?></b>: <p class="store_locator_tip"><?php esc_html_e('You can get some styles from','store_locator');?><a target="_blanck" href="https://snazzymaps.com"> <?php _e('Snazzy Maps','store_locator');?></a></p> </label>
                                <textarea name="store_locator_map[custom_style]"  rows="10" cols="70" id="store_locator_map_style" class="widefat"><?php echo stripslashes($map_options['custom_style']); ?></textarea></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="map-settings" value="<?php esc_html_e("Save Changes", 'store_locator'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
    public function dynamic_text_settings(){
         //handle save single page settings
        if (isset($_POST['placeholder-setting'])) {
            $placeholders = array();
            $placeholders['get_location_btn_txt'] = $_POST['get_location_btn_txt'];
            $placeholders['search_btn_txt'] = $_POST['search_btn_txt'];
            $placeholders['getdirection_btn_txt'] = $_POST['getdirection_btn_txt'];
            $placeholders['enter_location_txt'] = $_POST['enter_location_txt'];
            $placeholders['select_category_txt'] = $_POST['select_category_txt'];
            $placeholders['select_tags_txt'] = $_POST['select_tags_txt'];
            $placeholders['search_options_btn']=$_POST['search_options_btn'];
            $placeholders['location_not_found'] = $_POST['location_not_found'];
            $placeholders['store_list'] = $_POST['store_list'];
            $placeholders['visit_website'] = $_POST['visit_website'];
            update_option('placeholder_settings',$placeholders);
        }
        $placeholder_setting = get_option('placeholder_settings');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
        <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Placeholder Text", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                        <table>
                            <tbody>
                            <tr><th><?php esc_html_e('Get Location Text Button','store_locator');?></th>
                            <td><input type="text" name="get_location_btn_txt" value="<?php echo !empty($placeholder_setting['get_location_btn_txt']) ? esc_attr($placeholder_setting['get_location_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr> 
							<tr><th><?php esc_html_e('Search Button Text','store_locator');?></th>
                            <td><input type="text" name="search_btn_txt" value="<?php echo !empty($placeholder_setting['search_btn_txt']) ? esc_attr($placeholder_setting['search_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr> 
							<tr><th><?php esc_html_e('Get Direction Text','store_locator');?></th>
                            <td><input type="text" name="getdirection_btn_txt" value="<?php echo !empty($placeholder_setting['getdirection_btn_txt']) ? esc_attr($placeholder_setting['getdirection_btn_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Enter Location Text','store_locator');?></th>
                            <td><input type="text" name="enter_location_txt" value="<?php echo !empty($placeholder_setting['enter_location_txt']) ? esc_attr($placeholder_setting['enter_location_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Select Category','store_locator');?></th>
                            <td><input type="text" name="select_category_txt" value="<?php echo !empty($placeholder_setting['select_category_txt']) ? esc_attr($placeholder_setting['select_category_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Select Tags','store_locator');?></th>
                            <td><input type="text" name="select_tags_txt" value="<?php echo !empty($placeholder_setting['select_tags_txt']) ? esc_attr($placeholder_setting['select_tags_txt']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Search Options Button Text','store_locator');?></th>
                            <td><input type="text" name="search_options_btn" value="<?php echo !empty($placeholder_setting['search_options_btn']) ? esc_attr($placeholder_setting['search_options_btn']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                             <tr><th><?php esc_html_e('Location not found text','store_locator');?></th>
                            <td><input type="text" name="location_not_found" value="<?php echo !empty($placeholder_setting['location_not_found']) ? esc_attr($placeholder_setting['location_not_found']) : ''; ?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Store list text','store_locator');?></th>
                            <td><input type="text" name="store_list" value="<?php echo !empty($placeholder_setting['store_list']) ? esc_attr($placeholder_setting['store_list']) : '' ;?>" class="regular-text" /></td>
                            </tr>
                            <tr><th><?php esc_html_e('Visit Website text','store_locator');?></th>
                            <td><input type="text" name="visit_website" value="<?php echo !empty($placeholder_setting['visit_website']) ? esc_attr($placeholder_setting['visit_website']) : '' ;?>" class="regular-text" /></td>
                            </tr>
                            <tr class="submit">
                                <td colspan="2"><input type="submit" class="button-primary" name="placeholder-setting" value="<?php esc_html_e("Save Changes", 'store_locator'); ?>"></td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </form>
              </div>
        </div>
    </div>
        <?php
    }
    public function grid_settings(){
        //handle save grid settings
        if (isset($_POST['grid-settings'])) {
            //$_POST['store_locator_grid']['columns'] = explode(",", $_POST['store_locator_grid']['columns']);
            update_option('store_locator_grid', $_POST['store_locator_grid']);
        }
        $grid_options = get_option('store_locator_grid');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Grid settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle">
                            <span><?php esc_html_e("Grid Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php esc_html_e("Show the results in grid in the frontend","store_locator"); ?>" for="store_locator_grid_enable"><?php esc_html_e("Show grid on frontend", 'store_locator'); ?>?</label>
                            </th><td>
                                <input value="0" type="hidden" name="store_locator_grid[enable]" >
                                <input <?php echo ($grid_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_enable" name="store_locator_grid[enable]" >
                           </td> </tr>

                            <tr><th>
                                <label title="<?php esc_html_e("Maximum number of markers to be displayed","store_locator") ?>" for="store_locator_grid_number"><?php esc_html_e("Maximum number of markers to be displayed", 'store_locator'); ?>:</label>
                                </th><td>
                                <input value="<?php echo isset($grid_options['total_markers']) ? trim($grid_options['total_markers']) : '-1'; ?>" type="text" id="store_locator_grid_number" name="store_locator_grid[total_markers]" >
                            </td> </tr>
                            <?php /*
                            <tr><th>
                                <label title="<?php esc_html_e("Enable/Disable autoload results when scroll down","store_locator") ?>" for="store_locator_grid_scroll"><?php esc_html_e("Autoload results on scroll", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_grid[scroll]" >
                                <input <?php echo ($grid_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_scroll" name="store_locator_grid[scroll]" >
                            </td> </tr>
                           
                            <!-- <tr><th>
                                <label title="<?php esc_html_e("Select the displayed column in the grid in the frontend by order","store_locator") ?>" for="store_locator_grid_columns"><?php esc_html_e("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php esc_html_e("Select columns with order to be displayed on frontend", 'store_locator'); ?></span></label>
                                </th><td>
                                <select  id="store_locator_grid_columns" multiple="multiple" class="regular-text">
                                    <?php
                                    if(isset($grid_options['columns']) && $grid_options['columns']){
                                        $selectedColumns  = $grid_options['columns'];
                                    }else{
                                        $selectedColumns  = array();
                                    }
                                    ?>
                                    <?php
                                    $columns = array("name", "address", "city", "state", "country", "zipcode", "website", "full_address", "managers", "phone", "working_hours", "fax");
                                    if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
                                        $columns[] = 'gravity_form';
                                    }
                                    $columns = array_diff($columns, $selectedColumns);
                                    $columns = array_merge($selectedColumns, $columns);
                                    ?>
                                    <?php foreach ($columns as $column): ?>
                                        <?php if ($column): ?>
                                            <option value="<?php echo $column; ?>" <?php echo (in_array($column, $selectedColumns)) ? "selected" : ""; ?>><?php echo $column; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <input name="store_locator_grid[columns]" type="hidden" value="<?php echo implode(",", $selectedColumns); ?>">
                            </td> </tr> --> */ ?>
                            <tr><th>
                                <label title="Map Result Show on" for="store_locator_map_type"><?php esc_html_e("Map Result Show on", 'store_locator'); ?>:</label>
                                </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Left Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Right Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'right')?'checked':''; ?> type="radio" value="right" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('Below Map','store_locator');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'below_map')?'checked':''; ?> type="radio" value="below_map" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                </ul>
                                </td> </tr>
                            
                            <tr><th>
                                <label title="Map Search Options Window Show on" for="store_locator_map_type"><?php esc_html_e("Map Search Options Window Show on", 'store_locator'); ?>:</label>
                            </th><td>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Left Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('On Map Right Side','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_search_right')?'checked':''; ?> type="radio" value="wpml_search_right" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php esc_html_e('Above Map','store_locator');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_above_map')?'checked':''; ?> type="radio" value="wpml_above_map" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                </ul>
                           </td> </tr>
                           <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="grid-settings" value="<?php esc_html_e('Save Changes', 'store_locator'); ?>">
                            </p>
                            </td> </tr>
                        </table>
                        </div>
                    </div>
                </form>
             </div>
        </div>
    </div>
    <?php
    }
    public function single_page_settings(){
        //handle save single page settings
        if (isset($_POST['single-settings'])) {
            $_POST['store_locator_single']['items'] = explode(",", $_POST['store_locator_single']['items']);
            update_option('store_locator_single', $_POST['store_locator_single']);
        }
        $single_options = get_option('store_locator_single');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php esc_html_e("Single Page Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">

                            <table style="text-align: left;">
                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable when click on store goto single page for more details','store_locator') ?>" for="store_locator_single_page"><?php esc_html_e("Link store to a single page", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[page]" >
                                <input <?php echo (isset($single_options['page']) && $single_options['page'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_page" name="store_locator_single[page]" >
                             </td></tr>
                            
                            <tr><th>
                                <label title="<?php esc_html_e('Enter Unique Slug Name','store_locator') ?>" for="store_locator_slug"><?php esc_html_e("Enter Unique Slug Name", 'store_locator'); ?></label>   </th><td>                             
                                <input <?php echo (isset($single_options['store_locator_slug']) && $single_options['store_locator_slug'] != '')? $single_options['store_locator_slug']:''; ?> placeholder="store-locator" value="<?php echo (isset($single_options['store_locator_slug']) && !empty($single_options['store_locator_slug']) ? $single_options['store_locator_slug'] : '')?>" type="text" id="store_locator_slug" name="store_locator_single[store_locator_slug]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable the display of feature image of the store in the inner page','store_locator') ?>" for="store_locator_single_image"><?php esc_html_e("Show feature image of the store?", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[image]" >
                                <input <?php echo (isset($single_options['image']) && $single_options['image'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_image" name="store_locator_single[image]" >
                            </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Enable/Disable showing map in the inner page of the store','store_locator') ?>" for="store_locator_single_map"><?php esc_html_e("Show map on page?", 'store_locator'); ?>?</label>
                                </th><td>
                                <input value="0" type="hidden" name="store_locator_single[map]" >
                                <input <?php echo (isset($single_options['map']) && $single_options['map'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_map" name="store_locator_single[map]" >
                           </td></tr>

                            <tr><th>
                                <label title="<?php esc_html_e('Select the displayed column in the page in the frontend by order','store_locator') ?>" for="store_locator_single_items"><?php esc_html_e("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php esc_html_e("Select details you want to display on the page", 'store_locator'); ?></span></label>
                                </th><td>
                                <select  id="store_locator_single_items" multiple="multiple" class="regular-text">
                                    <?php
                                    if(isset($single_options['items']) && $single_options['items']){
                                        $selectedItems  = $single_options['items'];
                                    }else{
                                        $selectedItems  = array();
                                    }
                                    ?>
                                    <?php
                                    $items = array("name", "website", "full_address", "managers", "phone", "working_hours", "fax", "description");
                                    $items = array_diff($items, $selectedItems);
                                    $items = array_merge($selectedItems, $items);
                                    ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php if ($item): ?>
                                            <option value="<?php echo $item; ?>" <?php echo (in_array($item, $selectedItems)) ? "selected" : ""; ?>><?php echo $item; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>

                                <input name="store_locator_single[items]" type="hidden" value="<?php echo implode(",", $selectedItems); ?>">
                            </td></tr>
                            <tr><td colspan="2">
                            <p class="submit">
                                <input type="submit" class="button-primary" name="single-settings" value="<?php esc_html_e("Save Changes", 'store_locator'); ?>">
                            </p>
                            </td></tr>
                        </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
}
    new WP_Multi_Store_Locator_Settings();
}