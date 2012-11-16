// JavaScript Document

// load user login select form
function lnchMnFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/usr_login_sel.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

// load customer signup form
function ldSignUp(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/popSignUp.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

// load create advertiser form
function ldAdvCreFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/advCreFrm.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

// load customer password form
function ldCustPassFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/custFgtPass.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

// load forget password form
function ldAdvPassFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/advFgtPass.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

// get city drop down data
function st_state(){

	var state_sel = jQuery("#state_sel").val();

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/cityDDgen.deal",
	  data: "state_sel="+state_sel,
	  cache: false,
	  success: function(msg){
		jQuery("#city_st_dd").html(msg);
	  }
	});
}

// assign location cookie values
function st_loc(){

	var state_sel = jQuery("#state_sel").val();
	var city_sel = jQuery("#city_sel").val();
	
	jQuery("#search_box").val(city_sel+", "+state_sel);

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/st_loc.deal",
	  data: "state_sel="+state_sel+"&city_sel="+city_sel,
	  cache: false,
	  success: function(msg){
	  }
	});
	
	ldEmailFrm();
}

// load email form
function ldEmailFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/email_frm.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

function isValidEmailAddress(emailAddress) {
  var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
return pattern.test(emailAddress);
}

function sub_eml(){

	var email_sub = jQuery("#email_sub").val();

	if(isValidEmailAddress(email_sub)){
	  $.ajax({
		type: "POST",
		url: "/includes/ajaxcls/set_sve_eml.deal",
		data: "email_sub="+email_sub,
		cache: false,
		success: function(msg){
			$('#zip_search').submit();
		}
	  });
	} else {
		jQuery("#pop_err").html("The entered address appears to be invalid.");
	}
}
