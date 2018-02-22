var store_locator_map;

var road_direction = 0;

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
                //jQuery('.leftsidebar').css('position','absolute');

                jQuery(".load-img").css("display", "none");
                jQuery(".overlay-store").css("display", "none");
                button.val(old_text);
                button.attr('disabled', false);

                var body_width = jQuery('body').width();

                jQuery("html, body").animate({scrollTop: jQuery('.col-left').offset().top - 30}, 2000);
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



    jQuery( "#search-location" ).keypress(function(e) {
        var search_location = jQuery(this).val();
        var search_radius = jQuery('#wpmsl-search-radius').val();
        if(e.keyCode == 13) {
            jQuery('#wpmsl-search-radius').focus();
            //wpmsl_update_map(search_location,search_radius);
        }
    });



});


function wpmsl_update_map(location_postition,search_radius,services_to_search) {

    // var input = document.getElementById('store_locatore_search_input');
    /* var autocomplete = new google.maps.places.Autocomplete(location_postition);
     var search_lat = ''; var search_lng = '';
     google.maps.event.addListener(autocomplete, 'place_changed', function () {
     var place = autocomplete.getPlace();
     search_lat = place.geometry.location.lat();
     search_lng = place.geometry.location.lng();
     }); */

    services_to_search = typeof services_to_search !== 'undefined' ? services_to_search : 'nothing';  // custom code to get services to search // Added by Mursaleen

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
                data: 'services_to_search=' + services_to_search + '&store_locatore_search_input='+location_postition+'&store_locatore_search_lat='+latitude+'&store_locatore_search_lng='+longitude+'&store_locatore_search_radius='+search_radius+'&store_locator_category=' + '&action=make_search_request' + '&lat=' + latitude + '&lng=' + longitude,
                //data: 'store_locatore_search_input='+location_postition+'&store_locatore_search_lat='+latitude+'&store_locatore_search_lng='+longitude+'&store_locatore_search_radius='+search_radius+'&store_locator_category=' + '&action=make_search_request' + '&lat=' + latitude + '&lng=' + longitude,
                type: 'post',
                success: function (html) {
                    jQuery('#store_locatore_search_results').html(html);
                    //console.log(html);
                    if (html.indexOf("No Clinics Found") >= 0) {
                        jQuery(".overlay-store").show();
                        jQuery(".load-img").show();
                        var default_location = getCookie('default_location');
                        //wpmsl_update_map(default_location,search_radius);
                    }
                    //(').attr('selected','selected');
                    var hh = jQuery( ".store-locator-item-container" ).detach();
                    hh.appendTo( ".col-left" );
                    hh.appendTo( ".map-listings" );
                    jQuery('.store-locator-item-container:nth-child(2)').remove();
                    //jQuery('.leftsidebar').css('position','absolute');
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
                    //wpmsl_update_map(default_location,search_radius);
                    //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                    jQuery(".load-img").css("display", "none");
                    jQuery(".overlay-store").css("display", "none");
                }
            });

        }
    });
}

jQuery(document).ready(function() {
    var default_radius = getCookie('default_radius');
    //jQuery('#wpmsl-search-radius option[value='+default_radius+']').attr('selected','selected');
});

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

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
}

