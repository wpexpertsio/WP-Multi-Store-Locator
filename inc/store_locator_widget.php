<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Creating nearby widget 
class wpmsl_nearby_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'wpmsl_widget', __('Stor Locator Nearby Stores', 'wpmsl'), array('description' => __('Display nearby stores to website visitor', 'wpmsl'),)
        );
    }

    // This is where the action happens
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $distance = $instance['distance'];
        $limit = $instance['number_of_stores'];

			wp_enqueue_script('store_locator_widgetjs',  STORE_LOCATOR_PLUGIN_URL . '/assets/js/widgetjs.js', array('jquery'));
			wp_localize_script( 'store_locator_widgetjs', 'wpmsl_widgetjs',
				array( 
					'distance' => $distance,
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'wpmsl_widgetjs_nonce' =>  wp_create_nonce( "wpmsl-widgetjs156951357753456654" ),
					'limit' => $limit
				)
			);	



        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        echo $args['before_title'].$title.$args['after_title'];


        echo "<div id='store_locator_widget_results'></div>";


        echo $args['after_widget'];
    }


    // Widget Backend 
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Nearest Stores', 'store_locator');
        }
        if (isset($instance['number_of_stores'])) {
            $number_of_stores = $instance['number_of_stores'];
        } else {
            $number_of_stores = 3;
        }
        if (isset($instance['distance'])) {
            $distance = $instance['distance'];
        } else {
            $distance = 50;
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            <label for="<?php echo $this->get_field_id('number_of_stores'); ?>"><?php _e('Number of displayed Stores:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('number_of_stores'); ?>" name="<?php echo $this->get_field_name('number_of_stores'); ?>" type="text" value="<?php echo esc_attr($number_of_stores); ?>" />
            <label for="<?php echo $this->get_field_id('distance'); ?>"><?php _e('Distance Within(miles):'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('distance'); ?>" name="<?php echo $this->get_field_name('distance'); ?>" type="text" value="<?php echo esc_attr($distance); ?>" />
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['number_of_stores'] = (!empty($new_instance['number_of_stores']) ) ? strip_tags($new_instance['number_of_stores']) : '';
        $instance['distance'] = (!empty($new_instance['distance']) ) ? strip_tags($new_instance['distance']) : '';
        return $instance;
    }

    static function get_nearby_stores($user_lat, $user_lng, $distance, $limit) {

        global $wpdb;

        $radius = (isset($distance)) ? ("HAVING distance < " . $distance) : "";
        $limit = (isset($limit)) ? (" LIMIT " . $limit) : "";
        $unit = 3959;
        $stores = array();

        $stores = $wpdb->get_results("SELECT post_lat.meta_value AS lat,
                           post_lng.meta_value AS lng,
                           posts.post_title, 
                           ( $unit * acos( cos( radians( $user_lat ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( $user_lng ) ) + sin( radians( $user_lat ) ) * sin( radians( post_lat.meta_value ) ) ) ) 
                      AS distance
                      FROM $wpdb->posts AS posts
                      INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'store_locator_lat'
                      INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'store_locator_lng'

                      WHERE posts.post_type = 'store_locator' 
                      AND posts.post_status = 'publish' GROUP BY posts.ID $radius ORDER BY distance $limit"
        );
        return $stores;
    }

}

// Register and load the widget
add_action('widgets_init', 'wpmsl_load_widget');

function wpmsl_load_widget() {
    register_widget('wpmsl_nearby_widget');
}

// Do Search Ajax
add_action('wp_ajax_wpmsl_get_nearby_stores_ajx', 'wpmsl_get_nearby_stores_ajx');
add_action('wp_ajax_nopriv_wpmsl_get_nearby_stores_ajx', 'wpmsl_get_nearby_stores_ajx');

function wpmsl_get_nearby_stores_ajx() {

	check_ajax_referer( 'wpmsl-widgetjs156951357753456654', 'security' );
     $user_lat = floatval($_POST['lat']);
     $user_lng = floatval($_POST['lng']);
     $distance = floatval($_POST['distance']);
     $limit = intval($_POST['limit']);


    if ($user_lat && $user_lng) {
        $stores = wpmsl_nearby_widget::get_nearby_stores($user_lat, $user_lng, $distance, $limit);
        if ($stores) {
            echo '<ul>';
            foreach ($stores as $store) {
                echo "<li>" . ' <span class="dashicons dashicons-location"></span>' . $store->post_title . " <br><span>" . number_format($store->distance, 2) . "Mile. </span>" . "</li>";
            }
            echo '</ul>';
        } else {
            echo "<div class='store_locator_widget_nodata'>" . __("No Stores found near you.", "store_locator") . "</div>";
        }
    } else {
        echo "<div class='store_locator_widget_nodata'>" . __("Couldn't detect user location.", "store_locator") . "</div>";
    }
    wp_die();
}
