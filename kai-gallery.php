<?php 

/*
Plugin Name: Kai Gallery
Plugin URI: http://www.microthemes.ca
Description: Create multiple galleries with easy drag n drop functionality.
Version: 1.0
Author: Micro Themes
Author URI: http://www.microthemes.ca
License: GPLv2
*/

//Actions
add_action('init', 'pm_ln_register_gallery_post_type');
//add_action('init', 'pm_ln_gallery_categories');
//add_action('init', 'pm_ln_gallery_tags');

//custom category fields for gallery
/* add_action( 'gallerycats_add_form_fields', 'pm_ln_gallerycats_add_new_meta_field', 10, 2 );
add_action( 'gallerycats_edit_form_fields', 'pm_ln_gallerycats_edit_meta_field', 10, 2 ); */

/* add_action( 'edited_gallerycats', 'pm_ln_gallerycats_save_custom_meta', 10, 2 );
add_action( 'create_gallerycats', 'pm_ln_gallerycats_save_custom_meta', 10, 2 ); */

//Custom metaboxes
add_action('admin_init', 'pm_ln_gallery_metaboxes');

//Load scripts
add_action('admin_enqueue_scripts', 'pm_load_gallery_admin_scripts');
add_action('wp_enqueue_scripts', 'pm_load_gallery_front_scripts');

//Settings page
add_action('admin_menu', 'pm_ln_add_gallery_settings' );// ADD SETTINGS PAGE

//Featured image rewrite
//add_action('do_meta_boxes', 'pm_ln_render_new_gallery_post_thumbnail_meta_box');

//Save post fields
add_action('save_post', 'pm_ln_save_gallery_fields', 10, 2); //SAVE FIELDS

//Translation support
add_action('plugins_loaded', 'pm_ln_load_gallery_textdomain');

add_shortcode("kai_gallery", "kai_gallery"); 	

//Ajax loader function
add_action('wp_ajax_fw_load_more', 'fw_load_more');
add_action('wp_ajax_nopriv_fw_load_more', 'fw_load_more');


/**** FUNCTIONS **********************************************************************************************/

/* function pm_ln_render_new_gallery_post_thumbnail_meta_box() {
	
    global $post_type; // lets call the post type 
     
    // remove the old meta box
    remove_meta_box( 'postimagediv','post_videos','side' );
             
    // adding the new meta box.
    add_meta_box('postimagediv', esc_html__('Featured Image', 'progallery'), 'pm_ln_new_post_gallery_thumbnail_meta_box', 'post_gallery', 'side', 'low');
	
} */

function pm_ln_new_post_gallery_thumbnail_meta_box() {
	
    global $post; // we know what this does
     
    echo '<p>'. esc_html__('Recommended size', 'progallery') .': 810x595px</p>';
     
    $thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true ); // grabing the thumbnail id of the post
    echo _wp_post_thumbnail_html( $thumbnail_id ); // echoing the html markup for the thumbnail
     
    //echo '<p>Content below the image.</p>';
}



