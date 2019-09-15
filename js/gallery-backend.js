(function($){

	$(document).ready(function(e) {		
		
		//post slider system
		if(wp.media !== undefined){
			
			//Global vars
			//var globalScope = 'test';
			
			var image_custom_uploader,
			target_text_field = '';
			
			//Target multiple image upload buttons
			if($('.slider_system_upload_image_button').length > 0) {				
				methods.bindClickEvent();									
			}
						
			methods.sortableFiles();

			//Add New slide btn
			if( $('#pm-slider-system-add-new-slide-btn').length > 0 ){
			
				$('#pm-slider-system-add-new-slide-btn').click(function(e) {
					
					e.preventDefault();
					
					//Get counter value based on last input field in container
					if( $('#pm_gallery_images_container').find('.pm-slider-system-field-container:last-child').length > 0 ){
						var counterValue = $('.pm-slider-system-field-container:last-child').attr('id'),
						counterValueId = counterValue.substring(counterValue.lastIndexOf('_') + 1),
						counterValueIdFinal = ++counterValueId;
					} else {
						counterValueIdFinal = 0;
						$('#pm_gallery_images_container').html('');
					}
					
					//Append new slide field
					var wrapperStart = '<div class="pm-slider-system-field-container" id="pm_slider_system_field_container_'+counterValueIdFinal+'">';

					var field1 = '<div class="gallery-item-drag-handle dashicons dashicons-move"></div>';

					var field2 = '<div class="gallery-item-thumbnail" id="gallery_item_thumbnail_'+counterValueIdFinal+'"></div>';

					var field3 = '<input type="text" value="" name="pm_slider_system_post_caption[]" id="pm_slider_system_post_caption_'+counterValueIdFinal+'" placeholder="Image caption" class="pm-caption-field" />';

					var field4 = '<input type="text" value="" name="pm_slider_system_post[]" id="pm_slider_system_post_'+counterValueIdFinal+'" class="pm-slider-system-upload-field" />';

					var field5 = '<input type="button" value="Media Library Image" class="button-primary slider_system_upload_image_button" id="pm_slider_system_post_btn_'+counterValueIdFinal+'" />';

					var field6 = '&nbsp; <input type="button" value="Remove Image" class="button button-secondary button-large delete slider_system_remove_image_button" id="pm_slider_system_post_remove_btn_'+counterValueIdFinal+'" />';					
					
					var wrapperEnd = '</div>';
					$('#pm_gallery_images_container').append(wrapperStart + field1 + field2 + field3 + field4 + field5 + field6 + wrapperEnd);
					
					//Bind button events
					methods.bindClickEvent();
					methods.bindRemoveImageClickEvent();
					methods.sortableFiles();
					
				});
				
			}
			
			if( $('.slider_system_remove_image_button').length > 0 ){			
				methods.bindRemoveImageClickEvent();				
			}			
						
		}//end if	
		
		
	});
	
	/* ==========================================================================
	   Methods
	   ========================================================================== */
		var methods = {
			
			bindClickEvent : function(e) {
							
				$('.slider_system_upload_image_button').click(function(e) {
					
					e.preventDefault();
					
					var btnId = $(this).attr('id'),
					targetTextFieldID = btnId.substring(btnId.lastIndexOf('_') + 1);
					
					//console.log(target_text_field.attr('id'));
	
					 //If the uploader object has already been created, reopen the media library window
					 if (image_custom_uploader) {
						 image_custom_uploader.open();
						 target_text_field = $('#pm_slider_system_post_'+targetTextFieldID);
						 gallery_item_thumbnail = $('#gallery_item_thumbnail_'+targetTextFieldID);
						 return;
					 }
						
				});
				
				//Triggers the Media Library window
				image_custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
					text: 'Choose Image'
					},
					 multiple: false
				 });
				 
				 //When a file is selected, grab the URL and set it as the text field's value
				 image_custom_uploader.on('select', function() {
					 
					attachment = image_custom_uploader.state().get('selection').first().toJSON();
					var url = '';
					url = attachment['url'];
					
					//console.log(target_text_field.attr('id'));
					
					$(target_text_field).val(url);
					$(gallery_item_thumbnail).html('');
					$(gallery_item_thumbnail).append('<img src="'+url+'" alt="Thumbnail" />');
					//$('.pm-admin-upload-field-preview').html('<img src="'+ url +'" />');
		
				 });
				
			},
			
			bindRemoveImageClickEvent : function(e) {
				
				$('.slider_system_remove_image_button').each(function(index, element) {
                    
					$(this).click(function(e) {
						
						e.preventDefault();
						
						var btnId = $(this).attr('id'),
						targetTextFieldID = btnId.substring(btnId.lastIndexOf('_') + 1);
						
						var targetTextFieldContainer = $('#pm_slider_system_field_container_'+targetTextFieldID).remove(),
						targetTextField = $('#pm_slider_system_post_'+targetTextFieldID).remove(),
						targetLibraryBtn = $('#pm_slider_system_post_btn_'+targetTextFieldID).remove();
						
						$(this).remove();
						
					});
					
        });
				
			},

			sortableFiles : function() {
			
				$( '#pm_gallery_images_container' ).sortable({
					handle: '.gallery-item-drag-handle',
					cursor: 'grabbing',
					tolerance: "pointer",
					//grid: [ 20, 10 ],
					//axis: "y",
					stop: function( e, ui ) {
						methods.updateFilesOrder();
					},
				});
				
			},

			updateFilesOrder : function () {
 
		    /* In each of rows */
		    $('.pm-slider-system-field-container').each( function(i){
		 
					/* Increase index by 1 to avoid "0" as first number. */
					var $this =  $(this),
					counter = i;

					$this.attr('id', 'pm_slider_system_field_container_'+counter+'');
					
					/* Update id on child elements */
					$this.find('.gallery-item-thumbnail').attr('id', 'gallery_item_thumbnail_'+counter+'');
					$this.find('.pm-caption-field').attr('id', 'pm_slider_system_post_caption_'+counter+'');
					$this.find('.pm-slider-system-upload-field').attr('id', 'pm_slider_system_post_'+counter+'');
					$this.find('.slider_system_upload_image_button').attr('id', 'pm_slider_system_post_btn_'+counter+'');
					$this.find('.slider_system_remove_image_button').attr('id', 'pm_slider_system_post_remove_btn_'+counter+'');	

		    });

			},


			
		}

})(jQuery);