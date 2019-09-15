<?php

/* Load More AJAX Call */
function fw_load_more(){	

	if(!wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) die('Invalid nonce');
  if(!is_numeric($_POST['page']) || $_POST['page'] < 0) die('Invalid page'); 
  
  $page_offset = 0;
  $limit = 0;
  $total_items = 0;
  $more_items_to_load = false;

  $args = [];
  $args['post_type'] = 'post_gallery';
  $args['post_status'] = 'publish';
  $args['posts_per_page'] = 1;	
    
  if(isset($_POST['gallery_id']) && $_POST['gallery_id']){	   
    $args['p'] = $_POST['gallery_id'];		
  }

  if(isset($_POST['page']) && $_POST['page']){		 
    $page_offset = $_POST['page'];	
  }   
  
  if(isset($_POST['limit']) && $_POST['limit']){	   
    $limit = $_POST['limit'];		
	}
  
  if(isset($_POST['total_items']) && $_POST['total_items']){    
    $total_items = $_POST['total_items'];		
	} 
  
  $pm_gallery_show_rollover_btn = get_option('pm_gallery_show_rollover_btn');
	$pm_gallery_show_expand_btn = get_option('pm_gallery_show_expand_btn');
	$pm_gallery_zoom_effect = get_option('pm_gallery_zoom_effect');
	$pm_gallery_rollover_text = get_option('pm_gallery_rollover_text');

  ob_start();
  $query = new WP_Query($args);

  while( $query->have_posts() ){ $query->the_post();  
    
    $pm_post_slides = get_post_meta( get_the_ID(), 'pm_post_slides', true ); //ARRAY VALUE

    if( is_array($pm_post_slides) && count($pm_post_slides) > 0 ) {		

			$gallery_items = count($pm_post_slides);
			$start_index = $page_offset * $limit;
			$next_load = $start_index + $limit;
			$display_total = $total_items;

			if($next_load >= $gallery_items) {
				$next_load = $gallery_items;
			}	else {
        $more_items_to_load = true;
      }

      for($i = $start_index; $i >= $start_index && $i < $next_load; $i++) {

        echo '<li>';
        
          echo '<a href="'. esc_url($pm_post_slides[$i]['image']) .'" class="lightbox" data-rel="prettyPhoto[gallery]">';

            if($pm_gallery_show_rollover_btn === "yes") :
              echo '<span></span>';
              echo '<div class="as-gallery-expand-icon"><span class="fas fa-expand"></span> '. esc_attr($pm_gallery_rollover_text) .'</div>';
            endif;

            echo '<img src="'. esc_url($pm_post_slides[$i]['image']) .'" alt="'. esc_attr($pm_post_slides[$i]['caption']) .'" />';
          echo '</a>';

          if($pm_gallery_show_expand_btn === "yes") :

            echo '<a href="'. esc_url($pm_post_slides[$i]['image']) .'" class="lightbox as-gallery-expand-btn" data-rel="prettyPhoto[gallery]"><i class="fas fa-expand"></i></a>';	

          endif;

        echo '</li>';	
      }

    }//end if

	}//end while loop	

	wp_reset_postdata();
  $content = ob_get_contents();
	ob_end_clean();	

	echo json_encode(

		array(
			'load_more_data' => $more_items_to_load,
			'content' => $content
		)

	);	

	exit;

}

?>