jQuery(document).ready(function(){
	jQuery('.minigallery').each(function(index, value){
		var container 	=	jQuery(this);
		var thumbs 		=	container.find('.thumb-link');
		var big_img 	=	container.find('.big-image');
		var thumbs_list	=	container.find('.thumbs-list');
		var caption		=	container.find('.caption');
		
		thumbs.click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var anchor = jQuery(this);
			
			container.find('.current').removeClass('current');
			anchor.addClass('current');
			caption.fadeOut();
			big_img.fadeOut('slow', function(){
				caption.html('').html(anchor.attr('data-caption')).hide(0);
				big_img.html(
					jQuery('<img>',{
						"src":anchor.attr('href'),
						"alt":anchor.attr('title')
					})
				).fadeIn('slow', function(){
					if(caption.html()){
						caption.slideDown();
					}
				});
			});
		}).eq(0).click();
		
		thumbs_list.cycle({
			fx:'scrollHorz',
			next:container.find('.next'),
			prev:container.find('.prev'),
			pause:1
		}).cycle('pause');
	});
});