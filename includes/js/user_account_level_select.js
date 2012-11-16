var curArray = [];
var requirementsArray = [];

jQuery(function(){
				
	jQuery('.advertiser_level_id').click(function() {
		jQuery("#review").html(curArray[jQuery('input[name=advertiser_level_id]:checked').val()]);
	});
	
	jQuery(".clickrow td").css("cursor","pointer");
	jQuery('#clickrow2 td').click(function(event) {
	if (event.target.type !== 'radio') {
	  jQuery(':radio', this).trigger('click');
	  jQuery("#review").html(curArray[jQuery(':radio', this).val()]);
	}
	});
});

