jQuery(document).ready(function(){
	jQuery('#slideshow').doSlideshow();
});


jQuery.fn.doSlideshow = function() {
	var slideshow = jQuery(this);
	var id = slideshow.attr('id');
	if (
		typeof(window.preload_images[id]) == 'undefined' 
		|| typeof(window.preload_images[id].images) == 'undefined' 
		|| window.preload_images[id].images.length == 0
	){ return; }
	
	return this.each(function(){
		var images_container = jQuery(this);
		var status_box = images_container.find('.loading');
		var status_template = status_box.attr('data-description')+'';
		status_template.replace(/%total%/g, window.preload_images[id].images.length+'');
		var cycle_container = jQuery('<div/>',{'class':'cycle'});
		
		//Reset
		images_container.empty();
		
		jQuery.each(window.preload_images[id].images, function(index, image) {
			var img = jQuery('<img/>', {
				'class'		:	'cycle-image',
				'src'		:	image.src,
				'width'		:	image.width,
				'height'	:	image.height
			});
			
			cycle_container.append(
				jQuery('<div/>',{
					'class':'cycle-element'
				}).append(img)
			);
		});
		
		//initialize deferred object for imagesLoaded
		var dfd = images_container.imagesLoaded();
		
		//Everytime an image ends loading...
		dfd.progress(function(isOk, images, proper, broken ) {
			status_box.html(status_template.replace(/%number%/g, proper.length+''));
		});
		
		//When all images are loaded...
		dfd.always(function(){
			images_container.append(cycle_container);
			cycle_container.cycle({
				speed: 800,
				timeout: 3000,
				slideResize: false
			});	
			status_box.fadeOut(600);
		});
	
	});
}