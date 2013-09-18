wp.media.wpuMediaManager = {
	frame: function(clickedjQueryElement) {
		var origin = this.origin = clickedjQueryElement;
		var selection = this.select();
		
		this._frame = wp.media({
			id:         origin.data('frame-id'),                
			frame:      'post',
			state:      'gallery-edit',
			title:      origin.data('title'),
			editing:    true,
			multiple:   true,
			selection:	selection
		});
		
		this._frame.on( 'update', function(selection) {
		    // save the shortag into the meta
		    wp.media.post( 'wpu-media-manager-update', {
		        nonce:      wp.media.view.settings.post.nonce, 
		        html:       wp.media.wpuMediaManager.shortcode(selection).string(), //wp.media.wpuMediaManager.link,
		        post_id:    wp.media.view.settings.post.id,
		        elem_id:	origin.data('elem-id')
		    }).done(function(html){
		    	jQuery(origin.data('target')).val(html);
		    	wp.media.wpuMediaManager.update_counter();
		    }).fail(function(e){
		    	//TODO: manage errors :D
		    });
		 
		});
		
		return this._frame;
	},
	
	update_counter: function(){
		var origin = this.origin;
    	var label = '';
    	var counter = jQuery(origin.data('counter'));
    	var controller = wp.media.wpuMediaManager._frame.states.get('gallery-edit');
        var library = controller.get('library');
        // Need to get all the attachment ids for gallery
        var ids = library.pluck('id');
    	
    	switch(ids.length){
	    	case 0:
	    		label = counter.data('label-no-images');
	    		break;
	    	
	    	case 1:
	    		label = counter.data('label-one-image');
	    		break;
	    		
	    	default:
	    		label = counter.data('label-more-images').replace(/%s/g, ids.length);
	    		break;
    	}

    	counter.html(label);
    	
	},
 
    init: function() {
		jQuery('.media-manager-button').click(function(event){
			event.preventDefault();
			var clickedjQueryElement = jQuery(this);
			clickedjQueryElement.blur();
			jQuery(clickedjQueryElement.data('target-undo')).fadeOut();
			jQuery(clickedjQueryElement.data('target-delete')).fadeIn();
			wp.media.wpuMediaManager.frame(clickedjQueryElement).open();
		});
		
		jQuery('.delete-media-manager-gallery').click(function(){
			var element = jQuery(this);
			wp.media.post( 'wpu-media-manager-delete', {
		        nonce:      wp.media.view.settings.post.nonce, 
		        post_id:    wp.media.view.settings.post.id,
		        elem_id:	element.data('gallery')
		    }).done(function(html){
		    	element.fadeOut(function(){
		    		jQuery(element.data('target-undo')).data('undo-value',html).fadeIn();
		    	});
		    	jQuery(element.data('target')).val('');
		    	
		    	var clickedjQueryElement = jQuery(element.data('target-origin'));
		    	wp.media.wpuMediaManager.frame(clickedjQueryElement);
		    	wp.media.wpuMediaManager.update_counter();
		    }).fail(function(e){
		    	//TODO: manage errors :D
		    });
		 
		});
		
		jQuery('.undo-media-manager-gallery').click(function(){
			var element = jQuery(this);
			
		    wp.media.post( 'wpu-media-manager-update', {
		        nonce:      wp.media.view.settings.post.nonce, 
		        html:       element.data('undo-value'), 
		        post_id:    wp.media.view.settings.post.id,
		        elem_id:	element.data('elem-id')
		    }).done(function(html){
		    	jQuery(element.data('target')).val(html);
		    	jQuery(element.data('target-delete')).fadeIn();
		    	
		    	// FIXME: find a way to hook to correct callback
			    var test = setInterval(function(){
			    	var clickedjQueryElement = jQuery(element.data('target-origin'));
			    	wp.media.wpuMediaManager.frame(clickedjQueryElement);
			    	wp.media.wpuMediaManager.update_counter();
		    	}, 100);
			    setTimeout(function(){
			    	clearInterval(test);
			    }, 1000);
		    }).fail(function(e){
		    	//TODO: manage errors :D
		    });
			
			element.fadeOut();
		});
	},
	
	// Gets initial gallery-edit images. Function modified from wp.media.gallery.edit
	// in wp-includes/js/media-editor.js.source.html
	select: function() {
		var html = jQuery(this.origin.data('target')).val(),
	    	shortcode = wp.shortcode.next(this.origin.data('shortcode'), html),
	        defaultPostId = wp.media.gallery.defaults.id,
	        attachments, selection;
		
	    // Bail if we didn't match the shortcode or all of the content.
	    if ( ! shortcode ){
	        return;
		}
	 
	    // Ignore the rest of the match object.
	    shortcode = shortcode.shortcode;
	 
	    if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
	        shortcode.set( 'id', defaultPostId );
	 
	    attachments = wp.media.gallery.attachments( shortcode );
	    selection = new wp.media.model.Selection( attachments.models, {
	        props:    attachments.props.toJSON(),
	        multiple: true
	    });
	     
	    selection.gallery = attachments.gallery;
	 
	    // Fetch the query's attachments, and then break ties from the
	    // query to allow for sorting.
	    selection.more().done( function() {
	        // Break ties with the query.
	        selection.props.set({ query: false });
	        selection.unmirror();
	        selection.props.unset('orderby');
	    });
	    
	    return selection;
	},

	shortcode: function( attachments ) {
		var props = attachments.props.toJSON(),
			attrs = _.pick( props, 'orderby', 'order' ),
			shortcodeTag = this.origin.data('shortcode'), 
			shortcode, clone;

		if ( attachments.gallery )
			_.extend( attrs, attachments.gallery.toJSON() );

		// Convert all gallery shortcodes to use the `ids` property.
		// Ignore `post__in` and `post__not_in`; the attachments in
		// the collection will already reflect those properties.
		attrs.ids = attachments.pluck('id');

		// Copy the `uploadedTo` post ID.
		if ( props.uploadedTo )
			attrs.id = props.uploadedTo;

		// Check if the gallery is randomly ordered.
		if ( attrs._orderbyRandom )
			attrs.orderby = 'rand';
		delete attrs._orderbyRandom;

		// If the `ids` attribute is set and `orderby` attribute
		// is the default value, clear it for cleaner output.
		if ( attrs.ids && 'post__in' === attrs.orderby )
			delete attrs.orderby;

		// Remove default attributes from the shortcode.
		_.each( wp.media.gallery.defaults, function( value, key ) {
			if ( value === attrs[ key ] )
				delete attrs[ key ];
		});

		shortcode = new wp.shortcode({
			tag:    shortcodeTag,
			attrs:  attrs,
			type:   'single'
		});

		return shortcode;
	}
};
 
jQuery(document).ready(function(){
	jQuery(wp.media.wpuMediaManager.init);
});

