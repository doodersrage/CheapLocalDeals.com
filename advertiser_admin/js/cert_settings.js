
var loading_str = '<center><img src="/images/loading.gif"></center>';

function load_frag2a() {
  jQuery("#fragment-2a").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/certificate_settings.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-2a").html(html);
	}
  });
}

function load_frag2b() {
  jQuery("#fragment-2b").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/active_certificates.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-2b").html(html);
	}
  });
}

function load_frag2c() {
  jQuery("#fragment-2c").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/disabled_certificates.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-2c").html(html);
	}
  });
}

function load_frag2d() {
  jQuery("#fragment-2d").empty().html(loading_str);
  $.ajax({
	url: "/advertiser_admin/sections/disable_certificate_byid.deal",
	cache: false,
	success: function(html){
	  jQuery("#fragment-2d").html(html);
	}
  });
}

jQuery(function() {

	jQuery('.error').css('margin-top',60+'px');
	jQuery('.error').css('left',235+'px');
	
	jQuery('#container-4').tabs(1);
	
	jQuery('.fragment-2a').click(function() {
		load_frag2a();
	});
	
	jQuery('.fragment-2b').click(function() {
		load_frag2b();
	});
	
	jQuery('.fragment-2c').click(function() {
		load_frag2c();
	});
	
	jQuery('.fragment-2d').click(function() {
		load_frag2d();
	});

});
 
function disable_cert_by_id() {
  
  // set post values
  var disable_certificate_id = jQuery('#disable_certificate_id').val();
  
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/dis_certificate_byid.deal",
	data: "submit="+'Disable Certificate'+"&certificate_id="+disable_certificate_id,
	cache: false,
	success: function(html){
	  if (html != '') {
		  jQuery('.error_box').html(html);
	  }
	  load_frag8();
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

function update_certificate_settings_proc() {
  
  var form = jQuery("#update_certificate_settings");  
  var serializedFormStr = form.serialize();  
  var withoutEmpties = serializedFormStr.replace(/[^&]+=\.?(?:&|$)/g, '') 

  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/update_certificates.deal",
	data: withoutEmpties,
	cache: false,
	success: function(html){
	  if (html != '') {
		  jQuery('.error_box').html(html);
		  load_frag4();
	  }
  }
  });		
}

function disable_cert(cert_id) {
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/unused_certificates.deal",
	 data: "disable="+cert_id,
	cache: false,
	success: function(html){
		jQuery('#certlst_'+cert_id).hide("medium");
	}
  });		
}

function enable_cert(cert_id) {
  $.ajax({
	type: "POST",
	url: "/advertiser_admin/form_actions/used_certificates.deal",
	 data: "enable="+cert_id,
	cache: false,
	success: function(html){
		jQuery('#certlst_en_'+cert_id).hide("medium");
	}
  });		
}

