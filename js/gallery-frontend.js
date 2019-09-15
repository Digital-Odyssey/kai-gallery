(function($){

	$.global = new Object();
	$.global.item = 1;
	$.global.total = 0;
	$.global.windowWidth = 0;
	$.global.slideCount = 0;
	$.global.slidesWidth = 0;
	$.global.currentSlide = 1;

	$.global.autoPlay = false;
	$.global.autoPlayInterval = undefined;
	$.global.autoPlaySpeed = 5000;

	$(document).ready(function(e) {

		if($("#slide-window").length > 0) {

			UpdateNavStatus();

			$.global.autoPlay = $("#slide-window").data("autoplay");
			$.global.autoPlaySpeed = $("#slide-window").data("autoplayspeed");			

			if($.global.autoPlay == true) {
				AutoPlay();
			}		

			$.global.slideCount = $('#slides li').length;
			$.global.windowWidth = $("#slide-window").width();
			$.global.slidesWidth = $.global.slideCount * $.global.windowWidth;						
			$.global.item = 0;
			$.global.total = $.global.slideCount; 
				
			$('#slide-wrapper').css('width', $.global.windowWidth+'px');
			$('.slide').css('width', $.global.windowWidth+'px');
			$('#slides').css('width', $.global.slidesWidth+'px');			

			$("#slides li:nth-child(1)").addClass('alive');
				
			$('#slide-left-btn').on('click', function() { 	
				
				if($.global.currentSlide > 1) {
					$.global.currentSlide--;
					Slide('back'); 
					UpdateNavStatus();
				} 

				ResetAutoPlay();
				
			}); 

			$('#slide-right-btn').on('click', function() { 	
				
				if($.global.currentSlide < $.global.slideCount) {
					$.global.currentSlide++;
					Slide('forward'); 	
					UpdateNavStatus();				
				} 	

				ResetAutoPlay();
				
			}); 

			function ResetAutoPlay() {
				if($.global.autoPlay == true) {
					clearInterval($.global.autoPlayInterval);
					AutoPlay();
				}	
			}

			function AutoPlay() {

				$.global.autoPlayInterval = setInterval(function() {					

					if($.global.currentSlide < $.global.slideCount) {
						$.global.currentSlide++;
						Slide('forward'); 
					} else {
						//slide back to first
						Slide('forward'); 
						$.global.currentSlide = 1;						
					}

					UpdateNavStatus();	

				}, $.global.autoPlaySpeed);

			}

			function UpdateNavStatus() {

				//prev btn
				if($.global.currentSlide == 1) {
					$('#slide-left-btn').addClass("disabled");
				} else {
					$('#slide-left-btn').removeClass("disabled");
				}

				//next btn
				if($.global.currentSlide == $.global.slideCount) {
					$('#slide-right-btn').addClass("disabled");
				} else {
					$('#slide-right-btn').removeClass("disabled");
				}

			}
				
			function Slide(direction) {
		
				if (direction == 'back') { var $target = $.global.item - 1; }
				if (direction == 'forward') { var $target = $.global.item + 1; }  
				
				if ($target == -1) { 

					DoSlide($.global.total-1); 

				} else if ($target == $.global.total) { 

					DoSlide(0); 

				} else { 

					DoSlide($target); 

				}
			
			}

			function DoSlide(target) {
			
				var $windowwidth = $.global.windowWidth;
				var $margin = $windowwidth * target; 
				var $actualtarget = target+1;
				
				$("#slides li:nth-child("+$actualtarget+")").addClass('alive');
				
				$('#slides').css('transform','translate3d(-'+$margin+'px,0px,0px)');	
				
				$.global.item = target; 
				
				$('#count').html($.global.item+1);
				
			}

		}//end of slider check		

		$(window).resize(function() {

			if($("#slide-window").length > 0) {
				
				//recalculate slider window width
				$.global.windowWidth = $("#slide-window").width();
				$.global.slidesWidth = $.global.slideCount * $.global.windowWidth;

				$('#slide-wrapper').css('width', $.global.windowWidth+'px');
				$('.slide').css('width', $.global.windowWidth+'px');
				$('#slides').css('width', $.global.slidesWidth+'px');
			}

			
		});

	/* ==========================================================================
		PrettyPhoto activation
		========================================================================== */
		if( $("a[data-rel^='prettyPhoto']").length > 0 ){
		  							
			$("a[data-rel^='prettyPhoto']").prettyPhoto({
				animation_speed: 'normal', /* fast/slow/normal */
				slideshow: false, /* false OR interval time in ms */
				autoplay_slideshow: false, /* true/false */
				opacity: 0.80, /* Value between 0 and 1 */
				show_title: true, /* true/false */
				allow_resize: true, /* Resize the photos bigger than viewport. true/false */
				default_width: 500,
				default_height: 344,
				counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
				theme: 'dark_square', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
				horizontal_padding: 20, /* The padding on each side of the picture */
				hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
				wmode: 'opaque', /* Set the flash wmode attribute */
				autoplay: false, /* Automatically start videos: True/False */
				modal: false, /* If set to true, only the close button will close the window */
				deeplinking: true, /* Allow prettyPhoto to update the url to enable deeplinking. */
				overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
				keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
				changepicturecallback: function(){}, /* Called everytime an item is shown/changed */
				callback: function(){}, /* Called when prettyPhoto is closed */
				ie6_fallback: false,
				markup: '<div class="pp_pic_holder"> \
							<div class="ppt">&nbsp;</div> \
							<div class="pp_top"> \
								<div class="pp_left"></div> \
								<div class="pp_middle"></div> \
								<div class="pp_right"></div> \
							</div> \
							<div class="pp_content_container"> \
								<div class="pp_left"> \
								<div class="pp_right"> \
									<div class="pp_content"> \
										<div class="pp_loaderIcon"></div> \
										<div class="pp_fade"> \
											<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
											<div class="pp_hoverContainer"> \
												<a class="pp_next" href="#">next</a> \
												<a class="pp_previous" href="#">previous</a> \
											</div> \
											<div id="pp_full_res"></div> \
											<div class="pp_details"> \
												<div class="pp_nav"> \
													<a href="#" class="pp_arrow_previous">Previous</a> \
													<p class="currentTextHolder">0/0</p> \
													<a href="#" class="pp_arrow_next">Next</a> \
												</div> \
												<p class="pp_description"></p> \
												{pp_social} \
												<a class="pp_close" href="#">Close</a> \
											</div> \
										</div> \
									</div> \
								</div> \
								</div> \
							</div> \
							<div class="pp_bottom"> \
								<div class="pp_left"></div> \
								<div class="pp_middle"></div> \
								<div class="pp_right"></div> \
							</div> \
						</div> \
						<div class="pp_overlay"></div>',
				gallery_markup: '<div class="pp_gallery"> \
									<a href="#" class="pp_arrow_previous">Previous</a> \
									<div> \
										<ul> \
											{gallery} \
										</ul> \
									</div> \
									<a href="#" class="pp_arrow_next">Next</a> \
								</div>',
				image_markup: '<img id="fullResImage" src="{path}" />',
				flash_markup: '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
				quicktime_markup: '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',
				iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',
				inline_markup: '<div class="pp_inline">{content}</div>',
				custom_markup: '',
				social_tools: false
		
				
			});
			
		}	
		
	
		//Ajax load more button
		if( $("#load_more_btn").length > 0 ) {

			$("#load_more_btn").on('click', function(e) {

				e.preventDefault();

				if(!options.dataIsLoading) {

					$("#fw_spinner").addClass("active");

					var $this = $(this),
					gallery_id = $this.data("gallery_id"),
					limit = $this.data("limit"),
					total_items = $this.data("total_items"),
					//order = $this.data("order"),
					page = ++options.pageCount;

					methods.loadAjaxPosts(page, gallery_id, limit, total_items);

					$this.css({
						opacity : '.5'
					});					

				}

			});

		}		
		 
	/* ==========================================================================
	   Options
	   ========================================================================== */
		 var options = {
			pageCount : 0,
			dataIsLoading : false
		}	
	   
	/* ==========================================================================
	   Methods
	   ========================================================================== */
	   var methods = {

			loadAjaxPosts : function(page, gallery_id, limit, total_items) {

				options.dataIsLoading = true;

				var ajax_data = {
					action : 'fw_load_more',
					nonce : ajaxOptions.nonce,
					page : page,
					gallery_id : gallery_id,
					limit : limit,
					total_items : total_items
				}

				var jqxhr = $.post( ajaxOptions.ajax_url, ajax_data, function(data) {	

					var ajaxData = JSON.parse(data),
					load_more_data = ajaxData.load_more_data,
					content = ajaxData.content;					

					//console.log(content);

					$('#ajax_posts_container').append(content);

					if(load_more_data) {
						$("#load_more_btn_container").show();
					} else {
						$("#load_more_btn_container").hide(); 
						
					}

				})
				.done(function() {
					//console.log( "second success" );
				})
				.fail(function() {
					console.log( "error" );
				})
				.always(function() {

					//console.log( "finished" );

					options.dataIsLoading = false;

					$("#fw_spinner").removeClass("active");
					$(".fw-cat-spinner").remove();

					$("#load_more_btn").css({
						opacity : '1'
					});

					methods.loadPrettyPhoto();

				});

			},

			loadPrettyPhoto : function() {
				
				if( $("a[data-rel^='prettyPhoto']").length > 0 ){
		  							
					$("a[data-rel^='prettyPhoto']").prettyPhoto({
						animation_speed: 'fast', /* fast/slow/normal */
						slideshow: 5000, /* false OR interval time in ms */
						autoplay_slideshow: false, /* true/false */
						opacity: 0.80, /* Value between 0 and 1 */
						show_title: true, /* true/false */
						allow_resize: true, /* Resize the photos bigger than viewport. true/false */
						default_width: 500,
						default_height: 344,
						counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
						theme: 'dark_square', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
						horizontal_padding: 20, /* The padding on each side of the picture */
						hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
						wmode: 'opaque', /* Set the flash wmode attribute */
						autoplay: true, /* Automatically start videos: True/False */
						modal: false, /* If set to true, only the close button will close the window */
						deeplinking: true, /* Allow prettyPhoto to update the url to enable deeplinking. */
						overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
						keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
						changepicturecallback: function(){}, /* Called everytime an item is shown/changed */
						callback: function(){}, /* Called when prettyPhoto is closed */
						ie6_fallback: true,
						markup: '<div class="pp_pic_holder"> \
									<div class="ppt">&nbsp;</div> \
									<div class="pp_top"> \
										<div class="pp_left"></div> \
										<div class="pp_middle"></div> \
										<div class="pp_right"></div> \
									</div> \
									<div class="pp_content_container"> \
										<div class="pp_left"> \
										<div class="pp_right"> \
											<div class="pp_content"> \
												<div class="pp_loaderIcon"></div> \
												<div class="pp_fade"> \
													<a href="#" class="pp_expand" title="Expand the image">Expand</a> \
													<div class="pp_hoverContainer"> \
														<a class="pp_next" href="#">next</a> \
														<a class="pp_previous" href="#">previous</a> \
													</div> \
													<div id="pp_full_res"></div> \
													<div class="pp_details"> \
														<div class="pp_nav"> \
															<a href="#" class="pp_arrow_previous">Previous</a> \
															<p class="currentTextHolder">0/0</p> \
															<a href="#" class="pp_arrow_next">Next</a> \
														</div> \
														<p class="pp_description"></p> \
														{pp_social} \
														<a class="pp_close" href="#">Close</a> \
													</div> \
												</div> \
											</div> \
										</div> \
										</div> \
									</div> \
									<div class="pp_bottom"> \
										<div class="pp_left"></div> \
										<div class="pp_middle"></div> \
										<div class="pp_right"></div> \
									</div> \
								</div> \
								<div class="pp_overlay"></div>',
						gallery_markup: '<div class="pp_gallery"> \
											<a href="#" class="pp_arrow_previous">Previous</a> \
											<div> \
												<ul> \
													{gallery} \
												</ul> \
											</div> \
											<a href="#" class="pp_arrow_next">Next</a> \
										</div>',
						image_markup: '<img id="fullResImage" src="{path}" />',
						flash_markup: '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
						quicktime_markup: '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',
						iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',
						inline_markup: '<div class="pp_inline">{content}</div>',
						custom_markup: '',
						social_tools: false
				
						
					});
					
				}	
				
			},

			kaiSlider : function() {

				

			}
		   
	   }//end of methods
		
	});

})(jQuery);