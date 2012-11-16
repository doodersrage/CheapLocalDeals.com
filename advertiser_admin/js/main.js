		  
function load_frag1a() {

  jQuery("#fragment-1a").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/image_def.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-1a").html(html);
	}
  });
}

function load_frag1b() {
var test = 'tstt';
  jQuery("#fragment-1b").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/address_update.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-1b").html(html);
	}
  });
}

function load_frag1c() {
  
  jQuery("#fragment-1c").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/edit_business_information.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-1c").html(html);
	}
  });
  
}

function load_frag1d() {
  jQuery("#fragment-1d").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/alt_locations.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-1d").html(html);
	}
  });
}

function load_frag1e() {
  jQuery("#fragment-1e").empty().html(loading_str);
  $.ajax({
		url: "/advertiser_admin/sections/change_advertiser_password.deal",
		cache: false,
		success: function(html){
		  jQuery("#fragment-1e").html(html);
		}
  });
}

jQuery(function() {

	jQuery('.error').css('margin-top',60+'px');
	jQuery('.error').css('left',235+'px');
	
	jQuery('#container-3').tabs(1);
	
	jQuery('.fragment-1a').click(function() {
		load_frag1a();
	});
	
	jQuery('.fragment-1b').click(function() {
		load_frag1b();
	});
	
	jQuery('.fragment-1c').click(function() {
		load_frag1c();
	});
	
	jQuery('.fragment-1d').click(function() {
		load_frag1d();
	});
	
	jQuery('.fragment-1e').click(function() {
		load_frag1e();
	});
	
});

function disable(status) {
	$.ajax({
	  url: "/advertiser_admin/form_actions/disable_advert.deal",
	   data: "disable="+status,
	  cache: false,
	  success: function(html){
		  if (status == 1) {
			  jQuery('#disable_lnk').html('<a class="admin_menu" onclick="disable(0)" href="javascript:void(0);">Pause Ad (hide ad from listings)</a>');
		  } else {
			  jQuery('#disable_lnk').html('<a class="admin_menu" onclick="disable(1)" href="javascript:void(0);">Resume Ad (show ad in listings)</a>');
		  }
	  }
	});		
}
 
function update_address() {
  
  // set post values
  var form = jQuery("#address_update_frm");  
  var serializedFormStr = form.serialize();  
  var withoutEmpties = serializedFormStr.replace(/[^&]+=\.?(?:&|$)/g, '')
  
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/address_update.deal",
	data: "submit="+'Update Account'+"&"+withoutEmpties,
	cache: false,
	success: function(html){
	  if (html != '') {
		  jQuery('.error_box').html(html);
	  }
	  load_frag2();
  }
  });		
}

function change_password() {
  
  // set post values
  var password = jQuery('#password').val();
  var repassword = jQuery('#repassword').val();
  
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/change_password.deal",
	data: "submit="+'Change Password'+"&password="+password+"&repassword="+repassword,
	cache: false,
	success: function(html){
	  if (html != '') {
		  jQuery('.error_box').html(html);
	  }

  }
  });		
}

function update_business_info_proc() {
  
  var form = jQuery("#account_signup_form");  
  var serializedFormStr = form.serialize();  
  var withoutEmpties = serializedFormStr.replace(/[^&]+=\.?(?:&|$)/g, '')
  
  // set post values
  if (jQuery('#hide_address').attr('checked') == 1) {
  } else {
	  var hide_address = 0;
	  withoutEmpties += "&hide_address="+hide_address;
  }
  if (jQuery('#allow_multiple_logins').attr('checked') == 1) {
  } else {
	  var allow_multiple_logins = 0;
	  withoutEmpties += "&allow_multiple_logins="+allow_multiple_logins;
  }


  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/business_info.deal",
	data: withoutEmpties,
	cache: false,
	success: function(html){
	  if (html != '') {
		  jQuery('.error_box').html(html);
		  load_frag3();
	  }
  }
  });		
}

