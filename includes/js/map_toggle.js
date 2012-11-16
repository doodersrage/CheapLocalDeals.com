// JavaScript Document

jQuery(function(){

jQuery('a.slide1').click(function() {
        var id = jQuery(this).attr('id');
		 
		if (jQuery('a.slide1').text() == 'Display Map') {
			jQuery('a.slide1').text('Hide Map');
			jQuery('#slidebox1').slideDown("medium");
		} else {
			jQuery('a.slide1').text('Display Map');
			jQuery('#slidebox1').slideUp("medium");
		}
		
     return false;
     });

	jQuery('#slidebox1head').hover(
		function(){
			jQuery(this).css('background-color','#DDF8DC'); 
		},
		function(){
			jQuery(this).css('background-color','#F2F2F2');
		}
	);
}); 

