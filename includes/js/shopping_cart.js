function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function update_quant(field_name,amt) {
	var field_value = parseFloat(jQuery('#'+field_name).val());
	var update_value = parseFloat(amt);
	var new_amt = field_value+update_value;
	
	if(field_value !== 0 && update_value !== 0) {
	  jQuery('#'+field_name).val(new_amt);
	} else if(field_value == 0 && update_value > 0) {
	  jQuery('#'+field_name).val(new_amt);
	}
}

jQuery(function(){
				
// var chkchk = readCookie('agreechk');
//
//  if(chkchk == 1) {
//	  jQuery('#agreement_txt_block').toggle();
//	  jQuery('#co_pay_opt').toggle();
//  }
//  
// $('#agree_check').click( function() {
//
//   if($(this).attr("checked") == 1) {
//	  jQuery('#agreement_txt_block').toggle();
//	  jQuery('#co_pay_opt').toggle();
//	  createCookie('agreechk',1,1)
//    }
//
//  })
 
 $('#agree_check').click( function() {
	  jQuery('#agreement_txt_block').toggle();
	  jQuery('#co_pay_opt').toggle();
  })

});