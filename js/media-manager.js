/*jQuery(document).ready(function() {
	jQuery('.media-manager-button').each(function() {
		var toggleAnchor = jQuery(this);
		var input = jQuery(toggleAnchor.data('target'));
		var value = input.val();
		var toRet = '[gallery ids="%ids%"]';
		
		if(value == ''){
			value = '[gallery]';
		} else {
			value = toRet.replace(/%ids%/g, value);
		}
		
		toggleAnchor.on('click', function(event) {
			event.preventDefault();
			wp.media.gallery.edit(value).on('update', function(obj){
				var ids = [];
				obj.models.map(function(attachment){
					ids.push(attachment.id);
				});
				input.val(ids.join(','));
			}).on('delete', function(obj){
				console.log(obj);
			});
			
		});
	});
});*/

wp.media.wpuMediaManager = {
	frame: function() {
		if(this._frame)
			return this._frame;
		
		var selection = this.select();
		
		this._frame = wp.media({
			id:         'my-frame',                
			frame:      'post',
			state:      'gallery-edit',
			title:      wp.media.view.l10n.editGalleryTitle,
			editing:    true,
			multiple:   true,
			selection:	selection
		});
		
		this._frame.on( 'update', function() {
		    var controller = wp.media.wpuMediaManager._frame.states.get('gallery-edit');
		    var library = controller.get('library');
		    // Need to get all the attachment ids for gallery
		    var ids = library.pluck('id');
		 
		    // send ids to server
		    wp.media.post( 'wpu-media-manager-update', {
		        nonce:      wp.media.view.settings.post.nonce, 
		        html:       wp.media.wpuMediaManager.link,
		        post_id:    wp.media.view.settings.post.id,
		        ids:        ids
		    }).done( function() {
		        window.location = wp.media.wpuMediaManager.link;
		    });
		 
		});
		
		return this._frame;
	},
 
    init: function() {
		jQuery('.media-manager-button').click(function(event){
			event.preventDefault();
			wp.media.wpuMediaManager.frame().open();
		});
	},
	
	// Gets initial gallery-edit images. Function modified from wp.media.gallery.edit
	// in wp-includes/js/media-editor.js.source.html
	select: function() {
	    var shortcode = wp.shortcode.next( 'wpuCustomGallery', wp.media.view.settings.wpuCustomGallery.shortcode ),
	        defaultPostId = wp.media.gallery.defaults.id,
	        attachments, selection;
	 
	    // Bail if we didn't match the shortcode or all of the content.
	    if ( ! shortcode )
	        return;
	 
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
};
 
jQuery(document).ready(function(){
	jQuery(wp.media.wpuMediaManager.init);
});