function pm_ln_register_gallery_post_type() {
	
	$pm_gallery_slug_name = get_option('pm_gallery_slug_name');
	
	add_theme_support( 'post-formats', array(
		//'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );
	
    register_post_type('post_gallery',
		array(
			'labels' => array(
				'name' => __( 'Galleries', 'progallery' ),
				'singular_name' => __( 'Gallery', 'progallery' ),
				'add_new' => __( 'Add New Gallery', 'progallery' ),
				'add_new_item' => __( 'Add New Gallery', 'progallery' ),
				'edit' => __( 'Edit', 'progallery' ),
				'edit_item' => __( 'Edit Gallery', 'progallery' ),
				'new_item' => __( 'New Gallery', 'progallery' ),
				'view' => __( 'View', 'progallery' ),
				'view_item' => __( 'View Galleries', 'progallery' ),
				'search_items' => __( 'Search Galleries', 'progallery' ),
				'not_found' => __( 'No Galleries found', 'progallery' ),
				'not_found_in_trash' => __( 'No Galleries found in Trash', 'progallery' ),
				'parent' => __( 'Parent Gallery', 'progallery' )
			),
			'public' => true,
            'menu_position' => 5, //5 - below posts 10 - below Media 15 - below Links 
            'supports' => array('title'),
            //'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true,
			'description' => __( 'Easily lets you create new galleries.', 'progallery' ),
			'public' => true,
			'show_ui' => true, 
			'_builtin' => false,
			'map_meta_cap' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'pages' => true,
			'rewrite' => array('slug' => !empty($pm_gallery_slug_name) ? $pm_gallery_slug_name : 'gallery-post'),
			//'taxonomies' => array('category', 'post_tag')
		)
	); 
	
	flush_rewrite_rules();
	
}

function pm_ln_gallery_categories() {
	
	// create the array for 'labels'
    $labels = array(
		'name' => __( 'Gallery Categories', 'progallery' ),
		'singular_name' => __( 'Gallery Categories', 'progallery' ),
		'search_items' =>  __( 'Search Gallery Categories', 'progallery' ),
		'popular_items' => __( 'Popular Gallery Categories', 'progallery' ),
		'all_items' => __( 'All Gallery Categories', 'progallery' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Gallery Category', 'progallery' ),
		'update_item' => __( 'Update Gallery Category', 'progallery' ),
		'add_new_item' => __( 'Add Gallery Category', 'progallery' ),
		'new_item_name' => __( 'New Gallery Category Name', 'progallery' ),
		'separate_items_with_commas' => __( 'Separate Gallery Categories with commas', 'progallery' ),
		'add_or_remove_items' => __( 'Add or remove Gallery Categories', 'progallery' ),
		'choose_from_most_used' => __( 'Choose from the most used Gallery Categories', 'progallery' )
    );
	
    // register your Flags taxonomy
    register_taxonomy( 'gallerycats', 'post_gallery', array(
		'hierarchical' => true, //Set to true for categories or false for tags
		'labels' => $labels, // adds the above $labels array
		'show_ui' => true,
		'query_var' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => 'gallery-category' ), // changes name in permalink structure
    ));
	
}




function pm_ln_gallery_tags() {
	
	// create the array for 'labels'
    $labels = array(
		'name' => esc_html__( 'Gallery Tags', 'progallery' ),
		'singular_name' => esc_html__( 'Gallery Tags', 'progallery' ),
		'search_items' =>  esc_html__( 'Search Gallery Tags', 'progallery' ),
		'popular_items' => esc_html__( 'Popular Gallery Tags', 'progallery' ),
		'all_items' => esc_html__( 'All Gallery Tags', 'progallery' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => esc_html__( 'Edit Gallery Tag', 'progallery' ),
		'update_item' => esc_html__( 'Update Gallery Tag', 'progallery' ),
		'add_new_item' => esc_html__( 'Add Gallery Tag', 'progallery' ),
		'new_item_name' => esc_html__( 'New Gallery Tag Name', 'progallery' ),
		'separate_items_with_commas' => esc_html__( 'Separate Gallery Tags with commas', 'progallery' ),
		'add_or_remove_items' => esc_html__( 'Add or remove Gallery Tags', 'progallery' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used Gallery Tags', 'progallery' )
    );
	
    // register your Flags taxonomy
    register_taxonomy( 'gallerytags', 'post_gallery', array(
		'hierarchical' => false, //Set to true for categories or false for tags
		'labels' => $labels, // adds the above $labels array
		'show_ui' => true,
		'query_var' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => 'gallery-tag' ), // changes name in permalink structure
    ));
}



function pm_ln_default_post_format_filter( $format ) {
    return in_array( $format, pm_ln_get_allowed_project_formats() ) ? $format : 'standard';
}

function pm_ln_get_allowed_project_formats() {
    return array( 'audio', 'gallery', 'image', 'video' );
}



/* function pm_ln_gallerycats_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[cat_icon]"><?php _e( 'Google Material Icon', 'progallery' ); ?></label>
        <input type="text" name="term_meta[cat_icon]" id="term_meta[cat_icon]" value="">
        <p class="description"><?php printf(__( 'Choose your category icon from <a href="%s" target="_blank">Google Material Icons</a>','progallery' ), 'https://material.io/icons/'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="term_meta[cat_color]"><?php _e( 'Category Color', 'progallery' ); ?></label>
        <input type="text" name="term_meta[cat_color]" id="term_meta[cat_color]" value="<?php echo esc_attr( $term_meta['cat_color'] ) ? esc_attr( $term_meta['cat_color'] ) : ''; ?>" class="wordpress-color-field" data-default-color="#ffffff" />
    </div>
    
<?php
} */





function pm_ln_gallerycats_edit_meta_field($term) {
 
    // put the term ID into a variable
    $t_id = $term->term_id;
 
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$t_id" ); ?>
    <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[cat_icon]"><?php _e( 'Google Material Icon', 'progallery' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[cat_icon]" id="term_meta[cat_icon]" value="<?php echo esc_attr( $term_meta['cat_icon'] ) ? esc_attr( $term_meta['cat_icon'] ) : ''; ?>">
            <p class="description"><?php printf(__( 'Choose your category icon from <a href="%s" target="_blank">Google Material Icons</a>. For example: <b>explore</b>','progallery' ), 'https://material.io/icons/'); ?></p>
        </td>
    </tr>
    
    <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[cat_color]"><?php _e( 'Category Color', 'progallery' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[cat_color]" id="term_meta[cat_color]" value="<?php echo esc_attr( $term_meta['cat_color'] ) ? esc_attr( $term_meta['cat_color'] ) : ''; ?>" class="wordpress-color-field" data-default-color="#ffffff" />
        </td>
    </tr>
<?php
}



/* function pm_ln_gallerycats_save_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "taxonomy_$t_id", $term_meta );
    }
} */



//Add sub menus
function pm_ln_add_gallery_settings() {

	//create custom top-level menu
	//add_menu_page( 'Pulsar Framework Documentation', 'Theme Documentation', 'manage_options', __FILE__, 'pm_documentation_main_page',	plugins_url( '/images/wp-icon.png', __FILE__ ) );
	
	//create sub-menu items
	add_submenu_page( 'edit.php?post_type=post_gallery', esc_attr__('Gallery Settings', 'progallery'),  esc_attr__('Gallery Settings', 'progallery'), 'manage_options', 'gallery_settings',  'pm_ln_gallery_settings_page' );
	
	//create an options page under Settings tab
	//add_options_page('My API Plugin', 'My API Plugin', 'manage_options', 'pm_myplugin', 'pm_myplugin_option_page');	
}

//Settings page
function pm_ln_gallery_settings_page() {
		
	//Save data first
	if (isset($_POST['pm_gallery_settings_update'])) {
		
		update_option('pm_gallery_slug_name', sanitize_text_field($_POST["pm_gallery_slug_name"]));
		update_option('pm_gallery_show_rollover_btn', sanitize_text_field($_POST["pm_gallery_show_rollover_btn"]));
		update_option('pm_gallery_show_expand_btn', sanitize_text_field($_POST["pm_gallery_show_expand_btn"]));	
		update_option('pm_gallery_zoom_effect', sanitize_text_field($_POST["pm_gallery_zoom_effect"]));	
		update_option('pm_gallery_rollover_text', sanitize_text_field($_POST["pm_gallery_rollover_text"]));	
		
		echo '<div id="message" class="updated fade"><h4>'.esc_attr__('Your settings have been saved.', 'progallery').'</h4></div>';
		
	}//end of save data
	
	$pm_gallery_slug_name = get_option('pm_gallery_slug_name');
	$pm_gallery_show_rollover_btn = get_option('pm_gallery_show_rollover_btn');
	$pm_gallery_show_expand_btn = get_option('pm_gallery_show_expand_btn');
	$pm_gallery_zoom_effect = get_option('pm_gallery_zoom_effect');
	$pm_gallery_rollover_text = get_option('pm_gallery_rollover_text');
	
	?>
	
	<div class="wrap">
    
		<?php screen_icon(); ?>
        
		<h2><?php esc_attr_e('Gallery Settings', 'progallery') ?></h2>
		
		<h4><?php esc_attr_e('Configure the settings for the Gallery Post Type plug-in below:', 'progallery') ?></h4>
		
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		
			<input type="hidden" name="pm_gallery_settings_update" id="pm_gallery_settings_update" value="true" />
			
			<div>

				<label for="pm_gallery_slug_name"><?php esc_attr_e('Custom post type slug', 'progallery') ?>:</label>
				
				<br><br>
				<input type="text" name="pm_gallery_slug_name" id="pm_gallery_slug_name" value="<?php echo $pm_gallery_slug_name !== '' ? esc_attr($pm_gallery_slug_name) : '' ?>" />
				<br><br>
				
			<div>

			<div>
			
				<label for="pm_gallery_show_rollover_btn"><?php esc_attr_e('Show Rollover Expand Button?', 'progallery') ?>:</label>

				<br><br>
				<select name="pm_gallery_show_rollover_btn">
					<option value="yes" <?php echo $pm_gallery_show_rollover_btn == 'yes' ? 'selected' : '' ?>>YES</option>
					<option value="no" <?php echo $pm_gallery_show_rollover_btn == 'no' ? 'selected' : '' ?>>NO</option>
				</select>
				<br><br>
			
			</div>

			<div>

				<label for="pm_gallery_rollover_text"><?php esc_attr_e('Rollover Text', 'progallery') ?>:</label>
				
				<br><br>
				<input type="text" name="pm_gallery_rollover_text" id="pm_gallery_rollover_text" value="<?php echo $pm_gallery_rollover_text !== '' ? esc_attr($pm_gallery_rollover_text) : '' ?>" />
				<br><br>
				
			<div>

			<div>
			
				<label for="pm_gallery_show_expand_btn"><?php esc_attr_e('Show Exterior Expand Button?', 'progallery') ?>:</label>

				<br><br>
				<select name="pm_gallery_show_expand_btn">
					<option value="no" <?php echo $pm_gallery_show_expand_btn == 'no' ? 'selected' : '' ?>>NO</option>
					<option value="yes" <?php echo $pm_gallery_show_expand_btn == 'yes' ? 'selected' : '' ?>>YES</option>
				</select>
				<br><br>
			
			</div>

			<div>
			
				<label for="pm_gallery_zoom_effect"><?php esc_attr_e('Zoom In on Hover?', 'progallery') ?>:</label>

				<br><br>
				<select name="pm_gallery_zoom_effect">
					<option value="no" <?php echo $pm_gallery_zoom_effect == 'no' ? 'selected' : '' ?>>NO</option>
					<option value="yes" <?php echo $pm_gallery_zoom_effect == 'yes' ? 'selected' : '' ?>>YES</option>
				</select>
				<br><br>
			
			</div>
			
            
			<div>
				<input type="submit" name="pm_settings_update" class="button button-primary button-large" value="<?php esc_attr_e('Update Settings', 'progallery'); ?> &raquo;" />
			</div>
		
		</form>
		
	</div>
	
	<?php
	
}


function pm_ln_load_gallery_textdomain() { 
	load_plugin_textdomain( 'progallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
} 

function pm_load_gallery_admin_scripts() {

	wp_enqueue_media();
	
	wp_enqueue_script('jquery-ui-core');
	//wp_enqueue_script("jquery-effects-core");

	wp_enqueue_script( 'gallery-back-js', plugin_dir_url(__FILE__) . 'js/gallery-backend.js', array(), '1.0', true );
	wp_enqueue_style( 'gallery-back-css', plugin_dir_url(__FILE__) . 'css/gallery-backend.css' );
	wp_enqueue_style( 'gallery-back-responsive-css', plugin_dir_url(__FILE__) . 'css/gallery-backend-responsive.css' );
	
}

function pm_load_gallery_front_scripts() {

	//wp_enqueue_script( 'jquery-ui-core' );

	wp_enqueue_style( 'prettyPhoto', plugin_dir_url(__FILE__) . 'js/prettyphoto/css/prettyPhoto.css');
	wp_enqueue_script( 'prettyphoto', plugin_dir_url(__FILE__) . 'js/prettyphoto/js/jquery.prettyPhoto.js', array('jquery'), '1.0', true );

	wp_enqueue_style( 'fontawesome', plugin_dir_url(__FILE__) . 'css/fontawesome-free/css/all.css');

	wp_enqueue_style( 'gallery-front', plugin_dir_url(__FILE__) . 'css/gallery-frontend.css' );
	wp_enqueue_style( 'gallery-slider', plugin_dir_url(__FILE__) . 'css/gallery-slider-frontend.css' );
	wp_enqueue_script( 'gallery-front-js', plugin_dir_url(__FILE__) . 'js/gallery-frontend.js', array('jquery'), '1.0', true );	


	$js_file = get_stylesheet_directory_uri() . 'js/wordpress.js'; 

	wp_enqueue_script( 'jcustom', $js_file );

	$array = array( 
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajax_nonce'),
		'loading' => __('Loading...', 'divi')
	);

	wp_localize_script( 'jcustom', 'ajaxOptions', $array );
	
}



function pm_ln_gallery_metaboxes() {	
	
	//Post Image
	/* add_meta_box( 
		'pm_post_image', //ID
		__('Image', 'progallery'),  //label
		'pm_gallery_post_image_function' , //function
		'post_gallery', //Post type
		'normal', 
		'high' 
	); */
	
	//Post Gallery
	add_meta_box( 
		'pm_post_gallery', //ID
		__('Gallery Manager', 'progallery'),  //label
		'pm_gallery_entries_function' , //function
		'post_gallery', //Post type
		'normal', 
		'high' 
	);
	
	
	
}

function pm_gallery_entries_function($post) {
	
	// Use nonce for verification
    wp_nonce_field( 'theme_metabox', 'post_meta_nonce' );
	
	//Retrieve the meta value if it exists
	$pm_post_slides = get_post_meta( $post->ID, 'pm_post_slides', true ); //ARRAY VALUE	
	//print_r($pm_post_slides);
		
	?>        

		<p><?php _e('Use the following shortcode to display your gallery:', 'procastreviews') ?> <b class="shortcode-text">[kai_gallery gallery_id="<?php echo get_the_ID(); ?>" gallery_limit="4" total_items="12" slider_mode="false"]</b></p>

		<div class="gallery-divider"></div>

        <div class="pm-featured-properties-settings-container visible" id="pm_featured_properties_settings_container">
                                    
            <div id="pm_gallery_images_container">
            
                <?php 
                
                    $counter = 0;
                
                    if(is_array($pm_post_slides)){

						if(count($pm_post_slides) > 0) {

							foreach($pm_post_slides as $val) {
                        
								echo '<div class="pm-slider-system-field-container" id="pm_slider_system_field_container_'.$counter.'">';
								
									echo '<div class="gallery-item-drag-handle dashicons dashicons-move"></div>';
								
									echo '<div class="gallery-item-thumbnail" id="gallery_item_thumbnail_'.$counter.'"><img src="'.esc_html($val['image']).'" alt="Thumbnail" /></div>';
	
									echo '<input type="text" value="'.esc_html($val['caption']).'" name="pm_slider_system_post_caption[]" id="pm_slider_system_post_caption_'.$counter.'" placeholder="'.esc_attr('Image caption', 'progallery').'" class="pm-caption-field" />';
	
									echo '<input type="text" value="'.esc_html($val['image']).'" name="pm_slider_system_post[]" id="pm_slider_system_post_'.$counter.'" class="pm-slider-system-upload-field" />';	
	
									echo '<input type="button" value="'.__('Media Library Image', 'progallery').'" class="button-primary slider_system_upload_image_button" id="pm_slider_system_post_btn_'.$counter.'" />';
	
									echo '&nbsp; <input type="button" value="'.__('Remove Image', 'progallery').'" class="button button-secondary button-large delete slider_system_remove_image_button" id="pm_slider_system_post_remove_btn_'.$counter.'" />';
								
								echo '</div>';
								
								$counter++;
								
							}

						} else {

							//Default value upon post initialization
							echo '<div class="gallery-items-not-found"><b>'.__('No gallery images found. Go ahead and add some!', 'progallery').'</b></div>';

						}
						                        
                        
                        
                    } else {
                    
                        //Default value upon post initialization
                        echo '<div class="gallery-items-not-found"><b>'.__('No gallery images found. Go ahead and add some!', 'progallery').'</b></div>';
                        
                    }                    
                
                ?>            
            
			</div>
			
			<div class="gallery-insert-divider"></div>
            
            <input type="button" id="pm-slider-system-add-new-slide-btn" class="button button-primary button-large" value="<?php _e('Insert Gallery Image','progallery') ?>">
        
        </div><!-- /.pm-featured-properties-settings-container -->        
    
    <?php
	
}

//SAVE DATA
function pm_ln_save_gallery_fields( $post_id, $post_type ) { //@param: id @param: verify post type
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;
	  
	//Security measure
	if( isset($_POST['post_meta_nonce'])) :
	
		if ( !wp_verify_nonce( $_POST['post_meta_nonce'], 'theme_metabox' ) )
		    return;
			
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	
		// Verify post type 
		if ( $post_type->post_type == 'post_gallery' ) {
			
			if( isset( $_POST['pm_gallery_header_image_meta'] ) ) {				
				update_post_meta($post_id, "pm_gallery_header_image_meta", sanitize_text_field($_POST['pm_gallery_header_image_meta']));				
			}
			
			
			
			if(isset($_POST['pm_gallery_post_image_url'])){
				update_post_meta($post_id, "pm_gallery_post_image_url", sanitize_text_field($_POST['pm_gallery_post_image_url']));
			}
			
			if(isset($_POST["pm_slider_system_post"])){
				
				$images = array();		
				$counter = 0;
				$cap_counter = 0;
								
				foreach($_POST["pm_slider_system_post"] as $key => $text_field){
					
					if(!empty($text_field)){
						$images[$counter] = array('image' => $text_field, 'caption' => $_POST["pm_slider_system_post_caption"][$counter]);
					}					
					$counter++;					
				}
							
				//$pm_slider_system_post = $_POST['pm_slider_system_post'];
				update_post_meta($post_id, "pm_post_slides", $images);
				
			} else {
			
				//insert empty string			
				update_post_meta($post_id, "pm_post_slides", '');
				
			}						
						
		}
	
	endif;	
}

include("functions.php");
include("shortcodes.php");

?>