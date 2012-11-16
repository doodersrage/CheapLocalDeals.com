// JavaScript Document

jQuery(function(){
		
	// draw new alternate location form
   jQuery("#new_alt_loc").click(function () {
	  
	   $.ajax({
		 type: "POST",
		 url: "ajax_calls/advert_alt_loc_frm.deal",
		 success: function(msg){
		   jQuery("#alt_loc_form_area").html(msg);
		 }
	   });
	   
   });
		
	// draw new alternate location form
   jQuery("#alt_select_delete").click(function () {
	  var id = jQuery("#alt_select_id").val();
	  var advertiser_id = jQuery("#alt_advert_id").val();
	  
	   $.ajax({
		 type: "POST",
		 url: "ajax_calls/delete_advert_alt_loc.deal",
		 data: "id="+id+"&advertiser_id="+advertiser_id,
		 success: function(msg){
			 jQuery("#alt_message_area").html(msg);
			 $.ajax({
			   type: "POST",
			   url: "ajax_calls/advert_alt_loc_opt.deal",
			   data: "cid="+advertiser_id,
			   success: function(msg){
				 jQuery("#select_alt_area").html(msg);
			   }
			 });
		 }
	   });
	   
   });
		
	// draw update alternate location form
   jQuery("#alt_select_loc").click(function () {
	  var id = jQuery("#alt_select_id").val();
	  
	   $.ajax({
		 type: "POST",
		 url: "ajax_calls/advert_alt_loc_frm.deal",
		 data: "id="+id,
		 success: function(msg){
			 jQuery("#alt_loc_form_area").html(msg);
		 }
	   });
	   
   });
   
}); 

   
   // process new location
function save_alt_lox() {
	  
	  var id = jQuery("#alt_id").val();
	  var advertiser_id = jQuery("#alt_advert_id").val();
	  if (jQuery('#alt_location_enabled').attr('checked') == 1) {
		var enabled = jQuery("#alt_location_enabled").val();
	   } else {
		var enabled = 0;
	  }
	  var location_name = jQuery("#alt_location_name").val();
	  var products_services = jQuery("#alt_location_prods_servs").val();
	  var description = jQuery("#alt_location_description").val();
	  var website = jQuery("#alt_location_website").val();
	  if (jQuery('#alt_location_hide_address').attr('checked') == 1) {
		var hide_address = jQuery("#alt_location_hide_address").val();
	   } else {
		var hide_address = 0;
	  }
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
		 url: "ajax_calls/update_advert_alt_loc.deal",
		 data: "id="+id+"&advertiser_id="+advertiser_id+"&enabled="+enabled+"&location_name="+location_name+"&products_services="+products_services+"&description="+description+"&website="+website+"&hide_address="+hide_address+"&address_1="+address_1+"&address_2="+address_2+"&city="+city+"&state="+state+"&zip="+zip+"&phone_number="+phone_number+"&fax_number="+fax_number+"&email_address="+email_address+"&alt_loc_type="+alt_loc_type,
		 success: function(msg){
		   $.ajax({
			 type: "POST",
			 url: "ajax_calls/advert_alt_loc_opt.deal",
			 data: "cid="+advertiser_id,
			 success: function(msg){
			   jQuery("#select_alt_area").html(msg);
			 }
		   });
		   jQuery("#alt_message_area").html(msg);
		 }
	   });
	  
	   
}
   
