jQuery(document).ready(function(){
	jQuery('.progressive-cycle-slideshow').each(function(){
		var slideshow = jQuery(this);
		var id = slideshow.attr('id');
		
		slideshow.cycle({
		    progressive: window.preload_images[id].images
		});
		
		
	});
});