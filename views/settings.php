<link rel="stylesheet" href="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/css/jquery-ui.css'; ?>">
<div class="store_locator_settings_div">
    <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="updated below-h2"><p><?php echo __("Settings updated.", 'store_locator'); ?></p></div>
    <?php endif; ?>

</div>
<?php 
if(!class_exists('WP_Multi_Store_Locator_Settings')){
class WP_Multi_Store_Locator_Settings
{
    public function __construct(){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $tabs = array(
        'basic-settings'   => __( 'Initialize', 'wpmsl' ), 
        'map-settings'  => __( 'Map Settings', 'wpmsl' ),
        'dynamic-text'  => __('Placeholder Settings','wpmsl'),
        'grid-settings' => __('Grid Settings','wpmsl'),
        'single-page-settings' => __('Single Page Settings','wpmsl'),
        );
        $this->init_tabs(apply_filters('wpml_setting_tabs',$tabs));
        $this->current_tab(apply_filters('wpml_current_tab',$current));
    }
    public function init_tabs($tabs=array()){
        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'basic-settings';
        $html = '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="edit.php?post_type=store_locator&page=store_locator_settings_page&tab=' . $tab . '">' . $name . '</a>';
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
        <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Google Maps Api", 'wpmsl'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">


                            <p>
                                <label  for="store_locator_API_KEY"><?php echo __("API KEY", 'wpmsl'); ?>:</label>
                                <input value="<?php print_r($store_locator_API_KEY); ?>" type="text" id="store_locator_API_KEY" name="store_locator_API_KEY" >
                            </p>
                           
                            <div class="default_Address_landing">
                                <h3><?php echo __("Map Landing Address", 'wpmsl'); ?></h3>
                                 <?php if(!empty($store_locator_API_KEY)): ?>
                            <table class="widefat" style="border: 0px;">
                        <tbody>
                        <tr>
                            <td><?php echo __("Address", 'wpmsl'); ?></td>
                            <td>
                                <input id="store_locator_address" class="regular-text" type="text" value="<?php echo isset($map_landing_address['address']) ? $map_landing_address['address'] : ''; ?>" name="map_landing_address[address] "/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __("Country", 'wpmsl'); ?></td>
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
                            <td><?php echo __("State", 'store_locator'); ?></td>
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
                            <td><?php echo __("City", 'wpmsl'); ?></td>
                            <td>
                                <input id="store_locator_city" type="text" class="regular-text" value="<?php echo isset($map_landing_address['city']) ? $map_landing_address['city'] : ''; ?>" name="map_landing_address[city]"/>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo __("Postal Code", 'wpmsl'); ?></td>
                            <td>
                                <input id="store_locator_zipcode" class="regular-text" type="text" value="<?php echo isset($map_landing_address['zipcode']) ? $map_landing_address['zipcode'] : ''; ?>" name="map_landing_address[zipcode]"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <input type="hidden" value="<?php echo isset($map_landing_address['lat']) ? $map_landing_address['lat'] : ''; ?>" name="map_landing_address[lat]" id="store_locator_lat"/>
                    <input type="hidden" value="<?php echo isset($map_landing_address['lng']) ? $map_landing_address['lng'] : ''; ?>" name="map_landing_address[lng]" id="store_locator_lng"/>
                                <div id="map-container" style="position: relative;">

                                <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
                                <div id="map-canvas" style="height: 200px;width: 100%;"></div>
                            </div>

                            <script>
                                jQuery(document).ready(function (jQuery) {
                                    store_locator_initializeMapBackend();
                                });
                            </script>
                              <?php else: ?>
                                <?php _e('To set map landing address please add API key first.'); ?>
                            <?php endif; ?>
                            </div>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="api-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
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
             
            update_option('store_locator_map', $_POST['store_locator_map']);
        }
        $map_options  = get_option('store_locator_map');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
         <!-- Map settings -->
                <form method="POST">
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Map Settings", 'wpmsl'); ?></span></h3>
                        <div class="inside store_locator_map_settings">
                            <p>
                                <label title="Enable the display of map on the frontend" for="store_locator_map_enable"><?php echo __("Show map on frontend", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[enable]" >
                                <input <?php echo ($map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_enable" name="store_locator_map[enable]" >
                            </p>

                            <p>
                                <label title="Select Map Width pixels" for="store_locator_map_width"><?php echo __('Map Width', 'wpmsl'); ?>:</label>
                                <input value="<?php echo $map_options['width']; ?>" type="text" id="store_locator_map_width" name="store_locator_map[width]" size="4">
                                <select name="store_locator_map[widthunit]" id="store_locator_map_widthunit" >
                                    <option <?php  ($map_options['widthunit'] == 'px') ?"selected=": ""; ?> selected value="px">PX</option>
                                    <option <?php  echo ($map_options['widthunit'] == '%') ?"selected=": ""; ?>  value="%">%</option>
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select>
                            </p>

                            <p>
                                <label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php echo __("Map Height ", 'wpmsl'); ?>:</label>
                                <input value="<?php echo $map_options['height']; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
                                    <option <?php  echo ($map_options['heightunit'] == 'px') ?"selected=": ""; ?>  value="px">PX</option>           
                                    <?php /* <option <?php  ($map_options['heightunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */?>
                                </select>
                            </p>

                            <p>
                                <label title="Select Map Type" for="store_locator_map_type"><?php echo __("Map Type", 'wpmsl'); ?>:</label>
                                <select name="store_locator_map[type]" id="store_locator_map_type">
                                    <option <?php echo ($map_options['type'] == 'roadmap') ?"selected=": ""; ?> value="roadmap"><?php _e('Roadmap','wpmsl');?></option>
                                    <option <?php echo ($map_options['type'] == 'hybrid') ?"selected=": ""; ?> value="hybrid"><?php _e('Hybrid','wpmsl');?></option>
                                    <option <?php echo ($map_options['type'] == 'satellite') ?"selected=": ""; ?> value="satellite"><?php _e('Satellite','wpmsl');?></option>
                                    <option <?php echo ($map_options['type'] == 'terrain') ?"selected=": ""; ?> value="terrain"><?php _e('Terrain','wpmsl');?></option>
                                </select>
                            </p>

                            <p>
                                <label title="Choose the unit of search km/mile" for="store_locator_map_unit"><?php echo __("Search Unit", 'wpmsl'); ?>:</label>
                                <select name="store_locator_map[unit]" id="store_locator_map_unit">
                                    <option <?php echo ($map_options['unit'] == 'km') ?"selected=": ""; ?> value="km">Km</option>
                                    <option <?php echo ($map_options['unit'] == 'mile') ?"selected=": ""; ?> value="mile">Mile</option>
                                </select>
                            </p>

                            <p>
                                <label title="Choose search options here. the default one will be between square brakets" for="store_locator_map_radius"><?php echo __("Search radius options", 'wpmsl'); ?>:</label>
                                <input value="<?php echo $map_options['radius']; ?>" type="text" id="store_locator_map_radius" name="store_locator_map[radius]" >
                                <span class="store_locator_tip">e.g: 5,10,[25],50,100,200,500</span>
                            </p>
                           
                            <p>
                                <label title="Show street control on the map in frontend" for="store_locator_map_streetViewControl"><?php echo __("Show street view control", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[streetViewControl]" >
                                <input <?php echo ($map_options['streetViewControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_streetViewControl" name="store_locator_map[streetViewControl]" >
                            </p>

                            <p>
                                <label title="Enable the user to change the map type from the frontend" for="store_locator_map_mapTypeControl"><?php echo __("Show map type control", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[mapTypeControl]" >
                                <input <?php echo ($map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_mapTypeControl" name="store_locator_map[mapTypeControl]" >
                            </p>

                            <p>
                                <label title="Enable/Disable zoom by scroll on map" for="store_locator_map_scroll"><?php echo __('Zoom by scroll on map', 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[scroll]" >
                                <input <?php echo ($map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[scroll]" >
                            </p>

                            <p>
                                <label title="Display default Map Search" for="store_locator_default_search"><?php echo __('Default Map Search', 'wpmsl'); ?></label>
                                <input value="0" type="hidden" name="store_locator_map[default_search]" >
                                <input <?php echo ($map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_default_search" name="store_locator_map[default_search]" >
                            </p>

                            <p>
                                <label title="Hide Field Options" for="store_locator_default_search"><?php echo __('Hide Fields for Search', 'wpmsl'); ?></label>
                                <ul class="hide_fields" style="margin-left: 36%">
                                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_get_my_location]" ><?php _e('Get My Location','wpmsl');?></li>
                                    <li>
            <input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_location]" ><?php _e('Location Field','wpmsl');?>
                                    </li>
                                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_radius]" ><?php _e('Radius Field','wpmsl');?></li>
                                    <li><input <?php echo (isset($map_options['category']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[category]" ><?php _e('Category Field','wpmsl');?></li>
                                    <li><input <?php echo (isset($map_options['tag']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[tag]" ><?php _e('Tags Field','wpmsl');?></li>
                                </ul>
                            </p>

                            <p>
                                <label title="Map Search Open as Default" for="map_window_open"><?php echo __("Map Search Open as Default", 'wpmsl'); ?></label>
                                <input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="store_locator_map[map_window_open]" >
                            </p>
                            
                            <p>
                                <label title="Switch To RTL" for="rtl_enabled"><?php echo __("Switch To RTL", 'wpmsl'); ?></label>
                                <input <?php echo (isset($map_options['rtl_enabled']))?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="store_locator_map[rtl_enabled]" >
                            </p>
                            
                            <p><b><?php _e('Map Styles','wpmsl');?></b></p>
                            <div class="map_Styles_div">
                            
                            <p>
                                <label title="Standard Map" for="store_locator_map_style1"><?php echo __("Standard Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 1)?'checked':''; ?> value="1" type="radio" id="store_locator_map_style1" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/staticmap.png'; ?>" />
                            </p>


                            <p>
                                <label title="Silver Map" for="store_locator_map_style2"><?php echo __("Silver Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 2)?'checked':''; ?> value="2" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/silver.png'; ?>" />
                            </p>

                            <p>
                                <label title="Retro Map" for="store_locator_map_style2"><?php echo __("Retro Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 3)?'checked':''; ?> value="3" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/retro.png'; ?>" />
                            </p>

                            <p>
                                <label title="Dark Map" for="store_locator_map_style2"><?php echo __("Dark Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 4)?'checked':''; ?> value="4" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/dark.png'; ?>" />
                            </p>

                            <p>
                                <label title="Night Map" for="store_locator_map_style2"><?php echo __("Night Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 5)?'checked':''; ?> value="5" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/night.png'; ?>" />
                            </p>

                            <p>
                                <label title="Aubergine Map" for="store_locator_map_style2"><?php echo __("Aubergine Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 6)?'checked':''; ?> value="6" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/aubergine.png'; ?>" />
                            </p>

                            <p>
                                <label title="Basic Map" for="store_locator_map_style2"><?php echo __("Basic Map", 'wpmsl'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 7)?'checked':''; ?> value="7" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/basic.png'; ?>" />
                            </p>

                        </div>
                        <div style="clear: both;"></div>
                          

                            <p>
                                <label title="Choose the color of user marker" for="store_locator_map_type"><?php echo __("User Marker", 'wpmsl'); ?>:</label>
                            </p>
                            <ul>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker1]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'; ?>" />
                                    <input <?php echo ($map_options['marker1'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker1]" />
                                </li>

                            </ul>
                            <p>
                              <?php _e('or add custom marker url','wpmsl');?><input type="text" placeholder="http://markerimage.png" value="<?php echo $map_options['marker1_custom'];?>" name="store_locator_map[marker1_custom]" />
                            </p>

                            <p>
                                <label title="Choose the color of store marker" for="store_locator_map_type"><?php echo __("Store Marker", 'wpmsl'); ?>:</label>
                            </p>
                            <ul>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/blue.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/red.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/green.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/orange.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/purple.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="store_locator_map[marker2]" />
                                </li>
                                <li>
                                    <img src= "<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/yellow.png'; ?>" />
                                    <input <?php echo ($map_options['marker2'] == 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="store_locator_map[marker2]" />
                                </li>
                            </ul>
                            <p>
                               <?php _e('or add custom marker url','wpmsl');?><input type="text" placeholder="http://markerimage.png" value="<?php echo $map_options['marker2_custom'];?>" name="store_locator_map[marker2_custom]" />
                            </p>
                            <?php
                            echo do_action('wpmsl_private_marker_settings');
                            ?>

                            <p>
                                <label title="You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content" for="store_locator_map_infowindow"><?php echo __("Info Window Content", 'wpmsl'); ?>: <span class="store_locator_tip">placeholders: {image} {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</span></label>
                                <textarea name="store_locator_map[infowindow]" id="store_locator_map_infowindow"><?php echo $map_options['infowindow']; ?></textarea>
                            </p>

                            <p>
                                <label title="You can customize the look of the map by adding styles here" for="store_locator_map_style"><?php echo __("Customised Map Style", 'wpmsl'); ?>: <span class="store_locator_tip"><?php _e('You can get some styles from','wpmsl');?><a target="_blanck" href="https://snazzymaps.com"> <?php _e('Snazzy Maps','wpmsl');?></a></span> </label>
                                <textarea name="store_locator_map[custom_style]" id="store_locator_map_style"><?php echo $map_options['custom_style']; ?></textarea>
                            </p>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="map-settings" value="<?php echo __("Save Changes", 'wpmsl'); ?>">
                            </p>
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
            $placeholders['enter_location_txt'] = $_POST['enter_location_txt'];
            $placeholders['select_category_txt'] = $_POST['select_category_txt'];
            $placeholders['select_tags_txt'] = $_POST['select_tags_txt'];
            $placeholders['search_options_btn']=$_POST['search_options_btn'];
            update_option('placeholder_settings',$placeholders);
        }
        $placeholder_setting = get_option('placeholder_settings');
        ?>
        <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">
        <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Placeholder Text", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                            <p><?php _e('Get Location Text Button','wpmsl');?></p>
                            <input type="text" name="get_location_btn_txt" value="<?php echo $placeholder_setting['get_location_btn_txt']?>"/>

                            <p><?php _e('Enter Location Text','wpmsl');?></p>
                            <input type="text" name="enter_location_txt" value="<?php echo $placeholder_setting['enter_location_txt']?>" />

                            <p><?php _e('Select Category','wpmsl');?></p>
                            <input type="text" name="select_category_txt" value="<?php echo $placeholder_setting['select_category_txt']?>"/>

                            <p><?php _e('Select Tags','wpmsl');?></p>
                            <input type="text" name="select_tags_txt" value="<?php echo $placeholder_setting['select_tags_txt']?>"/>
                            <p><?php _e('Search Options Button Text','wpmsl');?></p>
                            <input type="text" name="search_options_btn" value="<?php echo $placeholder_setting['search_options_btn']?>"/>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="placeholder-setting" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
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
            $_POST['store_locator_grid']['columns'] = explode(",", $_POST['store_locator_grid']['columns']);
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
                            <span><?php echo __("Grid Settings", 'wpmsl'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">

                            <p>
                                <label title="Show the results in grid in the frontend" for="store_locator_grid_enable"><?php echo __("Show grid on frontend", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_grid[enable]" >
                                <input <?php echo ($grid_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_enable" name="store_locator_grid[enable]" >
                            </p>

                            <p>
                                <label title="Select number of stores to be displayed per page" for="store_locator_grid_number"><?php echo __("Number of stores/page", 'wpmsl'); ?>:</label>
                                <input value="<?php echo $grid_options['number']; ?>" type="text" id="store_locator_grid_number" name="store_locator_grid[number]" >
                            </p>

                            <p>
                                <label title="Enable/Disable autoload results when scroll down" for="store_locator_grid_scroll"><?php echo __("Autoload results on scroll", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_grid[scroll]" >
                                <input <?php echo ($grid_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_scroll" name="store_locator_grid[scroll]" >
                            </p>

                            <p>
                                <label title="Select the displayed column in the grid in the frontend by order" for="store_locator_grid_columns"><?php echo __("Displayed Columns", 'wpmsl'); ?>:<span class="store_locator_tip"><?php echo __("Select columns with order to be displayed on frontend", 'store_locator'); ?></span></label>
                                <select  id="store_locator_grid_columns" multiple="multiple">
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
                            </p>
                            <p>
                                <label title="Grid type view!"><?php echo __("Grid Type", 'store_locator'); ?> </label>
                                <?php /* <?php echo __("Table", 'store_locator'); ?> <input <?php  (empty($grid_options['view']) || $grid_options['view'] == 'table')?'checked':''; ?> value="table" type="radio" disabled  name="store_locator_grid[view]" > */ ?>
                                <?php echo __("Cards", 'store_locator'); ?> <input <?php  (!empty($grid_options['view']) && $grid_options['view'] == 'card')?'checked':''; ?> value="card" checked  readonly type="radio" name="store_locator_grid[view]" >
                            </p>
                            <div class="" style="overflow: hidden;">
                                 <p>
                                <label title="Map Result Show on" for="store_locator_map_type"><?php echo __("Map Result Show on", 'wpmsl'); ?>:</label>
                                </p>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Left Side','wpmsl');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Right Side','wpmsl');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'right')?'checked':''; ?> type="radio" value="right" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('Below Map','wpmsl');?>
                                        <input <?php echo (isset($grid_options['listing_position']) && $grid_options['listing_position'] == 'below_map')?'checked':''; ?> type="radio" value="below_map" name="store_locator_grid[listing_position]" /></label>
                                    </li>
                                </ul>
                            </div>
                            <div class="" style="overflow: hidden;">
                                 <p>
                                <label title="Map Search Options Window Show on" for="store_locator_map_type"><?php echo __("Map Search Options Window Show on", 'wpmsl'); ?>:</label>
                                </p>
                                <ul class='listing_postions_grid_settings'>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Left Side','wpmsl');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('On Map Right Side','wpmsl');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_search_right')?'checked':''; ?> type="radio" value="wpml_search_right" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                    <li>
                                        <label style="width: 26px;"><?php _e('Above Map','wpmsl');?>
                                        <input <?php echo (isset($grid_options['search_window_position']) && $grid_options['search_window_position'] == 'wpml_above_map')?'checked':''; ?> type="radio" value="wpml_above_map" name="store_locator_grid[search_window_position]" /></label>
                                    </li>
                                </ul>
                            </div>
                            <p class="submit">
                                <input type="submit" class="button-primary" name="grid-settings" value="<?php echo __('Save Changes', 'wpmsl'); ?>">
                            </p>
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
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Single Page Settings", 'wpmsl'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">

                            <p>
                                <label title="Enable/Disable when click on store goto single page for more details" for="store_locator_single_page"><?php echo __("Link store to a single page", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[page]" >
                                <input <?php echo ($single_options['page'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_page" name="store_locator_single[page]" >
                            </p>
                            
                            <p>
                                <label title="Enter Unique Slug Name" for="store_locator_slug"><?php echo __("Enter Unique Slug Name", 'wpmsl'); ?></label>                                
                                <input <?php echo ($single_options['store_locator_slug'] != '')? $single_options['store_locator_slug']:''; ?> placeholder="store-locator" value="<?php echo $single_options['store_locator_slug']?>" type="text" id="store_locator_slug" name="store_locator_single[store_locator_slug]" >
                            </p>

                            <p>
                                <label title="Enable/Disable the display of feature image of the store in the inner page" for="store_locator_single_image"><?php echo __("Show feature image of the store?", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[image]" >
                                <input <?php echo ($single_options['image'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_image" name="store_locator_single[image]" >
                            </p>

                            <p>
                                <label title="Enable/Disable showing map in the inner page of the store" for="store_locator_single_map"><?php echo __("Show map on page?", 'wpmsl'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[map]" >
                                <input <?php echo ($single_options['map'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_map" name="store_locator_single[map]" >
                            </p>

                            <p>
                                <label title="Select the displayed column in the page in the frontend by order" for="store_locator_single_items"><?php echo __("Displayed Columns", 'wpmsl'); ?>:<span class="store_locator_tip"><?php echo __("Select details you want to display on the page", 'store_locator'); ?></span></label>
                                <select  id="store_locator_single_items" multiple="multiple">
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
                            </p>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="single-settings" value="<?php echo __("Save Changes", 'wpmsl'); ?>">
                            </p>
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
?>

<style>
    .ui-widget-content{
        background: #0073AA url("images/ui-bg_flat_75_ffffff_40x100.png") 50% 50% repeat-x;
        color: white;
    }
    .ui-tooltip:after {
        content: "\f142";
        font-family: dashicons;
        font-size: 30px;
        top: -11px;
        position: absolute;
        color: #0073AA;
    }
    .ui-tooltip{
        box-shadow: none;
        border-width: 0px !important;
    }</style>