//Bind events to the page 
jQuery(document).ready(function (jQuery) {
    jQuery('#store_locator_country').change(function () {
        if (jQuery('#store_locator_country').val() == 'United States') {
            jQuery('#store_locator_state').parents('tr').show();
        } 
        else {
            jQuery('#store_locator_state').parents('tr').hide();
            jQuery('#store_locator_state').val('');
        }
    });
    
    // bind onchange event on selecting open/close store days 
    jQuery('#store_locator_hours input[type="radio"]').change(function () {
        if (jQuery(this).val() == '1') {
            var start_elem = jQuery(this).attr('name').replace('[status]', '[start]');
            var end_elem = jQuery(this).attr('name').replace('[status]', '[end]');
            jQuery('[name="'+start_elem+'"]').show();
            jQuery('[name="'+end_elem+'"]').show();
        } 
        else {
            var start_elem = jQuery(this).attr('name').replace('[status]', '[start]');
            var end_elem = jQuery(this).attr('name').replace('[status]', '[end]');
            jQuery('[name="'+start_elem+'"]').val('');
            jQuery('[name="'+start_elem+'"]').hide();
            jQuery('[name="'+end_elem+'"]').val('');
            jQuery('[name="'+end_elem+'"]').hide();
        }
    });
    
    jQuery('#store_locator_sales').select2();
    jQuery('#sales_manager_stores').select2();
    jQuery('#store_locator_grid_columns').select2();
    jQuery("#store_locator_grid_columns").on('change', function(){
        var data = jQuery(this).select2('data');
        var array = [];
        jQuery.each(data, function(index, val) {
            array[index]=val.id;
        });        
        jQuery("input[name='store_locator_grid[columns]']").val( array );
    });
    jQuery('#store_locator_single_items').select2();
    jQuery("#store_locator_single_items").on('change', function(){
        var data = jQuery(this).select2('data');
        var array = [];
        jQuery.each(data, function(index, val) {
            array[index]=val.id;
        });        
        jQuery("input[name='store_locator_single[items]']").val( array );
    });
	
	
	// initialize input widgets first
        jQuery('.start_time, .end_time').timepicker({
            'showDuration': true,
            'timeFormat': 'g:i a'
        });
		
		/*if(wpmsl_ajax_object_backend.screen ==  'store_locator'){
			store_locator_initializeMapBackend();
		}*/
		
         

	
});

    function  store_locator_initializeMapBackend() {
        jQuery('#map_loader').hide();
        // Handle google maps
        var oldMarker;
        var updateMapDuration;
        var mapOptions = {
            scrollwheel: false,
            zoom: 13,
            center: new google.maps.LatLng(1, 1)
        };

        //display default address on map
        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        setTimeout(function () {

            if (jQuery('#store_locator_lat').val() && jQuery('#store_locator_lng').val()) {
                var currentLatLng = new google.maps.LatLng(parseFloat(jQuery('#store_locator_lat').val()), parseFloat(jQuery('#store_locator_lng').val()));
                marker = new google.maps.Marker({
                    position: currentLatLng,
                    map: map
                });
                oldMarker = marker;
                map.setCenter(currentLatLng);
            } else {
                var addressString = jQuery('#store_locator_address').val();
                addressString     = (jQuery('#store_locator_city').val()) ?(addressString+" "+ jQuery('#store_locator_city').val()):addressString;
                addressString     = (jQuery('#store_locator_state').val()) ?(addressString+", "+ jQuery('#store_locator_state').val()):addressString;
                addressString     = (jQuery('#store_locator_country').val()) ?(addressString+" "+ jQuery('#store_locator_country').val()):addressString;
                addressString     = (jQuery('#store_locator_zipcode').val()) ?(addressString+" "+ jQuery('#store_locator_zipcode').val()):addressString;
                
                var address = (addressString) ? addressString : "United State";
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        jQuery('#store_locator_lat').val(results[0].geometry.location.lat());
                        jQuery('#store_locator_lng').val(results[0].geometry.location.lng());
                        var currentLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                        marker = new google.maps.Marker({
                            position: currentLatLng,
                            map: map
                        });
                        oldMarker = marker;
                        map.setCenter(currentLatLng);
                    }
                });
            }
        }, 1000);
        // move marker when click on map
		
        google.maps.event.addListener(map, "click", function (event) {
            marker = new google.maps.Marker({
                position: event.latLng,
                map: map
            });

            if (oldMarker != undefined)
                oldMarker.setMap(null);

            oldMarker = marker;
            jQuery('#store_locator_lat').val(event.latLng.lat());
            jQuery('#store_locator_lng').val(event.latLng.lng());
        });


        jQuery('#store_locator_address, #store_locator_city, #store_locator_zipcode, #store_locator_country, #store_locator_state').change(function () {
//        jQuery(document).on("change keypress", '#store_locator_address, #store_locator_city, #store_locator_zipcode, #store_locator_country, #store_locator_state'), function () {
            
            clearTimeout(updateMapDuration);
            jQuery('#map_loader').show();
            updateMapDuration = setTimeout(function () {
                var addressString = jQuery('#store_locator_address').val();
                addressString     = (jQuery('#store_locator_city').val()) ?(addressString+" "+ jQuery('#store_locator_city').val()):addressString;
                addressString     = (jQuery('#store_locator_state').val()) ?(addressString+", "+ jQuery('#store_locator_state').val()):addressString;
                addressString     = (jQuery('#store_locator_country').val()) ?(addressString+" "+ jQuery('#store_locator_country').val()):addressString;
                addressString     = (jQuery('#store_locator_zipcode').val()) ?(addressString+" "+ jQuery('#store_locator_zipcode').val()):addressString;
                console.log(addressString);
                var address = (addressString) ? addressString : "United State";
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        jQuery('#map_loader').hide();
                        jQuery('#store_locator_lat').val(results[0].geometry.location.lat());
                        jQuery('#store_locator_lng').val(results[0].geometry.location.lng());
                        var currentLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                        marker = new google.maps.Marker({
                            position: currentLatLng,
                            map: map
                        });
                        if (oldMarker != undefined)
                            oldMarker.setMap(null);

                        oldMarker = marker;
                        map.setCenter(currentLatLng);
                    }
                });
            }, 1000);
        });
    }


jQuery(document).ready(function (jQuery) {
    var custom_uploader;
    var caller;
    jQuery('.upload_image_button').click(function (e) {
        e.preventDefault();
        caller = jQuery(this);
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            caller.prev('input').val(attachment.url);
        });

        //Open the uploader dialog
        custom_uploader.open();
    });
 
});