function renew_advert(status) {
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/renew_advertiser.deal",
	 data: "renew="+status,
	cache: false,
	success: function(html){
		jQuery('#renew_link').hide("medium");
	}
  });		
}


// added 9/17/2009 for advertiser alternate locations
		
	// draw new alternate location form
   function new_alt_loc() {
	  
	   $.ajax({
		 type: "POST",
		 url: "/advertiser_admin/form_actions/advert_alt_loc_frm.deal",
		 success: function(msg){
		   jQuery("#alt_loc_form_area").html(msg);
		 }
	   });
	   
   }
		
	// draw new alternate location form
   function alt_select_delete() {
	  var id = jQuery("#alt_select_id").val();
	  var advertiser_id = jQuery("#alt_advert_id").val();
	  
	   $.ajax({
		 type: "POST",
		 url: "/advertiser_admin/form_actions/delete_advert_alt_loc.deal",
		 data: "id="+id+"&advertiser_id="+advertiser_id,
		 success: function(msg){
			 jQuery("#alt_message_area").html(msg);
			 $.ajax({
			   type: "POST",
			   url: "/advertiser_admin/form_actions/advert_alt_loc_opt.deal",
			   data: "cid="+advertiser_id,
			   success: function(msg){
				 jQuery("#select_alt_area").html(msg);
			   }
			 });
		 }
	   });
	   
   }
		
	// draw update alternate location form
   function alt_select_loc() {
	  var id = jQuery("#alt_select_id").val();
	  
	   $.ajax({
		 type: "POST",
		 url: "/advertiser_admin/form_actions/advert_alt_loc_frm.deal",
		 data: "id="+id,
		 success: function(msg){
			 jQuery("#alt_loc_form_area").html(msg);
		 }
	   });
	   
   }
   
   // process new location
function save_alt_lox() {
	  
	  var id = jQuery("#alt_id").val();
	  var advertiser_id = jQuery("#alt_advert_id").val();
	  var enabled = jQuery("#alt_location_enabled").val();
	  var location_name = jQuery("#alt_location_name").val();
	  var products_services = jQuery("#alt_location_prods_servs").val();
	  var description = jQuery("#alt_location_description").val();
	  var website = jQuery("#alt_location_website").val();
	  var hide_address = jQuery("#alt_location_hide_address").val();
	  var address_1 = jQuery("#alt_location_address1").val();
	  var address_2 = jQuery("#alt_location_address2").val();
	  var city = jQuery("#alt_location_city").val();
	  var state = jQuery("#alt_location_state").val();
	  var zip = jQuery("#alt_location_zip").val();
	  var phone_number = jQuery("#alt_location_phone").val();
	  var fax_number = jQuery("#alt_location_fax").val();
	  var email_address = jQuery("#alt_location_email").val();
	  var alt_loc_type = jQuery("#alt_loc_type").val();

		
	  
	   $.ajax({
		 type: "POST",
		 url: "/advertiser_admin/form_actions/update_advert_alt_loc.deal",
		 data: "id="+id+"&advertiser_id="+advertiser_id+"&enabled="+enabled+"&location_name="+location_name+"&products_services="+products_services+"&description="+description+"&website="+website+"&hide_address="+hide_address+"&address_1="+address_1+"&address_2="+address_2+"&city="+city+"&state="+state+"&zip="+zip+"&phone_number="+phone_number+"&fax_number="+fax_number+"&email_address="+email_address+"&alt_loc_type="+alt_loc_type,
		 success: function(msg){
		   $.ajax({
			 type: "POST",
			 url: "/advertiser_admin/form_actions/advert_alt_loc_opt.deal",
			 data: "cid="+advertiser_id,
			 success: function(msg){
			   jQuery("#select_alt_area").html(msg);
			 }
		   });
		   jQuery("#alt_message_area").html(msg);
		 }
	   });
	  
	   
}