function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB,latlngsp) {

    road_direction = 1;

    directionsService.route({
        origin: pointA,
        destination: pointB,
        avoidTolls: true,
        avoidHighways: false,
        travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {

            if(store_locator_map_options.unit== 'km' || store_locator_map_options.unit== 'Km' ){

                var km = response.routes[0].legs[0].distance.value  / 1000;

                jQuery('.data-distance-'+latlngsp).html('Distance : '+km.toFixed(1) + " km");
            } else if(store_locator_map_options.unit == 'mile' || store_locator_map_options.unit ==  'Mile') {
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

function store_locator_map_initialize(locations) {

    var styledMapType = new google.maps.StyledMapType(
        JSON.parse(map_style(store_locator_map_options.map_style)),
        {name: 'Styled Map'});

    if ( store_locator_map_options.custom_style ) {
        var styledMapType = new google.maps.StyledMapType(
            JSON.parse(store_locator_map_options.custom_style),
            {name: 'Styled Map'});
    }

    var store_locator_map = new google.maps.Map(document.getElementById('store_locatore_search_map'), {
        zoom: 10,
        center: new google.maps.LatLng(41.923, 12.513),
        mapTypeControl: Number( store_locator_map_options.mapTypeControl ),
        // scrollwheel: Number( store_locator_map_options.scroll ),
        scrollwheel: false,
        streetViewControl: Number( store_locator_map_options.streetViewControl ),
        gestureHandling: 'cooperative',
        mapTypeId: google.maps.MapTypeId[ store_locator_map_options.type.toUpperCase() ]
    });

    store_locator_map.mapTypes.set('styled_map', styledMapType);
    store_locator_map.setMapTypeId('styled_map');


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

                var fenway = {lat: latitude, lng: longitude};

                var store_id = jQuery('#list-item-'+i).attr('data-store-id');
                var pano_address = jQuery('#pano-address-'+store_id).val();
                var geocoder = new google.maps.Geocoder();

                var streetViewService = new google.maps.StreetViewService();
                var STREETVIEW_MAX_DISTANCE = 100;

                var latLng = new google.maps.LatLng(latitude, longitude);

                streetViewService.getPanoramaByLocation(latLng, STREETVIEW_MAX_DISTANCE, function (streetViewPanoramaData, status) {

                    //jQuery('.pano-loader').show();
                    /*
                    if (status === google.maps.StreetViewStatus.OK) {
                        // ok
                        setTimeout(function(){

                            var panorama = new google.maps.StreetViewPanorama(
                                document.getElementById('pano-'+store_id), {
                                    position: fenway,
                                    pov: {
                                        heading: 34,
                                        pitch: 10
                                    },
                                    addressControlOptions: {
                                        position: google.maps.ControlPosition.BOTTOM_CENTER
                                    },
                                    linksControl: false,
                                    panControl: false,
                                    enableCloseButton: false
                                });

                            store_locator_map.setStreetView(panorama);
                        },100);
                        //setTimeout(function(){ jQuery('.pano-loader').hide(); },1500);
                    } else {
                        jQuery('#pano-'+store_id).height('0px');
                        //jQuery('.pano-loader').hide();
                    } 
                    */
                });



            }
        })(marker, i));
    }



    store_locator_map.fitBounds(bounds);

    
    jQuery('.store-direction').click(function (e) {

        e.preventDefault();
        e.stopImmediatePropagation();

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
        jQuery("html, body").animate({scrollTop: jQuery('#store-locator-id').offset().top -120 }, 300);
    });


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

        var marker_boune = jQuery(this).index() - 1;

        markers[marker_boune].setAnimation(google.maps.Animation.BOUNCE);
        marker_bouncing = marker_boune + 1;

        setTimeout(function() {
            markers[marker_boune].setAnimation(null);
        },2400);

        google.maps.event.trigger(markers[marker_boune], 'click');
        store_locator_map.panTo(markers[marker_boune].getPosition());
        jQuery('.wpsl-choose-location-btn').css('background-color','#9e9e9e');
        jQuery(this).find('.wpsl-choose-location-btn').css('background-color','#FF5A1A');
        //$('#store-locator-id')
        jQuery("html, body").animate({scrollTop: jQuery('#store-locator-id').offset().top -120 }, 300);
    });

}

// Bouning Marker on Hover
jQuery(document).ready(function() {

    jQuery(".search-options-btn").click(function(){
        jQuery(".store-search-fields").slideToggle();
    });

});

