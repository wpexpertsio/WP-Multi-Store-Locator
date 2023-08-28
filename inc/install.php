<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}  
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
        update_option('store_locator_map',  array ( 'enable' => 1, 'width' => 100, 'widthunit' => '%', 'height' => 550,
            'heightunit' => 'PX', 'type' => 'Roadmap', 'unit' => 'mile', 'radius' => '5,10,[25],50,100,200,500',
            'category' => 1, 'tag' => 1, 'streetViewControl' => 1, 'mapTypeControl' => 1, 'scroll' => 1,
            'marker1' => 'red.png', 'marker2' => 'blue.png',
            'infowindow' => '<div><div>{image}</div><h3>{name}</h3><p>{address} {city}, {state} {country} {zipcode}</p><span>{phone}</span><span>{website}</span><div>',
            'style' => '', 'csize' => 50, 'cluster' => 0,'total_sponsored'=>3,
            'default_search' => 1,'search_field_get_my_location' => 1,'search_field_location' => 1,'search_field_radius'=>1,
            'search_field_category'=>1,'search_field_tags'=>1,'map_window_open'=>'','map_style'=>1,'listing_position'=>'left',
            'custom_style'=>'','show_accordion'=>'','marker1_custom'=>'','marker2_custom'=>'', 'search_layouts'=>'onmap') );
    }
    $options_to_grid = get_option('store_locator_grid');
    if(empty($options_to_grid)) {
        update_option('store_locator_grid', array('enable' => 1, 'total_markers' => 10000, 'scroll' => 1, 'columns' => array("name", "address"), 'view' => 'card','listing_position'=>'left','search_window_position'=>'wpml_search_right'));
    } else{
		$options_to_grid = get_option('store_locator_grid',true);
		$options_to_grid['listing_position'] = 'left';
		$options_to_grid['search_window_position'] = 'wpml_search_right';
		update_option('store_locator_grid', $options_to_grid );
	}
     $placeholders = get_option('placeholder_settings');
    if(empty($placeholders)) {
         $placeholders['location_not_found'] = __('No details available for input:','store_locator');
         $placeholders['select_tags_txt'] = __('Select tags','store_locator');
         $placeholders['select_category_txt'] = __('Select category','store_locator');
         update_option('placeholder_settings',$placeholders);
    } else{ 
		$placeholders['location_not_found'] = __('No details available for input:','store_locator');
		$placeholders['search_options_btn'] = __('Search Options','store_locator');
		update_option('placeholder_settings',$placeholders);
	}