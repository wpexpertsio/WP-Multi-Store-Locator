<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
if(!class_exists('WPMSL_Manage_Stores')){
	class WPMSL_Manage_Stores{
		public function __construct(){
			add_action('init', array($this,'store_init'));
			add_action('admin_menu', array($this,'disable_new_stores_posts'));
			//create custom fields
			add_action('add_meta_boxes',array($this, 'add_store_locator_meta'));
			//save custom fields
			add_action('save_post', array($this, 'store_locator_save_meta_box_data'));
			//manage custom coulmns display for stores
			add_filter('manage_edit-store_locator_columns', array($this,'store_list_columns'));
			//manage custom coulmns content display for Stores
			add_filter('manage_store_locator_posts_custom_column', array($this,'manage_stores_columns'), 10, 2);
			//manage custom coulmns display for sales managers
			add_filter('manage_edit-sales_manager_columns', array($this,'sales_manager_list_columns'));
			//manage custom coulmns content display for Sales
			add_filter('manage_sales_manager_posts_custom_column', array($this,'manage_sales_manager_columns'), 10, 2);
			// add filters to the query
			add_filter('parse_query', array($this,'custom_posts_filter'));
			// handle the dislay of new fillters
			add_action('restrict_manage_posts', array($this, 'custom_posts_restrict_manage_posts'));
		}
		public function store_init() {
		    $store_locator_single = get_option('store_locator_single',true);
		    $store_locator_slug = '';
		    if(isset( $store_locator_single['store_locator_slug'] )) {
		        $store_locator_slug = $store_locator_single['store_locator_slug'];
		    }
		    if(empty($store_locator_slug))
		        $store_locator_slug = 'store-locator';
		    $labels = array(
		        'name' => esc_html__('Stores Locator', 'store_locator'),
		        'singular_name' => esc_html__('Stores Locator', 'store_locator'),
		        'menu_name' => esc_html__('Stores Locator', 'store_locator'),
		        'name_admin_bar' => esc_html__('Store', 'store_locator'),
		        'add_new' => esc_html__('Add New Store', 'store_locator'),
		        'add_new_item' => esc_html__('Add New Store', 'store_locator'),
		        'new_item' => esc_html__('New Store', 'store_locator'),
		        'edit_item' => esc_html__('Edit Store', 'store_locator'),
		        'view_item' => esc_html__('View Store', 'store_locator'),
		        'all_items' => esc_html__('Stores List', 'store_locator'),
		        'search_items' => esc_html__('Search Store', 'store_locator'),
		        'parent_item_colon' => esc_html__('Store Partner:', 'store_locator'),
		        'not_found' => esc_html__('No Store found.', 'store_locator'),
		        'not_found_in_trash' => esc_html__('No Store found in Trash.', 'store_locator')
		    );
		    $single_options = get_option('store_locator_single');
		    $args = array(
		        'labels' => $labels,
		        'description' => esc_html__('Description.', 'store_locator'),
		        'public' => ((isset($single_options['page']) && !empty($single_options['page']))?true:false),
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'query_var' => true,
		        'rewrite' => array('slug' => $store_locator_slug),
		        'capability_type' => 'post',
		        'has_archive' => true,
		        'hierarchical' => false,
		        'menu_position' => null,
		        'menu_icon' => "dashicons-location-alt",
		        'supports' => array('thumbnail')
		    );
		    $labelsSM = array(
		        'name' => esc_html__('Sales Managers', 'store_locator'),
		        'singular_name' => esc_html__('Sales Managers', 'store_locator'),
		        'menu_name' => esc_html__('Sales Managers', 'store_locator'),
		        'name_admin_bar' => esc_html__('Book', 'store_locator'),
		        'add_new' => esc_html__('Add New', 'store_locator'),
		        'add_new_item' => esc_html__('Add New Sales Manager', 'store_locator'),
		        'new_item' => esc_html__('New Sales Manager', 'store_locator'),
		        'edit_item' => esc_html__('Edit Sales Manager', 'store_locator'),
		        'view_item' => esc_html__('View Sales Manager', 'store_locator'),
		        'all_items' => esc_html__('Sales Managers List', 'store_locator'),
		        'search_items' => esc_html__('Search Manager', 'store_locator'),
		        'parent_item_colon' => esc_html__('Parent Manager:', 'store_locator'),
		        'not_found' => esc_html__('No Sales Manager found.', 'store_locator'),
		        'not_found_in_trash' => esc_html__('No Sales Manager found in Trash.', 'store_locator')
		    );

		    $argsSM = array(
		        'labels' => $labelsSM,
		        'description' => esc_html__('Description.', 'store_locator'),
		        'public' => false,
		        'publicly_queryable' => false,
		        'show_ui' => true,
		        'show_in_menu' => false,
		        'query_var' => true,
		        'exclude_from_search' => true,
		        'rewrite' => array('slug' => 'sales-manager'),
		        'capability_type' => 'post',
		        'has_archive' => true,
		        'hierarchical' => false,
		        'menu_position' => null,
		        'supports' => false
		    );
		    // multiple stores
		    $mapslabels = array(
		        'name' => esc_html__('Maps', 'store_locator'),
		        'singular_name' => esc_html__('Map', 'store_locator'),
		        'menu_name' => esc_html__('Maps', 'store_locator'),
		        'name_admin_bar' => esc_html__('Maps', 'store_locator'),
		        'add_new' => esc_html__('Add New Map', 'store_locator'),
		        'add_new_item' => esc_html__('Add New Map', 'store_locator'),
		        'new_item' => esc_html__('New Map', 'store_locator'),
		        'edit_item' => esc_html__('Edit Map', 'store_locator'),
		        'view_item' => esc_html__('View Map', 'store_locator'),
		        'all_items' => esc_html__('Maps List', 'store_locator'),
		        'search_items' => esc_html__('Search Maps', 'store_locator'),
		        'not_found' => esc_html__('No Map found.', 'store_locator'),
		        'not_found_in_trash' => esc_html__('No Map found in Trash.', 'store_locator')
		    );
		    $mapsargs = array(
		        'labels' => $mapslabels,
		        'description' => esc_html__('Description.', 'store_locator'),
		        'public' => false,
		        'show_ui' => true,
		        'show_in_menu' => false,
		        'query_var' => true,
		        'capability_type' => 'post',
		        'has_archive' => true,
		        'hierarchical' => false,
		        'menu_position' => null,
		        'supports' => array('title','thumbnail')
		    );

		    register_post_type('store_locator', $args);
		    register_post_type('sales_manager', $argsSM);
		    register_post_type('maps', $mapsargs);

		    // create custom category for stores
		    register_taxonomy( 'store_locator_category', array('store_locator','maps'), array(
		            'hierarchical' => true,
		            'label' => esc_html__('Store Categories', 'store_locator'),
		            'singular_label' => esc_html__('Category', 'store_locator'),
		            'rewrite' => array( 'slug' => 'categories', 'with_front'=> false )
		        )
		    );
		    register_taxonomy_for_object_type( 'store_locator_category', 'store_locator' );

		    // create custom tags for stores
		    register_taxonomy(
		        'store_locator_tag',
		        'store_locator',
		        array(
		            'hierarchical'  => false,
		            'label'         => esc_html__("Store Tags", 'store_locator'),
		            'singular_name' => esc_html__("Tag", 'store_locator'),
		            'rewrite'       => true,
		            'query_var'     => true
		        )
		    );
		}
		public function disable_new_stores_posts() {
		    global $submenu;
		    unset($submenu['edit.php?post_type=store_locator'][10]);
		    add_submenu_page('edit.php?post_type=store_locator', esc_html__('Maps','store_locator'), esc_html__('Maps','store_locator'), 'manage_options', 'edit.php?post_type=maps');
		    add_submenu_page('edit.php?post_type=store_locator', esc_html__('Sales Manager','store_locator'), esc_html__('Sales Managers List','store_locator'), 'manage_options', 'edit.php?post_type=sales_manager');
		}
		public function add_store_locator_meta() {
		    add_meta_box('store-info', 
		        esc_html__('Store Info', 'store_locator'), 
		        array($this,'store_locator_meta_box_callback_store_info'), 
		        'store_locator');
		    add_meta_box('address-info',
		        esc_html__('Address Info', 'store_locator'), 
		        array($this,'store_locator_meta_box_callback_address_info'), 
		        'store_locator');

		    add_meta_box('sales-manager-info', 
		            esc_html__('Sales Manager Info', 'store_locator'), 
		            array($this,'sales_managers_meta_box_callback'),
		            'sales_manager');
		}
		public function store_locator_meta_box_callback_store_info($post) {
		    // Add a nonce field so we can check for it later.
		    wp_nonce_field('store_locator_save_meta_box_data', 'store_locator_meta_box_nonce');
		    $post_id = $post->ID;
		    ?>
		    <table class="form-table" style="border: 0px;">
		        <tbody>
		        <tr>
		            <td><?php echo esc_html__("Code", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_code', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : $post_id; ?>" name="store_locator_code" class="widefat" readonly />
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Name", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_name', true); ?>
		                <input type="text" class="widefat" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_name"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Description", 'store_locator'); ?></td>
		            <td>
		                <?php
		                $content = get_post_meta( $post_id, 'store_locator_description', true );
		                wp_editor( $content, "store_locator_description" );
		                ?>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Phone", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_phone', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_phone" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Fax", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_fax', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_fax" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("E-Mail", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_email', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_email" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Website", 'store_locator'); ?></td>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_website', true); ?>
		                <input type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_website" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Working Hours", 'store_locator'); ?></td>
		            <?php
		            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		            $days_meta = get_post_meta($post_id, 'store_locator_days', true);
		            ?>
		            <td>
		                <table id="store_locator_hours" style="background-color: rgb(241, 241, 241); border-radius: 5px;" class="widefat">
		                    <?php foreach ($days as $day): ?>
		                        <tr>
		                            <td style="border-bottom: 1px solid #dbdbdb;"><?php echo $day; ?></td>
		                            <td style="border-bottom: 1px solid #dbdbdb;">
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'checked':''; ?> type="radio" value="1" id="store_locator_days_<?php echo $day; ?>_1" name="store_locator_days[<?php echo $day; ?>][status]" > <label for="store_locator_days_<?php echo $day; ?>_1"> Opened </label>
		                                <input <?php echo (!isset($days_meta[$day]) || $days_meta[$day]['status'] == '0')?'checked':''; ?> type="radio" value="0" id="store_locator_days_<?php echo $day; ?>_0" name="store_locator_days[<?php echo $day; ?>][status]" /> <label for="store_locator_days_<?php echo $day; ?>_0"> Closed </label>
		                            </td>
		                            <td style="border-bottom: 1px solid #dbdbdb;">
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'':'style="display: none;"'; ?> size="9" placeholder="Open Time" type="text" value="<?php echo (isset($days_meta[$day]))?$days_meta[$day]['start']:''; ?>" name="store_locator_days[<?php echo $day; ?>][start]" class="start_time"/>
		                                <input <?php echo (isset($days_meta[$day]) && $days_meta[$day]['status'] == '1')?'':'style="display: none;"'; ?> size="9" placeholder="Close Time" type="text" value="<?php echo (isset($days_meta[$day]))?$days_meta[$day]['end']:''; ?>" name="store_locator_days[<?php echo $day; ?>][end]" class="end_time" />
		                            </td>
		                        </tr>
		                    <?php endforeach; ?>
		                </table>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Sales Managers", 'store_locator'); ?></td>
		            <td>
		                <select name="store_locator_sales[]" id="store_locator_sales" multiple="multiple" class="widefat">
		                    <?php
		                    $args = array(
		                        'posts_per_page' => -1,
		                        'post_type' => 'sales_manager',
		                        'status' => 'publish',
		                    );
		                    $allSales = get_posts($args);
		                    $selectedSales  = get_post_meta($post_id, 'store_locator_sales', true);
		                    if(!$selectedSales){
		                        $selectedSales = array();
		                    }
		                    foreach ($allSales as $manager) { ?>
		                        <option value="<?php echo $manager->ID; ?>" <?php echo (in_array($manager->ID, $selectedSales)) ? "selected" : ""; ?>><?php echo $manager->post_title; ?></option>
		                    <?php } ?>
		                </select>
		            </td>
		        </tr>
		        </tbody>
		    </table>
		    <script>
		        // initialize input widgets first
		        jQuery('.start_time, .end_time').timepicker({
		            'showDuration': true,
		            'timeFormat': 'g:i a'
		        });
		    </script>
		    <?php
		}
		public function store_locator_meta_box_callback_address_info($post) {
		    $post_id = $post->ID; ?>
		    <table class="form-table">
		        <tbody>
		        <tr>
		            <th><?php echo esc_html__("Address", 'store_locator'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_address', true); ?>
		                <input id="store_locator_address" type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" name="store_locator_address" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Store Longitude", 'store_locator'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_lng', true); ?>
		                <input   type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" class="widefat" disabled />
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Store latitude", 'store_locator'); ?></th>
		            <td><?php $meta= get_post_meta($post_id, 'store_locator_lat', true); ?>
		                <input  type="text" value="<?php echo !empty($meta) ? esc_attr($meta) : ''; ?>" class="widefat" disabled  />
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Country", 'store_locator'); ?></th>
		            <td>
		                <select name="store_locator_country" id="store_locator_country" class="widefat">
		                    <option value="" ></option>
		                    <?php
		                    global $wpdb;
		                    $allCountries = $wpdb->get_results("SELECT * FROM store_locator_country");
		                    $selectedCountry = get_post_meta($post_id, 'store_locator_country', true);
		                    foreach ($allCountries as $country) { ?>
		                        <option value="<?php echo $country->name; ?>" <?php echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
		                    <?php } ?>
		                </select>
		            </td>
		        </tr>
		        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
		            <th><?php echo esc_html__("State", 'store_locator'); ?></th>
		            <td>
		                <select name="store_locator_state" id="store_locator_state" class="widefat">
		                    <option value="" ></option>
		                    <?php
		                    global $wpdb;
		                    $allStates = $wpdb->get_results("SELECT * FROM store_locator_state");
		                    $selectedState = get_post_meta($post_id, 'store_locator_state', true);
		                    foreach ($allStates as $state) { ?>
		                        <option value="<?php echo $state->name; ?>" <?php echo ($selectedState == $state->name) ? "selected" : ""; ?>><?php echo $state->name; ?></option>
		                    <?php } ?>
		                </select>
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("City", 'store_locator'); ?></th>
		            <td>
		                <input id="store_locator_city" type="text" value="<?php echo get_post_meta($post_id, 'store_locator_city', true) ? get_post_meta($post_id, 'store_locator_city', true) : ''; ?>" name="store_locator_city" class="widefat"/>
		            </td>
		        </tr>
		        <tr>
		            <th><?php echo esc_html__("Postal Code", 'store_locator'); ?></th>
		            <td>
		                <input id="store_locator_zipcode" type="text" value="<?php echo get_post_meta($post_id, 'store_locator_zipcode', true) ? get_post_meta($post_id, 'store_locator_zipcode', true) : ''; ?>" name="store_locator_zipcode" class="widefat"/>
		            </td>
		        </tr>
		        </tbody>
		    </table>
		    <input type="hidden" value="<?php echo get_post_meta($post_id, 'store_locator_lat', true) ? get_post_meta($post_id, 'store_locator_lat', true) : ''; ?>" name="store_locator_lat" id="store_locator_lat"/>
		    <input type="hidden" value="<?php echo get_post_meta($post_id, 'store_locator_lng', true) ? get_post_meta($post_id, 'store_locator_lng', true) : ''; ?>" name="store_locator_lng" id="store_locator_lng"/>
		    <div id="map-container" style="position: relative;">
		        <div id="map_loader" style="z-index: 9;width: 100%; height: 200px;position: absolute;background-color: #fff;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
		        <div id="map-canvas" style="height: 200px;width: 100%;"></div>
		    </div>
		    <script>
		        jQuery(document).ready(function (jQuery) {
		            initializeMapBackend();
		        });
		    </script>
		    <?php
		}

		public function sales_managers_meta_box_callback($post) {
		    // Add a nonce field so we can check for it later.
		    wp_nonce_field('sales_manager_save_meta_box_data', 'sales_manager_meta_box_nonce');
		    $post_id = $post->ID;
		    ?>
		    <table class="widefat" style="border: 0px;">
		        <tbody>
		        <tr>
		            <td><?php echo esc_html__("Identification", 'store_locator'); ?></td>
		            <td>
		                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_id', true) ? get_post_meta($post_id, 'sales_manager_id', true) : ''; ?>" name="sales_manager_id" class="regular-text"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Title", 'store_locator'); ?></td>
		            <td>
		                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_title', true) ? get_post_meta($post_id, 'sales_manager_title', true) : ''; ?>" name="sales_manager_title" class="regular-text"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Name", 'store_locator'); ?></td>
		            <td>
		                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_name', true) ? get_post_meta($post_id, 'sales_manager_name', true) : ''; ?>" name="sales_manager_name" class="regular-text"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("Phone", 'store_locator'); ?></td>
		            <td>
		                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_phone', true) ? get_post_meta($post_id, 'sales_manager_phone', true) : ''; ?>" name="sales_manager_phone" class="regular-text"/>
		            </td>
		        </tr>
		        <tr>
		            <td><?php echo esc_html__("E-Mail", 'store_locator'); ?></td>
		            <td>
		                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_email', true) ? get_post_meta($post_id, 'sales_manager_email', true) : ''; ?>" name="sales_manager_email" class="regular-text"/>
		            </td>
		        </tr>
		        </tbody>
		    </table>
		    <?php
		}
		public function store_locator_save_meta_box_data($post_id) {
		    if (isset($_POST['post_type']) && 'store_locator' == $_POST['post_type']) {
		        // Check if our nonce is set.
		        if (!isset($_POST['store_locator_meta_box_nonce'])) {
		            return;
		        }
		        // Verify that the nonce is valid.
		        if (!wp_verify_nonce($_POST['store_locator_meta_box_nonce'], 'store_locator_save_meta_box_data')) {
		            return;
		        }
		        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
		        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		            return;
		        }

		        //update post title
		        remove_action('save_post', array($this,'store_locator_save_meta_box_data'));
		        $my_post = array(
		            'ID' => $post_id,
		            'post_title' => $_POST['store_locator_name'],
		            'post_name' => wp_unique_post_slug(
		                $_POST['store_locator_name'],
		                $post_id,
		                'publish',
		                'store_locator',
		                $post_parent=null
		            )
		        );
		        wp_update_post($my_post);
		        add_action('save_post', array($this,'store_locator_save_meta_box_data'));

		        // update post meta
		        if (isset($_POST['store_locator_name']))
		            update_post_meta($post_id, 'store_locator_name', $_POST['store_locator_name']);

		        if (isset($_POST['store_locator_address']))
		            update_post_meta($post_id, 'store_locator_address', $_POST['store_locator_address']);

		        if (isset($_POST['store_locator_lat']))
		            update_post_meta($post_id, 'store_locator_lat', $_POST['store_locator_lat']);

		        if (isset($_POST['store_locator_lng']))
		            update_post_meta($post_id, 'store_locator_lng', $_POST['store_locator_lng']);

		        if (isset($_POST['store_locator_country']))
		            update_post_meta($post_id, 'store_locator_country', $_POST['store_locator_country']);

		        if (isset($_POST['store_locator_state']))
		            update_post_meta($post_id, 'store_locator_state', $_POST['store_locator_state']);

		        if (isset($_POST['store_locator_city']))
		            update_post_meta($post_id, 'store_locator_city', $_POST['store_locator_city']);

		        if (isset($_POST['store_locator_phone']))
		            update_post_meta($post_id, 'store_locator_phone', $_POST['store_locator_phone']);

		        if (isset($_POST['store_locator_fax']))
		            update_post_meta($post_id, 'store_locator_fax', $_POST['store_locator_fax']);

		         if (isset($_POST['store_locator_email']))
		            update_post_meta($post_id, 'store_locator_email', $_POST['store_locator_email']);

		        if (isset($_POST['store_locator_website']))
		            update_post_meta($post_id, 'store_locator_website', $_POST['store_locator_website']);

		        if (isset($_POST['store_locator_zipcode']))
		            update_post_meta($post_id, 'store_locator_zipcode', $_POST['store_locator_zipcode']);

		        if (isset($_POST['store_locator_code']))
		            update_post_meta($post_id, 'store_locator_code', $_POST['store_locator_code']);

		        if (isset($_POST['store_locator_sales']))
		            update_post_meta($post_id, 'store_locator_sales', $_POST['store_locator_sales']);

		        if (isset($_POST['store_locator_days']))
		            update_post_meta($post_id, 'store_locator_days', $_POST['store_locator_days']);

		        if (isset($_POST['store_locator_description']))
		            update_post_meta($post_id, 'store_locator_description', $_POST['store_locator_description']);
		        if (isset($_POST['store_locator_gform']))
		            update_post_meta($post_id, 'store_locator_gform', $_POST['store_locator_gform']);
		        
		        $args = array(
					'posts_per_page'   => -1,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'post_type'        => 'store_locator',
					'post_status'      => 'publish',
					'fields'           => '',
				);
				$posts_array = get_posts( $args );
				$stores=array();
				if(!empty($posts_array)){
					foreach ($posts_array as $key => $store) {
						$temp=$meta=array();
						$temp= (array) $store;
						$term_ids='';
						if ( $terms = get_the_terms($store, 'store_locator_category' ) ) {
						    $term_ids = wp_list_pluck( $terms, 'term_id' );
						}
						
						if(!empty($term_ids) && is_array($term_ids)){
							$temp=array_merge($temp,array('categories'=> implode(',',$term_ids)));
						}
						$term_ids='';
						if ( $terms = get_the_terms($store, 'store_locator_tag' )) {
						    $term_ids = wp_list_pluck( $terms, 'term_id' );
						}
						$temp=array_merge($temp,array('tags'=> $term_ids));
						$metaArr=get_post_meta($store->ID);
						foreach ($metaArr as $mkey => $mvalue) {
							$meta[$mkey]=$mvalue[0];
						}
						$temp=array_merge($temp,array('post_meta'=> $meta));
						$stores[$store->ID]=$temp;
					}
				}
				update_option('wp_multi_store_locator_stores', $stores);
		    }

		    else if (isset($_POST['post_type']) && 'sales_manager' == $_POST['post_type']) {

		        // Check if our nonce is set.
		        if (!isset($_POST['sales_manager_meta_box_nonce'])) {
		            return;
		        }
		        // Verify that the nonce is valid.
		        if (!wp_verify_nonce($_POST['sales_manager_meta_box_nonce'], 'sales_manager_save_meta_box_data')) {
		            return;
		        }
		        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
		        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		            return;
		        }

		        //update post title
		        remove_action('save_post', array($this, 'store_locator_save_meta_box_data'));
		        $my_post = array('ID' => $post_id, 'post_title' => $_POST['sales_manager_name']);
		        wp_update_post($my_post);
		        add_action('save_post', array($this, 'store_locator_save_meta_box_data'));

		        //insert/update tax
		        $term = get_term_by('slug', $post_id, 'sales_manager_tax');
		        if($term){
		            wp_update_term($term->term_id, 'sales_manager_tax', array('name' => $_POST['sales_manager_name']));
		        }else{
		            wp_insert_term($_POST['sales_manager_name'], 'sales_manager_tax', array('slug'    => $post_id));
		        }

		        // update post meta
		        if (isset($_POST['sales_manager_id']))
		            update_post_meta($post_id, 'sales_manager_id', $_POST['sales_manager_id']);

		        if (isset($_POST['sales_manager_title']))
		            update_post_meta($post_id, 'sales_manager_title', $_POST['sales_manager_title']);

		        if (isset($_POST['sales_manager_name']))
		            update_post_meta($post_id, 'sales_manager_name', $_POST['sales_manager_name']);

		        if (isset($_POST['sales_manager_phone']))
		            update_post_meta($post_id, 'sales_manager_phone', $_POST['sales_manager_phone']);

		        if (isset($_POST['sales_manager_email']))
		            update_post_meta($post_id, 'sales_manager_email', $_POST['sales_manager_email']);

		        if (isset($_POST['sales_manager_stores']))
		            $term_taxonomy_ids = wp_set_object_terms( $post_id, $_POST['sales_manager_stores'], 'store_locator_tax' );
		        else
		            $term_taxonomy_ids = wp_set_object_terms( $post_id, null, 'store_locator_tax' );
		    }
		}
		public function store_list_columns($columns) {
		    unset(
		        $columns['date']
		    );
		    $new_columns = array(
		        'title' => esc_html__('Name', 'store_locator'),
		        'store_address' => esc_html__('Address', 'store_locator'),
		        'store_sales' => esc_html__('Sales Managers', 'store_locator'),
		    );
		    return array_merge($columns, $new_columns);
		}
		public function manage_stores_columns($column, $post_id) {
		    global $post;
		    switch ($column) {
		        case 'store_address':
		            $meta = get_post_meta($post_id);
		            echo $meta['store_locator_address'][0] . " " . $meta['store_locator_city'][0] . " " . $meta['store_locator_state'][0] . " " . $meta['store_locator_country'][0] . " " . $meta['store_locator_zipcode'][0];
		            break;
		        case 'store_sales':
		            $sales = get_post_meta($post_id, 'store_locator_sales', true);
		            if($sales){
		                foreach ($sales as $manager) {
		                    echo get_post($manager)->post_title . "<br>";
		                }
		            }
		            break;
		        default :
		            break;
		    }
		}
		public function sales_manager_list_columns($columns) {
		    unset(
		        $columns['date']
		    );
		    $new_columns = array(
		        'title' => esc_html__('Name', 'store_locator'),
		        'sales_title' => esc_html__('Title', 'store_locator'),
		        'sales_phone' => esc_html__('Phone', 'store_locator'),
		        'sales_email' => esc_html__('Email', 'store_locator'),
		    );
		    return array_merge($columns, $new_columns);
		}
		public function manage_sales_manager_columns($column, $post_id) {
		    global $post;
		    switch ($column) {
		        case 'sales_title':
		            echo get_post_meta($post_id, 'sales_manager_title', true);
		            break;
		        case 'sales_phone':
		            echo get_post_meta($post_id, 'sales_manager_phone', true);
		            break;
		        case 'sales_email':
		            echo get_post_meta($post_id, 'sales_manager_email', true);
		            break;
		        default :
		            break;
		    }
		}
		public function custom_posts_filter($query) {
		    global $pagenow;
		    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['store_locator_manager']) && $_GET['store_locator_manager'] != '') {
		        $query->set('meta_query', array(
		                array(
		                    'key' => 'store_locator_sales',
		                    'value' => ':"' . $_GET['store_locator_manager'] .'"',
		                    'compare' => 'REGEXP'
		                )
		            )
		        );
		    }
		}
		public function custom_posts_restrict_manage_posts() {
		    $currentManager = isset($_GET['store_locator_manager']) ? $_GET['store_locator_manager'] : ''; ?>
		    <?php if (isset($_GET['post_type']) && $_GET['post_type'] == 'store_locator'): ?>
		        <select style="width: 186px;" name="store_locator_manager" >
		            <option value=""> <?php echo esc_html__("All Sales Managers", 'store_locator'); ?></option>
		            <?php
		            global $wpdb;
		            $q = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='sales_manager' AND post_status = 'publish' order by 'ID' Desc");

		            foreach ($q as $manager) { ?>
		                <option value="<?php echo $manager->ID; ?>" <?php echo ($manager->ID == $currentManager) ? "selected" : ""; ?>><?php echo $manager->post_title; ?></option>
		        	<?php } ?>
		        </select>
		    <?php endif; ?>
		    <?php
		}
	}
	new WPMSL_Manage_Stores();
}