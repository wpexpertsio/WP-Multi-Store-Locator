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
                                var address = results[0].formatted_address;
                                if (!address) {
                                    return;
                                }
                                var geocoder = new google.maps.Geocoder();
                                geocoder.geocode({
                                    address: address
                                }, function (results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        jQuery.ajax({
                                            url: wpmsl_widgetjs.ajaxurl,
                                            data: 'action=wpmsl_get_nearby_stores_ajx' + '&lat=' + results[0].geometry.location.lat() + '&lng=' + results[0].geometry.location.lng() + "&limit=" + wpmsl_widgetjs.limit + "&distance=" + wpmsl_widgetjs.distance+ '&security=' + wpmsl_widgetjs.wpmsl_widgetjs_nonce,
                                            type: 'post',
                                            success: function (html) {
                                                jQuery("#store_locator_widget_results").html(html);
                                            }
                                        });

                                    }
                                });

                            } else {
                                
                            }
                        } else {
                            
                        }
                    });
                }, function () {

                });
            } else {

            }