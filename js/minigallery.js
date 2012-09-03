jQuery(document).ready(function(){
	jQuery('.minigallery').each(function(index, value){
		var container 	=	jQuery(this);
		var thumbs 		=	container.find('.thumb-link');
		var big_img 	=	container.find('.big-image');
		var thumbs_list	=	container.find('.thumbs-list');
		
		thumbs.click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var anchor = jQuery(this);
			
			container.find('.current').removeClass('current');
			anchor.addClass('current');
			
			big_img.fadeOut('slow', function(){
				big_img.html(
					jQuery('<img>',{
						"src":anchor.attr('href'),
						"alt":anchor.attr('title')
					})
				).fadeIn('slow');
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