function map_style(map_style) {
    if(map_style == 1) {
        return '[]';
    } else if(map_style == 2) {
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#f5f5f5"      }    ]  },  {    "elementType": "labels.icon",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#f5f5f5"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#bdbdbd"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#eeeeee"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#e5e5e5"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#ffffff"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#dadada"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry",    "stylers": [      {        "color": "#e5e5e5"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#eeeeee"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#c9c9c9"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  }]';
    } else if (map_style == 3){
        return '[        {            "elementType": "geometry",            "stylers": [            {                "color": "#ebe3cd"            }        ]        },        {            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#523735"            }        ]        },        {            "elementType": "labels.text.stroke",            "stylers": [            {                "color": "#f5f1e6"            }        ]        },        {            "featureType": "administrative",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#c9b2a6"            }        ]        },        {            "featureType": "administrative.land_parcel",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#dcd2be"            }        ]        },        {            "featureType": "administrative.land_parcel",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#ae9e90"            }        ]        },        {            "featureType": "landscape.natural",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "poi",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "poi",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#93817c"            }        ]        },        {            "featureType": "poi.park",            "elementType": "geometry.fill",            "stylers": [            {                "color": "#a5b076"            }        ]        },        {            "featureType": "poi.park",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#447530"            }        ]        },        {            "featureType": "road",            "elementType": "geometry",            "stylers": [            {                "color": "#f5f1e6"            }        ]        },        {            "featureType": "road.arterial",            "elementType": "geometry",            "stylers": [            {                "color": "#fdfcf8"            }        ]        },        {            "featureType": "road.highway",            "elementType": "geometry",            "stylers": [            {                "color": "#f8c967"            }        ]        },        {            "featureType": "road.highway",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#e9bc62"            }        ]        },        {            "featureType": "road.highway.controlled_access",            "elementType": "geometry",            "stylers": [            {                "color": "#e98d58"            }        ]        },        {            "featureType": "road.highway.controlled_access",            "elementType": "geometry.stroke",            "stylers": [            {                "color": "#db8555"            }        ]        },        {            "featureType": "road.local",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#806b63"            }        ]        },        {            "featureType": "transit.line",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "transit.line",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#8f7d77"            }        ]        },        {            "featureType": "transit.line",            "elementType": "labels.text.stroke",            "stylers": [            {                "color": "#ebe3cd"            }        ]        },        {            "featureType": "transit.station",            "elementType": "geometry",            "stylers": [            {                "color": "#dfd2ae"            }        ]        },        {            "featureType": "water",            "elementType": "geometry.fill",            "stylers": [            {                "color": "#b9d3c2"            }        ]        },        {            "featureType": "water",            "elementType": "labels.text.fill",            "stylers": [            {                "color": "#92998d"            }        ]        }    ]';
    } else if( map_style == 4 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#212121"      }    ]  },  {    "elementType": "labels.icon",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#212121"      }    ]  },  {    "featureType": "administrative",    "elementType": "geometry",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "administrative.country",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9e9e9e"      }    ]  },  {    "featureType": "administrative.land_parcel",    "stylers": [      {        "visibility": "off"      }    ]  },  {    "featureType": "administrative.locality",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#bdbdbd"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#181818"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1b1b1b"      }    ]  },  {    "featureType": "road",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#2c2c2c"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8a8a8a"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "geometry",    "stylers": [      {        "color": "#373737"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#3c3c3c"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry",    "stylers": [      {        "color": "#4e4e4e"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#616161"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#757575"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#000000"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#3d3d3d"      }    ]  }]';
    } else if( map_style == 5 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#242f3e"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#746855"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#242f3e"      }    ]  },  {    "featureType": "administrative.locality",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry",    "stylers": [      {        "color": "#263c3f"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#6b9a76"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#38414e"      }    ]  },  {    "featureType": "road",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#212a37"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#9ca5b3"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#746855"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#1f2835"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#f3d19c"      }    ]  },  {    "featureType": "transit",    "elementType": "geometry",    "stylers": [      {        "color": "#2f3948"      }    ]  },  {    "featureType": "transit.station",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#d59563"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#17263c"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#515c6d"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#17263c"      }    ]  }]';
    } else if( map_style == 6 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8ec3b9"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1a3646"      }    ]  },  {    "featureType": "administrative.country",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#4b6878"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#64779e"      }    ]  },  {    "featureType": "administrative.province",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#4b6878"      }    ]  },  {    "featureType": "landscape.man_made",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#334e87"      }    ]  },  {    "featureType": "landscape.natural",    "elementType": "geometry",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#283d6a"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#6f9ba5"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#3C7680"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#304a7d"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#98a5be"      }    ]  },  {    "featureType": "road",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#2c6675"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#255763"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#b0d5ce"      }    ]  },  {    "featureType": "road.highway",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#023e58"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#98a5be"      }    ]  },  {    "featureType": "transit",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#1d2c4d"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#283d6a"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#3a4762"      }    ]  },  {    "featureType": "water",    "elementType": "geometry",    "stylers": [      {        "color": "#0e1626"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#4e6d70"      }    ]  }]';
    } else if( map_style == 7 ){
        return '[  {    "elementType": "geometry",    "stylers": [      {        "color": "#ebe3cd"      }    ]  },  {    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#523735"      }    ]  },  {    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#f5f1e6"      }    ]  },  {    "featureType": "administrative",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#c9b2a6"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#dcd2be"      }    ]  },  {    "featureType": "administrative.land_parcel",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#ae9e90"      }    ]  },  {    "featureType": "landscape.natural",    "elementType": "geometry",    "stylers": [      {        "color": "#fefdd3"      }    ]  },  {    "featureType": "poi",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "poi",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#93817c"      }    ]  },  {    "featureType": "poi.park",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#a5b076"      }    ]  },  {    "featureType": "poi.park",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#447530"      }    ]  },  {    "featureType": "road",    "elementType": "geometry",    "stylers": [      {        "color": "#f5f1e6"      }    ]  },  {    "featureType": "road.arterial",    "elementType": "geometry",    "stylers": [      {        "color": "#fdfcf8"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry",    "stylers": [      {        "color": "#f8c967"      }    ]  },  {    "featureType": "road.highway",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#e9bc62"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry",    "stylers": [      {        "color": "#e98d58"      }    ]  },  {    "featureType": "road.highway.controlled_access",    "elementType": "geometry.stroke",    "stylers": [      {        "color": "#db8555"      }    ]  },  {    "featureType": "road.local",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#806b63"      }    ]  },  {    "featureType": "transit.line",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "transit.line",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#8f7d77"      }    ]  },  {    "featureType": "transit.line",    "elementType": "labels.text.stroke",    "stylers": [      {        "color": "#ebe3cd"      }    ]  },  {    "featureType": "transit.station",    "elementType": "geometry",    "stylers": [      {        "color": "#dfd2ae"      }    ]  },  {    "featureType": "water",    "elementType": "geometry.fill",    "stylers": [      {        "color": "#03526b"      }    ]  },  {    "featureType": "water",    "elementType": "labels.text.fill",    "stylers": [      {        "color": "#92998d"      }    ]  }]';
    } else if(map_style == '' || map_style > 7 || map_style < 1) {
        return '[]';
    }
}

jQuery(window).on('resize', function() {
    
        if (jQuery(window).width() < 860) {
            jQuery('.col-left.leftsidebar').addClass('wpml_above_map');
        } 
        else{
            jQuery('.col-left.leftsidebar').removeClass('wpml_above_map');
        }
    
});
