<link rel="stylesheet" href="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/css/jquery-ui.css'; ?>">
<script src="<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/jquery-ui.js'; ?>"></script>
<div class="store_locator_settings_div">
    <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="updated below-h2"><p><?php echo __("Settings updated.", 'store_locator'); ?></p></div>
    <?php endif; ?>
    <div class="wrap">
        <div class="metabox-holder">
            <div style="width: 75%;">

                <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Google Maps Api", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">


                            <p>
                                <label  for="store_locator_API_KEY"><?php echo __("API KEY", 'store_locator'); ?>:</label>
                                <input value="<?php print_r($store_locator_API_KEY); ?>" type="text" id="store_locator_API_KEY" name="store_locator_API_KEY" >
                            </p>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="api-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>
                <!-- Map settings -->
                <form method="POST">
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Map Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_map_settings">
                            <p>
                                <label title="Enable the display of map on the frontend" for="store_locator_map_enable"><?php echo __("Show map on frontend", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[enable]" >
                                <input <?php echo ($map_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_enable" name="store_locator_map[enable]" >
                            </p>

                            <p>
                                <label title="Select Map Width pixels" for="store_locator_map_width"><?php echo __("Map Width", 'store_locator'); ?>:</label>
                                <input value="<?php echo $map_options['width']; ?>" type="text" id="store_locator_map_width" name="store_locator_map[width]" size="4">
                                <select name="store_locator_map[widthunit]" id="store_locator_map_widthunit" >
                                    <option <?php  ($map_options['widthunit'] == 'px') ?"selected=": ""; ?> selected value="px">PX</option>
									<option <?php  echo ($map_options['widthunit'] == '%') ?"selected=": ""; ?>  value="%">%</option>
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select>
                            </p>

                            <p>
                                <label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php echo __("Map Height ", 'store_locator'); ?>:</label>
                                <input value="<?php echo $map_options['height']; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
									<option <?php  echo ($map_options['heightunit'] == 'px') ?"selected=": ""; ?>  value="px">PX</option>           
                                    <?php /* <option <?php  ($map_options['heightunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */?>
                                </select>
                            </p>

                            <p>
                                <label title="Select Map Type" for="store_locator_map_type"><?php echo __("Map Type", 'store_locator'); ?>:</label>
                                <select name="store_locator_map[type]" id="store_locator_map_type">
                                    <option <?php echo ($map_options['type'] == 'roadmap') ?"selected=": ""; ?> value="roadmap">Roadmap</option>
                                    <option <?php echo ($map_options['type'] == 'hybrid') ?"selected=": ""; ?> value="hybrid">Hybrid</option>
                                    <option <?php echo ($map_options['type'] == 'satellite') ?"selected=": ""; ?> value="satellite">Satellite</option>
                                    <option <?php echo ($map_options['type'] == 'terrain') ?"selected=": ""; ?> value="terrain">Terrain</option>
                                </select>
                            </p>

                            <p>
                                <label title="Choose the unit of search km/mile" for="store_locator_map_unit"><?php echo __("Search Unit", 'store_locator'); ?>:</label>
                                <select name="store_locator_map[unit]" id="store_locator_map_unit">
                                    <option <?php echo ($map_options['unit'] == 'km') ?"selected=": ""; ?> value="km">Km</option>
                                    <option <?php echo ($map_options['unit'] == 'mile') ?"selected=": ""; ?> value="mile">Mile</option>
                                </select>
                            </p>

                            <p>
                                <label title="Choose search options here. the default one will be between square brakets" for="store_locator_map_radius"><?php echo __("Search radius options", 'store_locator'); ?>:</label>
                                <input value="<?php echo $map_options['radius']; ?>" type="text" id="store_locator_map_radius" name="store_locator_map[radius]" >
                            </p>

                            <p>
                                <label title="Enable search by category in the frontend" for="store_locator_map_category"><?php echo __("Enable Search with categories", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[category]" >
                                <input <?php echo ($map_options['category'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_category" name="store_locator_map[category]" >
                            </p>

                            <p>
                                <label title="Enable search by tag in the frontend" for="store_locator_map_tag"><?php echo __("Enable Search with Tags", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[tag]" >
                                <input <?php echo ($map_options['tag'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_tag" name="store_locator_map[tag]" >
                            </p>

                            <p>
                                <label title="Show street control on the map in frontend" for="store_locator_map_streetViewControl"><?php echo __("Show street view control", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[streetViewControl]" >
                                <input <?php echo ($map_options['streetViewControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_streetViewControl" name="store_locator_map[streetViewControl]" >
                            </p>

                            <p>
                                <label title="Enable the user to change the map type from the frontend" for="store_locator_map_mapTypeControl"><?php echo __("Show map type control", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[mapTypeControl]" >
                                <input <?php echo ($map_options['mapTypeControl'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_mapTypeControl" name="store_locator_map[mapTypeControl]" >
                            </p>

                            <p>
                                <label title="Enable/Disable zoom by scroll on map" for="store_locator_map_scroll"><?php echo __("Zoom by scroll on map", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_map[scroll]" >
                                <input <?php echo ($map_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_scroll" name="store_locator_map[scroll]" >
                            </p>

                            <p>
                                <label title="Display default Map Search" for="store_locator_default_search"><?php echo __("Default Map Search", 'store_locator'); ?></label>
                                <input value="0" type="hidden" name="store_locator_map[default_search]" >
                                <input <?php echo ($map_options['default_search'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_default_search" name="store_locator_map[default_search]" >
                            </p>

                            <p>
                                <label title="Search Field Options" for="store_locator_default_search"><?php echo __("Hide Fields for Search", 'store_locator'); ?></label>
                                <ul class="hide_fields" style="margin-left: 36%">
                                    <li><input <?php echo (isset($map_options['search_field_get_my_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_get_my_location]" > Get My Location</li>
                                    <li><input <?php echo (isset($map_options['search_field_location']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_location]" > Location Field</li>
                                    <li><input <?php echo (isset($map_options['search_field_radius']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_radius]" > Radius Field</li>
                                    <li><input <?php echo (isset($map_options['search_field_category']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_category]" > Category Field</li>
                                    <li><input <?php echo (isset($map_options['search_field_tags']))?'checked':''; ?> value="hide-field" type="checkbox" id="search_field_get_my_location" name="store_locator_map[search_field_tags]" > Tags Field</li>
                                </ul>
                            </p>

                            <p>
                                <label title="Map Search Open as Default" for="map_window_open"><?php echo __("Map Search Open as Default", 'store_locator'); ?></label>
                                <input <?php echo (isset($map_options['map_window_open']))?'checked':''; ?> value="1" type="checkbox" id="map_window_open" name="store_locator_map[map_window_open]" >
                            </p>
							
							<p>
                                <label title="Switch To RTL" for="rtl_enabled"><?php echo __("Switch To RTL", 'store_locator'); ?></label>
                                <input <?php echo (isset($map_options['rtl_enabled']))?'checked':''; ?> value="1" type="checkbox" id="rtl_enabled" name="store_locator_map[rtl_enabled]" >
                            </p>
                            
                            <p><b>Map Styles</b></p>
                            <p>
                                <label title="Standard Map" for="store_locator_map_style1"><?php echo __("Standard Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 1)?'checked':''; ?> value="1" type="radio" id="store_locator_map_style1" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/staticmap.png'; ?>" />
                            </p>


                            <p>
                                <label title="Silver Map" for="store_locator_map_style2"><?php echo __("Silver Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 2)?'checked':''; ?> value="2" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/silver.png'; ?>" />
                            </p>

                            <p>
                                <label title="Retro Map" for="store_locator_map_style2"><?php echo __("Retro Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 3)?'checked':''; ?> value="3" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/retro.png'; ?>" />
                            </p>

                            <p>
                                <label title="Dark Map" for="store_locator_map_style2"><?php echo __("Dark Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 4)?'checked':''; ?> value="4" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/dark.png'; ?>" />
                            </p>

                            <p>
                                <label title="Night Map" for="store_locator_map_style2"><?php echo __("Night Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 5)?'checked':''; ?> value="5" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/night.png'; ?>" />
                            </p>

                            <p>
                                <label title="Aubergine Map" for="store_locator_map_style2"><?php echo __("Aubergine Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 6)?'checked':''; ?> value="6" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/aubergine.png'; ?>" />
                            </p>

                            <p>
                                <label title="Basic Map" for="store_locator_map_style2"><?php echo __("Basic Map", 'store_locator'); ?></label>
                                <input <?php echo ($map_options['map_style'] == 7)?'checked':''; ?> value="7" type="radio" id="store_locator_map_style2" name="store_locator_map[map_style]" >
                                <img src="<?php echo STORE_LOCATOR_PLUGIN_URL . 'assets/img/basic.png'; ?>" />
                            </p>


                            <p>
                                <label title="Map Result Show on" for="store_locator_map_type"><?php echo __("Map Result Show on", 'store_locator'); ?>:</label>
                            </p>
                            <ul>
                                <li>
                                    <label style="width: 26px;">Left</label>
                                    <input <?php echo ($map_options['listing_position'] == 'left')?'checked':''; ?> type="radio" value="left" name="store_locator_map[listing_position]" />
                                </li>
                                <li>
                                    <label style="width: 26px;">Right</label>
                                    <input <?php echo ($map_options['listing_position'] == 'right')?'checked':''; ?> type="radio" value="right" name="store_locator_map[listing_position]" />
                                </li>
                            </ul>

                            <p>
                                <label title="Choose the color of user marker" for="store_locator_map_type"><?php echo __("User Marker", 'store_locator'); ?>:</label>
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
                                    or add custom marker url <input type="text" placeholder="http://markerimage.png" value="<?php echo $map_options['marker1_custom'];?>" name="store_locator_map[marker1_custom]" />
                            </p>

                            <p>
                                <label title="Choose the color of store marker" for="store_locator_map_type"><?php echo __("Store Marker", 'store_locator'); ?>:</label>
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
                                or add custom marker url <input type="text" placeholder="http://markerimage.png" value="<?php echo $map_options['marker2_custom'];?>" name="store_locator_map[marker2_custom]" />
                            </p>
                            <?php
                            echo do_action('wpmsl_private_marker_settings');
                            ?>

                            <p>
                                <label title="You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content" for="store_locator_map_infowindow"><?php echo __("Info Window Content", 'store_locator'); ?>: <span class="store_locator_tip">placeholders: {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</span></label>
                                <textarea name="store_locator_map[infowindow]" id="store_locator_map_infowindow"><?php echo $map_options['infowindow']; ?></textarea>
                            </p>

                            <p>
                                <label title="You can customize the look of the map by adding styles here" for="store_locator_map_style"><?php echo __("Customised Map Style", 'store_locator'); ?>: <span class="store_locator_tip">You can get some styles from <a target="_blanck" href="https://snazzymaps.com">Snazzy Maps</a></span> </label>
                                <textarea name="store_locator_map[custom_style]" id="store_locator_map_style"><?php echo $map_options['custom_style']; ?></textarea>
                            </p>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="map-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>

                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Placeholder Text", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">
                            <p>Get Location Text Button</p>
                            <input type="text" name="get_location_btn_txt" value="<?php echo $placeholder_setting['get_location_btn_txt']?>"/>

                            <p>Enter Location Text</p>
                            <input type="text" name="enter_location_txt" value="<?php echo $placeholder_setting['enter_location_txt']?>" />

                            <p>Select Category</p>
                            <input type="text" name="select_category_txt" value="<?php echo $placeholder_setting['select_category_txt']?>"/>

                            <p>Select Tags</p>
                            <input type="text" name="select_tags_txt" value="<?php echo $placeholder_setting['select_tags_txt']?>"/>

                            <p class="submit">
                                <input type="submit" class="button-primary" name="placeholder-setting" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>

                <!-- Grid settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Grid Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_grid_settings">

                            <p>
                                <label title="Show the results in grid in the frontend" for="store_locator_grid_enable"><?php echo __("Show grid on frontend", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_grid[enable]" >
                                <input <?php echo ($grid_options['enable'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_enable" name="store_locator_grid[enable]" >
                            </p>

                            <p>
                                <label title="Select number of stores to be displayed per page" for="store_locator_grid_number"><?php echo __("Number of stores/page", 'store_locator'); ?>:</label>
                                <input value="<?php echo $grid_options['number']; ?>" type="text" id="store_locator_grid_number" name="store_locator_grid[number]" >
                            </p>

                            <p>
                                <label title="Enable/Disable autoload results when scroll down" for="store_locator_grid_scroll"><?php echo __("Autoload results on scroll", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_grid[scroll]" >
                                <input <?php echo ($grid_options['scroll'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_grid_scroll" name="store_locator_grid[scroll]" >
                            </p>

                            <p>
                                <label title="Select the displayed column in the grid in the frontend by order" for="store_locator_grid_columns"><?php echo __("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php echo __("Select columns with order to be displayed on frontend", 'store_locator'); ?></span></label>
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
                            <p class="submit">
                                <input type="submit" class="button-primary" name="grid-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>


                <!-- Single page settings -->
                <form method="POST" >
                    <div class="postbox" >
                        <div class="handlediv"><br></div><h3 style="cursor: auto;" class="hndle"><span><?php echo __("Single Page Settings", 'store_locator'); ?></span></h3>
                        <div class="inside store_locator_singel_page_settings">

                            <p>
                                <label title="Enable/Disable when click on store goto single page for more details" for="store_locator_single_page"><?php echo __("Link store to a single page", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[page]" >
                                <input <?php echo ($single_options['page'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_page" name="store_locator_single[page]" >
                            </p>
							
							<p>
                                <label title="Enter Unique Slug Name" for="store_locator_slug"><?php echo __("Enter Unique Slug Name", 'store_locator'); ?></label>                                
                                <input <?php echo ($single_options['store_locator_slug'] != '')? $single_options['store_locator_slug']:''; ?> placeholder="store-locator" value="<?php echo $single_options['store_locator_slug']?>" type="text" id="store_locator_slug" name="store_locator_single[store_locator_slug]" >
                            </p>

                            <p>
                                <label title="Enable/Disable the display of feature image of the store in the inner page" for="store_locator_single_image"><?php echo __("Show feature image of the store?", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[image]" >
                                <input <?php echo ($single_options['image'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_image" name="store_locator_single[image]" >
                            </p>

                            <p>
                                <label title="Enable/Disable showing map in the inner page of the store" for="store_locator_single_map"><?php echo __("Show map on page?", 'store_locator'); ?>?</label>
                                <input value="0" type="hidden" name="store_locator_single[map]" >
                                <input <?php echo ($single_options['map'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_single_map" name="store_locator_single[map]" >
                            </p>

                            <p>
                                <label title="Select the displayed column in the page in the frontend by order" for="store_locator_single_items"><?php echo __("Displayed Columns", 'store_locator'); ?>:<span class="store_locator_tip"><?php echo __("Select details you want to display on the page", 'store_locator'); ?></span></label>
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
                                <input type="submit" class="button-primary" name="single-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
                            </p>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    jQuery( '.store_locator_settings_div' ).tooltip();
</script>

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