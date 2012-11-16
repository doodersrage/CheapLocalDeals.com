		  
var loading_str = '<center><img src="/images/loading.gif"></center>';

function load_frag3a() {
	
  jQuery("#fragment-3a").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/reviews_awaiting.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-3a").html(html);
	}
  });

}

function load_frag3b() {
	
  jQuery("#fragment-3b").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/reviews_approved.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-3b").html(html);
	}
  });

}

jQuery(function() {

	jQuery('.error').css('margin-top',60+'px');
	jQuery('.error').css('left',235+'px');
	
	jQuery('#container-5').tabs(1);
	
	jQuery('.fragment-3a').click(function() {
		load_frag3a();
	});
	
	jQuery('.fragment-3b').click(function() {
		load_frag3b();
	});

});

function approve_review(review_id) {
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/approve_review.deal",
	 data: "review_id="+review_id,
	cache: false,
	success: function(html){
		jQuery('#reviewlst_'+cert_id).hide("medium");
	}
  });		
}

function disapprove_review(review_id) {
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/disapprove_review.deal",
	 data: "review_id="+review_id,
	cache: false,
	success: function(html){
		jQuery('#reviewlst_'+cert_id).hide("medium");
	}
  });		
}
