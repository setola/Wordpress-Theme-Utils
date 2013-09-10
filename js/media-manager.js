jQuery(document).ready(function() {
	jQuery('.media-manager-button').each(function() {
		var toggleAnchor = jQuery(this);
		var customMediaFrame;
		toggleAnchor.on('click', function(event) {
			event.preventDefault();
			
			if (typeof (customMediaFrame) !== "undefined") {
				customMediaFrame.open();
				return;
			}
			
			customMediaFrame = wp.media.frames.customMediaFrame = wp.media({
				title : toggleAnchor.data('title'),
				button : {
					text : toggleAnchor.data('button-label'),
				},
				multiple : true
			});
			
			customMediaFrame.on('select', function(){
				var selection = customMediaFrame.state().get('selection');
				console.log(selection);
				selection.map(function(attachment){
					attachment = attachment.toJSON();
				});
				jQuery(toggleAnchor.data('target')).val(JSON.stringify(selection));
			});

			customMediaFrame.open();
		});
	});
});