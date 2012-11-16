// JavaScript Document
			  
var loading_str = '<center><img src="/images/loading.gif"></center>';

function load_frag1a() {

	jQuery("#fragment-1a").empty().html(loading_str);
$.ajax({
  url: "/customer_admin/sections/customer_address_update.deal",
  cache: false,
  success: function(html){
	jQuery("#fragment-1a").html(html);
  }
});
}

function load_frag1b() {

	jQuery("#fragment-1b").empty().html(loading_str);
$.ajax({
  url: "/customer_admin/sections/change_password.deal",
  cache: false,
  success: function(html){
	jQuery("#fragment-1b").html(html);
  }
});
}

function load_frag2a() {
	
		jQuery("#fragment-2a").empty().html(loading_str);
		$.ajax({
		  url: "/customer_admin/sections/previous_orders.deal",
		  cache: false,
		  success: function(html){
			jQuery("#fragment-2a").html(html);
		  }
		});

}

function load_frag2b() {
		jQuery("#fragment-2b").empty().html(loading_str);
		$.ajax({
		  url: "/customer_admin/sections/view_available_certificates.deal",
		  cache: false,
		  success: function(html){
			jQuery("#fragment-2b").html(html);
		  }
		});
}

function load_frag3a() {
		jQuery("#fragment-3a").empty().html(loading_str);
		$.ajax({
		  url: "/customer_admin/sections/available_balance.deal",
		  cache: false,
		  success: function(html){
			jQuery("#fragment-3a").html(html);
		  }
		});
}

function load_frag3b() {
		jQuery("#fragment-3b").empty().html(loading_str);
		$.ajax({
		  url: "/customer_admin/sections/add_funds.deal",
		  cache: false,
		  success: function(html){
			jQuery("#fragment-3b").html(html);
		  }
		});
}

jQuery(function() {

	jQuery('.error').css('margin-top',30+'px');
	jQuery('.error').css('margin-left',275+'px');
	
	jQuery('#container-2').tabs(1);
	jQuery('#container-3').tabs(1);
	jQuery('#container-4').tabs(1);
	jQuery('#container-5').tabs(1);
	
	jQuery('.fragment-1a').click(function() {
		load_frag1a();
	});
	
	jQuery('.fragment-1b').click(function() {
		load_frag1b();
	});
	
	jQuery('.fragment-2a').click(function() {
		load_frag2a();
	});
	
	jQuery('.fragment-2b').click(function() {
		load_frag2b();
	});
	
	jQuery('.fragment-3a').click(function() {
		load_frag3a();
	});
	
	jQuery('.fragment-3b').click(function() {
		load_frag3b();
	});
});
 
function hide_error() {
	jQuery('.error').hide("medium");
}

function update_cust_pass_proc() {
	
	var form = jQuery("#update_password_frm");  
	var serializedFormStr = form.serialize();  
	var withoutEmpties = serializedFormStr.replace(/[^&]+=\.?(?:&|$)/g, '') 
 
	$.ajax({
	  type: "POST",
	  url: "/customer_admin/form_actions/update_password.deal",
	  data: withoutEmpties,
	  cache: false,
	  success: function(html){
		if (html != '') {
			jQuery('.error_box').html(html);
		}
	}
	});		
}

function view_order_proc(order_id) {
 
	jQuery("#fragment-2a").empty().html(loading_str);
	
	$.ajax({
	  type: "POST",
	  url: "/customer_admin/sections/order_view.deal",
	  data: "id="+order_id,
	  cache: false,
	  success: function(html){
		if (html != '') {
			jQuery("#fragment-2a").html(html);
		}
	}
	});		
}

function update_address_proc() {
	
	var form = jQuery("#update_address_frm");  
	var serializedFormStr = form.serialize();  
	var withoutEmpties = serializedFormStr.replace(/[^&]+=\.?(?:&|$)/g, '') 
 
	$.ajax({
	  type: "POST",
	  url: "/customer_admin/form_actions/update_address.deal",
	  data: "submit="+"Update Account"+"&"+withoutEmpties,
	  cache: false,
	  success: function(html){
		if (html != '') {
			jQuery('.error_box').html(html);
			load_frag4();
		}
	}
	});		
}

function callank(ank) {
	window.location.href = ank;
}


function proc_coupon() {
 	
 	var coupon_code = jQuery("#coupon_code").val();
 
	jQuery("#fragment-3b").empty().html(loading_str);
	
	$.ajax({
	  type: "POST",
	  url: "/customer_admin/form_actions/process_coupon.deal",
	  data: "coupon_code="+coupon_code,
	  cache: false,
	  success: function(html){
		if (html != '') {
			jQuery("#fragment-3b").html(html);
		}
	}
	});		
}

