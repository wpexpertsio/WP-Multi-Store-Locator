jQuery(document).ready(function ($) {
	/*
	$(document).on('click','#maps-basic-settings', function(e){
		e.preventDefault();
		$(document).find('.maps-basic-settings').show();
		$(document).find('.maps-map-settings').hide();
		$(document).find('.maps-grid-settings').hide();
		$(document).find('.maps-dynamic-text').hide();
	});
	$(document).on('click','#maps-map-settings', function(e){
		e.preventDefault();
		$(document).find('.maps-map-settings').show();
		$(document).find('.maps-basic-settings').hide();
		$(document).find('.maps-grid-settings').hide();
		$(document).find('.maps-dynamic-text').hide();
	});
	$(document).on('click','#maps-grid-settings', function(e){
		e.preventDefault();
		$(document).find('.maps-grid-settings').show();
		$(document).find('.maps-basic-settings').hide();
		$(document).find('.maps-map-settings').hide();
		$(document).find('.maps-dynamic-text').hide();
	});
	$(document).on('click','maps-dynamic-text', function(e){
		e.preventDefault();
		$(document).find('.maps-dynamic-text').show();
	});
	*/
	$.each( $('#maps_tabs_menu h2 '), function(i, a) {
	   	$('a.nav-tab', a).each(function() {
		   	$(document).on('click','#'+$(this).attr('id'),function(e){
		   		console.log($(this).attr('id'));
		   		$.each( $('#maps_tabs_menu h2 '), function(i, a) {
				   	$('a.nav-tab', a).each(function() {

				   	});
			   	});	
		   	});
	   	});
	});

	jQuery('.metabox-tabs li a').each(function(i) {
		var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');

		if ( 'active' != jQuery(this).attr('class') )
			jQuery('div.' + thisTab).hide();
		
		jQuery('div.' + thisTab).addClass('tab-content');
 
		jQuery(this).click(function(){
			// hide all child content
			jQuery(this).parent().parent().parent().children('div').hide();
 
			// remove all active tabs
			jQuery(this).parent().parent('ul').find('li.active').removeClass('active');
 
			// show selected content
			jQuery(this).parent().parent().parent().find('div.'+thisTab).show();
			jQuery(this).parent().parent().parent().find('li.'+thisTab).addClass('active');
		});
	});

	jQuery('.heading').hide();
	jQuery('.metabox-tabs').show();
});