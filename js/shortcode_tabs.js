jQuery(document).ready(function(){
	jQuery('.tab').eq(0).addClass('active');
	jQuery('.tabs').each(function(){
		var tabs = jQuery(this);
		tabs.find('a').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var current_link = jQuery(this);
			tabs.find('.active').removeClass('active');
			current_link.addClass('active');
			jQuery('.tab.active').removeClass('active');
			jQuery(current_link.attr('href')).addClass('active');
			if(current_link.attr('data-title')){
				jQuery('#tabs-title').html(current_link.attr('data-title'));
			}
			if(
				typeof(google)!='undefined'
				&& typeof(google.maps)!='undefined'
				&& typeof(window.map)!='undefined'
				&& typeof(map_info)!='undefined'
				&& current_link.attr('data-from') 
			){
				
				window.infowindow.close();
				google.maps.event.removeListener(window.tilesloaded_listener);
				window.directionsDisplay.setMap(window.map);
				
				window.directionsDisplay.setPanel(jQuery(current_link.attr('href')).find('.mCSB_container').get(0));
				
				window.directionsService.route({
						destination	: new google.maps.LatLng(map_info.point.lat, map_info.point.lng),
						origin		: current_link.attr('data-from'),
						travelMode	: (current_link.attr('data-route-type')) 
							? google.maps.TravelMode[current_link.attr('data-route-type')] 
							: google.maps.TravelMode.DRIVING
					}, 
					function(result, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							window.directionsDisplay.setDirections(result);
						}
					}
				);
			}
		});
	}).find('#'+jQuery('.tab.active').attr('id')).addClass('active');
});