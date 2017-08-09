<?php
/*
  Plugin Name: WP Multi Store Locator
  Description: This plugin provides a number of options for admin in backend to manage their stores and sales manager for respective franchise. WP Store Locator have awesome user interface and displays results with google map in front end. Its a complete package with lots of features like search store, nearby you stores functionality and much more.
  Version: 1.5
  Author: WpExpertsio
  Author URI: https://wpexperts.io/
  Text Domain: WPMSL
  License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
define('STORE_LOCATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('STORE_LOCATOR_PLUGIN_PATH', plugin_dir_path(__FILE__));

include STORE_LOCATOR_PLUGIN_PATH . 'inc/store_locator_widget.php';
include STORE_LOCATOR_PLUGIN_PATH . 'inc/gravityforms-multiple-form-instances.php';

//create tables
register_activation_hook(__FILE__, 'store_locator_plugin_activation');
function store_locator_plugin_activation() {
    //create tables
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $wpdb;
    if (!empty($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    if (!empty($wpdb->collate))
        $charset_collate .= " COLLATE $wpdb->collate";

    //country table
    $country_table = 'store_locator_country';
    if ($wpdb->get_var("SHOW TABLES LIKE '$country_table'") != $country_table) {
        $sql = "CREATE TABLE " . $country_table . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NULL,
			PRIMARY KEY (`id`)
		) $charset_collate;";
        dbDelta($sql);
        // Insert Countries
        $sql = "INSERT INTO " . $country_table . " (`name`) VALUES ('Afghanistan'),('Albania'),('Algeria'),('American Samoa'),('Andorra'),('Angola'),('Anguilla'),('Antarctica'),('Antigua And Barbuda'),('Argentina'),('Armenia'),('Aruba'),('Australia'),('Austria'),('Azerbaijan'),('Bahamas'),('Bahrain'),('Bangladesh'),('Barbados'),('Belarus'),('Belgium'),('Belize'),('Benin'),('Bermuda'),('Bhutan'),('Bolivia'),('Bosnia And Herzegovina'),('Botswana'),('Bouvet Island'),('Brazil'),('British Indian Ocean Territory'),('Brunei Darussalam'),('Bulgaria'),('Burkina Faso'),('Burundi'),('Cambodia'),('Cameroon'),('Canada'),('Cape Verde'),('Cayman Islands'),('Central African Republic'),('Chad'),('Chile'),('China'),('Christmas Island'),('Cocos (keeling) Islands'),('Colombia'),('Comoros'),('Congo'),('Congo'),('Cook Islands'),('Costa Rica'),('Cote D\'ivoire'),('Croatia'),('Cuba'),('Cyprus'),('Czech Republic'),('Denmark'),('Djibouti'),('Dominica'),('Dominican Republic'),('East Timor'),('Ecuador'),('Egypt'),('El Salvador'),('Equatorial Guinea'),('Eritrea'),('Estonia'),('Ethiopia'),('Falkland Islands (malvinas)'),('Faroe Islands'),('Fiji'),('Finland'),('France'),('French Guiana'),('French Polynesia'),('French Southern Territories'),('Gabon'),('Gambia'),('Georgia'),('Germany'),('Ghana'),('Gibraltar'),('Greece'),('Greenland'),('Grenada'),('Guadeloupe'),('Guam'),('Guatemala'),('Guinea'),('Guinea-bissau'),('Guyana'),('Haiti'),('Heard Island And Mcdonald Islands'),('Holy See (vatican City State)'),('Honduras'),('Hong Kong'),('Hungary'),('Iceland'),('India'),('Indonesia'),('Iran'),('Iraq'),('Ireland'),('Israel'),('Italy'),('Jamaica'),('Japan'),('Jordan'),('Kazakstan'),('Kenya'),('Kiribati'),('Korea'),('Korea'),('Kosovo'),('Kuwait'),('Kyrgyzstan'),('Lao People\'s Democratic Republic'),('Latvia'),('Lebanon'),('Lesotho'),('Liberia'),('Libyan Arab Jamahiriya'),('Liechtenstein'),('Lithuania'),('Luxembourg'),('Macau'),('Macedonia'),('Madagascar'),('Malawi'),('Malaysia'),('Maldives'),('Mali'),('Malta'),('Marshall Islands'),('Martinique'),('Mauritania'),('Mauritius'),('Mayotte'),('Mexico'),('Micronesia'),('Moldova'),('Monaco'),('Mongolia'),('Montserrat'),('Montenegro'),('Morocco'),('Mozambique'),('Myanmar'),('Namibia'),('Nauru'),('Nepal'),('Netherlands'),('Netherlands Antilles'),('New Caledonia'),('New Zealand'),('Nicaragua'),('Niger'),('Nigeria'),('Niue'),('Norfolk Island'),('Northern Mariana Islands'),('Norway'),('Oman'),('Pakistan'),('Palau'),('Palestinian Territory'),('Panama'),('Papua New Guinea'),('Paraguay'),('Peru'),('Philippines'),('Pitcairn'),('Poland'),('Portugal'),('Puerto Rico'),('Qatar'),('Reunion'),('Romania'),('Russian Federation'),('Rwanda'),('Saint Helena'),('Saint Kitts And Nevis'),('Saint Lucia'),('Saint Pierre And Miquelon'),('Saint Vincent And The Grenadines'),('Samoa'),('San Marino'),('Sao Tome And Principe'),('Saudi Arabia'),('Senegal'),('Serbia'),('Seychelles'),('Sierra Leone'),('Singapore'),('Slovakia'),('Slovenia'),('Solomon Islands'),('Somalia'),('South Africa'),('South Georgia And The South Sandwich Islands'),('Spain'),('Sri Lanka'),('Sudan'),('Suriname'),('Svalbard And Jan Mayen'),('Swaziland'),('Sweden'),('Switzerland'),('Syrian Arab Republic'),('Taiwan'),('Tajikistan'),('Tanzania'),('Thailand'),('Togo'),('Tokelau'),('Tonga'),('Trinidad And Tobago'),('Tunisia'),('Turkey'),('Turkmenistan'),('Turks And Caicos Islands'),('Tuvalu'),('Uganda'),('Ukraine'),('United Arab Emirates'),('United Kingdom'),('United States'),('United States Minor Outlying Islands'),('Uruguay'),('Uzbekistan'),('Vanuatu'),('Venezuela'),('Viet Nam'),('Virgin Islands'),('Virgin Islands'),('Wallis And Futuna'),('Western Sahara'),('Yemen'),('Zambia'),('Zimbabwe');";
        dbDelta($sql);
    }

    //state table
    $state_table = 'store_locator_state';
    if ($wpdb->get_var("SHOW TABLES LIKE '$state_table'") != $state_table) {
        $sql = "CREATE TABLE " . $state_table . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NULL,
			PRIMARY KEY (`id`)
		) $charset_collate;";
        dbDelta($sql);
        // Insert States
        $sql = "INSERT INTO " . $state_table . " (`name`) VALUES ('Alabama'),('Alaska'),('Arizona'),('Arkansas'),('California'),('Colorado'),('Connecticut'),('Delaware'),('District of Columbia'),('Florida'),('Georgia'),('Hawaii'),('Idaho'),('Illinois'),('Indiana'),('Iowa'),('Kansas'),('Kentucky'),('Louisiana'),('Maine'),('Montana'),('Nebraska'),('Nevada'),('New Hampshire'),('New Jersey'),('New Mexico'),('New York'),('North Carolina'),('North Dakota'),('Ohio'),('Oklahoma'),('Oregon'),('Maryland'),('Massachusetts'),('Michigan'),('Minnesota'),('Mississippi'),('Missouri'),('Pennsylvania'),('Rhode Island'),('South Carolina'),('South Dakota'),('Tennessee'),('Texas'),('Utah'),('Vermont'),('Virginia'),('Washington'),('West Virginia'),('Wisconsin'),('Wyoming');";
        dbDelta($sql);
    }

    //transactions table
    $transactions_table = 'store_locator_transactions';
    if ($wpdb->get_var("SHOW TABLES LIKE '$transactions_table'") != $transactions_table) {
        $sql = "CREATE TABLE " . $transactions_table . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
                        `post_id` bigint(20) NULL,
                        `user_id` bigint(20) NULL,
                        `date` datetime NULL,
			PRIMARY KEY (`id`)
		) $charset_collate;";
        dbDelta($sql);
    }

    // save default options
    $options_to_map = get_option('store_locator_map');
    if(empty($options_to_map)) {
        update_option('store_locator_map',  array ( 'enable' => 1, 'width' => 1500, 'widthunit' => 'PX', 'height' => 550,
            'heightunit' => 'PX', 'type' => 'Roadmap', 'unit' => 'mile', 'radius' => '5,10,[25],50,100,200,500',
            'category' => 1, 'tag' => 1, 'streetViewControl' => 1, 'mapTypeControl' => 1, 'scroll' => 1,
            'marker1' => 'red.png', 'marker2' => 'blue.png',
            'infowindow' => '<div><div>{image}</div><h3>{name}</h3><p>{address} {city}, {state} {country} {zipcode}</p><span>{phone}</span><span>{website}</span><div>',
            'style' => '', 'csize' => 50, 'cluster' => 0,'listing_position' => 'right','total_sponsored'=>3,
            'default_search' => 1,'search_field_get_my_location' => 1,'search_field_location' => 1,'search_field_radius'=>1,
            'search_field_category'=>1,'search_field_tags'=>1,'map_window_open'=>'','map_style'=>1,'listing_position'=>'left',
            'custom_style'=>'','show_accordion'=>'','marker1_custom'=>'','marker2_custom'=>'') );
    }
    $options_to_grid = get_option('store_locator_grid');
    if(empty($options_to_grid)) {
        update_option('store_locator_grid', array('enable' => 1, 'number' => 10, 'scroll' => 1, 'columns' => array("name", "address"), 'view' => 'card'));
    }
}


//create 'partner' custom post type
add_action('init', 'store_init');
function store_init() {
	
	$store_locator_single = get_option('store_locator_single',true);
	$store_locator_slug = '';
	
	if(isset( $store_locator_single['store_locator_slug'] )) {
		$store_locator_slug = $store_locator_single['store_locator_slug'];
	}
	
	if(empty($store_locator_slug))
		$store_locator_slug = 'store-locator';
	
    $labels = array(
        'name' => __('Stores Locator', 'store_locator'),
        'singular_name' => __('Stores Locator', 'store_locator'),
        'menu_name' => __('Stores Locator', 'store_locator'),
        'name_admin_bar' => __('Store', 'store_locator'),
        'add_new' => __('Add New Store', 'store_locator'),
        'add_new_item' => __('Add New Store', 'store_locator'),
        'new_item' => __('New Store', 'store_locator'),
        'edit_item' => __('Edit Store', 'store_locator'),
        'view_item' => __('View Store', 'store_locator'),
        'all_items' => __('Stores List', 'store_locator'),
        'search_items' => __('Search Store', 'store_locator'),
        'parent_item_colon' => __('Store Partner:', 'store_locator'),
        'not_found' => __('No Store found.', 'store_locator'),
        'not_found_in_trash' => __('No Store found in Trash.', 'store_locator')
    );
    $single_options = get_option('store_locator_single');
    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'store_locator'),
        'public' => (($single_options['page'])?true:false),
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
        'name' => __('Sales Managers', 'store_locator'),
        'singular_name' => __('Sales Managers', 'store_locator'),
        'menu_name' => __('Sales Managers', 'store_locator'),
        'name_admin_bar' => __('Book', 'store_locator'),
        'add_new' => __('Add New', 'store_locator'),
        'add_new_item' => __('Add New Sales Manager', 'store_locator'),
        'new_item' => __('New Sales Manager', 'store_locator'),
        'edit_item' => __('Edit Sales Manager', 'store_locator'),
        'view_item' => __('View Sales Manager', 'store_locator'),
        'all_items' => __('Sales Managers List', 'store_locator'),
        'search_items' => __('Search Manager', 'store_locator'),
        'parent_item_colon' => __('Parent Manager:', 'store_locator'),
        'not_found' => __('No Sales Manager found.', 'store_locator'),
        'not_found_in_trash' => __('No Sales Manager found in Trash.', 'store_locator')
    );

    $argsSM = array(
        'labels' => $labelsSM,
        'description' => __('Description.', 'store_locator'),
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

    register_post_type('store_locator', $args);
    register_post_type('sales_manager', $argsSM);

    // create custom category for stores
    register_taxonomy( 'store_locator_category', array('store_locator'), array(
            'hierarchical' => true,
            'label' => __('Store Categories', 'store_locator'),
            'singular_label' => __('Category', 'store_locator'),
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
            'label'         => __("Store Tags", 'store_locator'),
            'singular_name' => __("Tag", 'store_locator'),
            'rewrite'       => true,
            'query_var'     => true
        )
    );
}


// Load translation files if exists
add_action( 'plugins_loaded', 'store_locator_load_plugin_textdomain' );
function store_locator_load_plugin_textdomain() {
    load_plugin_textdomain( 'store_locator', false, 'store-locator/languages' );
}


// add submenu page
add_action('admin_menu', 'register_stores_submenu_page');
function register_stores_submenu_page() {
    add_submenu_page('edit.php?post_type=store_locator', 'Sales Manager', 'Sales Managers List', 'manage_options', 'edit.php?post_type=sales_manager');
    add_submenu_page('edit.php?post_type=store_locator', 'Settings', 'Settings', 'manage_options', 'store_locator_settings_page', 'store_locator_settings_page_callback');
}


add_action('admin_menu', 'disable_new_stores_posts');
function disable_new_stores_posts() {
    global $submenu;
    unset($submenu['edit.php?post_type=store_locator'][10]);
}


//add scripts to backend
add_action('admin_enqueue_scripts', 'store_locator_backend_script');
function store_locator_backend_script() {
    ?>
    <script>
        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
    $store_locator_API_KEY = get_option('store_locator_API_KEY');

	$post_type = get_post_type( get_the_ID() );
  
	wp_enqueue_style('store_lcoator_backend-style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/style.css');	
	if( $post_type  == 'store_locator' || $_GET['page'] == 'store_locator_settings_page' ) {	 
		wp_enqueue_media();
		wp_enqueue_script('store_locator_backend_map', "https://maps.googleapis.com/maps/api/js?key=".$store_locator_API_KEY."&libraries=places");
		wp_enqueue_script('store_locator_backend_script',  STORE_LOCATOR_PLUGIN_URL . '/assets/js/backend_script.js', array('jquery'));
		wp_enqueue_script('store_backend_select2', STORE_LOCATOR_PLUGIN_URL . '/assets/js/select2.js');
		wp_enqueue_style('store_backend_select2_style', STORE_LOCATOR_PLUGIN_URL . '/assets/css/select2.css');
		wp_enqueue_script('ldm_script_time_js', STORE_LOCATOR_PLUGIN_URL . 'assets/js/jquery.timepicker.js');
		wp_enqueue_style('ldm_script_time_css', STORE_LOCATOR_PLUGIN_URL . 'assets/css/jquery.timepicker.css');
	}
}


//add scripts to frontend
add_action('wp_enqueue_scripts', 'store_frontend_script',200);
function store_frontend_script() {
    ?>
    <script>
        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
	$setting_options = get_option('store_locator_map');
	
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
//    wp_enqueue_style('store_frontend_script_cluster', STORE_LOCATOR_PLUGIN_URL . '/assets/js/map_cluster.js');
}


//create custom fields
add_action('add_meta_boxes', 'add_store_locator_meta');
function add_store_locator_meta() {
    add_meta_box('store-info', 'Store Info', 'store_locator_meta_box_callback_store_info', 'store_locator');
    add_meta_box('address-info', 'Address Info', 'store_locator_meta_box_callback_address_info', 'store_locator');

    if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
        add_meta_box('gforms-info', 'Gravity Forms Info', 'store_locator_meta_box_callback_gforms_info', 'store_locator');
    }

    add_meta_box('sales-manager-info', 'Sales Manager Info', 'sales_managers_meta_box_callback','sales_manager');
}

function store_locator_meta_box_callback_store_info($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('store_locator_save_meta_box_data', 'store_locator_meta_box_nonce');
    $post_id = $post->ID;
    ?>
    <table class="widefat" style="border: 0px;">
        <tbody>
        <tr>
            <td><?php echo __("Store Locator Shortcode", 'store_locator_Shortcode'); ?></td>
            <td>
                <input type="text" readonly value="&nbsp;&nbsp;&nbsp;[store_locator_show]" name="store_locator_show"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Code", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'store_locator_code', true) ? get_post_meta($post_id, 'store_locator_code', true) : ''; ?>" name="store_locator_code"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Name", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'store_locator_name', true) ? get_post_meta($post_id, 'store_locator_name', true) : ''; ?>" name="store_locator_name"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Description", 'store_locator'); ?></td>
            <td>
                <?php
                $content = get_post_meta( $post_id, 'store_locator_description', true );
                wp_editor( $content, "store_locator_description" );
                ?>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Phone", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'store_locator_phone', true) ? get_post_meta($post_id, 'store_locator_phone', true) : ''; ?>" name="store_locator_phone"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Fax", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'store_locator_fax', true) ? get_post_meta($post_id, 'store_locator_fax', true) : ''; ?>" name="store_locator_fax"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Website", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'store_locator_website', true) ? get_post_meta($post_id, 'store_locator_website', true) : ''; ?>" name="store_locator_website"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Working Hours", 'store_locator'); ?></td>
            <?php
            $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
            $days_meta = get_post_meta($post_id, 'store_locator_days', true);
            ?>
            <td>
                <table id="store_locator_hours" style="background-color: rgb(241, 241, 241); border-radius: 5px;">
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
            <td><?php echo __("Sales Managers", 'store_locator'); ?></td>
            <td>
                <select style="width: 186px;" name="store_locator_sales[]" id="store_locator_sales" multiple="multiple">
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
                    foreach ($allSales as $manager) {
                        ?>
                        <option value="<?php echo $manager->ID; ?>" <?php echo (in_array($manager->ID, $selectedSales)) ? "selected" : ""; ?>><?php echo $manager->post_title; ?></option>
                        <?php
                    }
                    ?>
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

function store_locator_meta_box_callback_address_info($post) {
    $post_id = $post->ID;
    ?>
    <table class="widefat" style="border: 0px;">
        <tbody>
        <tr>
            <td><?php echo __("Address", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_address" type="text" value="<?php echo get_post_meta($post_id, 'store_locator_address', true); ?>" name="store_locator_address"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Country", 'store_locator'); ?></td>
            <td>
                <select style="width: 186px;" name="store_locator_country" id="store_locator_country">
                    <option value="" ></option>
                    <?php
                    global $wpdb;
                    $allCountries = $wpdb->get_results("SELECT * FROM store_locator_country");
                    $selectedCountry = get_post_meta($post_id, 'store_locator_country', true);
                    foreach ($allCountries as $country) {
                        ?>
                        <option value="<?php echo $country->name; ?>" <?php echo ($selectedCountry == $country->name) ? "selected" : ""; ?>><?php echo $country->name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr <?php echo ($selectedCountry != "United States")?"style='display: none;'":""; ?> >
            <td><?php echo __("State", 'store_locator'); ?></td>
            <td>
                <select style="width: 186px;" name="store_locator_state" id="store_locator_state">
                    <option value="" ></option>
                    <?php
                    global $wpdb;
                    $allStates = $wpdb->get_results("SELECT * FROM store_locator_state");
                    $selectedState = get_post_meta($post_id, 'store_locator_state', true);
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
            <td><?php echo __("City", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_city" type="text" value="<?php echo get_post_meta($post_id, 'store_locator_city', true) ? get_post_meta($post_id, 'store_locator_city', true) : ''; ?>" name="store_locator_city"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Postal Code", 'store_locator'); ?></td>
            <td>
                <input id="store_locator_zipcode" type="text" value="<?php echo get_post_meta($post_id, 'store_locator_zipcode', true) ? get_post_meta($post_id, 'store_locator_zipcode', true) : ''; ?>" name="store_locator_zipcode"/>
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
            store_locator_initializeMapBackend();
        });
    </script>
    <?php
}

function store_locator_meta_box_callback_gforms_info($post) {
    $post_id = $post->ID;
    ?>
    <table class="widefat" style="border: 0px;">
        <tbody>
        <tr>
            <td><?php echo __("Select Gravity Form", 'store_locator'); ?></td>
            <td>
                <select style="width: 186px;" name="store_locator_gform" id="store_locator_gforms" >
                    <option value=""></option>
                    <?php

                    $forms = RGFormsModel::get_forms( null, 'title' );
                    $selectedForm = get_post_meta($post_id, 'store_locator_gform', true);

                    foreach ($forms as $form) {
                        ?>
                        <option value="<?php echo $form->id; ?>" <?php echo ($form->id == $selectedForm) ? "selected" : ""; ?>><?php echo $form->title; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}



function sales_managers_meta_box_callback($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('sales_manager_save_meta_box_data', 'sales_manager_meta_box_nonce');
    $post_id = $post->ID;
    ?>
    <table class="widefat" style="border: 0px;">
        <tbody>
        <tr>
            <td><?php echo __("Identification", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_id', true) ? get_post_meta($post_id, 'sales_manager_id', true) : ''; ?>" name="sales_manager_id"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Title", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_title', true) ? get_post_meta($post_id, 'sales_manager_title', true) : ''; ?>" name="sales_manager_title"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Name", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_name', true) ? get_post_meta($post_id, 'sales_manager_name', true) : ''; ?>" name="sales_manager_name"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("Phone", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_phone', true) ? get_post_meta($post_id, 'sales_manager_phone', true) : ''; ?>" name="sales_manager_phone"/>
            </td>
        </tr>
        <tr>
            <td><?php echo __("E-Mail", 'store_locator'); ?></td>
            <td>
                <input type="text" value="<?php echo get_post_meta($post_id, 'sales_manager_email', true) ? get_post_meta($post_id, 'sales_manager_email', true) : ''; ?>" name="sales_manager_email"/>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}


//save custom fields
add_action('save_post', 'store_locator_save_meta_box_data');
function store_locator_save_meta_box_data($post_id) {
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
        remove_action('save_post', 'store_locator_save_meta_box_data');
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
        add_action('save_post', 'store_locator_save_meta_box_data');

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
        remove_action('save_post', 'store_locator_save_meta_box_data');
        $my_post = array('ID' => $post_id, 'post_title' => $_POST['sales_manager_name']);
        wp_update_post($my_post);
        add_action('save_post', 'store_locator_save_meta_box_data');

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


//manage custom coulmns display for stores
add_filter('manage_edit-store_locator_columns', 'store_list_columns');
function store_list_columns($columns) {
    unset(
        $columns['date']
    );
    $new_columns = array(
        'title' => __('Name', 'store_locator'),
        'store_address' => __('Address', 'store_locator'),
        'store_sales' => __('Sales Managers', 'store_locator'),
    );
    return array_merge($columns, $new_columns);
}


//manage custom coulmns content display for Stores
add_filter('manage_store_locator_posts_custom_column', 'manage_stores_columns', 10, 2);
function manage_stores_columns($column, $post_id) {
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


//manage custom coulmns display for sales managers
add_filter('manage_edit-sales_manager_columns', 'sales_manager_list_columns');
function sales_manager_list_columns($columns) {
    unset(
        $columns['date']
    );
    $new_columns = array(
        'title' => __('Name', 'store_locator'),
        'sales_title' => __('Title', 'store_locator'),
        'sales_phone' => __('Phone', 'store_locator'),
        'sales_email' => __('Email', 'store_locator'),
    );
    return array_merge($columns, $new_columns);
}


//manage custom coulmns content display for Sales
add_filter('manage_sales_manager_posts_custom_column', 'manage_sales_manager_columns', 10, 2);
function manage_sales_manager_columns($column, $post_id) {
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


// add filters to the query
add_filter('parse_query', 'custom_posts_filter');
function custom_posts_filter($query) {
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


// handle the dislay of new fillters
add_action('restrict_manage_posts', 'custom_posts_restrict_manage_posts');
function custom_posts_restrict_manage_posts() {
    $currentManager = isset($_GET['store_locator_manager']) ? $_GET['store_locator_manager'] : '';
    ?>
    <?php if (isset($_GET['post_type']) && $_GET['post_type'] == 'store_locator'): ?>
        <select style="width: 186px;" name="store_locator_manager" >
            <option value=""> <?php echo __("All Sales Managers", 'store_locator'); ?></option>
            <?php
            global $wpdb;
            $q = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='sales_manager' AND post_status = 'publish' order by 'ID' Desc");

            foreach ($q as $manager) {
                ?>
                <option value="<?php echo $manager->ID; ?>" <?php echo ($manager->ID == $currentManager) ? "selected" : ""; ?>><?php echo $manager->post_title; ?></option>
                <?php
            }
            ?>
        </select>
    <?php endif; ?>
    <?php
}


add_shortcode('store_locator_show', 'store_locator_show_func');
function store_locator_show_func($atts) {

    ob_start();
    $map_options = get_option('store_locator_map');
    $grid_options = get_option('store_locator_grid');
    $radius = ($map_options['radius'])?explode(",",trim($map_options['radius'])):false;
    $tag = $map_options['tag'];
    $category = $map_options['category'];

    $map_options['marker1'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . $map_options['marker1'];
    $map_options['marker2'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . $map_options['marker2'];
//    $map_options['marker3'] = STORE_LOCATOR_PLUGIN_URL . "assets/img/" . $map_options['marker3'];

    if(!empty($map_options['marker1_custom'])) {
        $map_options['marker1'] = $map_options['marker1_custom'];
    }

    if(!empty($map_options['marker2_custom'])) {
        $map_options['marker2'] = $map_options['marker2_custom'];
    }

    $default_radius = 50;
    preg_match("/^(.*\[)(.*)(\])/", $map_options['radius'], $find);
    $default_radius = trim($find[2]);

    // Attributes
    $atts = shortcode_atts(
        array(
            'location' => 'United States',
            'radius' => $default_radius,
            'city' => '',
            'state' => '',
        ),
        $atts
    );

    if (is_ssl()) {
        $placeholder_setting = get_option('placeholder_settings');
        $btn = $placeholder_setting['get_location_btn_txt'];
        if(empty($btn));
            $btn='Get my location';

        $display = '';
    } else {
        $btn='Get my location ssl must be activated';
        $display = 'style="display:none;"';
    }

    $state = (!empty($atts['state'])) ? ', ' . $atts['state']  : '';

    $address = $atts['location'] .' '. $atts['city'] . $state;
//	setcookie('default_location', $address, time() + (86400 * 30), "/");
//    setcookie('default_radius', $atts['radius'], time() + (86400 * 30), "/");
    ?>
    <script>
        var store_locator_map_options  =  <?php echo json_encode($map_options); ?>;
        var store_locator_grid_options =  <?php echo json_encode($grid_options); ?>;

        setTimeout(function() {
            wpmsl_update_map('<?php echo $address ?>','<?php echo $atts['radius']?>');
            jQuery('#store_locatore_search_input').val('<?php echo $address?>');
            jQuery('#store_locatore_search_radius option[value="<?php echo $atts['radius']?>"]').prop('selected', true);
        },2000);
    </script>

    <script type='text/javascript' src='<?php echo STORE_LOCATOR_PLUGIN_URL . '/assets/js/frontend_script.js'; ?>'></script>
    <script type="text/javascript" src="//rawgit.com/googlemaps/v3-utility-library/master/infobox/src/infobox.js"></script>

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

        <?php
        echo do_action('wpmsl_before_map');?>

        <div class="loader"><div>

                <?php if(!empty($map_options['default_search'])) {?>
                    <div class="col-left leftsidebar slide-left <?php echo $map_options['listing_position'].'-skip'?>">

                        <?php
						$map_window_open = '';
						if(isset($map_options['map_window_open'])) {
							$map_window_open = $map_options['map_window_open'];
							if(!empty($map_window_open))
								$map_window_open = 'show';
						}
                        ?>
                        <div class="search-options-btn"><?php _e('Search Options','wpmsl')?></div>

                        <?php
                        $placeholder_settings = get_option('placeholder_settings');
                        ?>
                        <div class="store-search-fields <?php echo $map_window_open?>">
                            <form id="store_locator_search_form" >
                                <input id="store_locatore_get_btn" class="<?php echo $map_options['search_field_get_my_location']?>"  type="button" value="<?php  echo __($btn, 'store_locator'); ?>"  <?php echo $display; ?>  />
                                <!-- <span class="closesidebar " style="width:18px; height:18px;display: inline-block;
                    
                        cursor: pointer;"></span> -->
                                <input id="store_locatore_search_input"  class="wpsl_search_input <?php echo $map_options['search_field_location']?>" name="store_locatore_search_input" type="text" placeholder="<?php echo ($placeholder_settings['enter_location_txt'] == ''? _e('Location / Zip Code','wpmsl') :$placeholder_settings['enter_location_txt']); ?>">
                                <input id="store_locatore_search_lat" name="store_locatore_search_lat" type="hidden" >
                                <input id="store_locatore_search_lng" name="store_locatore_search_lng" type="hidden" >
                                <div>
                                    <?php if($radius): ?>
                                        <select id="store_locatore_search_radius" name="store_locatore_search_radius" class="wpsl_search_radius <?php echo $map_options['search_field_radius']?>">
                                            <?php foreach ($radius as $option): ?>
                                                <?php
                                                $default = (preg_match("/\[[^\]]*\]/", $option))?true:false;
                                                $option = str_replace(array('[',']'), array('',''), $option);
                                                ?>
                                                <option value="<?php echo $option; ?>" <?php echo ($default)?"selected":"" ?> ><?php echo $option." ".$map_options['unit'] ; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>

                                    <?php
                                    $terms = get_terms( 'store_locator_category', array('hide_empty' => 0));
                                    if ( $category ): ?>
                                        <select name="store_locator_category" id="wpsl_store_locator_category" class="wpsl_locator_category <?php echo $map_options['search_field_category']?>">
                                            <option value=""> <?php echo ($placeholder_settings['select_category_txt'] == ''? _e("Select Category","wpmsl") :$placeholder_settings['select_category_txt']); ?> </option>
                                            <?php foreach ( $terms as $term ) : ?>
                                                <option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>

                                    <?php
                                    $terms = get_terms( 'store_locator_tag', array('hide_empty' => 0));
                                    if ( $tag ): ?>
                                        <select placeholder="<?php echo ($placeholder_settings['search_field_tags'] == ''? _e('Select Tags','wpmsl'):$placeholder_settings['search_field_tags']); ?>" name="store_locator_tag[]" class="wpsl_locator_category <?php echo $map_options['search_field_tags']?>" id="store_locator_tag" multiple="multiple">
                                            <?php foreach ( $terms as $term ) : ?>
                                                <option value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <input id="store_locatore_search_btn" type="submit" value="<?php  _e("Search", 'wpmsl'); ?>" />
                            </form>
                        </div>

                    </div>
                <?php }?>
                <div class="col-right right-sidebar">
                    <div id="map-container" style="position: relative;width: 100%;right: 0%;" class="<?php echo $map_options['listing_position']?>">
                        <div id="map_loader" style="display:none;z-index: 9;height: <?php echo $map_options['height'].$map_options['heightunit']; ?>;width: <?php echo $map_options['width'].'%'; ?>;position: absolute;"><div class="uil-ripple-css" style="transform: scale(0.6); margin-left: auto; margin-right: auto;"><div></div><div></div></div></div>
                        <div id="store_locatore_search_results"></div>
                    </div>
                    <?php 
					if( !empty( $grid_options['enable'] ) ) {						
					?>
						<div class="map-listings <?php echo $map_options['listing_position']?>" style="height: <?php echo $map_options['height']?>px">
						</div>
					<?php } ?>      
                    <div class="show-more">Show More</div>
                </div>

            </div>
            <script>
                // adding class in content div
                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
                jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
                jQuery( ".loader" ).append( '<img class="load-img" src="<?php echo apply_filters('wpmsl_loading_img',STORE_LOCATOR_PLUGIN_URL.'assets/img/loader.gif'); ?>" width="350" height="350" >' );
                // jQuery( ".map-listings" ).append( '<input type="button" value="Show More" class="show-more"/>' );
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
    do_action('wpmsl_end_shortcode',$address,$atts['radius']);
    return ob_get_clean();
}

// Do Search Ajax
add_action('wp_ajax_make_search_request', 'make_search_request');
add_action('wp_ajax_nopriv_make_search_request', 'make_search_request');
function make_search_request() {

//    $address    = $_POST['store_locator_address'];
    global $wpdb;
    $map_options  = get_option('store_locator_map');
    $grid_options = get_option('store_locator_grid');
    $center_lat   = $_POST['lat'];
    $center_lng   = $_POST['lng'];
    $radius       = (isset($_POST["store_locatore_search_radius"]))?("HAVING distance < ".$_POST["store_locatore_search_radius"]):"";
    $unit         = ( $map_options['unit'] == 'km' ) ? 6371 : 3959;
    $stores       = array();



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
//    echo $tag_filter;
//    echo $cat_filter;

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
                      AND posts.post_status = 'publish' GROUP BY posts.ID $radius ORDER BY distance LIMIT 0,40"

    );

//    $stores_query= '';

    $stores = apply_filters('wpmsl_store_query',$stores_query,$strore_post_type,$strore_lat,$strore_lng,$center_lat,$center_lng,$unit,$radius);

    include STORE_LOCATOR_PLUGIN_PATH . 'views/results.php';
    wp_die();
}

// Settings Page
function store_locator_settings_page_callback() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = array_map( 'stripslashes_deep', $_POST );
        //handle save map settings
        if (isset($_POST['map-settings'])) {
            update_option('store_locator_map', $_POST['store_locator_map']);
        }

        //handle save api settings
        if (isset($_POST['api-settings'])) {
            update_option('store_locator_API_KEY', $_POST['store_locator_API_KEY']);
            update_option('store_locator_street_API_KEY', $_POST['store_locator_street_API_KEY']);
        }

        //handle save grid settings
        if (isset($_POST['grid-settings'])) {
            $_POST['store_locator_grid']['columns'] = explode(",", $_POST['store_locator_grid']['columns']);
            update_option('store_locator_grid', $_POST['store_locator_grid']);
        }

        //handle save single page settings
        if (isset($_POST['single-settings'])) {
            $_POST['store_locator_single']['items'] = explode(",", $_POST['store_locator_single']['items']);
            update_option('store_locator_single', $_POST['store_locator_single']);
        }

        //handle save single page settings
        if (isset($_POST['placeholder-setting'])) {
            $placeholders = array();
            $placeholders['get_location_btn_txt'] = $_POST['get_location_btn_txt'];
            $placeholders['enter_location_txt'] = $_POST['enter_location_txt'];
            $placeholders['select_category_txt'] = $_POST['select_category_txt'];
            $placeholders['select_tags_txt'] = $_POST['select_tags_txt'];
            update_option('placeholder_settings',$placeholders);
        }
    }

    $store_locator_API_KEY  = get_option('store_locator_API_KEY');
    $store_locator_street_API_KEY  = get_option('store_locator_street_API_KEY');
    $map_options  = get_option('store_locator_map');
    $grid_options = get_option('store_locator_grid');
    $single_options = get_option('store_locator_single');
    $placeholder_setting = get_option('placeholder_settings');

    include STORE_LOCATOR_PLUGIN_PATH . 'views/settings.php';
}

function sample_admin_notice__error($array) {
    $class = 'notice notice-error';
    // $error = '<p>For get your current location SSL must required</p>';


    if(!empty(admin_notice_array($array))){


        foreach(admin_notice_array($array) as $key => $value){
            if(
                //condition one is site must be ssl.
                !is_ssl() and $key == 'wp-locator-ssl-error'
            ){
                $message = __( $value, $key );
                printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
            }
        }
    }
    $store_locator_API_KEY = get_option('store_locator_API_KEY');
    if(empty($store_locator_API_KEY)){
        $message = __( 'Must Provide Google Map Api for Search Location via google map <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">Click here to Create Google Map Api Key.</a>', 'wp-locator-map-api-error' );
        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }

}
add_action( 'admin_notices', 'sample_admin_notice__error',10,1 );

function admin_notice_array($array){
    Return array(
        'wp-locator-ssl-error' => '<p>Get Current location functionality is disabled For Store Locator Plugin. You must enable SSL for your domain in order to enable this functionality.</p>',
    );
}

// export csv
add_action('admin_post_printStores.csv', 'export_store_locator_csv');
function export_store_locator_csv() {
    if (!current_user_can('manage_options'))
        return;

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=Stores.csv');
    header('Pragma: no-cache');
    $output_handle = @fopen('php://output', 'w');

    $args = array(
        'post_type' => 'store_locator',
        'post_status' => 'publish',
        'posts_per_page' => -1
    );
    $csv_fields = array('Code', 'Name', 'Address', 'Country', 'City', 'State', 'Phone', 'Fax', 'Website', 'Zipcode');
    fputcsv($output_handle, $csv_fields);
    $my_query = new WP_Query($args);
    if ($my_query->have_posts()) {
        while ($my_query->have_posts()) : $my_query->the_post();
            global $post;
            $csv_fields = array(
                get_post_meta($post->ID, 'store_locator_code', true),
                get_post_meta($post->ID, 'store_locator_name', true),
                get_post_meta($post->ID, 'store_locator_address', true),
                get_post_meta($post->ID, 'store_locator_country', true),
                get_post_meta($post->ID, 'store_locator_city', true),
                get_post_meta($post->ID, 'store_locator_state', true),
                get_post_meta($post->ID, 'store_locator_phone', true),
                get_post_meta($post->ID, 'store_locator_fax', true),
                get_post_meta($post->ID, 'store_locator_website', true),
                get_post_meta($post->ID, 'store_locator_zipcode', true),
            );
            fputcsv($output_handle, $csv_fields);
        endwhile;
    }
    wp_reset_query();


// Close output file stream
    fclose($output_handle);

    die();
}


add_action('admin_post_printSales.csv', 'export_sales_manager_csv');
function export_sales_manager_csv() {
    if (!current_user_can('manage_options'))
        return;

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=Sales_Managers.csv');
    header('Pragma: no-cache');
    $output_handle = @fopen('php://output', 'w');

    $args = array(
        'post_type' => 'sales_manager',
        'post_status' => 'publish',
        'posts_per_page' => -1
    );
    $csv_fields = array('Code', 'Title', 'Name', 'Phone','Email');
    fputcsv($output_handle, $csv_fields);
    $my_query = new WP_Query($args);
    if ($my_query->have_posts()) {
        while ($my_query->have_posts()) : $my_query->the_post();
            global $post;
            $csv_fields = array(
                get_post_meta($post->ID, 'sales_manager_id', true),
                get_post_meta($post->ID, 'sales_manager_title', true),
                get_post_meta($post->ID, 'sales_manager_name', true),
                get_post_meta($post->ID, 'sales_manager_phone', true),
                get_post_meta($post->ID, 'sales_manager_email', true),
            );
            fputcsv($output_handle, $csv_fields);
        endwhile;
    }
    wp_reset_query();


// Close output file stream
    fclose($output_handle);

    die();
}


// get Statistics Ajax
add_action('wp_ajax_show_store_statistics', 'show_store_statistics');
function show_store_statistics() {
    $store_id = NULL;
    $transactions = array();
    if(isset($_POST['store_id']) && $_POST['store_id']){
        $store_id = $_POST['store_id'];
        global $wpdb;
        $transactions = $wpdb->get_results("SELECT DATE_FORMAT(date, '%Y-%m-%d') as date, COUNT(*) as total_count FROM store_locator_transactions WHERE post_id=".$store_id." GROUP BY DATE_FORMAT(date, '%Y-%m-%d')");
        $piDataQuery = $wpdb->get_results("SELECT COUNT(*) as total_count, user_id as user FROM store_locator_transactions WHERE post_id=".$store_id." GROUP BY user_id");
        $piData = array(array('user'=>'Visitor', 'total_count'=> 0),array('user'=>'Registered Users', 'total_count'=> 0));
        foreach ($piDataQuery as $record) {
            if($record->user == 0){
                $piData[0]['total_count'] += $record->total_count;
            }else{
                $piData[1]['total_count'] += $record->total_count;
            }
        }
//    echo $store_id.'<br><pre>';
//    print_r($piData);
//    exit;
        include STORE_LOCATOR_PLUGIN_PATH . 'views/statistics_single_store.php';
    }
    wp_die();
}

add_action('wp_dashboard_setup', 'store_locator_custom_dashboard_widgets');

function store_locator_custom_dashboard_widgets() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('store_locator_custom_dashboard_widget', __('Top Search for Stores'), 'store_locator_custom_dashboard_widget_callback');
}


function store_locator_custom_dashboard_widget_callback() {
    global $wpdb;
    $posts_table = $wpdb->prefix . 'posts';
    $transactions = $wpdb->get_results("SELECT ps.post_title as store, count(tr.post_id) as total_count FROM $posts_table ps LEFT JOIN store_locator_transactions tr ON tr.post_id=ps.ID WHERE ps.post_type='store_locator' AND ps.post_status='publish' GROUP BY ps.ID ORDER BY total_count DESC LIMIT 3");
    if($transactions){
        ?>
        <table class="store_locator_data_dashboard">
            <tr>
                <th><?php echo __("Store Name", 'store_locator'); ?></th>
                <th><?php echo __("Hits", 'store_locator'); ?></th>
            </tr>
            <?php foreach ($transactions as $store): ?>
                <tr>
                    <td><?php echo $store->store; ?></td>
                    <td><?php echo $store->total_count; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <a href="<?php echo admin_url('edit.php?post_type=store_locator&page=statistics_submenu_page'); ?>" ><?php echo __("See more ...","store_locator"); ?></a>
        <?php
    }else{
        echo "<div class='store_locator_nodata_dashboard'>"._("No Data found yet.")."</div>";
    }
}


add_filter( 'template_include', 'store_locator_single_id_template', 99 );
function store_locator_single_id_template( $template ) {
    $post_id = get_the_ID();
    $post = get_post($post_id);

    if ( is_single() &&  $post->post_type == "store_locator" ) {
        $template = STORE_LOCATOR_PLUGIN_PATH . 'templates/single-store_locator.php';
    }

    return $template;
}




add_filter( 'gform_submit_button', 'add_paragraph_below_submit', 10, 2 );
function add_paragraph_below_submit( $button, $form ) {

    return $button = "<p>your <a href='http://yourlink.com'>text</a> goes here</p>" . $button;
}

add_action( 'gform_after_submission', 'store_locator_gf_after_submission', 10, 2 );

function store_locator_gf_after_submission( $entry, $form ) {

    if( empty($_POST['gform_field_values']) ) {
        return;
    }

    parse_str($_POST['gform_field_values'], $field_values);

    if( empty($field_values['store_id']) ) {
        return;
    }

    $store_id = $field_values['store_id'];

    //getting post
    $post = get_post( $store_id );

    if($post->post_type != 'store_locator') {
        return;
    }

    $message    = __('This message would tell you that new form entry has been submitted.');
    $message   .= __('Title: ').rgar($entry, '1');

    $sales = get_post_meta($store_id, 'store_locator_sales', true);

    if($sales){
        foreach ($sales as $manager) {
            // get manager email
            $manager_email = get_post_meta($manager, 'sales_manager_email', true);

            if( !empty($manager_email) ) {
                wp_mail($manager_email, 'Store locator form', $message);
            }
        }
    }
}