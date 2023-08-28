<?php 
if(!class_exists('Store_Locator_Multiple_Maps_Admin'))
{
	class Store_Locator_Multiple_Maps_Admin
	{
	public function __construct(){
      add_action( 'store_locator_category_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
      add_action( 'store_locator_category_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
      add_action( 'created_store_locator_category', array ( $this, 'save_category_image' ), 10, 2 );
      add_action( 'edited_store_locator_category', array ( $this, 'updated_category_image' ), 10, 2 );
      //manage custom coulmns display for stores
      add_filter('manage_edit-maps_columns', array($this,'map_list_columns'));
      //manage custom coulmns content display for Stores
      add_filter('manage_maps_posts_custom_column', array($this,'manage_map_columns'), 10, 2);
		}
    public function add_category_image(){
        $url=STORE_LOCATOR_PLUGIN_URL.'assets/img/'; ?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="category-image-id"><?php esc_html_e( 'Image', 'store_locator' ); ?></label>
            </th>
            <td>
                <div class="store_locator_map_settings">
                    <p>
                        <label title="Choose the color of user marker" for="store_locator_map_type"><?php esc_html_e("User Marker", 'store_locator'); ?>:</label>
                    </p>
                    <ul class="default_markers">
                        <li>
                            <img src= "<?php echo esc_url($url.'blue.png'); ?>" />
                            <input type="radio" value="blue.png" name="catImage" />
                        </li>
                        <li>
                            <img src= "<?php echo esc_url($url.'red.png'); ?>" />
                            <input type="radio" value="red.png" name="catImage" />
                        </li>
                        <li>
                            <img src= "<?php echo esc_url($url.'green.png'); ?>" />
                            <input type="radio" value="green.png" name="catImage" />
                        </li>
                        <li>
                            <img src= "<?php echo esc_url($url.'orange.png'); ?>" />
                            <input type="radio" value="orange.png" name="catImage" />
                        </li>
                        <li>
                            <img src= "<?php echo esc_url($url.'purple.png'); ?>" />
                            <input type="radio" value="purple.png" name="catImage" />
                        </li>
                        <li>
                            <img src= "<?php echo esc_url($url.'yellow.png'); ?>" />
                            <input type="radio" value="yellow.png" name="catImage" />
                        </li>
                    </ul>
                <p><?php esc_html_e('or add custom marker url','store_locator');?>
                <?php 
                $marker1=$url.'upload.png';
                $class='wpmsl_custom_marker_upload'; 
                $uploadRemove =esc_html__('Upload','store_locator'); ?>
                <div class="<?php echo $class; ?>">
                    <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                    <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="cat_marker_custom" />
                    <p><?php echo $uploadRemove; ?></p>
                </div>
                </p>
           <hr>
          </div>
           </td>
         </tr>
            <?php
   		 }
		public function update_category_image( $term, $taxonomy){
          $url=STORE_LOCATOR_PLUGIN_URL.'assets/img/';
          ?>
           <tr class="form-field term-group-wrap">
           <th scope="row">
             <label for="category-image-id"><?php esc_html_e( 'Image', 'store_locator' ); ?></label>
           </th>
           <td>
             <?php $image_id = get_term_meta ( $term -> term_id, 'catImage', true ); ?>
             <div class="store_locator_map_settings">
          <p>
              <label title="Choose the color of user marker" for="store_locator_map_type"><?php esc_html_e("User Marker", 'store_locator'); ?>:</label>
          </p>
          <ul class="default_markers">
              <li>
                  <img src= "<?php echo esc_url($url.'blue.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id == 'blue.png')?'checked':''; ?> type="radio" value="blue.png" name="catImage" />
              </li>
              <li>
                  <img src= "<?php echo esc_url($url.'red.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id == 'red.png')?'checked':''; ?> type="radio" value="red.png" name="catImage" />
              </li>
              <li>
                  <img src= "<?php echo esc_url($url.'green.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id == 'green.png')?'checked':''; ?> type="radio" value="green.png" name="catImage" />
              </li>
              <li>
                  <img src= "<?php echo esc_url($url.'orange.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id == 'orange.png')?'checked':''; ?> type="radio" value="orange.png" name="catImage" />
              </li>
              <li>
                  <img src= "<?php echo esc_url($url.'purple.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id == 'purple.png')?'checked':''; ?> type="radio" value="purple.png" name="catImage" />
              </li>
              <li>
                  <img src= "<?php echo esc_url($url.'yellow.png'); ?>" />
                  <input <?php echo (isset($image_id) && $image_id== 'yellow.png')?'checked':''; ?> type="radio" value="yellow.png" name="catImage" />
              </li>

          </ul>
          <p>
            <?php esc_html_e('or add custom marker url','store_locator');?>
            <?php $cat_marker_custom=get_term_meta( $term -> term_id,'cat_marker_custom_image',true);
                  if(isset($cat_marker_custom) && !empty($cat_marker_custom)){
                      $marker1=$cat_marker_custom;
                      $class='wpmsl_custom_marker';
                      $uploadRemove=__('Remove','store_locator');
                  }
                  else{
                      $marker1=$url.'upload.png';
                      $class='wpmsl_custom_marker_upload'; 
                      $uploadRemove =__('Upload','store_locator');
                  }
              ?>
             <div class="<?php echo $class; ?>">
              <img src="<?php echo $marker1; ?>" width="50px" height="50px">
                <input type="hidden" value="<?php echo ($class=='wpmsl_custom_marker') ? $marker1 : ''; ?>" name="cat_marker_custom" />
                <p><?php echo $uploadRemove; ?></p>
            </div>
          </p>
           <hr>
          </div>
           </td>
         </tr>
            <?php
        }
        public function save_category_image($term_id, $tt_id){
			
            if( isset( $_POST['catImage'] ) && '' !== $_POST['catImage'] ){
             $image = $_POST['catImage'];
             add_term_meta( $term_id, 'catImage', $image, true );
           }
           if( isset( $_POST['cat_marker_custom'] ) && '' !== $_POST['cat_marker_custom'] ){
             $custom_image = $_POST['cat_marker_custom'];
             add_term_meta( $term_id, 'cat_marker_custom_image', $custom_image, true );
           }
        }
        public function updated_category_image ( $term_id, $tt_id ) {
           if( isset( $_POST['catImage'] ) && '' !== $_POST['catImage'] ){

             // $image = STORE_LOCATOR_PLUGIN_URL.'assets/img/'.$_POST['catImage'];
             $image = $_POST['catImage'];
             update_term_meta ( $term_id, 'catImage', $image );
           } else {
             update_term_meta ( $term_id, 'catImage', '' );
           }
           if(isset($_POST['cat_marker_custom']) && ''!==$_POST['cat_marker_custom']){
                update_term_meta ( $term_id, 'cat_marker_custom_image', $_POST['cat_marker_custom']);
           } else {
			   update_term_meta ( $term_id, 'cat_marker_custom_image', '');
		   }
        }
        public function map_list_columns($columns) {
            unset(
                $columns['date']
            );
            $new_columns = array(
                'map_shortcode' => esc_html__('Shortcode', 'store_locator'),
                'map_type' => esc_html__('Map Type', 'store_locator'),
                'date' => esc_html__('Date', 'store_locator'),
            );
            return array_merge($columns, $new_columns);
        }
        public function manage_map_columns($column, $post_id) {
            global $post;
            switch ($column) {
                case 'map_shortcode':
                    // echo '<p> <input class="widefat" type="text" value="[wp_multi_store_locator_map id='.$post_id.']" readonly="readonly" name="shortcode"> </p>';
					echo '<p> <input class="widefat" type="text" value="' . esc_attr('[wp_multi_store_locator_map id=' . $post_id . ']') . '" readonly="readonly" name="shortcode"> </p>';
                    break;
                case 'map_type':
                    $map_layouts=get_post_meta($post_id, 'map_layouts', true);
                    echo (isset($map_layouts['layout']) && !empty($map_layouts['layout'])) ? $map_layouts['layout'] : esc_html__('Custom','store_locator');
                    break;
                default :
                    break;
            }
        }
	}
$many_maps=new Store_Locator_Multiple_Maps_Admin();
}