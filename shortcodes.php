<?php

function kai_gallery($atts, $content = null) {

	extract(shortcode_atts(array(
	  "gallery_id" => '0',
	  "gallery_limit" => '4',
	  "total_items" => '12',
	  "slider_mode" => "false",
	  "autoplay" => "false",
	  "speed" => "5000"
	), 
  
	$atts));
  
	//Retrieve testimonials from post type
	$gallery_args = array(
	  'post_type' => 'post_gallery',
	  'p' => $gallery_id,
	  'post_status' => 'publish',
	  'posts_per_page' => 1,
	);
  
	$pm_gallery_show_rollover_btn = get_option('pm_gallery_show_rollover_btn');
	$pm_gallery_show_expand_btn = get_option('pm_gallery_show_expand_btn');
	$pm_gallery_zoom_effect = get_option('pm_gallery_zoom_effect');
	$pm_gallery_rollover_text = get_option('pm_gallery_rollover_text');
  
	$gallery_query = new WP_Query($gallery_args);

	if($slider_mode == "true") {

		//Build Slider
		$html = '<div id="slide-wrapper">';

			$html .= '<div id="slide-window" data-autoplay="'. $autoplay .'" data-autoplayspeed="'. $speed .'">';
	
				$html .= '<ul id="slides">';

				if ($gallery_query->have_posts()) : while ($gallery_query->have_posts()) : $gallery_query->the_post();
				
					$pm_post_slides = get_post_meta( get_the_ID(), 'pm_post_slides', true ); //ARRAY VALUE
					$slides_length = count($pm_post_slides);

					for($i = 0; $i < $slides_length; $i++) {						

						$html .= '<li class="slide color-'. $i .' alive"><img src="'. esc_url($pm_post_slides[$i]['image']) .'" alt="'. esc_attr($pm_post_slides[$i]['caption']) .'" /></li>';
					}

				endwhile; else:

				endif;
				
				$html .= '</ul>';
				
			$html .= '</div>';

			$html .= '<div class="slides-navigation">';
				$html .= '<span class="slides-nav-btn" id="slide-left-btn"><i class="fas fa-chevron-left"></i></span>';
				$html .= '<span class="slides-nav-btn" id="slide-right-btn"><i class="fas fa-chevron-right"></i></span>';
			$html .= '</div>';

		$html .= '</div>';

		


	} else {

		//Build Gallery layout
		if ($gallery_query->have_posts()) : while ($gallery_query->have_posts()) : $gallery_query->the_post(); 
	
			$pm_post_slides = get_post_meta( get_the_ID(), 'pm_post_slides', true ); //ARRAY VALUE
			
			if( is_array($pm_post_slides) && count($pm_post_slides) > 0 ) {			
								
				$page_offset = 0;
				$limit = $gallery_limit; //param
				$gallery_items = count($pm_post_slides);
				$start_index = $page_offset * $limit;
				$next_load = $start_index + $limit;
				$display_total = $total_items; //param

				if($next_load > $gallery_items) {
					$next_load = $gallery_items;
				}

				$html = '<ul class="as-gallery-list'. ($pm_gallery_zoom_effect === "yes" ? ' zoom-on' : '') .'" id="ajax_posts_container">';

					for($i = $start_index; $i >= $start_index && $i < $next_load; $i++) {

						$html .= '<li>';
						
							$html .= '<a href="'. esc_url($pm_post_slides[$i]['image']) .'" class="lightbox" data-rel="prettyPhoto[gallery]">';	

								if($pm_gallery_show_rollover_btn === "yes") :
									$html .= '<span></span>';
									$html .= '<div class="as-gallery-expand-icon"><span class="fas fa-expand"></span> '. esc_attr($pm_gallery_rollover_text) .'</div>';
								endif;
								
								$html .= '<img src="'. esc_url($pm_post_slides[$i]['image']) .'" alt="'. esc_attr($pm_post_slides[$i]['caption']) .'" />';
							$html .= '</a>';

							if($pm_gallery_show_expand_btn === "yes") :

								$html .= '<a href="'. esc_url($pm_post_slides[$i]['image']) .'" class="lightbox as-gallery-expand-btn" data-rel="prettyPhoto[gallery]"><i class="fas fa-expand"></i></a>';	

							endif;

						$html .= '</li>';	
					}

				$html .= '</ul>'; 
			
				if($next_load < $gallery_items) {        

					$html .= '<div class="as-gallery-load-container" id="load_more_btn_container"><a href="#" id="load_more_btn" data-limit="'.$gallery_limit.'" data-total_items="12" data-gallery_id="'.$gallery_id.'" class="gl-btn offset-color">Load More</a></div>';

					$html .= '<div class="lds-dual-ring" id="fw_spinner"></div>';
				
				}					
				
			} else {				
				$html .= '<p>'. esc_attr('No gallery images found.', 'progallery') .'</p>';				
			}	
		
			endwhile; else:

		endif;

	}//end $slider_mode
  
	
	
    
  wp_reset_postdata();  	

	//return the shortcode  
	return $html;    
  
}

?>