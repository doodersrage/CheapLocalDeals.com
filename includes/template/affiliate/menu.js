		  
jQuery(function(){

jQuery('.form_field').hover(
	function(){
		jQuery(this).css('background-color','#D7D7FD'); 
	},
	function(){
		jQuery(this).css('background-color','#FFF');
	}
);

});

var name = "#menu";
var menuYloc = null;

jQuery(function(){
	menuYloc = parseInt(jQuery(name).css("top").substring(0,jQuery(name).css("top").indexOf("px")))
	jQuery(window).scroll(function () { 
		offset = menuYloc+jQuery(document).scrollTop()+"px";
		jQuery(name).animate({top:offset},{duration:100,queue:false});
	});
}); 

