jQuery(function(){
	jQuery('#footer_content').html(jQuery('.page_header_foot_content').html());
	jQuery('.page_header_foot_content').html('');
});

function update_requirements(selected_value) {
	
	jQuery("#review_"+selected_value).html(requirementsArray[selected_value][jQuery("#cert_"+selected_value).val()]);
	
}

function form_submit(form_name) {
	jQuery('#'+form_name).submit();
}

jQuery(function(){

	jQuery('.add_cart_img').hover(
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocarthover.gif');
		},
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocart.gif');
		}
	);

});
