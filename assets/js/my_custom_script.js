jQuery(document).ready(function(){
	if(jQuery('.store-search-fields').hasClass('show_store_locator')){
		jQuery('.store-search-fields').css('display', 'block');
	}
	else{
		jQuery('.store-search-fields').css('display', 'none');
	}
});