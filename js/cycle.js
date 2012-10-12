jQuery(document).ready(function(){
	
	jQuery.each(window.preload_images, function(index, value){
		var slideshow = jQuery('#'+value.uid+' .cycle');
		var loading = slideshow.closest('.loading');
		var placeholder_text = value.loading.replace(/%lenght%/, value.lenght);
		
		jQuery.each(window.preload_images.slideshow.images, function(index, value){
			slideshow.append(jQuery('<img>',{
				src				:	value.src,
				alt				:	value.alt,
				title			:	value.title,
				'data-desc'		:	value.desc,
				'data-caption'	:	value.caption,
				id				:	'image_'+value.id
			}));
		});
		
		var dfd = slideshow.imagesLoaded();
		dfd.always(function(){
			slideshow.each(function(){
				var that = jQuery(this);
				that.cycle({
					prev	:	that.parent().find('.prev'),
					next	:	that.parent().find('.next'),
					pause	:	true,
					fx		:	that.attr('data-fx') || 'fade'
				});
			});
		});
		
		dfd.progress( function( isBroken, $images, $proper, $broken ){
			loading.html(placeholder_text.replace(/%number%/, $proper.length));
		});
	});
	
	jQuery('img').imagesLoaded(function($images, $proper, $broken){
		jQuery(this).fadeIn();
	});
});