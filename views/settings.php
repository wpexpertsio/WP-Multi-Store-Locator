<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="store_locator_settings_div">

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
							<?php wp_nonce_field( 'apinonact', 'apinonce' ); ?>
                            
							
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
                                    <?php /* <option <?php  ($map_options['widthunit'] == '%') ?"selected=": ""; ?> value="px">% in future</option> */ ?>
                                </select>
                            </p>

                            <p>
                                <label title="Select Map Height pixels. Min height 500px" for="store_locator_map_height"><?php echo __("Map Height ", 'store_locator'); ?>:</label>
                                <input value="<?php echo $map_options['height']; ?>" type="text" id="store_locator_map_height"  min="550" max="800" name="store_locator_map[height]" size="4" >
                                <select name="store_locator_map[heightunit]" id="store_locator_map_heightunit" >
                                    <option <?php  ($map_options['heightunit'] == 'px') ?"selected=": ""; ?> selected value="px">PX</option>
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
                                <label title="Enable/Disable Map Clustering" for="store_locator_map_cluster"><?php echo __("Marker Clusterer", 'store_locator'); ?>?</label> 
                                <input value="0" type="hidden" name="store_locator_map[cluster]" >
                                <input <?php echo ($map_options['cluster'])?'checked':''; ?> value="1" type="checkbox" id="store_locator_map_cluster" name="store_locator_map[cluster]" >
                            </p>
                            
                            <p>
                                <label title="Cluster Grid size" for="store_locator_map_csize"><?php echo __("Cluster Size", 'store_locator'); ?>?</label> 
                                <input value="<?php echo $map_options['csize']; ?>" type="text" id="store_locator_map_csize" name="store_locator_map[csize]" size="4" >
                            </p>
                            
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
                                <label title="You can customise the contetnt of the info window here by adding HTML if you need. Also you can use the below variables in the content" for="store_locator_map_infowindow"><?php echo __("Info Window Content", 'store_locator'); ?>: <span class="store_locator_tip">placeholders: {address} {city} {state} {country} {zipcode} {name} {phone} {website} {working_hours}</span></label> 
                                <textarea name="store_locator_map[infowindow]" id="store_locator_map_infowindow"><?php echo $map_options['infowindow']; ?></textarea>
                            </p>

                            <p>
                                <label title="You can customize the look of the map by adding styles here" for="store_locator_map_style"><?php echo __("Customised Map Style", 'store_locator'); ?>: <span class="store_locator_tip">You can get some styles from <a target="_blanck" href="https://snazzymaps.com">Snazzy Maps</a></span> </label> 
                                <textarea name="store_locator_map[style]" id="store_locator_map_style"><?php echo $map_options['style']; ?></textarea>
                            </p>               
							<?php wp_nonce_field( 'mapsettingsact', 'mapsettingsnonce' ); ?>
                            <p class="submit">
                                <input type="submit" class="button-primary" name="map-settings" value="<?php echo __("Save Changes", 'store_locator'); ?>">
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
								<?php wp_nonce_field( 'gridnonact', 'gridnonce' ); ?>
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
								<?php wp_nonce_field( 'singlesettingsnonceact', 'single-settings-nonce' ); ?>
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

