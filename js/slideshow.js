jQuery(document).ready(function(){
	jQuery('.slideshow-container').doSlideshow();
});


jQuery.fn.doSlideshow = function() {
	var slideshow_container = jQuery(this);
	var slideshow = slideshow_container.find('.slideshow');
	var id = slideshow.attr('id');
	var controls = slideshow_container.find('.controls');
	if (
		typeof(window.preload_images[id]) == 'undefined' 
		|| typeof(window.preload_images[id].images) == 'undefined' 
		|| window.preload_images[id].images.length == 0
	){ return; }
	
	return slideshow.each(function(){
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
			controls.find('.play-pause').addClass('pause').click(function(){
				var control = jQuery(this);
				if(control.hasClass('pause')){
					cycle_container.cycle('pause');
					control.removeClass('pause').addClass('play');
				} else {
					cycle_container.cycle('resume');
					control.removeClass('play').addClass('pause');
				}
			});
			controls.find('.next').click(function(){
				cycle_container.cycle('next');
			});
			controls.find('.prev').click(function(){
				cycle_container.cycle('prev');
			});
			status_box.fadeOut(600);
		});
	
	});
}