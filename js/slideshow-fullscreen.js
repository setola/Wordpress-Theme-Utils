jQuery(document).ready(function(){
	jQuery('#slideshow').doSlideshow();
	jQuery('a.go-down-arrow').manageNavArrow(); 
});

jQuery(window).resize(function(){
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
		var cycle_container = jQuery('<div/>',{'class':'cycle-full'});
		
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
			img.imageResize();
		});
		
		cycle_container.css('display','none');
		images_container.append(cycle_container);
		
		//initialize deferred object for imagesLoaded
		var dfd = images_container.imagesLoaded();
		
		//Everytime an image ends loading...
		dfd.progress(function(isOk, images, proper, broken ) {
			status_box.html(status_template.replace(/%number%/g, proper.length+''));
		});
		
		//When all images are loaded...
		dfd.always(function(){
			//images_container.append(cycle_container);
			cycle_container.fadeIn().cycle({
				speed: 800,
				timeout: 3000,
				slideResize: 0,
				containerResize: 0
			});	
			status_box.fadeOut(600);
		});
	
	});
}


jQuery.fn.imageResize = function(){
	return this.each(function(){
		var element = jQuery(this);
		var images;
		if(element.is('img')){
			images = element;
		} else {
			images = element.find('img');
		}
		
		images.each(function(){
			var image = jQuery(this);
			//IE fix - test if it works plz...
			//no, it does not :|
			/*if (image.prop('naturalWidth') == undefined) {
				var $tmpImg = jQuery('<img/>').attr('src', image.attr('src'));
				image.prop('naturalWidth', $tmpImg[0].width);
				image.prop('naturalHeight', $tmpImg[0].height);
			}*/
			
			var dimensions = {
				/*natural	:	{
					width	:	image.prop('naturalWidth'),
					height	:	image.prop('naturalHeight'),
					ratio	:	image.prop('naturalWidth') / image.prop('naturalHeight')
				},*/
				natural	:	{
					width	:	image.width(),
					height	:	image.height(),
					ratio	:	image.width() / image.height()
				},
				window 	:	{
					width	:	jQuery(window).width(),
					height	:	jQuery(window).height(),
					ratio	:	jQuery(window).width() / jQuery(window).height()
				},
				image	:	{
					width	:	jQuery(window).width(),
					height	:	jQuery(window).height()
				},
				margins	:	{
					top		:	0,
					left	:	0
				}
			};
			
			var ratio = {
				width	:	dimensions.window.height / dimensions.natural.height, 
				height	:	dimensions.window.width / dimensions.natural.width
			};
			
			var ratio_to_use;
			
			if(ratio.width > ratio.height){
				ratio_to_use = ratio.width;
			} else {
				ratio_to_use = ratio.height;
			}
			
			dimensions.image.width = ratio_to_use * dimensions.natural.width;
			dimensions.image.height = ratio_to_use * dimensions.natural.height;

			dimensions.margins.left = Math.round((dimensions.window.width - dimensions.image.width) / 2);
			dimensions.margins.top = Math.round((dimensions.window.height - dimensions.image.height) / 2);
			
			image.css({
				width	:	dimensions.image.width,
				height	:	dimensions.image.height,
				'margin-left'	:	dimensions.margins.left,
				'margin-top'	:	dimensions.margins.top
			})
			.attr('width', dimensions.image.width)
			.attr('height', dimensions.image.height)
			.parent()
			.css({
				width	:	dimensions.window.width,
				height	:	dimensions.window.height
			});
			
		});
	});
};

jQuery.fn.manageNavArrow = function(){
	return this.each(function(){
		var that 				=	jQuery(this);
		var referenceElement	=	jQuery('#bookingform');
		var scrollPoint 		=	referenceElement.offset().top;
		var currentScrollPoint 	=	Math.abs(jQuery('html').offset().top);

		jQuery(window).scroll(function(){
			scrollPoint 		=	referenceElement.offset().top;
			var currentScrollPoint 	=	Math.abs(jQuery('html').offset().top);
			
			if(currentScrollPoint >= scrollPoint){
				that.addClass('flip');
			} else {
				that.removeClass('flip');
			}
		});
		
		jQuery(this).click(function(){
			if(that.hasClass('flip')){
				jQuery(document).scrollTo(0, 'slow');
			} else {
				jQuery(document).scrollTo(scrollPoint, 'slow');
			}
		});
	});
}
