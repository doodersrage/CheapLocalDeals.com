<?PHP


function prnt_left_nav() {
  global $dbh;
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Advertisers'] == 1) {
	
	$section_link = array('title' => 'Advertisers',
						'links' => array(array('name' => 'Existing',
							  'link' => '?sect=retcustomer&mode=view'),
						array('name' => 'Add New',
							  'link' => '?sect=retcustomer&mode=add'),
						array('name' => 'Pending Approval',
							  'link' => '?sect=retcustomer&mode=pending_approval'),
						array('name' => 'Change Pending Approval',
							  'link' => '?sect=retcustomer&mode=change_pending_approval'),
						array('name' => 'Non-Cert Adverts',
							  'link' => '?sect=retcustomer&mode=noncertadverts'),
						array('name' => 'Reviews',
							  'link' => '?sect=retcustomer&mode=reviews'),
						array('name' => 'Payment Problems',
							  'link' => '?sect=retcustomer&mode=payment_problems'),
						array('name' => 'Deleted',
							  'link' => '?sect=retcustomerbackup&mode=view'),
						array('name' => 'Advertiser Levels',
							  'link' => '?sect=retcustomer&mode=customerlevels'),
						array('name' => 'Certificate Rates',
							  'link' => '?sect=retcustomer&mode=certificateamount'),
						array('name' => 'Payment Methods',
							  'link' => '?sect=retcustomer&mode=paymentmethods'),
						array('name' => 'Promo Codes',
							  'link' => '?sect=retcustomer&mode=promocodes'),
						array('name' => 'Download Excel Doc',
							  'link' => '?sect=retcustomer&mode=printcsv'),
						)
						);
	
	$left_nav = left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Customers'] == 1) {
	
	// regular customers section
	$section_link = array('title' => 'Customers',
						'links' => array(array('name' => 'Existing',
							  'link' => '?sect=regcustomer&mode=view'),
						array('name' => 'Add New',
							  'link' => '?sect=regcustomer&mode=add'),
						array('name' => 'Promo Codes',
							  'link' => '?sect=regcustomer&mode=promocodes'),
						array('name' => 'Balance Coupons',
							  'link' => '?sect=regcustomer&mode=balancecoupons'),
						array('name' => 'Download Excel Doc',
							  'link' => '?sect=regcustomer&mode=printcsv'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Zip Codes'] == 1) {
	
	// Zip Codes section
	$section_link = array('title' => 'Locations',
						'links' => array(array('name' => 'View/Edit States',
							  'link' => '?sect=states&mode=view'),
						array('name' => 'View/Edit Cities & Towns',
							  'link' => '?sect=cities&mode=view'),
						array('name' => 'View/Edit Zip Codes',
							  'link' => '?sect=zipcodes&mode=view'),
						array('name' => 'Download ZIPs CSV',
							  'link' => '?sect=zipcodes&mode=download'),
						array('name' => 'Upload Zips CSV',
							  'link' => '?sect=zipcodes&mode=upload'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Categories'] == 1) {
	
	// Categories section
	$section_link = array('title' => 'Categories',
						'links' => array(array('name' => 'View/Edit',
							  'link' => '?sect=categories&mode=view'),
						array('name' => 'Add New',
							  'link' => '?sect=categories&mode=add'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Pages'] == 1) {
	
	// Pages section
	$section_link = array('title' => 'Pages',
						'links' => array(array('name' => 'View/Edit',
							  'link' => '?sect=pages&mode=view'),
						array('name' => 'Add New',
							  'link' => '?sect=pages&mode=add'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Stats'] == 1) {
	
	// Stats section
	$section_link = array('title' => 'Stats',
						'links' => array(array('name' => 'Sessions',
							  'link' => '?sect=sessions&mode=view'),
						array('name' => 'Category Views',
							  'link' => '?sect=categories&mode=viewhits'),
						array('name' => 'Zip Code Views',
							  'link' => '?sect=zipcodes&mode=viewhits'),
						array('name' => 'New Advertisers',
							  'link' => '?sect=retcustomer&mode=newcustomers'),
						array('name' => 'New Customers',
							  'link' => '?sect=regcustomer&mode=newcustomers'),
						array('name' => '404 Page Hits',
							  'link' => '?sect=404&mode=view'),
						)
	//					array('name' => 'Page Hits',
	//						  'link' => '?sect=page_hits&mode=view'),
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Orders'] == 1) {
	
	// orders section
	$section_link = array('title' => 'Orders',
						'links' => array(array('name' => 'Recent Orders',
							  'link' => '?sect=orders&mode=recent_orders'),
						array('name' => 'All Orders',
							  'link' => '?sect=orders&mode=all_orders'),
						array('name' => 'Create Certificate',
							  'link' => '?sect=createcertificate&mode=new'),
						array('name' => 'Active Certificates',
							  'link' => '?sect=orders&mode=active_certificates_listing'),
						array('name' => 'Inactive Certificates',
							  'link' => '?sect=orders&mode=inactive_certificates_listing'),
						array('name' => 'Processed Advertiser Memberships',
							  'link' => '?sect=orders&mode=processed_advertiser_mems'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Email'] == 1) {
	
	// admin users section
	$section_link = array('title' => 'Email',
						'links' => array(array('name' => 'Email Customers',
							  'link' => '?sect=email&mode=customers'),
						array('name' => 'Email Customers By State',
							  'link' => '?sect=email&mode=statecustomers'),
						array('name' => 'Email Advertisers',
							  'link' => '?sect=email&mode=advertisers'),
						array('name' => 'Email By State Advertisers',
							  'link' => '?sect=email&mode=stateadvertisers'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Admin Users'] == 1) {
	
	// admin users section
	$section_link = array('title' => 'Admin Users',
						'links' => array(array('name' => 'Users List',
							  'link' => '?sect=admin_users&mode=admin_users_listing'),
						array('name' => 'Add New User',
							  'link' => '?sect=admin_users&mode=new_user'),
						)
						);
	
	$left_nav .= left_menu_sub($section_link);
	
  }
  
  // added to allow user lockout
  if ($_SESSION['allowed_access']['Settings'] == 1) {
	
	$section_link = array();
	
	// settings section
	$section_link['title'] = 'Settings';
	// pull and populate settings menu
	$sql_query = "SELECT
					id,
					name
				 FROM
					settings_groups
				 ORDER BY
					sort_order ASC, name ASC
				 ";
	$rows = $dbh->queryAll($sql_query);
	$i = 0;
	foreach($rows as $settings_g) {
	$section_link['links'][$i]['name'] = $settings_g['name'];
	$section_link['links'][$i]['link'] = '?sect=settings&id='.$settings_g['id'];
	$i++;
	}
	$section_link['links'][$i]['name'] = 'Update XML Sitemaps';
	$section_link['links'][$i]['link'] = '../tools/generate_sitemaps/';
	$i++;
	$section_link['links'][$i]['name'] = 'Generate City Category Search Friendly Names';
	$section_link['links'][$i]['link'] = '../tools/import_states.php';
	$i++;
	$section_link['links'][$i]['name'] = 'Page Cache Admin';
	$section_link['links'][$i]['link'] = '../tools/cache_admin/';
	$section_link['links'][$i]['name'] = 'API Access Settings';
	$section_link['links'][$i]['link'] = '?sect=apiaccess&mode=view';
	$left_nav .= left_menu_sub($section_link);
  }
  
  $left_nav .= T.'<ul>' . LB;
  $left_nav .= T.'<li><a href="'.SITE_URL.'logoff.php">Logout</a></li>' . LB;
  $left_nav .= T.'</ul>' . LB;
  
return $left_nav;
}


// print sub menu
function left_menu_sub($section_link) {
  
  $left_nav = '<ul><li><h2>'.$section_link['title'].'</h2>';
  
  $left_nav .= T.'<ul>'.LB;
  
  $cl_count = count($section_link['links']);
  
  for ($i = 0; $i < $cl_count; $i++) {
  $left_nav .= T.T.'<li>'.print_link(SITE_ADMIN_SSL_URL,$section_link['links'][$i]['link'],$section_link['links'][$i]['name']).'</li>'.LB;
  }
  
  $left_nav .= T.'</ul></li></ul>' . LB;
  
return $left_nav;
}

?>