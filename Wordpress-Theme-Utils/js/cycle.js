jQuery(document).ready(function(){
	
	if(typeof(preload_images_default)!='undefined' && preload_images_default){
		var slideshow = jQuery('.cycle');
		var loading = slideshow.closest('.loading');
		var placeholder_text = loading_label_default.replace(/%lenght%/, preload_images_default.lenght);
		jQuery.each(preload_images_default, function(index, value){
			slideshow.append(jQuery('<img>',{
				src: value.url,
				alt: value.title,
				id: 'image_'+value.id
			}));
		});
		var dfd = slideshow.imagesLoaded();
		dfd.always( function(){
			slideshow.each(function(){
				var that = jQuery(this);
				that.cycle({
					prev	:	that.parent().find('.prev'),
					next	:	that.parent().find('.next'),
					pause	:	true,
					fx		:	'scrollHorz'
				});
			});
		});
		dfd.progress( function( isBroken, $images, $proper, $broken ){
			loading.html(placeholder_text.replace(/%number%/, $proper.length));
		});
	}
	jQuery('img').imagesLoaded(function($images, $proper, $broken){
		jQuery(this).fadeIn();
	});
});