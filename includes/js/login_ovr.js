// JavaScript Document
function ldUsrFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/usrFrm.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}

function ldAdvFrm(){

	$.ajax({
	  type: "POST",
	  url: "/includes/ajaxcls/advFrm.deal",
	  cache: false,
	  success: function(msg){
		jQuery("#signUpFrmAr").html(msg);
	  }
	});
	
}
