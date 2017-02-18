var store_locator_map;

//Bind events to the page 
jQuery(document).ready(function (jQuery) {
    //bind select2 effect
    jQuery('#store_locator_category, #store_locator_tag').select2();
    
    //bind autocomplete 
    var input = document.getElementById('store_locatore_search_input');
    var autocomplete = new google.maps.places.Autocomplete(input);
	 google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('store_locatore_search_lat').value = place.geometry.location.lat();
            document.getElementById('store_locatore_search_lng').value = place.geometry.location.lng();
        });
	
	
    // get my location handling
	
    jQuery('#store_locatore_get_btn').on('click', function () {
        var button = jQuery(this);
        var old_text = button.val();
        
        button.val('Loading ...');
        button.attr('disabled', true);

        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude, 
                    lng: position.coords.longitude
                    };
					

                var geocoder = new google.maps.Geocoder;
                geocoder.geocode({
                    'location': pos
                }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            jQuery('#store_locatore_search_input').val(results[0].formatted_address);
                            jQuery('#store_locatore_search_lat').val(pos.lat);
                            jQuery('#store_locatore_search_lng').val(pos.lng);
                        } else {
                            jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                        }
                    } else {
                        jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                    }
                    button.val(old_text);
                    button.attr('disabled', false);
                });
            }, function () {
				 jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
                // handleLocationError(true, infoWindow, map.getCenter());
				  button.val(old_text);
                    button.attr('disabled', false);
            });
        } else {
			 jQuery('#store_locatore_search_input').attr('placeholder', "Couldn't be detected ...");
            // Browser doesn't support Geolocation
            // handleLocationError(false, infoWindow, map.getCenter());
			  button.val(old_text);
                    button.attr('disabled', false);
        }
    });


	
	/* function initMapDirection() {
    var pointA = new google.maps.LatLng(51.7519, -1.2578),
        pointB = new google.maps.LatLng(50.8429, -0.1313),
        myOptions = {
            zoom: 7,
            center: pointA
        },
        map = new google.maps.Map(document.getElementById('store_locatore_search_map'), myOptions),
        // Instantiate a directions service.
        directionsService = new google.maps.DirectionsService,
        directionsDisplay = new google.maps.DirectionsRenderer({
            map: map
        }),
        markerA = new google.maps.Marker({
            position: pointA,
            title: "point A",
            label: "A",
            map: map
        }),
        markerB = new google.maps.Marker({
            position: pointB,
            title: "point B",
            label: "B",
            map: map
        });

    // get route from A to B
    calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);

} */





    // ajax search
    jQuery('#store_locatore_search_btn').on('click', function () {
		
		if (jQuery('#store_locatore_search_input').val().length > 0) {
			// do something
			jQuery(".load-img").css("display", "block");
			jQuery(".overlay-store").css("display", "block");
		}
		
		
        var address = document.getElementById("store_locatore_search_input").value;
        var addresslat = document.getElementById("store_locatore_search_lat").value;
        var addresslng = document.getElementById("store_locatore_search_lng").value;

        if(!address){
            return ;
        }
        var button = jQuery(this);
        var old_text = button.val();
        button.val('Searching ...');
        button.attr('disabled', true);
        jQuery('#store_locatore_search_results').html('');
        jQuery('#map-container').show();
        // jQuery('#map_loader').show();
        jQuery( ".store-locator-item-container" ).remove();
                jQuery.ajax({
                    url: wpmsl_ajax_object.ajax_url,
                    data: jQuery('#store_locator_search_form').serialize() + '&action=wpmsl_make_search_request' + '&lat=' + addresslat + '&lng=' + addresslng+ '&security=' + wpmsl_ajax_object.wpmsl_make_search_request_nonce,
                    
					type: 'post',
                    success: function (html) {
						
                        jQuery('#store_locatore_search_results').html(html);
							jQuery.ajax({
								url: wpmsl_ajax_object.ajax_url,
								data: '&action=get_data_formap'+ '&security=' + wpmsl_ajax_object.get_data_formap_nonce,
								type: 'post',
								success: function (htmls) {
									// console.log(JSON.parse(htmls));
									var locations = JSON.parse(htmls);
									wpmsl_map_initialize(locations);
								},
								error: function(XMLHttpRequest, textStatus, errorThrown) { 
								alert("Status: " + textStatus); alert("Error: " + errorThrown); 
								jQuery(".load-img").css("display", "none");
								jQuery(".overlay-store").css("display", "none");
								} 
							});
						var hh = jQuery( ".store-locator-item-container" ).detach();
						// jQuery('.col-left').appendTo(hh);
						hh.appendTo( ".col-left" );
						jQuery('.leftsidebar').css('position','absolute');
						jQuery('.col-left').height(jQuery('#store_locatore_search_map').height()-22);
						
						// jQuery('.store-locator-item-container').height(jQuery('#store_locatore_search_map').height()/1.8);
						jQuery(".load-img").css("display", "none");
						jQuery(".overlay-store").css("display", "none");
						button.val(old_text);
                        button.attr('disabled', false);
						jQuery('.ob_stor-relocator').height(jQuery('#store_locatore_search_map').height());
						jQuery('.store-locator-item-container').height(jQuery('.leftsidebar').height()/2);
                    },
					error: function(XMLHttpRequest, textStatus, errorThrown) { 
						alert("Status: " + textStatus); alert("Error: " + errorThrown); 
						jQuery(".load-img").css("display", "none");
						jQuery(".overlay-store").css("display", "none");
					} 
                });
				// jQuery(".load-img").css("display", "none");
				
				// initMapDirection();
     
    });
    
    // array to cache forms
    var storeGfForms = [];
    // before form shown
    jQuery(document).on('show.bs.modal', '.store_locator_gf_form', function() {
        var formContent = jQuery(this).find('.modal-body').html();
        if(storeGfForms[jQuery(this).attr('id')] === undefined ) {
            // caching form
            storeGfForms[jQuery(this).attr('id')] = formContent;
        }
        else {
            // display cached version of form
            jQuery('.modal-body', jQuery(this)).html(storeGfForms[jQuery(this).attr('id')]);
        }
    });
    // after form hidden
    jQuery(document).on('hidden.bs.modal', '.store_locator_gf_form', function() {
        // clear form to display caching version next time
        jQuery('.modal-body', jQuery(this)).html('');
    });

    jQuery(document).on('submit', '.gform_wrapper form', function(e) {
        var $this = jQuery(this);
        var modal = jQuery(this).closest('.modal');
        jQuery(window).one('scroll', function(){
            modal.animate({
                scrollTop: $this.offset().top
            });
        });
    });
	
	
	
	
		jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		jQuery( ".ob_stor-relocator" ).addClass( "full_width_div" );
		jQuery( ".ob_stor-relocator" ).append( '<img class="load-img" src="'+wpmsl_ajax_object.STORE_LOCATOR_PLUGIN_URL+'assets/img/loader.gif" width="350" height="350" >' );
		jQuery( ".ob_stor-relocator" ).append( '<div class="overlay-store"></div>' );
		jQuery( ".closesidebar" ).click(function() {
			jQuery( '.leftsidebar' ).toggleClass( "slide-left" );
			jQuery( this ).toggleClass( "arrow_right" );
		});
	
});

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
}

