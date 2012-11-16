<?PHP

  // page output
  $page_output = '<center><strong>Thank you for your order. You will receive an email shortly containing information about your order.</strong></center>';
  $page_output .= '<center><a href="'.SITE_SSL_URL.'customer_admin/"><strong><font color="#0000FF">Click here to view and print your recently purchased certificates.</font></strong></a></center>';

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Checkout Success';
$page_meta_description = 'Checkout Success';
$page_meta_keywords = 'shopping cart';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>