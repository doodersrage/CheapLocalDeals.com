<?PHP
// assign previous url link
assign_previous_url_val();

// builds keywords string based on advert descripition string
function bldAdvertKeywords($desc = ''){
  // build keywords list
  $products_services = preg_replace('/[^a-zA-Z0-9]/', ' ', $desc);
	  
  $products_services_array = array();
  $products_services_array_res = array();
  $products_services_array = explode(' ',$products_services);
  foreach($products_services_array as $products_services_part) {
	  if (!empty($products_services_part)) {
		  $products_services_array_res[] = $products_services_part;
	  }
  }
  
  // put keywords list into string
  $products_services = implode(' ',$products_services_array_res);
  
  // disallowed title keywords list
  $disallowed_words = array(
						  'i',
						  'and',
						  'of',
						  'the',
						  'then',
						  'or',
						  'a',
						  'in',
						  'where',
						  'how',
						  'to'
						  );
  
  // build title keyword extension
  $title_keywords = array();
  $index_cnt = 0;
  for($i = 0;$i <= 6;$i++) {
	  if(!empty($products_services_array_res[$index_cnt])) {
		  if(array_search(strtolower($products_services_array_res[$index_cnt]), $disallowed_words) > 0) {
			  $i--;
		  } else {
			  $title_keywords[] = $products_services_array_res[$index_cnt];
		  }
	  }
	  $index_cnt++;
  }
  
  // put title keywords into string
  $title_keywords_string = implode(' ',$title_keywords);
  
  // remove ignored punctuation
  $title_keywords_strong = str_replace(array(',','.',';','?','[',']','(',')','@','/','*','<','>'),'',$title_keywords_string);

return $title_keywords_strong;
}

// print non-cert id info
if(!empty($_GET['ncid'])){
	
	// get business info
	$bus_tbl->get_db_vars($_GET['ncid']);
	$advert_image = '<img border="0" src="';
	$advert_image .= OVERRIDE_SITE_URL.'includes/resize_image.deal?image=&amp;new_width=150&amp;new_height=150';
	$advert_image .= '" alt="'. htmlentities($bus_tbl->name) .'" /><br/>';
	
	$page_output = '<center>';
	$page_output .= $advert_image;
	
	$website_lnk = (!empty($bus_tbl->url) ? '<a href="'.(strstr($bus_tbl->url,'http') == 0 ? 'http://'.$bus_tbl->url : $bus_tbl->url ).'" target="_blank" rel="nofollow"><u><b>'.htmlentities($bus_tbl->name).'</b></u></a>' : $bus_tbl->name).'<br/>'; 
	
	$page_output .= $website_lnk;

	$address_opp = '<span class="location_info">';
	$address_opp .= '<br/>'.$bus_tbl->address.'<br/>'
	.$bus_tbl->city.', '.$bus_tbl->state.' '.$bus_tbl->zip.'<br/>
	<a target="_blank" href="http://maps.google.com/maps?f=d&amp;source=s_d&amp;saddr=&amp;daddr='.str_replace(" ","+",$bus_tbl->address).',+'.str_replace(" ","+",$bus_tbl->city).',+'.$bus_tbl->state.'+'.$bus_tbl->zip.'&amp;hl=en&amp;geocode=&amp;mra=ls&amp;sll='.$bus_tbl->latitude.','.$bus_tbl->longitude.'&amp;sspn=27.146599,63.28125&amp;ie=UTF8&amp;z=16" style="text-decoration: underline; font-weight: bold;">Get Directions</a>';
	$address_opp .= ($bus_tbl->email != '' ? '<br/>Email: <a href="mailto:'.$bus_tbl->email.'">'.$bus_tbl->email.'</a>' : '');
	$address_opp .= '<br/><a href="tel:'.$bus_tbl->phone.'">'.$bus_tbl->phone.'</a></span>';
	
	$page_output .= $address_opp;
	$page_output .= '</center>';
	$page_output .= $bus_tbl->description;
	
	$title_keywords_strong = bldAdvertKeywords($bus_tbl->description);
	
	// set page header -- only assign for static header data
	$page_header_title = $bus_tbl->name.' - '.$title_keywords_string.' - CheapLocalDeals.com';
	$page_meta_description = preg_replace('/[^a-zA-Z0-9]/', ' ', $bus_tbl->name).'. '.$bus_tbl->description.'.';
	$page_meta_keywords = strtolower(str_replace('"', "'", $bus_tbl->name).' '.$bus_tbl->description);
	
// print certificate id info
} else {
  // load categories list
  if (!class_exists('location_info_pg')) {
	  require(MOB_CLASS.'locInfo.php');
	  $location_info_pg = new location_info_pg;
  }
  
  $title_keywords_strong = bldAdvertKeywords($adv_info_tbl->products_services);
  
  // gen page content
  $page_output = '<script src="'.CONNECTION_TYPE.'js_load.deal?js_doc[]='.urlencode('includes/js/loc_inf_advert_info.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.MetaData.js').'&amp;js_doc[]='.urlencode('includes/libs/star-rating/jquery.rating.js').'" type="text/javascript" language="javascript"></script>';
  $page_output .= '<center>'.$location_info_pg->assign_image().'<br/>'.$location_info_pg->print_website_lnk().'<br/>'.$location_info_pg->print_address();
  // load ratings module
  require(CLASSES_DIR.'sections/ratings.php');
  $adv_ratings = new adv_ratings;
  $page_output .= $adv_ratings->get_rating();
  $page_output .= '</center>';
  $page_output .= $location_info_pg->build_certificate_form();
  $page_output .= $location_info_pg->print_products_services();
  $page_output .= $location_info_pg->payment_methods_display();
  $page_output .= $location_info_pg->print_hours_of_operation();
  $page_output .= $location_info_pg->print_description();
  
  // set page header -- only assign for static header data
  $page_header_title = $adv_info_tbl->company_name.' - '.$title_keywords_string.' - CheapLocalDeals.com';
  $page_meta_description = preg_replace('/[^a-zA-Z0-9]/', ' ', $adv_info_tbl->company_name).'. '.$products_services.'.';
  $page_meta_keywords = strtolower(str_replace('"', "'", $adv_info_tbl->company_name).' '.$products_services);
}

$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>