var store_locator_map;

var road_direction = 0;

//Bind events to the page
jQuery(document).ready(function (jQuery) {
    //bind select2 effect
    jQuery('#store_locator_category, #store_locator_tag').select2();

    //bind autocomplete
    var input = document.getElementById('store_locatore_search_input');
    var autocomplete = new google.maps.places.Autocomplete(input);
   
     //autocomplete.bindTo('bounds', store_locator_map);
     
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
         // wpmsl_update_map(input);
        var current_location = jQuery('#store_locatore_search_input').val();
        var radius = jQuery('#store_locatore_search_radius').val();
        wpmsl_update_map(current_location,radius);
       //jQuery('#store_locatore_search_btn').trigger('click');
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

    // ajax search

    jQuery('#store_locatore_search_btn').on('click', function (e) {
        e.stopPropagation();
       
        if (jQuery('#store_locatore_search_input').val().length > 0) {
            // do something
            jQuery(".load-img").css("display", "block");
            jQuery(".overlay-store").css("display", "block");
        }

         var address = jQuery(document).find("#store_locatore_search_input").val(); 
        var addresslat = jQuery(document).find("#store_locatore_search_lat").val();
        var addresslng = jQuery(document).find("#store_locatore_search_lng").val();

      
            
        
        if(!address){
            jQuery('#store_locatore_search_input').css('box-shadow','0px 0px 4px red');
            return ;
        }

        var button = jQuery(this);
        var old_text = button.val();
        button.val('Searching ...');
        button.attr('disabled', true);
        jQuery('#store_locatore_search_results').html('');
        jQuery('#map-container').show();

        jQuery( ".store-locator-item-container" ).remove();
        jQuery.ajax({
            url: ajax_url,
            data: jQuery('#store_locator_search_form').serialize() + '&action=make_search_request' + '&lat=' + addresslat + '&lng=' + addresslng,
            type: 'post',
            success: function (html) {

                jQuery('#store_locatore_search_results').html(html);

                var hh = jQuery( ".store-locator-item-container" ).detach();

                hh.appendTo( ".col-left" );
                hh.appendTo( ".map-listings" );

                jQuery(".load-img").css("display", "none");
                jQuery(".overlay-store").css("display", "none");
                button.val(old_text);
                button.attr('disabled', false);

                var body_width = jQuery('body').width();
                if(store_locator_map_options.mapscrollsearch){
                    jQuery("html, body").animate({scrollTop: jQuery('.col-left').offset().top - 120}, 2000);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Status: " + textStatus); alert("Error: " + errorThrown);
                jQuery(".load-img").css("display", "none");
                jQuery(".overlay-store").css("display", "none");
            }
        });

    });

	jQuery('#store_locator_search_form').submit(function () {
		 return false;
	});
});

function wpmsl_update_map(location_postition,search_radius) {
    location_postition = location_postition.replace('+','');
    location_postition = location_postition.replace('+','');
    location_postition = location_postition.replace('+','');
    jQuery('#search-location').val(location_postition);
    jQuery('img.load-img').show();
    jQuery('.col-right.right-sidebar').css('opacity','0.5');

    var geocoder = new google.maps.Geocoder();
    var address = location_postition;
    var latitude = ''; var longitude = '';
    geocoder.geocode( { 'address': address}, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK) {
            latitude = results[0].geometry.location.lat();
            longitude = results[0].geometry.location.lng();

            document.getElementById('store_locatore_search_lat').value = latitude;
            document.getElementById('store_locatore_search_lng').value = longitude;

            jQuery.ajax({
                url: ajax_url,
                data: 'store_locatore_search_input='+location_postition+'&store_locatore_search_lat='+latitude+'&store_locatore_search_lng='+longitude+'&store_locatore_search_radius='+search_radius+'&store_locator_category=' + '&action=make_search_request' + '&lat=' + latitude + '&lng=' + longitude,
                type: 'post',
                success: function (html) {
                    jQuery('#store_locatore_search_results').html(html);

                    if (html.indexOf("No Clinics Found") >= 0) {
                        jQuery(".overlay-store").show();
                        jQuery(".load-img").show();
                        var default_location = getCookie('default_location');
                        //wpmsl_update_map(default_location,search_radius);
                    }

                    var hh = jQuery( ".store-locator-item-container" ).detach();
                    hh.appendTo( ".col-left" );
                    hh.appendTo( ".map-listings" );
                    jQuery('.store-locator-item-container:nth-child(2)').remove();

                    jQuery(".load-img").css("display", "none");
                    jQuery(".overlay-store").css("display", "none");
                    jQuery('.col-right.right-sidebar').css('opacity','1');

                    // SET SHOW MORE BUTTON ON LISTING
                    if(jQuery('.store-locator-item').length < 6)
                        jQuery('.show-more').hide();
                    else
                        jQuery('.show-more').show();

                    var body_width = jQuery('body').width();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log('ERROR');
                    console.log(errorThrown);
                    console.log(textStatus);
                    var default_location = getCookie('default_location');

                    jQuery(".load-img").css("display", "none");
                    jQuery(".overlay-store").css("display", "none");
                }
            });

        }
    });
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function store_locator_map_initialize(locations) {
   
	if(store_locator_map_options.scroll == true){
		var scrolbyzoom = true;
	} else {
		var scrolbyzoom = false;
	}
    store_locator_map = new google.maps.Map(document.getElementById('store_locatore_search_map'), {
        zoom: 5,
        center: new google.maps.LatLng(41.923, 12.513),
        mapTypeControl: Number( store_locator_map_options.mapTypeControl ),
        scrollwheel: scrolbyzoom,
        streetViewControl: Number( store_locator_map_options.streetViewControl ),
        gestureHandling: 'cooperative',
        mapTypeId: google.maps.MapTypeId[ store_locator_map_options.type.toUpperCase() ]
    });

    var bounds = new google.maps.LatLngBounds();
    var infowindow = new google.maps.InfoWindow();

    // user location display
    var marker_one = store_locator_map_options.marker1;
    var marker_two = store_locator_map_options.marker2;

    if(store_locator_map_options.marker1_custom != '') {
        marker_one = store_locator_map_options.marker1_custom;
    }

    if(store_locator_map_options.marker2_custom != '') {
        marker_two = store_locator_map_options.marker2_custom;
    }

    var marker1 = new google.maps.Marker({
        position: new google.maps.LatLng(locations['center']['lat'], locations['center']['lng']),
        map: store_locator_map,
        animation: google.maps.Animation.DROP,
        icon: marker_one
    });
    //extend the bounds to include each marker's position
    bounds.extend(marker1.position);
    google.maps.event.addListener(marker1, 'click', function () {
        infowindow.setContent(document.getElementById("store_locatore_search_input").value);
        infowindow.open(store_locator_map, marker1);
    });
    var markers = [];
    for (i = 0; i < locations['locations'].length; i++) {
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations['locations'][i]['lat'], locations['locations'][i]['lng']),
            map: store_locator_map,
            animation: google.maps.Animation.DROP,
            icon: marker_two
            // icon: icon_set,
        });
        markers.push(marker);
        //extend the bounds to include each marker's position
        bounds.extend(marker.position);
        jQuery('.data-direction-infowindow-'+locations['locations'][i]['lat'].replace('.', '')+'-'+locations['locations'][i]['lng'].replace('.', '')).val(locations['locations'][i]['infowindow']);
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            var latitude = parseFloat( locations['locations'][i]['lat'] );
            var longitude = parseFloat( locations['locations'][i]['lng'] );

                    return function () {
                    infowindow.setContent(locations['locations'][i]['infowindow']);
                    infowindow.open(store_locator_map, marker);
                }
        })(marker, i));
    }
    // Set Zoom to 10 when no markers are exist
    if(markers.length < 1) {
        setTimeout(function() {
            store_locator_map.setZoom(10);
        },50);
    }
    store_locator_map.fitBounds(bounds);
    var marker_bouncing = 0;
    jQuery('.store-locator-item').on('click',function() {
        if(road_direction == 1) {
            var list_id = jQuery(this).attr('id');
            var address = jQuery('#store_locatore_search_input').val();
            var radius = jQuery('#store_locatore_search_radius').val();
            wpmsl_update_map(address,radius);
            setTimeout(function() {
               jQuery('#'+list_id).click();
            },2000);
            road_direction = 0;
        } 
        var marker_boune = jQuery(this).attr('data-marker');
        markers[marker_boune].setAnimation(google.maps.Animation.BOUNCE);
        marker_bouncing = marker_boune + 1;
        setTimeout(function() {
            markers[marker_boune].setAnimation(null);
        },2400);
        google.maps.event.trigger(markers[marker_boune], 'click');
        store_locator_map.panTo(markers[marker_boune].getPosition());
        jQuery('.wpsl-choose-location-btn').css('background-color','#9e9e9e');
        jQuery(this).find('.wpsl-choose-location-btn').css('background-color','#FF5A1A');
        jQuery(".store-search-fields").slideUp();
        jQuery("html, body").animate({scrollTop: jQuery('#store-locator-id').offset().top -120 }, 300);
    });
}
// Bouning Marker on Hover
jQuery(document).ready(function() {
    jQuery(".search-options-btn").click(function(){
        jQuery(".store-search-fields").slideToggle();
    });
});
