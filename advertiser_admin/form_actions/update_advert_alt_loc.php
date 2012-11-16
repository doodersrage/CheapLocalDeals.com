<?PHP
// load application top
require('../../includes/application_top.php');

$mode = $_POST['alt_loc_type'];

$required_fields = array(
						 'location_name',
						 'address_1',
						 'city',
						 'state',
						 'zip'
						 );

$error = array();

foreach($required_fields as $cur_req_fld){
	if(empty($_POST[$cur_req_fld])) $error[] = $cur_req_fld;
}

$error_message = '';
if(count($error) > 0) $error_message = 'You forgot to assign a value for these fields: '.implode(', ',$error);

if($error_message == '') {
  $adv_alt_loc_tbl->get_post_vars();
  
  if ($adv_alt_loc_tbl->id == NULL) {
	  $adv_alt_loc_tbl->insert();
 	  echo '<script type="text/javascript">alert(\'Alternate Location Added.\')</script>';	
	  echo '<script type="text/javascript">
	  jQuery(function(){
		jQuery("#alt_loc_form_area").html(\'\');
	  });
	  </script>';
 } else {
	  $adv_alt_loc_tbl->update();
	  echo '<script type="text/javascript">alert(\'Alternate Location Updated.\')</script>';	
	  echo '<script type="text/javascript">
	  jQuery(function(){
		jQuery("#alt_loc_form_area").html(\'\');
	  });
	  </script>';
  }
} else {
	echo '<script type="text/javascript">alert(\''.$error_message.'\')</script>';	
}

?>