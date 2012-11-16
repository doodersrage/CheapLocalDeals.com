<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);
			
$page_output = '<form action="" method="post" name="img_frm"><table class="advertiser_form"><tr>'.(!empty($adv_info_tbl->image) ? '<td> 
<script type="text/javascript"> 
				jQuery(function(){
				   jQuery("#del_image_lnk").click(function () {
					  var current_advertiser_id = jQuery("#advert_id").val();
					  var current_old_image = jQuery("#old_image").val();
					  
					   $.ajax({
						 type: "POST",
						 url: "/advertiser_admin/form_actions/delete_image.deal",
						 data: "advert_id="+current_advertiser_id+"&image="+current_old_image,
						 success: function(msg){
						   jQuery("#del_image_lnk").css("display","none");
						   jQuery("#old_image").val("");
						   jQuery("#image_text").html("Image Deleted");
						   jQuery(\'.error\').html(html);
						   jQuery(\'.error\').show("medium");
						 }
					   });
				   });
				}); 
				</script><strong>Current Image:</strong><br /> <span id="image_text"><a class="thickbox" href="'.CONNECTION_TYPE.'images/customers/' . urlencode($adv_info_tbl->image) . '" target="blank"><img border="0" src="includes/resize_image.deal?image=customers/'.urlencode($adv_info_tbl->image).'&new_width='.LISTING_IMAGE_WIDTH.'&new_height='.LISTING_IMAGE_HEIGHT.'" alt="' . $adv_info_tbl->company_name . '" class="advertiser_img" /></a><br /><a class="thickbox" href="'.CONNECTION_TYPE.'images/customers/' . urlencode($adv_info_tbl->image) . '" target="blank"> ' . $adv_info_tbl->image . '</a></span><input id="old_image" name="old_image" type="hidden" value="'.$adv_info_tbl->image.'"></td>' : '').'<td><center><b>Add, change, or delete your image here.</b><br /><a class="del_image_lnk" id="del_image_lnk" href="javascript:void(0);"><img src="'.CONNECTION_TYPE.'images/delete-image.jpg" alt="Delete Image" border="0" /></a><link rel="stylesheet" type="text/css" href="/advertiser_admin/uploadify/uploadify.css" media="screen" />
<script type="text/javascript" src="/advertiser_admin/uploadify/jquery.uploadify.js"></script>
	<input type="file" name="fileInput" id="fileInput" />
<input type="hidden" name="advert_id" id="advert_id" value="'.$_SESSION['advertiser_id'].'"/>
<script type="text/javascript">
jQuery(function(){
jQuery(\'#fileInput\').fileUpload ({
\'uploader\'  : \'/advertiser_admin/uploadify/uploader.swf\',
\'script\'    : \'/advertiser_admin/uploadify/upload.php\',
\'cancelImg\' : \'/advertiser_admin/uploadify/cancel.png\',
\'auto\'      : true,
\'folder\'    : \'/images/customers/\'
});
});
</script></center></td></tr></table></form>';
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;

?>