// JavaScript Document
var curId = 1;
var maxId = 6;
var minId = 1;

// update page status on page load
jQuery(function() {
	butStatChk();
});

// allow user page selection
function pageSel(page){
	curId = page;
	jQuery(".newAdvert").css("display","none");
	jQuery("#frm"+page).css("display","block");
	butStatChk();
}

function selNextPage(){
	curId++;
	jQuery(".newAdvert").css("display","none");
	jQuery("#frm"+curId).css("display","block");
	butStatChk();
}

function selPrevPage(){
	curId--;
	jQuery(".newAdvert").css("display","none");
	jQuery("#frm"+curId).css("display","block");
	butStatChk();
}

// checks page status
function butStatChk(){
	
	if(curId >= maxId){
		jQuery("#subRht").css("display","none");
		jQuery("#subBtn").css("display","block");
	} else {
		jQuery("#subRht").css("display","block");
		jQuery("#subBtn").css("display","none");
	}
	
	if(curId > minId){
		jQuery("#subLft").css("display","block");
	} else {
		jQuery("#subLft").css("display","none");
	}
	
	jQuery(".frmLnks").css("font-weight","normal");
	jQuery(".frmLnks").css("font-size","1.4em");
	jQuery("#frmLnk"+curId).css("font-weight","700");
	jQuery("#frmLnk"+curId).css("font-size","1.5em");
	
}