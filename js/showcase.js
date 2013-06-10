jQuery(document).ready(function(){
	$(".showcase").each(function(){
		var that = jQuery(this);
		that.awShowcase({
				content_height:			that.attr('data-height') ? that.attr('data-height') : 400,
				fit_to_parent:			true,
				auto:					true,
				interval:				that.attr('data-interval') ? that.attr('data-interval') : 3000,
				continuous:				true,
				loading:				true,
				tooltip_width:			450,
				tooltip_icon_width:		32,
				tooltip_icon_height:	32,
				tooltip_offsetx:		18,
				tooltip_offsety:		0,
				arrows:					true,
				buttons:				false,
				btn_numbers:			false,
				keybord_keys:			true,
				mousetrace:				false, /* Trace x and y coordinates for the mouse */
				pauseonover:			false,
				stoponclick:			true,
				transition:				'hslide', /* hslide/vslide/fade */
				transition_speed:		1000,
				transition_delay:		800,
				show_caption:			'show', /* onload/onhover/show */
				thumbnails:				false,
				thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
				thumbnails_direction:	'horizontal', /* vertical/horizontal */
				thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
				dynamic_height:			false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
				speed_change:			false, /* Set to true to prevent users from swithing more then one slide at once. */
				viewline:				true /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. It's OK with only the width. */
			});
	});
});