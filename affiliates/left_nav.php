<?PHP


function prnt_left_nav() {
	global $dbh;

	// added to allow user lockout
	if ($_SESSION['allowed_access']['Affiliate System'] == 1) {
	
		$left_nav = T.'<ul>' . LB
		// affiliates section
		.T.'<li><h2>Affiliates</h2>' . LB;
		
		$section_link = array(
							array('name' => 'Existing Affiliates',
								  'link' => '?sect=affiliates&mode=view'),
							array('name' => 'Add Affiliates',
								  'link' => '?sect=affiliates&mode=add'),
							);
		
		$left_nav .= left_menu_sub($section_link);
		
		$left_nav .= T.'</li>' . LB;
		$left_nav .= T.'</ul>' . LB;
		
		// reports section
		$left_nav .= T.'<ul>' . LB;
		$left_nav .= T.'<li><h2>Affiliate Reports</h2>' . LB;
		
		$section_link = '';
		
		$section_link = array(
							array('name' => 'Advertiser Affiliate',
								  'link' => '?sect=affiliatesreports&mode=advertiser'),
							);
		
		$left_nav .= left_menu_sub($section_link);
		
		
		$left_nav .= T.'</li>' . LB;
		$left_nav .= T.'</ul>' . LB;
		
		// Zip Codes section
		$left_nav .= T.'<ul>' . LB;
		$left_nav .= T.'<li><h2>Advertiser to Advertiser Reports</h2>' . LB;
		
		$section_link = '';
		
		$section_link = array(
							array('name' => 'Advertisers Signed Up',
								  'link' => '?sect=salesrepreports&mode=signedup'),
							);
		
		$left_nav .= left_menu_sub($section_link);
		
		$left_nav .= T.'</li>' . LB;
		$left_nav .= T.'</ul>' . LB;
	}
	$left_nav .= T.'<ul>' . LB;
	$left_nav .= T.'<li><a href="'.SITE_URL.'logoff.php">Logout</a></li>' . LB;
	$left_nav .= T.'</ul>' . LB;
	
return $left_nav;
}


// print sub menu
function left_menu_sub($section_link) {

$left_nav .= T.'<ul>'.LB;

$cl_count = count($section_link);

for ($i = 0; $i < $cl_count; $i++) {
	$left_nav .= T.T.'<li>'.print_link(SITE_AFFILIATE_SSL_URL,$section_link[$i]['link'],$section_link[$i]['name']).'</li>'.LB;
}


$left_nav .= T.'</ul>' . LB;

return $left_nav;
}

?>