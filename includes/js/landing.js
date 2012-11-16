// JavaScript Document
// draws requirements
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

function lookup(search_box) {
    if(search_box.length == 0) {
        // Hide the suggestion box.
        jQuery('#suggestions').hide();
    } else {
        $.post("suggestions.deal", {queryString: ""+search_box+""}, function(data){
            if(data.length >0) {
                jQuery('#suggestions').show();
                jQuery('#autoSuggestionsList').html(data);
            }
        });
    }
} // lookup

function fill(thisValue) {
    jQuery('#search_box').val(thisValue);
   jQuery('#suggestions').hide();
}

var selected_itm = {
        'background-color' : '#47773D',
        'color' : '#fff'
      }
	  
var unselected_itm = {
        'background-color' : '#D9D9D9',
        'color' : '#666666'
      }

jQuery(function(){

	jQuery('#search_box').focus();

	jQuery('#search_box').keyup(function(){
			lookup(jQuery('#search_box').val());
	})

//	jQuery("body, input").keypress(function(e){
//			if(e.keyCode==38){
//				jQuery('#autoSuggestionsList li').prev().css(selected_itm).toggle();
//			}
//			if(e.keyCode==40){
//				jQuery('#autoSuggestionsList li').next().css(selected_itm).toggle();
//			}
//	});
	
});

//jQuery(function(){
//  jQuery("#search_box").keyup(function (e) { 
//        var code = (e.keyCode ? e.keyCode : e.which);
//
//        if (code == 40) {
//                if(jQuery("li.selected_itm").length == 0){
//                        jQuery("#autoSuggestionsList li").eq(0).addClass("selected_itm");
//                }else{
//                        jQuery("li.selected_itm").eq(0).removeClass("selected_itm").next().addClass("selected_itm");
//                }
//        };
//
////        if(code == 13) {
////                if(jQuery("li.hovered").length > 0){
////                        jQuery("#myInput").val(jQuery("li.hovered").eq(0).find("div.suggClass").eq(0).text());
////                }
////        }
//    });
//});

var currentSelection = 0;
var currentUrl = '';

// Register keypress events on the whole document
jQuery(document).keypress(function(e) {
  switch(e.keyCode) { 
	 // User pressed "up" arrow
	 case 38:
		navigate('up');
	 break;
	 // User pressed "down" arrow
	 case 40:
		navigate('down');
	 break;
	 // User pressed "enter"
	 case 13:
		if(currentUrl != '') {
		   window.location = currentUrl;
		}
	 break;
  }

	// Add data to let the hover know which index they have
	for(var i = 0; i < jQuery("#autoSuggestionsList ul li a").size(); i++) {
		jQuery("#autoSuggestionsList ul li a").eq(i).data("number", i);
	}
	
	// Simulote the "hover" effect with the mouse
	jQuery("#autoSuggestionsList ul li a").hover(
		function () {
			currentSelection = jQuery(this).data("number");
			setSelected(currentSelection);
		}, function() {
			jQuery("#autoSuggestionsList ul li a").removeClass("selected_itm");
			currentUrl = '';
		}
	);
});

function navigate(direction) {
   // Check if any of the menu items is selected
   if(jQuery("#autoSuggestionsList ul li .selected_itm").size() == 0) {
      currentSelection = -1;
   }
   
   if(direction == 'up' && currentSelection != -1) {
      if(currentSelection != 0) {
         currentSelection--;
      }
   } else if (direction == 'down') {
      if(currentSelection != jQuery("#autoSuggestionsList ul li").size() -1) {
         currentSelection++;
      }
   }
   setSelected(currentSelection);
}

function setSelected(menuitem) {
   jQuery("#autoSuggestionsList ul li a").removeClass("selected_itm");
   jQuery("#autoSuggestionsList ul li a").eq(menuitem).addClass("selected_itm");
   currentUrl = jQuery("#autoSuggestionsList ul li a").eq(menuitem).attr("href");
}
