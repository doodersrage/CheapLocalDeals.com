// JavaScript Document

function del_zip(){
	
  var zip_id = jQuery("#zip_codes").attr('value');
  var zip_codes_title = jQuery("#zip_codes").find("option:selected").attr("title");

   if(confirm('Are you sure you want to delete the '+zip_codes_title+' postal code?\n Deleting the here will remove it from the database.')){
	   $.ajax({
		 type: "POST",
		 url: "ajax_calls/del_zip.deal",
		 data: "zip_id="+zip_id,
		 success: function(msg){
		  $("#zip_codes option[value='"+zip_id+"']").remove();
		 }
	   });  
   }
}

function add_zip(){
	
  var new_zip = jQuery("#new_zip").val();
  var cityid = jQuery("#cityid").val();
	
   $.ajax({
	 type: "POST",
	 url: "ajax_calls/add_zip.deal",
	 data: "new_zip="+new_zip+"&cityid="+cityid,
	 success: function(msg){
	  $("#zip_codes").append('<option value="'+msg+'">'+new_zip+'</option>');
	  jQuery("#new_zip").val('');
	 }
   });
   
}

function ass_zip(){
  var all_zip_codes = jQuery("#all_zip_codes").attr('value');
  var all_zip_codes_title = jQuery("#all_zip_codes").find("option:selected").attr("title");
  if(all_zip_codes == ''){
	alert('You did not select a postal code!');
  } else {
	var cityid = jQuery("#cityid").val();
	  
	 $.ajax({
	   type: "POST",
	   url: "ajax_calls/ass_zip.deal",
	   data: "new_zip="+all_zip_codes+"&cityid="+cityid,
	   success: function(msg){
		$("#zip_codes").append('<option value="'+all_zip_codes+'" title="'+all_zip_codes_title+'">'+all_zip_codes_title+'</option>');
		$("#all_zip_codes option[value='"+all_zip_codes+"']").remove();
	   }
	 });
  }
}

function rem_zip(){
  var zip_codes = jQuery("#zip_codes").attr('value');
  var zip_codes_title = jQuery("#zip_codes").find("option:selected").attr("title");
  if(zip_codes == ''){
	alert('You did not select a postal code!');
  } else {
	var cityid = 0;
	  
	 $.ajax({
	   type: "POST",
	   url: "ajax_calls/ass_zip.deal",
	   data: "new_zip="+zip_codes+"&cityid="+cityid,
	   success: function(msg){
		$("#all_zip_codes").append('<option value="'+zip_codes+'" title="'+zip_codes_title+'">'+zip_codes_title+'</option>');
		$("#zip_codes option[value='"+zip_codes+"']").remove();
	   }
	 });
  }
}