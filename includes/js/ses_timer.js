
var orignum = num;
var session_close_delay = 240;
var session_clode_delay_mins = session_close_delay/60;
num = num - session_close_delay;
var timer;

function countdown(){
 if(num < 1){
	if(timer)
	   clearTimeout(timer);
	jQuery('.error_box').html('<script type="text/javascript">'+
			'jQuery(function(){'+
		'jQuery(\'.error\').css(\'margin-top\',jQuery(window).scrollTop()+150+\'px\');'+
		'jQuery(\'.error\').css(\'margin-left\',325+\'px\');'+
		'jQuery(".close_error").click(function() {'+
			'jQuery(\'.error\').hide("medium");'+
			'jQuery(\'#TB_overlay\').fadeOut("medium");'+
		'});'+
		'jQuery("#TB_overlay").addClass("TB_overlayBG");'+
		'jQuery(\'.error\').show("medium");'+
	'});'+
	'</script><div id=\'TB_overlay\'></div><div class="error"><div class="error_header">Notice!</div><div class="error_text">Your session is about to expire in '+session_clode_delay_mins+' minute(s).</div><div class="close_error" onclick="renew_sess()" >Click to continue session.</div></div>');
	num = session_close_delay;
	timer = '';
	log_out_countdown();
 }else{
	num--;
	if(!timer)
	   timer = setInterval("countdown()",1000);
 }
}

function renew_sess() {
	$.ajax({
	  url: "https://www.cheaplocaldeals.com/includes/renew_ses.deal",
	  cache: false,
	  success: function(html){
	}
	});	
	
	num = orignum - session_close_delay;
	timer = '';
	countdown();
}

function log_out_countdown(){
 if(num < 1){
	if(timer)
	   clearTimeout(timer);
		window.location="http://www.cheaplocaldeals.com/logoff.deal";
 }else{
	num--;
	if(!timer)
	   timer = setInterval("log_out_countdown()",1000);
 }
}

countdown();