function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB,latlngsp) {
    directionsService.route({
        origin: pointA,
        destination: pointB,
        avoidTolls: true,
        avoidHighways: false,
        travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
	
				// console.log(response.routes[0].legs[0].distance.value );
				// console.log(wpmsl_ajax_object.store_locator_map_options.unit );
			if(wpmsl_ajax_object.store_locator_map_options.unit== 'km' || wpmsl_ajax_object.store_locator_map_options.unit== 'Km' ){
			    
			    		
				var km = response.routes[0].legs[0].distance.value  / 1000;
				
				jQuery('.data-distance-'+latlngsp).html('Distance : '+km.toFixed(1) + " km");
			} else if(wpmsl_ajax_object.store_locator_map_options.unit == 'mile' || wpmsl_ajax_object.store_locator_map_options.unit ==  'Mile') {
				var miles = response.routes[0].legs[0].distance.value*0.000621371192;
				jQuery('.data-distance-'+latlngsp).html('Distance : '+miles.toFixed(1) + " miles");
			}
			
			
			
        
            directionsDisplay.setDirections(response);
			directionsDisplay.setOptions( { 
						suppressMarkers: true,
						
						} );
			
			
			
			
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}



function wpmsl_map_initialize(locations) {

    var store_locator_map = new google.maps.Map(document.getElementById('store_locatore_search_map'), {
       zoom: 10,
        center: new google.maps.LatLng(41.923, 12.513),
        mapTypeControl: Number( wpmsl_ajax_object.store_locator_map_options.mapTypeControl ),
        scrollwheel: Number( wpmsl_ajax_object.store_locator_map_options.scroll ),
        streetViewControl: Number( wpmsl_ajax_object.store_locator_map_options.streetViewControl ),
        mapTypeId: google.maps.MapTypeId[ wpmsl_ajax_object.store_locator_map_options.type.toUpperCase() ]
    });
	
	
	
	
	 
	
    
    // check if there is style
    if ( wpmsl_ajax_object.store_locator_map_options.style ) {
        store_locator_map.set( 'styles', JSON.parse(wpmsl_ajax_object.store_locator_map_options.style) );
    }
    
    var bounds = new google.maps.LatLngBounds();
    var infowindow = new google.maps.InfoWindow();

    // user location display
	
    var marker1 = new google.maps.Marker({
        position: new google.maps.LatLng(locations['center']['lat'], locations['center']['lng']),
        map: store_locator_map,
        icon: wpmsl_ajax_object.store_locator_map_options.marker1
    });
    //extend the bounds to include each marker's position
    bounds.extend(marker1.position);
    google.maps.event.addListener(marker1, 'click', function () {
            infowindow.setContent(document.getElementById("store_locatore_search_input").value);
            infowindow.open(store_locator_map, marker1);
    });
    var markers = [];
    // stores location display
    for (i = 0; i < locations['locations'].length; i++) {
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations['locations'][i]['lat'], locations['locations'][i]['lng']),
            map: store_locator_map,
            icon: wpmsl_ajax_object.store_locator_map_options.marker2
        });
        markers.push(marker);

        //extend the bounds to include each marker's position
        bounds.extend(marker.position);
		//set data-direction-infowindow
		
		
		jQuery('.data-direction-infowindow-'+locations['locations'][i]['lat'].replace('.', '')+'-'+locations['locations'][i]['lng'].replace('.', '')).val(locations['locations'][i]['infowindow']);
			
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
			
			
			
			
			
            return function () {
                infowindow.setContent(locations['locations'][i]['infowindow']);
                infowindow.open(store_locator_map, marker);
            }
        })(marker, i));
    }

    if(wpmsl_ajax_object.store_locator_map_options.cluster == 1){
        var markerCluster = new MarkerClusterer(store_locator_map, markers, {gridSize: Number( wpmsl_ajax_object.store_locator_map_options.csize)});
    } 
    store_locator_map.fitBounds(bounds);


	
	jQuery('.store-direction').click(function () {
		var latlng = jQuery(this).attr('data-direction').split(',');
		var lat = latlng[0];
		var lng = latlng[1];

		 var pointA = new google.maps.LatLng(document.getElementById("store_locatore_search_lat").value,document.getElementById("store_locatore_search_lng").value),
        pointB = new google.maps.LatLng(lat, lng),
        myOptions = {
            zoom: 7,
            center: pointA
        },
        map = new google.maps.Map(document.getElementById('store_locatore_search_map'), myOptions),
        // Instantiate a directions service.
		
        directionsService = new google.maps.DirectionsService,
        directionsDisplay = new google.maps.DirectionsRenderer({
            map: map
        });
         markerA = new google.maps.Marker({
            position: pointA,
            title: "point A",
            label: "A",
            map: map
        }),
        markerB = new google.maps.Marker({
            position: pointB,
            title: "point B",
            label: "B",
            map: map
        }); 
		
		
	google.maps.event.addListener(markerA, 'click', function () {
		infowindow.setContent(document.getElementById("store_locatore_search_input").value);
		infowindow.open(store_locator_map, markerA);
	});
	
	google.maps.event.addListener(markerB, 'click', function () {
		infowindow.setContent(jQuery('.data-direction-infowindow-'+lat.replace('.', '')+'-'+lng.replace('.', '')).val());
		infowindow.open(store_locator_map, markerB);
	});
	var latlngsp = lat.replace('.', '')+'-'+lng.replace('.', '');
    // get route from A to B
    calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB,latlngsp);


	});

	
}







