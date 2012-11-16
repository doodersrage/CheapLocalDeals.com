// JavaScript Document

var requirementsArray = [];

// draws requirements
function update_requirements(advertiser_id) {
	var dropdown = document.getElementById("cert_"+advertiser_id); 
	var index = dropdown.selectedIndex;
	var ddVal = dropdown.options[index].value; 
	
	jQuery("#review_"+advertiser_id).html(requirementsArray[advertiser_id][ddVal]);
	
}

function limitChars(textid, limit, infodiv) {
  var text = jQuery('#'+textid).val(); 
  var textlength = text.length;
  if(textlength > limit){
	jQuery('#' + infodiv).html('You cannot write more then '+limit+' characters!');
	jQuery('#'+textid).val(text.substr(0,limit));
	return false; 
  } else {
	jQuery('#' + infodiv).html('You have '+ (limit - textlength) +' characters left.');
	return true;
  }
}

function open_login_frm() {

  $.ajax({
	url: "/includes/ajaxcls/signin_frm.deal",
	cache: false,
	success: function(msg){
	  jQuery('.vote_form_area').html(msg);
	}
  });
  
}

function user_login() {

  var email_address = jQuery('#rev_email_address').val();
  var password = jQuery('#rev_password').val();

  $.ajax({
	type: "POST",
	url: "/includes/ajaxcls/login_process.deal",
	data: "email_address="+email_address+"&password="+password,
	cache: false,
	success: function(msg){
	  jQuery('.vote_form_area').html(msg);
	}
  });
}
 
jQuery(function(){
		   
	jQuery('.add_cart_img').hover(
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocarthover.gif');
		},
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocart.gif');
		}
	);

	jQuery('#rev_sub_btn').click(function(){
	  var review_txt = jQuery('.review_txt').val();
	  var rating = jQuery('input[name=\'star\']:checked').val();
	  var advert_id = jQuery('#advert_id').val();
	  var advert_alt_id = jQuery('#advert_alt_id').val();
	  var error = '';
	  	  
	  if (review_txt == '') {
		error += 'You did not enter a review.';
	  }
	  
	  if (error == '') {
		$.ajax({
		  type: "POST",
		  url: "/includes/ajaxcls/new_review.deal",
		  data: "review_txt="+review_txt+"&rating="+rating+"&advert_id="+advert_id+"&advert_alt_id="+advert_alt_id,
		  cache: false,
		  success: function(msg){
			jQuery("#rating_tbl").html('Thank you for submitting your review. Once approved it will be added to the current review listing.');
		  }
		});
	  } else {
		  alert(error);
	  }
		
	});
	
  jQuery('#word_count').keyup(function(){
	limitChars('word_count', 300, 'counter');
  });
  
});

function form_submit(form_name) {
	jQuery('#'+form_name).submit();
}

