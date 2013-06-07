jQuery(document).ready(function(){
	jQuery('.slideshow-container').each(function(){
		
		var slideshow_container = jQuery(this);
		var slideshow = slideshow_container.find('.slideshow');
		var controls = slideshow_container.find('.controls');
		var cycle_container = slideshow_container.find('.cycle');
		
		
		/*var slideshow = jQuery('#slideshow');
		var cycle_container = slideshow.find('.cycle');
		var controls = slideshow.find('.controls');*/
		
		cycle_container.cycle({
			speed: 800,
			timeout: 3000,
			slideResize: false,
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
	});
});