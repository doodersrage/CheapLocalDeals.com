// JavaScript Document

var requirementsArray = [];

// draws requirements
function update_requirements(selected_value) {
	
	jQuery("#review_"+selected_value).html(requirementsArray[selected_value][jQuery("#cert_"+selected_value).val()]);
	
}

// sets previous page value
var previous_sel = 0;

// displays selected listing page
function set_page_val(page) {
  
  var page_link_name = '.pagelnk' + page;
  var page_list_name = '#listing_table' + page;
  var prev_page_link_name = '.pagelnk' + previous_sel;
  var prev_list = '#listing_table' + previous_sel;

  jQuery(page_link_name).css('color','#618d50');
  jQuery(page_link_name).css('text-decoration','underline');
  jQuery(page_list_name).fadeIn('slow');
  if (previous_sel > 0 && previous_sel != page) {
	  jQuery(prev_page_link_name).css('color','#000');
	  jQuery(prev_page_link_name).css('text-decoration','none');
	  jQuery(prev_list).css('display','none');
  }
  
  jQuery('html, body').animate({scrollTop:0}, 'fast'); 
   
  previous_sel = page;
}

jQuery(function(){
	set_page_val(1);

	jQuery('.add_cart_img').hover(
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocarthover.gif');
		},
		function(){
			jQuery(this).attr('src','http://www.cheaplocaldeals.com/images/addtocart.gif');
		}
	);

});

// section added for page links selection
var selected_page = 1;
var links_sect_name = '.links_sect_';

function previouslinkspage() {
	hide_current_page();
	selected_page--;
	jQuery(links_sect_name+selected_page).css('display','block')	
}

function hide_current_page() {
	jQuery(links_sect_name+selected_page).css('display','none');
}

function nextlinkspage() {
	hide_current_page();
	selected_page++;
	jQuery(links_sect_name+selected_page).css('display','block')
}

function form_submit(form_name) {
	jQuery('#'+form_name).submit();
}
