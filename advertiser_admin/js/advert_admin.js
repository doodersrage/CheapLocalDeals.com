		  
var loading_str = '<center><img src="/images/loading.gif"></center>';

function load_frag1() {
	
	load_frag1a();
				
}

function load_frag2() {
	
	load_frag2b();

}

function load_frag3() {
	
	load_frag3a();

}

jQuery(function() {

	jQuery('.error').css('margin-top',60+'px');
	jQuery('.error').css('left',235+'px');
	
	jQuery('#container-2').tabs(1);
	
	jQuery('.fragment-1').click(function() {
		load_frag1();
	});
	
	jQuery('.fragment-2').click(function() {
		load_frag2();
	});
	
	jQuery('.fragment-3').click(function() {
		load_frag3();
	});

});
 
function hide_error() {
  jQuery('.error').hide("medium");
//  jQuery('body').removeClass('error');
}

