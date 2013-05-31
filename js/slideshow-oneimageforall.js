jQuery(document).ready(function(){
	var slideshow = jQuery('#slideshow');
	var cycle_container = slideshow.find('.cycle');
	cycle_container.cycle({
		speed: 800,
		timeout: 3000,
		slideResize: false,
		next: slideshow.find('.next'),
		prev: slideshow.find('.prev')
	});	
});