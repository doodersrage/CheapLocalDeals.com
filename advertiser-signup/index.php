<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Advertiser Signup Page';
$page_meta_description = 'Create an account with us today.';
$page_meta_keywords = 'Assign keywords here';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// load form processor
require('process.php');

// load forms
$page_output .= '<script type="text/javascript" src="includes/libs/jquery.popupwindow.js"></script>
				<script type="text/javascript" src="includes/js/popwindow.js"></script>
				<script type="text/javascript" src="advertiser-signup/proc.js"></script>
				<link rel="stylesheet" type="text/css" href="advertiser-signup/style.css"/>
				<table cellpadding="15" class="tbl_page_content" cellspacing="0">
      <tr>
        <td><table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" style="padding-bottom: 10px;"><span class="advert_form_header">Questions or no time to fill out our form? Call us, we can help! 866.283.6809</span></td>
            </tr>
            <tr>
              <td width="80%"><form enctype="multipart/form-data" name="form1" method="post" action="" class="create_advertiser_account_frm">
			  '.$form_write->input_hidden('form_submit',1);
					
// print company info form
require('sections/compInfo.php');
// print company bio form
require('sections/compBio.php');
// print category select form
require('sections/categories.php');
// print hours of operation form
require('sections/hoo.php');
// print payment methods form
require('sections/paymentMethods.php');
//// print certificate amounts form
//require('sections/certAmounts.php');
// print account info form
require('sections/accInfo.php');
				  
$page_output .= '<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
					<tr>
                      <td align="center" id="subFrm">
						<div id="navGator">
						<a class="frmLnks" id="frmLnk1" href="javascript: void(0)" onclick="pageSel(1)">1</a> 
						<a class="frmLnks" id="frmLnk2" href="javascript: void(0)" onclick="pageSel(2)">2</a> 
						<a class="frmLnks" id="frmLnk3" href="javascript: void(0)" onclick="pageSel(3)">3</a> 
						<a class="frmLnks" id="frmLnk4" href="javascript: void(0)" onclick="pageSel(4)">4</a> 
						<a class="frmLnks" id="frmLnk5" href="javascript: void(0)" onclick="pageSel(5)">5</a> 
						<a class="frmLnks" id="frmLnk6" href="javascript: void(0)" onclick="pageSel(6)">6</a> 
						</div>
					  </td>
                    </tr>
                  </table>
				  <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
					<tr>
                      <td align="left"><input id="subLft" class="submit_btn" type="button" name="previous" value="Previous" onclick="selPrevPage()"></td>
                      <td align="right"><input id="subRht" class="submit_btn" type="button" name="next" value="Next" onclick="selNextPage()"><input id="subBtn" class="submit_btn" type="submit" name="Submit" value="Create Account"></td>
                    </tr>
                  </table></form></td>
            </tr>
          </table></td>
      </tr>
    </table>';
	
// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'blank-wobox.php');

//	print_r($cert_requirements);
//	if(is_array($certificate_levels)) {
//		foreach($cert_requirements as $id => $value) {
//			echo $cert_requirements[$id].' ';
//			echo $requirement_text[$id][$value].' ';
//			echo '$requirement_text['.$id.']['.$value.']'.' ';
//			echo $requirement_text[$id]['excludes'].'<br>';
//		}
//	}

	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>