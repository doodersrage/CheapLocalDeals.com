<?PHP
// load application header
require('../../includes/application_top.php');
// load forms
$page_output = '<script type="text/javascript" src="includes/libs/jquery.popupwindow.js"></script>
				<script type="text/javascript" src="includes/js/popwindow.js"></script>
				<script type="text/javascript" src="advertiser-signup/proc.js"></script>
				<link rel="stylesheet" type="text/css" href="advertiser-signup/style.css"/>
				<table cellpadding="15" class="tbl_page_content" cellspacing="0">
      <tr>
        <td><table width="740" border="0" align="center" cellpadding="0" cellspacing="0" style="background:#FFF">
            <tr>
              <td align="center" style="padding-bottom: 10px;"><span class="advert_form_header">Questions or no time to fill out our form? Call us, we can help! 866.283.6809</span></td>
            </tr>
            <tr>
              <td width="80%"><form enctype="multipart/form-data" name="form1" method="post" action="" class="create_advertiser_account_frm">
			  '.$form_write->input_hidden('form_submit',1);
					
// print company info form
require(SITE_DIR.'advertiser-signup/sections/compInfo.php');
// print company bio form
require(SITE_DIR.'advertiser-signup/sections/compBio.php');
// print category select form
require(SITE_DIR.'advertiser-signup/sections/categories.php');
// print hours of operation form
require(SITE_DIR.'advertiser-signup/sections/hoo.php');
// print payment methods form
require(SITE_DIR.'advertiser-signup/sections/paymentMethods.php');
//// print certificate amounts form
//require('sections/certAmounts.php');
// print account info form
require(SITE_DIR.'advertiser-signup/sections/accInfo.php');
				  
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
	
echo create_warning_box($page_output,'Create Advertiser Account');
?>